<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("RegStaff"))
		header('Location: /index.php');

	$year = isset($_GET["year"]) ? $_GET["year"] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
	$engravedOnly = isset($_GET["engravedOnly"]) && $_GET["engravedOnly"] == 1;
	$conYear = $year - 1980;
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=Capricon' . $conYear . 'StaffList' . ($engravedOnly ? "Engraved" : "") . date("Ymd") . '.csv');

	$output = fopen('php://output', 'w');
	if($engravedOnly)
	{
		fputcsv($output, array('Badge Number', 'Badge Name', 'Department', 'Badge Type'));
		$sql = "SELECT BadgeNumber, BadgeName, Department, CASE WHEN BadgeTypeID = 3 THEN 'Silver' ELSE 'Gold' END AS BadgeType FROM PurchasedBadges WHERE Year = $year AND BadgeTypeID IN (3, 5) ORDER BY BadgeNumber";
	}
	else
	{
		fputcsv($output, array('Badge Number', 'Name', 'Badge Name', 'Department', 'Badge Type'));
		$sql = "SELECT BadgeNumber, CASE WHEN pb.PeopleID IS NULL THEN CONCAT(ot.FirstName, ' ', ot.LastName) ELSE CONCAT(p.FirstName, ' ', p.LastName) END AS Name, pb.BadgeName, Department, Description AS BadgeType FROM PurchasedBadges pb LEFT OUTER JOIN People p ON p.PeopleID = pb.PeopleID LEFT OUTER JOIN OneTimeRegistrations ot ON ot.OneTimeID = pb.OneTimeID JOIN BadgeTypes bt ON pb.BadgeTypeID = bt.BadgeTypeID WHERE Year = 2015 AND pb.BadgeTypeID IN (3, 4, 5) ORDER BY BadgeNumber";
	}

	$result = $db->query($sql);
	while($row = $result->fetch_array(MYSQLI_ASSOC))
		fputcsv($output, $row);
?>

