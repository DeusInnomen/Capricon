<?php
session_start();
include_once('includes/functions.php');
include_once('includes/paypal.php');

$year = date("n") >= 3 ? date("Y") + 1: date("Y");
$capriconYear = $year - 1980;
$invoiceIds = $_POST["invoiceIDs"];

$result = $db->query("SELECT i.InvoiceID, i.InvoiceType, i.Status, ils.SubTotal, ils.Taxes, ils.TotalDue, i.Created, i.Sent, i.Fulfilled, i.Cancelled, il.LineNumber, il.Description, "
    . "il.Price, il.Tax, il.ReferenceID FROM Invoice i JOIN InvoiceLine il ON i.InvoiceID = il.InvoiceID JOIN (SELECT InvoiceID, SUM(Price) AS SubTotal, SUM(Tax) AS Taxes, "
    . "SUM(Price) + SUM(Tax) AS TotalDue FROM InvoiceLine GROUP BY InvoiceID) ils ON i.InvoiceID = ils.InvoiceID WHERE i.InvoiceID IN ($invoiceIds) ORDER BY Created DESC, LineNumber ASC");
$invoices = array(); // Full invoices with lines
$invoicesShort = array(); // Just the first line, for Paypal processing
while($row = $result->fetch_array()) {
    if(!isset($invoices[$row["InvoiceID"]])) {
        $invoices[$row["InvoiceID"]] = array();
        $invoicesShort[] = $row;
    }
    $invoices[$row["InvoiceID"]][] = $row;
}
$result->close();

$priceTotal = 0.0;
$taxTotal = 0.0;
$total = 0.0;
$num = -1;

$_SESSION["InvoiceIDs"] = $invoiceIds;
unset($_SESSION["InvoiceIDs"]);

$padata =	'&METHOD=SetExpressCheckout' .
          '&RETURNURL=' . urlencode("https://registration.capricon.org/processInvoices.php") .
        '&CANCELURL=' . urlencode("https://registration.capricon.org/invoices.php") .
        '&PAYMENTREQUEST_0_PAYMENTACTION=SALE';

foreach($invoicesShort as $invoice)
{
    $num++;
    $price = floatval($invoice["SubTotal"]);
    $tax = floatval($invoice["Taxes"]);
    $thisTotal = floatval($invoice["TotalDue"]);

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
    if($tax > 0.0)
        $padata .= '&L_PAYMENTREQUEST_0_TAXAMT' . $num . '=' . urlencode(sprintf("%01.2f", $tax));

    $priceTotal += $price;
    $taxTotal += $tax;
    $total += $thisTotal;
}

$padata .=	'&NOSHIPPING=1' .
            '&PAYMENTREQUEST_0_ITEMAMT=' . urlencode(sprintf("%01.2f", $priceTotal)) .
            '&PAYMENTREQUEST_0_AMT=' . urlencode(sprintf("%01.2f", $total));
if($taxTotal > 0)
    $padata .= '&PAYMENTREQUEST_0_TAXAMT=' . urlencode(sprintf("%01.2f", $taxTotal));
$padata .=  '&PAYMENTREQUEST_0_CURRENCYCODE=USD' .
        '&LOCALECODE=US' .
        '&LOGOIMG=' . urlencode("https://registration.capricon.org/includes/capricious.png") .
        '&CARTBORDERCOLOR=0000FF' .
        '&ALLOWNOTE=1';
error_log("Paypal Request: $padata");
$paypal = new MyPayPal();
$response = $paypal->PPHttpPost('SetExpressCheckout', $padata);
if("SUCCESS" == strtoupper($response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($response["ACK"]))
{
    //Redirect user to PayPal store with Token received.
    $paypalmode = ($paypalModeSetting == 'sandbox') ? '.sandbox' : '';
    $paypalurl ='https://www' . $paypalmode . '.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=' . $response["TOKEN"];
    header('Location: '. $paypalurl);

}else{
    $_SESSION["PayPalError"] = urldecode($response["L_ERRORCODE0"]) . ": " . urldecode($response["L_LONGMESSAGE0"]);
    header('Location: invoices.php');
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Paying Invoices via Paypal</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="includes/global.js"></script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxnarrow">
            <br />
            <br />
            <br />
            <br />
            <p style="text-align: center;">You are being transferred to PayPal to finalize your invoice(s) payment. Please wait...</p>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>