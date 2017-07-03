<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("Marketing"))
    header('Location: index.php');

$year = isset($_GET["year"]) ? $_GET["year"] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
$engravedOnly = isset($_GET["engravedOnly"]) && $_GET["engravedOnly"] == 1;
$conYear = $year - 1980;
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Capricon' . $conYear . 'MailingAddressList' . date("Ymd") . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('Last Name', 'First Name', 'Address1', 'Address2', 'City', 'State', 'Zip Code', 'Country', 'Last Badge Purchased'));

$sql = "SELECT LastName, FirstName, Address1, IFNULL(Address2, '') AS Address2, City, State, ZipCode, Country, IFNULL(MAX(DATE(pb.Created)), '') AS LastBadgePurchased FROM People p " .
    "LEFT OUTER JOIN PurchasedBadges pb ON p.PeopleID = pb.PeopleID GROUP BY LastName, FirstName, Address1, Address2, City, State, ZipCode, Country ORDER BY LastName ASC";
$result = $db->query($sql);
while($row = $result->fetch_array(MYSQLI_ASSOC))
    fputcsv($output, $row);
?>

