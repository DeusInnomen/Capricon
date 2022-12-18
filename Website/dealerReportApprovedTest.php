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
fputcsv($output, array('# Tables', '# Badges', 'Electrical', 'Table #', 'Status', 'Email', 'Representative Name', 'Company Name',
    'Legal Name', 'Description', 'IL DoR', 'Address1', 'Address2', 'Address3', 'City State', 'Zip', 'Country', 'Phone', 'Phone Type',
    'Dealer\'s Comments', 'Website', 'Capricon Comments', 'Status Reason', 'Invoice Status', 'Invoice Amount'));

$query = <<<EOD
SELECT 
        dp.NumTables, 
        IFNULL(b.Badges + 1, 1), 
        CASE WHEN dp.ElectricalNeeded = 1 
             THEN 'Yes' ELSE 'No' 
        END, 
        '' AS TableNum, 
        dp.Status, 
        CASE WHEN d.OnlyUseThisEmail 
             THEN d.ContactEmail 
             WHEN IFNULL(d.ContactEmail, '') = '' 
             THEN p.Email 
             ELSE d.ContactEmail 
        END, 
        CONCAT(p.FirstName, ' ', p.LastName), 
        d.CompanyName, 
        IFNULL(d.LegalName, ''), 
        IFNULL(d.Description, ''), 
        IFNULL(d.TaxNumber, ''), 
        d.Address1, 
        IFNULL(d.Address2, ''), 
        IFNULL(d.Address3, ''), 
        CONCAT(d.City, ', ', d.State), 
        d.ZipCode, 
        d.Country, 
        IFNULL(d.Phone, ''), 
        IFNULL(d.PhoneType, ''), 
        dp.AddedDetails, 
        IFNULL(d.URL, ''), 
        '' AS CapDetails, 
        dp.StatusReason, 
        IFNULL(i.Status, 'Not Created') AS InvoiceStatus,
        IL.Amount AS InvoiceAmount  
    FROM 
                        DealerPresence dp 
                   JOIN Dealer d ON dp.DealerID = d.DealerID 
                   JOIN People p ON p.PeopleID = d.PeopleID 
        LEFT OUTER JOIN (
            SELECT DealerPresenceID, 
                   COUNT(DealerBadgeID) AS Badges 
              FROM DealerBadges 
              GROUP BY DealerPresenceID
            ) b ON b.DealerPresenceID = dp.DealerPresenceID 
        LEFT OUTER JOIN (
            SELECT i1.InvoiceID, 
                   i1.RelatedRecordID, 
                   i1.Status, 
                   i1.Created, 
                   i1.Sent, 
                   i1.Fulfilled, 
                   i1.Cancelled 
              FROM 
                                Invoice i1 
                LEFT OUTER JOIN Invoice i2 ON i1.RelatedRecordID = i2.RelatedRecordID AND i1.Created < i2.Created AND 
                            i1.InvoiceType = 'Dealer' 
                            WHERE i2.RelatedRecordID IS NULL
            ) I ON I.RelatedRecordID = dp.DealerPresenceID 
        LEFT OUTER JOIN (
            SELECT InvoiceID, 
                   SUM(Price) + SUM(Tax) AS Amount 
              FROM InvoiceLine 
              GROUP BY InvoiceID
            ) IL ON I.InvoiceID = IL.InvoiceID 
    WHERE 
        dp.Year = $year AND 
        dp.Status = 'Approved' 
    ORDER BY 
        d.CompanyName ASC
EOD;

$result = $db->query($query);


while($row = $result->fetch_array(MYSQLI_ASSOC))
    fputcsv($output, $row);
?>

