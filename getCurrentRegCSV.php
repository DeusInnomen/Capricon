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
	header('Content-Disposition: attachment; filename=Capricon' . $conYear . 'RegList' . date("Ymd") . '.csv');

	$output = fopen('php://output', 'w');
	fputcsv($output, array('Badge Number', 'Badge Name', 'First Name', 'Last Name', 'Full Name', 'Purchaser First Name', 'Purchase Last Name', 
		'Purchaser Full Name', 'Badge Type', 'Amount Paid', 'Purchase Date', 'Address 1', 'Address 2', 'City', 'State', 'Zip Code', 'Country', 
		'Email','Department'));
	
	//$result = $db->query("SELECT BadgeNumber, pb.BadgeName, p.FirstName, p.LastName, CONCAT(p.FirstName, ' ', p.LastName) AS FullName, 
	//	p2.FirstName AS PurchaserFirstName, p2.LastName AS PurchaserLastName, CONCAT(p2.FirstName, ' ', p2.LastName) AS PurchaserFullName, 
	//	b.Description AS BadgeType, AmountPaid, pb.Created, p2.Address1, p2.Address2, p2.City, p2.State, p2.ZipCode, p2.Country, 
	//	IFNULL( p.Email, p2.Email) AS Email FROM PurchasedBadges pb JOIN People p ON pb.PeopleID = p.PeopleID JOIN People p2 ON 
	//	pb.PurchaserID = p2.PeopleID JOIN BadgeTypes b ON pb.BadgeTypeID = b.BadgeTypeID WHERE pb.Year = $year ORDER BY BadgeNumber");

	$result = $db->query("SELECT pb.BadgeNumber, pb.BadgeName, 
		CASE WHEN pb.PeopleID IS NULL THEN ot.FirstName ELSE p.FirstName END AS FirstName, 
		CASE WHEN pb.PeopleID IS NULL THEN ot.LastName ELSE p.LastName END AS LastName,
		CASE WHEN pb.PeopleID IS NULL THEN CONCAT(ot.FirstName, ' ', ot.LastName) ELSE CONCAT(p.FirstName, ' ', p.LastName) END AS Name,
		CASE WHEN pb.PurchaserID IS NULL THEN ot2.FirstName ELSE p2.FirstName END AS PurchaserFirstName, 
		CASE WHEN pb.PurchaserID IS NULL THEN ot2.LastName ELSE p2.LastName END AS PurchaserLastName, 
		CASE WHEN pb.PurchaserID IS NULL THEN CONCAT(ot2.FirstName, ' ', ot2.LastName) ELSE CONCAT(p2.FirstName, ' ', p2.LastName) END AS PurchaserFullName, 
		b.Description AS BadgeType, AmountPaid, pb.Created, p2.Address1, p2.Address2, p2.City, p2.State, p2.ZipCode, p2.Country, 
		IFNULL( p.Email, p2.Email) AS Email, pb.Department
		FROM PurchasedBadges pb
		LEFT OUTER JOIN People p ON pb.PeopleID = p.PeopleID 
		LEFT OUTER JOIN People p2 ON pb.PurchaserID = p2.PeopleID 
		JOIN BadgeTypes b ON pb.BadgeTypeID = b.BadgeTypeID 
		LEFT OUTER JOIN OneTimeRegistrations ot ON pb.OneTimeID = ot.OneTimeID 
		LEFT OUTER JOIN OneTimeRegistrations ot2 ON pb.OneTimePurchaserID = ot2.OneTimeID 
		WHERE pb.Year = $year AND pb.Status = 'Paid'
		ORDER BY BadgeNumber");
	while($row = $result->fetch_array(MYSQLI_ASSOC))
		fputcsv($output, $row);
?>

