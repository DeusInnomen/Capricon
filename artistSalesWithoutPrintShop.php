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
header('Content-Disposition: attachment; filename=Capricon' . $conYear . 'ArtistSalesAuction' . date("Ymd") . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('Artist Number', 'Name', 'Total Sales', 'Commission', 'Fees Owed', 'Amount Owed'));

$result = $db->query("SELECT ap.ArtistNumber, CASE WHEN ad.LegalName = '' THEN ad.DisplayName ELSE ad.LegalName END AS Name,
    SUM(IFNULL(a.FinalSalePrice, 0)) AS Sales, CASE WHEN p.IsCharity = 1 THEN 0 ELSE  SUM(IFNULL(a.FinalSalePrice, 0)) * 0.1 END AS Commission,
    SUM(CASE WHEN a.FeesPaid = 0 AND ad.IsEAP = 0 THEN CASE WHEN a.MinimumBid < 100 THEN 0.5 ELSE 1 END ELSE 0 END) AS FeesOwed,
    CASE WHEN p.IsCharity = 1 THEN SUM(IFNULL(a.FinalSalePrice, 0)) ELSE SUM(IFNULL(a.FinalSalePrice, 0)) * 0.9 END - SUM(CASE WHEN a.FeesPaid = 0 AND ad.IsEAP = 0 THEN CASE WHEN a.MinimumBid < 100 THEN 0.5 ELSE 1 END ELSE 0 END) AS AmountOwed
    FROM ArtSubmissions a
    JOIN ArtistPresence ap ON a.ArtistAttendingID = ap.ArtistAttendingID
    JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID
    JOIN People p ON ad.PeopleID = p.PeopleID
    WHERE ap.Year = $year
    GROUP BY ap.ArtistNumber, ad.LegalName
    HAVING SUM(a.IsPrintShop) = 0
    AND SUM(IFNULL(a.FinalSalePrice, 0)) > 0");
while($row = $result->fetch_array(MYSQLI_ASSOC))
    fputcsv($output, $row);
?>

