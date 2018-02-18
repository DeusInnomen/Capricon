<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("RegStaff") && !DoesUserBelongHere("Marketing"))
    header('Location: /index.php');

$year = isset($_GET["year"]) ? $_GET["year"] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
$conYear = $year - 1980;
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Capricon' . $conYear . 'ArtistSalesPrintShop' . date("Ymd") . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('Artist Number', 'Name', 'Total Sales', 'Commission', 'Amount Owed'));

$result = $db->query("SELECT ap.ArtistNumber, CASE WHEN ad.LegalName = '' THEN ad.DisplayName ELSE ad.LegalName END AS Name, 
    SUM((a.QuantitySent - a.QuantitySold) * a.QuickSalePrice) AS Sales,
    CASE WHEN p.IsCharity = 1 THEN 0 ELSE SUM((a.QuantitySent - a.QuantitySold) * a.QuickSalePrice) * 0.1 END AS Commission,
    CASE WHEN p.IsCharity = 1 THEN SUM((a.QuantitySent - a.QuantitySold) * a.QuickSalePrice) ELSE SUM((a.QuantitySent - a.QuantitySold) * a.QuickSalePrice) * 0.9 END AS AmountOwed
    FROM ArtSubmissions a
    JOIN ArtistPresence ap ON a.ArtistAttendingID = ap.ArtistAttendingID
    JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID
    JOIN People p ON ad.PeopleID = p.PeopleID
    WHERE ap.Year = $year AND a.IsPrintShop = 1
    GROUP BY ap.ArtistNumber, ad.LegalName
    HAVING SUM(a.QuantitySold) > 0");
while($row = $result->fetch_array(MYSQLI_ASSOC))
    fputcsv($output, $row);
?>

