<?php
    session_start();
    include_once('includes/functions.php');

    if(!isset($_SESSION['PeopleID']))
	{
		echo '{ "success": false, "message": "No user is logged in." }';
		return;
	}
    if(!DoesUserBelongHere("Treasurer"))
	{
		echo '{ "success": false, "message": "An unknown error has occurred." }';
		return;
	}

	$task = $_POST["task"];
	$year = date("n") >= 3 ? date("Y") + 1: date("Y");

	if($task == "MarkInvoicesAsPaid")
	{
        $invoiceIds = $_POST["ids"];
        $checkNum = $_POST["checkNumber"];
        $message = "";
        $year = date("n") >= 3 ? date("Y") + 1: date("Y");
        $capriconYear = $year - 1980;
        $ref = strtoupper(uniqid());
        if($checkNum == "Cash") {
            $source = "Cash";
        }
        else {
            $source = "Check";
            $ref .= "_$checkNum";
        }
        $peopleID = "";

        $result = $db->query("SELECT i.InvoiceID, i.InvoiceType, i.PeopleID, i.RelatedRecordID, i.Status, ils.SubTotal, ils.Taxes, ils.TotalDue, i.Created, i.Sent, i.Fulfilled, i.Cancelled, il.LineNumber, "
            . "il.Description, il.Price, il.Tax, il.ReferenceID FROM Invoice i JOIN InvoiceLine il ON i.InvoiceID = il.InvoiceID JOIN (SELECT InvoiceID, SUM(Price) AS SubTotal, SUM(Tax) AS Taxes, "
            . "SUM(Price) + SUM(Tax) AS TotalDue FROM InvoiceLine GROUP BY InvoiceID) ils ON i.InvoiceID = ils.InvoiceID WHERE i.InvoiceID IN ($invoiceIds) ORDER BY Created DESC, LineNumber ASC");
        $invoices = array(); // Full invoices with lines
        $invoicesShort = array(); // Just the first line, for Paypal processing
        $priceTotal = 0.0;
        $taxTotal = 0.0;
        $total = 0.0;
        $multiPeopleError = false;
        while($row = $result->fetch_array()) {
            if(!isset($invoices[$row["InvoiceID"]])) {
                $invoices[$row["InvoiceID"]] = array();
                $invoicesShort[] = $row;
                if($peopleID != "" && $peopleID != $row["PeopleID"])
                    $multiPeopleError = true;
                $peopleID = $row["PeopleID"];
                $priceTotal += $row["SubTotal"];
                $taxTotal += $row["Taxes"];
                $total += $row["TotalDue"];
            }
            $invoices[$row["InvoiceID"]][] = $row;
        }
        $result->close();

        if(sizeof($invoices) == 0) {
		    echo '{ "success": false, "message": "There were no invoices chosen to be processed." }';
            return;
        }
        else if($multiPeopleError) {
            echo '{ "success": false, "message": "Invoices for multiple people were selected. Please check to make sure you\'ve selected invoices for a single person." }';
            return;
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

            $result = $db->query("SELECT Email, CONCAT(FirstName, ' ', LastName) AS Name, CompanyName, ContactEmail, IFNULL(OnlyUseThisEmail, 0) AS OnlyUseThisEmail FROM People p LEFT OUTER JOIN Dealer d "
                . "ON p.PeopleID = d.PeopleID WHERE p.PeopleID = $peopleID");
            $row = $result->fetch_array();
            $email = $row["Email"];
            $name = $row["Name"];
            $companyName = $row["CompanyName"];
            $contactEmail = $row["ContactEmail"];
            $onlyUseDealerEmail = $row["OnlyUseThisEmail"] == 1;
            $result->close();

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

                    $result = $db->query("SELECT BadgeID FROM PurchasedBadges WHERE Year = $year AND PeopleID = " . $_SESSION["PeopleID"]);
                    if($result->num_rows > 0) {
                        $hasBadge = true;
                        $result->close();
                    }
                    else
                        $hasBadge = false;

                    $sql = "SELECT p.FirstName, p.LastName, p.BadgeName, d.Address1, d.Address2, d.Address3, d.City, d.State, d.ZipCode, d.Country, d.Phone, d.PhoneType FROM People p "
                        . "JOIN Dealer d ON p.PeopleID = d.PeopleID JOIN DealerPresence dp ON dp.DealerID = d.DealerID WHERE dp.DealerPresenceID = $presenceID";
                    $result = $db->query($sql);
                    $dealer = $result->fetch_array();
                    $result->close();

                    $result = $db->query("SELECT BadgeName, FirstName, LastName, Price, BadgeTypeID FROM DealerBadges WHERE DealerPresenceID = $presenceID");
                    $badges = array();
                    while($row = $result->fetch_array())
                        $badges[] = $row;
                    $result->close();

                    if(!$hasBadge || sizeof($badges) > 0) {
                        $message .= "Badges Generated:\r\n";

                        if(!$hasBadge) {
                            $badgeName = $db->real_escape_string($dealer["BadgeName"]);
                            $badgeNumber = GetNextBadgeNumber($year);
                            foreach($invoiceData as $line) {
                                if($line["Description"] == "Dealer's Badge Fee")
                                    $price = $line["Price"];
                            }

                            $sql = "INSERT INTO PurchasedBadges (Year, PeopleID, PurchaserID, BadgeNumber, BadgeTypeID, BadgeName, Status, " .
                                "OriginalPrice, AmountPaid, PaymentSource, PaymentReference, RecordID, Created) VALUES ($year, " .
                                "$peopleID, $peopleID, $badgeNumber, 1, '$badgeName', 'Paid', $price, $price, '$source', '$ref', $recordID, NOW())";
                            $db->query($sql);

                            $message .= "$badgeName (#$badgeNumber for " . $dealer["FirstName"] . " " . $dealer["LastName"] . ")\r\n";
                        }
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
                    }
                    $message .= "\r\n";

                    break;
            }
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
        $mail->Subject = "Your Capricon Invoice Has Been Received!";

        $body = "Hello! This email acts as notice that we received your payment for invoice(s), and we have marked them as paid. " .
            "Your reference number for this order is '$ref'.\r\n\r\nInvoices processed:\r\n\r\n$message";
        if($taxTotal > 0) {
            $body .= "Sub Total:  " . sprintf("$%01.2f", $priceTotal) . "\r\n";
            $body .= "Taxes:      " . sprintf("$%01.2f", $taxTotal) . "\r\n";
        }
        $body .= "Total Paid: " . sprintf("$%01.2f", $total) . "\r\n\r\n";
        $body .= "If you have any questions regarding the invoices that were paid today, please contact the respective "
            . "departments via their email addresses. (See www.capricon.org for a reference.) Thanks for being a member of Phandemonium!\r\n";
        $mail->Body = $body;
        $mail->Send();

        echo '{ "success": true, "message": "The invoices have been marked as paid." }';
		return;
    }
	else
		echo '{ "success": false, "message": "Unknown request submitted." }';

?>