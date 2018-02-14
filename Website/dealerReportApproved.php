<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("DealerStaff"))
    header('Location: /index.php');

$year = isset($_GET["year"]) ? $_GET["year"] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
$conYear = $year - 1980;
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Capricon' . $conYear . 'ApprovedDealers' . date("Ymd") . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('# Tables', '# Badges', 'Electrical', 'Table #', 'Email', 'Name', 'Company', 'Description', 'IL DoR', 'Address1', 'City State', 'Zip', 'Dealer\'s Comments', 'Website', 'Capricon Comments'));

$result = $db->query("SELECT dp.NumTables, IFNULL(b.Badges + 1, 1), CASE WHEN dp.ElectricalNeeded = 1 THEN 'Yes' ELSE 'No' END, '' AS TableNum, CASE WHEN d.OnlyUseThisEmail THEN d.ContactEmail " 
    . "WHEN IFNULL(d.ContactEmail, '') = '' THEN p.Email ELSE d.ContactEmail END, CONCAT(p.FirstName, ' ', p.LastName), d.CompanyName, IFNULL(d.Description, ''), IFNULL(d.TaxNumber, ''), d.Address1, " 
    . "CONCAT(d.City, ', ', d.State), d.ZipCode, dp.AddedDetails, d.URL, '' AS CapDetails FROM DealerPresence dp JOIN Dealer d ON dp.DealerID = d.DealerID JOIN People p ON " 
    . "p.PeopleID = d.PeopleID LEFT OUTER JOIN (SELECT DealerPresenceID, COUNT(DealerBadgeID) AS Badges FROM DealerBadges GROUP BY DealerPresenceID) b ON b.DealerPresenceID = dp.DealerPresenceID " 
    . "WHERE dp.Year = $year AND dp.Status = 'Approved' ORDER BY d.CompanyName ASC");

while($row = $result->fetch_array(MYSQLI_ASSOC))
    fputcsv($output, $row);
?>

