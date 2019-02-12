<?php
session_start();
include_once('includes/functions.php');
require_once("Stripe/Stripe.php");
include_once('includes/paypal.php');

function HandleCart()
{
    global $db, $stripeKey, $smtpPass;
    Stripe::setApiKey($stripeKey);

    $token = isset($_POST["stripeToken"]) ? $_POST["stripeToken"] : "";
    $method = isset($_POST["method"]) ? $_POST["method"] : "";
    $invoiceIds = isset($_SESSION["InvoiceIDs"]) ? $_SESSION["InvoiceIDs"] : $_POST["invoiceIDs"];
    if(isset($_SESSION["InvoiceIDs"]))
        unset($_SESSION["InvoiceIDs"]);
    error_log("DEBUG: Session InvoiceIDs = '" + $_SESSION["InvoiceIDs"] + "'");
    error_log("DEBUG: Post InvoiceIDs = '" + $_POST["invoiceIDs"] + "'");
    error_log("DEBUG: InvoiceIDs = '$invoiceIds'");
    $ref = "";
    $message = "";
    $year = date("n") >= 3 ? date("Y") + 1: date("Y");
    $capriconYear = $year - 1980;
    $peopleID = $_SESSION["PeopleID"];

    $sql = "SELECT i.InvoiceID, i.InvoiceType, i.PeopleID, i.RelatedRecordID, i.Status, ils.SubTotal, ils.Taxes, ils.TotalDue, i.Created, i.Sent, i.Fulfilled, i.Cancelled, il.LineNumber, "
        . "il.Description, il.Price, il.Tax, il.ReferenceID FROM Invoice i JOIN InvoiceLine il ON i.InvoiceID = il.InvoiceID JOIN (SELECT InvoiceID, SUM(Price) AS SubTotal, SUM(Tax) AS Taxes, "
        . "SUM(Price) + SUM(Tax) AS TotalDue FROM InvoiceLine GROUP BY InvoiceID) ils ON i.InvoiceID = ils.InvoiceID WHERE i.InvoiceID IN ($invoiceIds) ORDER BY Created DESC, LineNumber ASC";
    error_log("DEBUG: Invoice SQL = $sql");

    $result = $db->query($sql);
    $invoices = array(); // Full invoices with lines
    $invoicesShort = array(); // Just the first line, for Paypal processing
    $priceTotal = 0.0;
    $taxTotal = 0.0;
    $total = 0.0;
    while($row = $result->fetch_array()) {
        if(!isset($invoices[$row["InvoiceID"]])) {
            $invoices[$row["InvoiceID"]] = array();
            $invoicesShort[] = $row;
            $priceTotal += $row["SubTotal"];
            $taxTotal += $row["Taxes"];
            $total += $row["TotalDue"];
        }
        $invoices[$row["InvoiceID"]][] = $row;
    }
    $result->close();

    if(sizeof($invoices) == 0)
        return array("success" => false, "message" => "There were no invoices chosen to be processed.");

    // PayPal will call this page directly using these two GET values.
    if(isset($_GET["token"]) && isset($_GET["PayerID"]))
    {
        $token = $_GET["token"];
        $pid = $_GET["PayerID"];
        $method = "PayPal";

        $num = 0;

        $padata =	'&TOKEN=' . urlencode($token) .
                '&PAYERID=' . urlencode($pid) .
                '&PAYMENTREQUEST_0_PAYMENTACTION=SALE';

        foreach($invoicesShort as $invoice)
        {
            $num++;
            $price = floatval($invoice["SubTotal"]);
            $tax = floatval($invoice["Taxes"]);

            if($invoice["InvoiceType"] == "Dealer") {
                $summary = "Dealer Application Fees";
                $description = "Dealer Application Fees for Capricon $capriconYear";
            }
            else {
                $summary = "";
                $description = "";
            }

            $padata .=	'&L_PAYMENTREQUEST_0_NAME' . $num . '=' . urlencode($summary).
                        '&L_PAYMENTREQUEST_0_DESC' . $num . '=' . urlencode($description).
                        '&L_PAYMENTREQUEST_0_AMT' . $num . '=' . urlencode(sprintf("%01.2f", $price)).
                        '&L_PAYMENTREQUEST_0_QTY' . $num . '=1';
            if($tax > 0)
                $padata .= '&L_PAYMENTREQUEST_0_TAXAMT' . $num . '=' . urlencode(sprintf("%01.2f", $tax));
        }

        $padata .=	'&PAYMENTREQUEST_0_ITEMAMT=' . urlencode(sprintf("%01.2f", $priceTotal) ).
                    '&PAYMENTREQUEST_0_AMT=' . urlencode(sprintf("%01.2f", $total));
        if($taxTotal > 0)
            $padata .= '&PAYMENTREQUEST_0_TAXAMT=' . urlencode(sprintf("%01.2f", $taxTotal));
        $padata .=  '&PAYMENTREQUEST_0_CURRENCYCODE=USD';

        $paypal = new MyPayPal();
        $response = $paypal->PPHttpPost('DoExpressCheckoutPayment', $padata);
        if("SUCCESS" == strtoupper($response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($response["ACK"]))
        {
            $ref = $response["PAYMENTINFO_0_TRANSACTIONID"];
        }else{
            return array("success" => false, "message" => "The charge failed: " . urldecode($response["L_LONGMESSAGE0"]));
        }
    }

    $result = $db->query("SELECT Email, CONCAT(FirstName, ' ', LastName) AS Name, CompanyName, ContactEmail, IFNULL(OnlyUseThisEmail, 0) AS OnlyUseThisEmail FROM People p LEFT OUTER JOIN Dealer d "
        . "ON p.PeopleID = d.PeopleID WHERE p.PeopleID = $peopleID");
    $row = $result->fetch_array();
    $email = $row["Email"];
    $name = $row["Name"];
    $companyName = $row["CompanyName"];
    $contactEmail = $row["ContactEmail"];
    $onlyUseDealerEmail = $row["OnlyUseThisEmail"] == 1;
    $result->close();

    $isPending = false;
    if($method == "Mail")
    {
        $isPending = true;
        $source = "Check";
        $ref = strtoupper(uniqid());
    }
    elseif($method == "PayPal")
    {
        $source = "PayPal";
    }
    else // Credit Card
    {
        $source = "Stripe";
        if($token == "")
            return array("success" => false, "message" => "No payment information was provided.");

        try
        {
            // Perform the charge.
            $charge = Stripe_Charge::create(array(
                "amount" => $total * 100,
                "currency" => "usd",
                "card" => $token,
                "description" => "Invoice Payments for $name (#$peopleID)")
                );
            $ref = $db->real_escape_string($charge["id"]);
        }
        catch(Stripe_CardError $e)
        {
            $obj = $e->getJsonBody();
            $err  = $obj['error'];
            return array("success" => false, "message" => "The charge was declined: " . $err['message']);
        }
    }

    if($isPending) {
        foreach($invoices as $invoiceData)
        {
            $invoice = reset($invoiceData);
            $invoiceID = $invoice["InvoiceID"];
            $db->query("UPDATE Invoice SET Status = 'Mailed' WHERE InvoiceID = $invoiceID");
        }

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Port = 587;
        $mail->Host = "mail.capricon.org";
        $mail->Username = "outgoing@capricon.org";
        $mail->Password = $smtpPass;
        $mail->From = "registration@capricon.org";
        $mail->FromName = "Capricon Registration";
        if(!$onlyUseDealerEmail)
            $mail->AddAddress($email, $name);
        if(!empty($contactEmail))
            $mail->AddAddress($contactEmail, $companyName);
        $mail->WordWrap = 135;
        $mail->Subject = "Your Capricon Invoice for Mailing Payment";

        $body = "Hello! This email acts as your receipt for invoices that you will be paying via mail to Capricon. " .
            "Your reference number for this order is '$ref'.\r\n\r\nInvoices processed:\r\n\r\n$message";
        if($taxTotal > 0) {
            $body .= "Sub Total:        " . sprintf("$%01.2f", $priceTotal) . "\r\n";
            $body .= "Taxes:            " . sprintf("$%01.2f", $taxTotal) . "\r\n";
        }
        $body .= "Total To Be Paid: " . sprintf("$%01.2f", $total) . "\r\n\r\n";
        $body .= "Attached to this email is a PDF containing your invoice. Please print this out and mail it with a check for the total amount due to:\r\n\r\n"
            . "Capricon\r\n"
            . "126 E Wing Street #244\r\n"
            . "Arlington Heights, IL 60004\r\n\r\n"
            . "If you have any questions regarding the invoices you have paid today, please contact the respective "
            . "departments via their email addresses. (See www.capricon.org for a reference.) Thanks for being a member of Phandemonium!\r\n";
        $mail->Body = $body;

        $pdf = produceInvoicePDF($invoiceIds);
        $mail->AddStringAttachment($pdf->Output('', 'S'), 'Capricon Invoice ' . date("Ymd") . '.pdf', 'base64', 'application/pdf');
        $mail->Send();
        return array("success" => true, "message" => "An email has been sent with the invoice for mailing, as well as instructions on where to send it.");
    }

    $desc = sizeof($invoices) . " Invoice" . (sizeof($invoices) == 1 ? "" : "s");
    $sql = "INSERT INTO PurchaseHistory (PurchaserID, ItemTypeName, Details, PeopleID, Price, Tax, Total, Year, Purchased, PaymentSource, PaymentReference) " .
        "VALUES ($peopleID, 'Invoices', '$desc', $peopleID, $priceTotal, $taxTotal, $total, $year, NOW(), '$source', '$ref')";
    $db->query($sql);
    $recordID = $db->insert_id;

    $records = "";
    foreach($invoicesShort as $invoiceData)
    {
        $records .= ", ($recordID, " . $invoiceData["InvoiceID"] . ")";
    }
    $records = substr($records, 2);
    $db->query("INSERT INTO InvoicesPaid (RecordID, InvoiceID) VALUES $records");

    $message = "";
    foreach($invoices as $invoiceData)
    {
        $invoice = reset($invoiceData);
        $invoiceID = $invoice["InvoiceID"];
        $type = $invoice["InvoiceType"];
        $peopleID = $invoice["PeopleID"];

        $db->query("UPDATE Invoice SET Status = 'Paid', Fulfilled = NOW() WHERE InvoiceID = $invoiceID");

        $linesDetail = "";
        $thisPrice = 0;
        $thisTax = 0;
        $thisTotal = 0;
        foreach($invoiceData as $line) {
            $linesDetail .=  $line["LineNumber"] . ". " . $line["Description"] . " @ " . sprintf("$%01.2f", $line["Price"]);
            if($line["Tax"] != 0)
                $linesDetail .= " (+ " . sprintf("$%01.2f", $line["Tax"]) . ")";
            $linesDetail .= "\r\n";
            $thisPrice += $line["Price"];
            $thisTax += $line["Tax"];
            $thisTotal += $line["Price"] + $line["Tax"];
        }

        switch($type)
        {
            case "Dealer":
                $message .= "Dealer Invoice Sent " . date("F d, Y g:i a", strtotime(!empty($invoice["Sent"]) ? $invoice["Sent"] : $invoice["Created"])) . "\r\n$linesDetail\r\n";
                if($thisTax > 0) {
                    $message .=  "Sub Total:     " . sprintf("$%01.2f", $thisPrice) . "\r\n";
                    $message .=  "Taxes:         " . sprintf("$%01.2f", $thisTax) . "\r\n";
                }
                $message .=  "Invoice Total: " . sprintf("$%01.2f", $thisTotal) . "\r\n\r\n";

                $presenceID = $invoice["RelatedRecordID"];

                $result = $db->query("SELECT IFNULL(BadgeFee, 45.00) AS BadgeFee FROM DealerConfig WHERE Year = $year");
                $row = $result->fetch_array();
                $price = $row["BadgeFee"];
                $result->close();

                $sql = "SELECT p.FirstName, p.LastName, p.BadgeName, d.Address1, d.Address2, d.Address3, d.City, d.State, d.ZipCode, d.Country, d.Phone, d.PhoneType FROM People p "
                    . "JOIN Dealer d ON p.PeopleID = d.PeopleID JOIN DealerPresence dp ON dp.DealerID = d.DealerID WHERE dp.DealerPresenceID = $presenceID";
                $result = $db->query($sql);
                $dealer = $result->fetch_array();
                $result->close();

                $badgeName = $db->real_escape_string($dealer["BadgeName"]);
                $badgeNumber = GetNextBadgeNumber($year);
                foreach($invoiceData as $line) {
                    if($line["Description"] == "Dealer's Badge Fee")
                        $price = $line["Price"];
                }
                $message .= "Badges Generated:\r\n$badgeName (#$badgeNumber for " . $dealer["FirstName"] . " " . $dealer["LastName"] . ")\r\n";

                $sql = "INSERT INTO PurchasedBadges (Year, PeopleID, PurchaserID, BadgeNumber, BadgeTypeID, BadgeName, Status, " .
                    "OriginalPrice, AmountPaid, PaymentSource, PaymentReference, RecordID, Created) VALUES ($year, " .
                    "$peopleID, $peopleID, $badgeNumber, 1, '$badgeName', 'Paid', $price, $price, '$source', '$ref', $recordID, NOW())";
                $db->query($sql);

                $result = $db->query("SELECT BadgeName, FirstName, LastName, Price, BadgeTypeID FROM DealerBadges WHERE DealerPresenceID = $presenceID");
                $badges = array();
                while($row = $result->fetch_array())
                    $badges[] = $row;
                $result->close();
                foreach($badges as $badge) {
                    $badgeName = $badge["BadgeName"];
                    $price = $badge["Price"];
                    $badgeTypeID = $badge["BadgeTypeID"];
                    $badgeNumber = GetNextBadgeNumber($year);

                    $sql = "INSERT INTO OneTimeRegistrations (FirstName, LastName, Address1, Address2, City, State, Country, ZipCode, Phone1, Phone1Type) "
				        . "VALUES ('" . $db->real_escape_string($badge["FirstName"])
                        . "', '" . $db->real_escape_string($badge["LastName"])
                        . "', '" . $db->real_escape_string($dealer["Address1"])
                        . "', '" . $db->real_escape_string($dealer["Address2"])
                        . "', '" . $db->real_escape_string($dealer["City"])
                        . "', '" . $db->real_escape_string($dealer["State"])
                        . "', '" . $db->real_escape_string($dealer["Country"])
                        . "', '" . $db->real_escape_string($dealer["ZipCode"])
                        . "', '" . $db->real_escape_string($dealer["Phone"])
                        . "', '" . $db->real_escape_string($dealer["PhoneType"]) . "')";
                    $db->query($sql);
                    $oneTimeID = $db->insert_id;

                    $sql = "INSERT INTO PurchasedBadges (Year, OneTimeID, PurchaserID, BadgeNumber, BadgeTypeID, BadgeName, Status, " .
                        "OriginalPrice, AmountPaid, PaymentSource, PaymentReference, RecordID, Created) VALUES ($year, " .
                        "$oneTimeID, $peopleID, $badgeNumber, $badgeTypeID, '$badgeName', 'Paid', $price, $price, '$source', '$ref', $recordID, NOW())";
                    $db->query($sql);
                    $message .= "$badgeName (#$badgeNumber for " . $badge["FirstName"] . " " . $badge["LastName"] . ")\r\n";
                }
                $message .= "\r\n";

                break;
        }
    }

    $postMessage = "Your invoices have been marked as paid. ";
    if(strlen($ref) > 0) $postMessage .= "Your reference number for this purchase is '$ref'. Please keep it for your records. ";
    $postMessage .= "A copy of your purchase information has been sent to your email address.";
    $postMessage .= "The following invoices were processed:<br />" . str_replace("\r\n", "<br>", $message);

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Port = 587;
    $mail->Host = "mail.capricon.org";
    $mail->Username = "outgoing@capricon.org";
    $mail->Password = $smtpPass;
    $mail->From = "registration@capricon.org";
    $mail->FromName = "Capricon Registration";
    if(!$onlyUseDealerEmail)
        $mail->AddAddress($email, $name);
    if(!empty($contactEmail))
        $mail->AddAddress($contactEmail, $companyName);
    $mail->WordWrap = 135;
    $mail->Subject = "Receipt for your Capricon Invoice Payments";

    $body = "Hello! This email acts as your receipt for invoices that you have paid at the Capricon Registration " .
        "System website. Your reference number for this order is '$ref'.\r\n\r\nInvoices processed:\r\n\r\n$message";
    if($taxTotal > 0) {
        $body .= "Sub Total:  " . sprintf("$%01.2f", $priceTotal) . "\r\n";
        $body .= "Taxes:      " . sprintf("$%01.2f", $taxTotal) . "\r\n";
    }
    $body .= "Total Paid: " . sprintf("$%01.2f", $total) . "\r\n\r\n";
    $body .= "If you have any questions regarding the invoices you have paid today, please contact the respective "
        . "departments via their email addresses. (See www.capricon.org for a reference.) Thanks for being a member of Phandemonium!\r\n";
    $mail->Body = $body;
    $mail->Send();

    return array("success" => true, "message" => $postMessage);
}

$results = handleCart();
$success = $results["success"];
$message = str_replace("\"", "'", $results["message"]);
?>
<html>
<head>
    <title>Capricon Registration Site -- Processing Invoices</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css" />
</head>
<body>
    <form action="invoices.php" method="post" name="results">
        <input type="hidden" name="processed" value="true" />
        <input type="hidden" name="success" value="<?php echo $success; ?>" />
        <input type="hidden" name="message" value="<?php echo $message; ?>" />
    </form>
    <div class="centeredMessage">
        <h2>Processing your invoices, please wait...</h2>
    </div>
    <script type="text/javascript">
		document.results.submit();
    </script>
</body>
</html>