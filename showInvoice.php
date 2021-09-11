<?php
session_start();
include_once('includes/functions.php');

if(!isset($_SESSION["PeopleID"]))
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
if(!isset($_POST["invoiceIDs"]))
    header('Location: invoices.php');

$pdf = produceInvoicePDF($_POST["invoiceIDs"]);
if($pdf == null) {
    header('Location: invoices.php');
    exit();
}
$pdf->Output();

?>