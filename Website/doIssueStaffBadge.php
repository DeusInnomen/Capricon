<?php
	session_start();
	include_once('includes/functions.php');
	if(!DoesUserBelongHere("RegLead"))
		header('Location: /index.php');
	
	$department = $db->real_escape_string($_POST["department"]);
	$badgeTypeID = $db->real_escape_string($_POST["badgeType"]);
	if(isset($_POST["peopleID"]))
	{
		$peopleID = $db->real_escape_string($_POST["peopleID"]);
		$result = $db->query("SELECT BadgeName, ParentID FROM People WHERE PeopleID = $peopleID");
		$row = $result->fetch_array();
		$badgename = $db->real_escape_string($row["BadgeName"]);
		$oneTimeID = "NULL";
		$result->close();		
	}
	else
	{
		$peopleID = "NULL";
		$first = $db->real_escape_string($_POST["firstname"]);
		$last = $db->real_escape_string($_POST["lastname"]);		
		$sql = "INSERT INTO OneTimeRegistrations (FirstName, LastName, Address1, City) VALUES ('$first', '$last', 'STAFF BADGE', '')";
		$db->query($sql);
		$oneTimeID = $db->insert_id;
		$badgename = $db->real_escape_string($_POST["BadgeName"]);
		if(trim($badgename) == "") $badgename = trim($first . " " . $last);
	}
	
	$year = date("n") >= 3 ? date("Y") + 1: date("Y");
	$sql = "SELECT CASE WHEN EXISTS (SELECT BadgeNumber FROM PurchasedBadges WHERE BadgeNumber = 1 AND Year = $year) THEN 99999 ELSE 1 END AS Next UNION SELECT (p1.BadgeNumber + 1) as Next FROM PurchasedBadges p1 WHERE NOT EXISTS (SELECT p2.BadgeNumber FROM PurchasedBadges p2 WHERE p2.BadgeNumber = p1.BadgeNumber + 1 AND p2.Year = $year) AND p1.Year = $year ORDER BY Next LIMIT 1";
	$result = $db->query($sql);
	$row = $result->fetch_array();
	$badgeNumber = $row["Next"];
	$result->close();
	$source = $badgeTypeID == 1 ? "Comp" : "Generated";

    $sql = "INSERT INTO PurchaseHistory (PurchaserID, PurchaserOneTimeID, ItemTypeName, ItemTypeID, Details, PeopleID, OneTimeID, Price, Year, Purchased, PaymentSource, PaymentReference) VALUES ($peopleID, $oneTimeID, 'Badge', $badgeTypeID, '$badgename', $peopleID, $oneTimeID, 0.00, $year, NOW(), '$source', 'NoCharge')";
    $db->query($sql);
    $recordID = $db->insert_id;

    $sql = "INSERT INTO PurchasedBadges (Year, PeopleID, OneTimeID, PurchaserID, OneTimePurchaserID, BadgeNumber, BadgeTypeID, BadgeName, Department, Status, OriginalPrice, AmountPaid, PaymentSource, PaymentReference, PromoCodeID, CertificateID, RecordID, Created) VALUES ($year, $peopleID, $oneTimeID, $peopleID, $oneTimeID, $badgeNumber, $badgeTypeID, '$badgename', '$department', 'Paid', 0.00, 0.00, '$source', 'NoCharge', NULL, NULL, $recordID, NOW())";
	if($db->query($sql) === false)
		echo '{ "success": false, "message": "' . $db->error . '" }';
	else
	{
		$order = $_POST["resultsOrder"] == "Department" ? "Department, LastName" : "BadgeNumber";
		$sql  = "SELECT pb.BadgeID, pb.BadgeNumber, CASE WHEN pb.PeopleID IS NULL THEN CONCAT(ot.FirstName, ' ', ot.LastName) ELSE CONCAT(p.FirstName, ' ', p.LastName) END AS Name, pb.BadgeName, CASE WHEN pb.PeopleID IS NULL THEN ot.FirstName ELSE p.FirstName END AS FirstName, CASE WHEN pb.PeopleID IS NULL THEN ot.LastName ELSE p.LastName END AS LastName, pb.Department, bt.Description AS BadgeType FROM PurchasedBadges pb JOIN BadgeTypes bt ON pb.BadgeTypeID = bt.BadgeTypeID LEFT OUTER JOIN People p ON p.PeopleID = pb.PeopleID LEFT OUTER JOIN OneTimeRegistrations ot ON ot.OneTimeID = pb.OneTimeID WHERE pb.Year = $year AND pb.Department IS NOT NULL AND pb.BadgeTypeID IN (3, 4, 5) ORDER BY $order";
		
		$badgeData = "";
		$result = $db->query($sql);
		if($result->num_rows > 0)
		{
			$rows = array();
			while($row = $result->fetch_array())
				$rows[] = $row;
			$result->close();
			$badgeData = json_encode($rows);
		}
		echo '{ "success": true, "message": "Badge ' . $badgeNumber . ' created.", "badgeData": ' . $badgeData . ' }';
	}
?>