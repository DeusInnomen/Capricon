<?php

?>
<!--
SELECT DISTINCT ph.RecordID, ph.PurchaserID, ph.PurchaserOneTimeID,
CASE WHEN ph.PurchaserID IS NULL THEN o.FirstName ELSE p.FirstName END AS FirstName,
CASE WHEN ph.PurchaserID IS NULL THEN o.LastName ELSE p.LastName END AS LastName,
CASE WHEN ph.PurchaserID IS NULL THEN pbo.BadgeName ELSE pbp.BadgeName END AS BadgeName,
ph.ItemTypeName, ph.Price AS TotalPrice, asa.Price AS PiecePrice,
ad.DisplayName, asu.Title,
ph.Purchased, ph.PaymentSource, ph.PaymentReference,
CASE WHEN ph.PaymentSource = 'Check' THEN SUBSTRING(ph.PaymentReference, 16) ELSE NULL END AS CheckNumber
FROM PurchaseHistory ph
LEFT OUTER JOIN People p ON ph.PurchaserID = p.PeopleID
LEFT OUTER JOIN OneTimeRegistrations o ON ph.PurchaserOneTimeID = o.OneTimeID
LEFT OUTER JOIN ArtSales asa ON ph.RecordID = asa.RecordID
LEFT OUTER JOIN ArtSubmissions asu ON asa.ArtID = asu.ArtID
LEFT OUTER JOIN ArtistPresence ap ON asu.ArtistAttendingID = ap.ArtistAttendingID
LEFT OUTER JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID
LEFT OUTER JOIN PurchasedBadges pbp ON ph.PeopleID = pbp.PeopleID AND ph.Year = pbp.Year
LEFT OUTER JOIN PurchasedBadges pbo ON ph.OneTimeID = pbo.OneTimeID AND ph.Year = pbo.Year
WHERE ph.Year = 2015 AND ph.ItemTypeName IN ('Print Shop', 'Auction Sales', 'Hanging Fees')
AND DATE(ph.Purchased) = '2015-02-15'
ORDER BY LastName, FirstName, RecordID
-->