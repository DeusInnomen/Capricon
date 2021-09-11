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
header('Content-Disposition: attachment; filename=Capricon' . $conYear . 'AllDealers' . date("Ymd") . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('# Tables', '# Badges', 'Electrical', 'Table #', 'Status', 'Email', 'Representative Name', 'Company Name', 'Legal Name', 'Description', 'IL DoR', 'Address1', 'Address2', 
    'Address3', 'City State', 'Zip', 'Country', 'Phone', 'Phone Type', 'Dealer\'s Comments', 'Website', 'Capricon Comments'));

$result = $db->query("SELECT dp.NumTables, IFNULL(b.Badges + 1, 1), CASE WHEN dp.ElectricalNeeded = 1 THEN 'Yes' ELSE 'No' END, '' AS TableNum, dp.Status, CASE WHEN d.OnlyUseThisEmail THEN d.ContactEmail "
    . "WHEN IFNULL(d.ContactEmail, '') = '' THEN p.Email ELSE d.ContactEmail END, CONCAT(p.FirstName, ' ', p.LastName), d.CompanyName, IFNULL(d.LegalName, ''), IFNULL(d.Description, ''), IFNULL(d.TaxNumber, ''), d.Address1, IFNULL(d.Address2, ''), IFNULL(d.Address3, ''), "
    . "CONCAT(d.City, ', ', d.State), d.ZipCode, d.Country, IFNULL(d.Phone, ''), IFNULL(d.PhoneType, ''), dp.AddedDetails, IFNULL(d.URL, ''), '' AS CapDetails FROM DealerPresence dp JOIN Dealer d ON dp.DealerID = d.DealerID JOIN People p ON "
    . "p.PeopleID = d.PeopleID LEFT OUTER JOIN (SELECT DealerPresenceID, COUNT(DealerBadgeID) AS Badges FROM DealerBadges GROUP BY DealerPresenceID) b ON b.DealerPresenceID = dp.DealerPresenceID "
    . "WHERE dp.Year = $year ORDER BY d.CompanyName ASC");

while($row = $result->fetch_array(MYSQLI_ASSOC))
    fputcsv($output, $row);
?>

