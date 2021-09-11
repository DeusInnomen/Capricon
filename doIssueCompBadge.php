<?php
	session_start();
	include_once('includes/functions.php');
	if(!DoesUserBelongHere("RegLead"))
		header('Location: index.php');
	
	$department = $db->real_escape_string($_POST["department"]);
	if(isset($_POST["peopleID"]))
	{
		$peopleID = $db->real_escape_string($_POST["peopleID"]);
		$result = $db->query("SELECT BadgeName, ParentID FROM People WHERE PeopleID = $peopleID");
		$row = $result->fetch_array();
		$badgename = $row["BadgeName"];
		$oneTimeID = "NULL";
		$purchaserID = is_null($row["ParentID"]) ? $peopleID : $row["ParentID"];
		$oneTimePurchaserID = "NULL";
		$result->close();		
	}
	else
	{
		$peopleID = "NULL";
		$purchaserID = "NULL";
		$first = $db->real_escape_string($_POST["firstname"]);
		$last = $db->real_escape_string($_POST["lastname"]);		
		$sql = "INSERT INTO OneTimeRegistrations (FirstName, LastName, Address1, City) VALUES ('$first', '$last', 'COMPLIMENTARY BADGE', '')";
		$db->query($sql);
		$oneTimeID = $db->insert_id;
		$oneTimePurchaserID = $oneTimeID;
		$badgename = $db->real_escape_string($_POST["badgename"]);
		if(trim($badgename) == "") $badgename = $first . " " . $last;
	}
	
	$year = date("n") >= 3 ? date("Y") + 1: date("Y");
	$sql = "SELECT CASE WHEN EXISTS (SELECT BadgeNumber FROM PurchasedBadges WHERE BadgeNumber = 150 AND Year = $year) THEN 99999 ELSE 150 END AS Next UNION SELECT (p1.BadgeNumber + 1) as Next FROM PurchasedBadges p1 WHERE NOT EXISTS (SELECT p2.BadgeNumber FROM PurchasedBadges p2 WHERE p2.BadgeNumber = p1.BadgeNumber + 1 AND p2.Year = $year) AND p1.Year = $year HAVING Next >= 150 ORDER BY Next";
	$result = $db->query($sql);
	$row = $result->fetch_array();
	$badgeNumber = $row["Next"];
	$result->close();

	$sql = "INSERT INTO PurchaseHistory (PurchaserID, PurchaserOneTimeID, ItemTypeName, ItemTypeID, Details, PeopleID, OneTimeID, Price, Total, Year, Purchased, PaymentSource, PaymentReference) " . 
        "VALUES ($purchaserID, $oneTimePurchaserID, 'Badge', 1, '$badgename', $peopleID, $oneTimeID, 0.00, 0.00, $year, NOW(), 'Comp', 'NoCharge')";
	$db->query($sql);
    $recordID = $db->insert_id;
	$sql = "INSERT INTO PurchasedBadges (Year, PeopleID, OneTimeID, PurchaserID, OneTimePurchaserID, BadgeNumber, BadgeTypeID, BadgeName, Department, Status, OriginalPrice, AmountPaid, PaymentSource, PaymentReference, PromoCodeID, CertificateID, RecordID, Created) VALUES ($year, $peopleID, $oneTimeID, $purchaserID, $oneTimePurchaserID, $badgeNumber, 1, '$badgename', '$department', 'Paid', 0.00, 0.00, 'Comp', 'NoCharge', NULL, NULL, $recordID, NOW())";
	$db->query($sql);
	
	echo '{ "success": true, "message": "Badge ' . $badgeNumber . ' created." }';
?>