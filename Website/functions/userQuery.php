<?php
	session_start();
	if($_SERVER["HTTPS"] != "on") exit();

	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/includes/functions.php");

	// I may handle more security after Cap 34 by putting in an authentication token to verify the usage of the token comes from
	// the same address that logged in.

	if(empty($_POST["action"])) exit();

	$action = $db->real_escape_string($_POST["action"]);

	if($action == "GetUsers")
	{
		$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 3 ? date("Y") + 1: date("Y"));
		$sql = "SELECT p.PeopleID, p.FirstName, p.LastName, p.Address1, p.Address2, p.City, p.State, p.ZipCode, p.Country, p.Phone1, p.Phone1Type, p.Phone2, p.Phone2Type, p.Email, p.Registered, p.BadgeName, p.Banned, p.ParentID, p.HeardFrom, p.LastChanged, CONCAT(par.FirstName, ' ', par.LastName) AS ParentName, par.Phone1 AS ParentContact FROM People p LEFT OUTER JOIN People par ON par.PeopleID = p.ParentID ";

		if(!empty($_POST["id"]))
			$sql .= "WHERE PeopleID = " . $db->real_escape_string($_POST["id"]);
		elseif(!empty($_POST["badgeNumber"]))
			$sql .= "JOIN PurchasedBadges pb ON pb.PeopleID = p.PeopleID WHERE YEAR = $year AND BadgeNumber = " . $db->real_escape_string($_POST["badgeNumber"]);
		elseif(!empty($_POST["whereField"]) && !empty($_POST["whereTerm"]))
		{
		 $sql .= "WHERE p." . $db->real_escape_string($_POST["whereField"]) . " " .
			 (!empty($_POST["whereSimilar"]) ? "LIKE" : "=") . " '" . $db->real_escape_string($_POST["whereTerm"]) .
			 (!empty($_POST["whereSimilar"]) ? "%" : "") . "'";
		}
		$result = $db->query($sql);

		$people = array();
		if($result->num_rows > 0)
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$people[] = $row;

		header("Content-type: application/json");
		echo json_encode($people);
	}
	elseif($action == "GetBadges")
	{
		$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 3 ? date("Y") + 1: date("Y"));
		if(empty($_POST["id"]))
		{
			$sql = "SELECT pb.BadgeID, pb.BadgeNumber, CASE WHEN pb.PeopleID IS NULL THEN ot.FirstName ELSE p.FirstName END AS FirstName, CASE WHEN pb.PeopleID IS NULL THEN ot.LastName ELSE p.LastName END AS LastName, pb.BadgeName, pb.BadgeTypeID, pb.Status, bt.Description AS BadgeDescription, CONCAT(par.FirstName, ' ', par.LastName) AS ParentName, par.Phone1 AS ParentContact FROM PurchasedBadges pb INNER JOIN BadgeTypes bt ON bt.BadgeTypeID = pb.BadgeTypeID LEFT OUTER JOIN People p ON p.PeopleID = pb.PeopleID LEFT OUTER JOIN OneTimeRegistrations ot ON ot.OneTimeID = pb.OneTimeID LEFT OUTER JOIN People par ON par.PeopleID = pb.PurchaserID AND pb.BadgeTypeID = 2 WHERE pb.Year = $year ";
			if(!empty($_POST["noSilver"]))
				$sql .= "AND pb.BadgeTypeID NOT IN (3, 5) ";
			$sql .= "ORDER BY pb.BadgeTypeID, pb.BadgeNumber";
		}
		else
		{
			$id = $db->real_escape_string($_POST["id"]);
			$sql = "SELECT pb.BadgeID, pb.BadgeNumber, CASE WHEN pb.PeopleID IS NULL THEN ot.FirstName ELSE p.FirstName END AS FirstName, CASE WHEN pb.PeopleID IS NULL THEN ot.LastName ELSE p.LastName END AS LastName, pb.BadgeName, pb.BadgeTypeID, pb.Status, bt.Description AS BadgeDescription, CONCAT(par.FirstName, ' ', par.LastName) AS ParentName, par.Phone1  AS ParentContact FROM PurchasedBadges pb INNER JOIN BadgeTypes bt ON bt.BadgeTypeID = pb.BadgeTypeID LEFT OUTER JOIN People p ON p.PeopleID = pb.PeopleID LEFT OUTER JOIN OneTimeRegistrations ot ON ot.OneTimeID = pb.OneTimeID LEFT OUTER JOIN People par ON par.PeopleID = pb.PurchaserID AND pb.BadgeTypeID = 2 WHERE pb.BadgeID = $id AND pb.Year = $year";
		}
		$result = $db->query($sql);

		$badges = array();
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$badges[] = $row;
		}

		header("Content-type: application/json");
		echo json_encode($badges);
	}
	elseif($action == "SaveNewPerson" || $action == "UpdatePerson")
	{
		$person = json_decode($_POST["person"], true);
		$password = !empty($password) ? $db->real_escape_string($_POST["password"]) : "NotYetActivated";
		if($person == null)
		{
			header("Content-type: application/json");
			echo json_encode(array("error" => "An invalid request was sent."));
			exit();
		}

		$fName = $db->real_escape_string($person["FirstName"]);
		$lName = $db->real_escape_string($person["LastName"]);
		$add1 = $db->real_escape_string($person["Address1"]);
		$add2 = !empty($person["Address2"]) ? "'" . $db->real_escape_string($person["Address2"]) . "'" : "NULL";
		$city = $db->real_escape_string($person["City"]);
		$state = !empty($person["State"]) ? "'" . $db->real_escape_string($person["State"]) . "'" : "NULL";
		$zip = !empty($person["ZipCode"]) ? "'" . $db->real_escape_string($person["ZipCode"]) . "'" : "NULL";
		$country = !empty($person["Country"]) ? "'" . $db->real_escape_string($person["Country"]) . "'" : "'USA'";
		if(!empty($person["Phone1"]))
		{
			$phone1 = "'" . $db->real_escape_string($person["Phone1"]) . "'";
			$phone1Type = "'" . $db->real_escape_string($person["Phone1Type"]) . "'";
		}
		else
		{
			$phone1 = "NULL";
			$phone1Type = "NULL";
		}
		if(!empty($person["Phone2"]))
		{
			$phone2 = "'" . $db->real_escape_string($person["Phone2"]) . "'";
			$phone2Type = "'" . $db->real_escape_string($person["Phone2Type"]) . "'";
		}
		else
		{
			$phone2 = "NULL";
			$phone2Type = "NULL";
		}
		$heardFrom = !empty($person["HeardFrom"]) ? $db->real_escape_string($person["HeardFrom"]) : "";
		$email = !empty($person["Email"]) ? $db->real_escape_string($person["Email"]) : "";
		$badgeName = $db->real_escape_string($person["BadgeName"]);
        $parentId = !empty($person["ParentID"]) ? $db->real_escape_string($person["ParentID"]) : "NULL";

		if($action == "SaveNewPerson")
		{
			$response = array();
			$sql = "INSERT INTO People (FirstName, LastName, Address1, Address2, City, State, ZipCode, Country, Phone1, Phone1Type, Phone2, Phone2Type, HeardFrom, Email, Password, Registered, BadgeName, ParentID) VALUES ('$fName', '$lName', '$add1', $add2, '$city', $state, $zip, $country, $phone1, $phone1Type, $phone2, $phone2Type, '$heardFrom', '$email', '$password', NOW(), '$badgeName', $parentId)";
			if($db->query($sql) === false)
			{
				$response["result"] = "Failure";
				$response["message"] = $db->error;
			}
			else
			{
				$id = $db->insert_id;
				$response["result"] = "Success";
				$response["peopleID"] = $id;
				if($email != "NULL" && $password == "NotActivatedYet")
					SendPasswordReset($email, $id, true);
			}
		}
		else
		{
			$id = $person["PeopleID"];

			$result = $db->query("SELECT FirstName, LastName, Address1, Address2, City, State, ZipCode, Country, Phone1, Phone1Type, Phone2, Phone2Type, Email, BadgeName, HeardFrom FROM People WHERE PeopleID = $id");
			$info = $result->fetch_array();
			$result->close();

			$changes = "";
			if($info['FirstName'] != $person['FirstName'])
				$changes .= ", FirstName = '$fName'";
			if($info['LastName'] != $person['LastName'])
				$changes .= ", LastName = '$lName'";
			if($info['BadgeName'] != $person['BadgeName'])
				$changes .= ", BadgeName = '$badgeName'";
			if($info['Address1'] != $person['Address1'])
				$changes .= ", Address1 = '$add1'";
			if($info['Address2'] != $person['Address2'])
				$changes .= ", Address2 = $add2";
			if($info['City'] != $person['City'])
				$changes .= ", City = '$city'";
			if($info['State'] != $person['State'])
				$changes .= ", State = $state";
			if($info['ZipCode'] != $person['ZipCode'])
				$changes .= ", ZipCode = $zip";
			if($info['Country'] != $person['Country'])
				$changes .= ", Country = $country";
			if($info['Phone1'] != $person['Phone1'] || $info['Phone1Type'] != $person['Phone1Type'])
				$changes .= ", Phone1 = $phone1, Phone1Type = $phone1Type";
			if($info['Phone2'] != $person['Phone2'] || $info['Phone2Type'] != $person['Phone2Type'])
				$changes .= ", Phone2 = $phone2, Phone2Type = $phone2Type";
			if($info['HeardFrom'] != $person['HeardFrom'])
				$changes .= ", HeardFrom = '$heardFrom'";
			if($info['Email'] != $person['Email'])
				$changes .= ", Email = '$email'";

			$response = array();
			if(strlen($changes) > 0)
			{
				$sql = "UPDATE People SET " . substr($changes, 2) . " WHERE PeopleID = $id";
				if($db->query($sql) === false)
				{
					$response["result"] = "Failure";
					$response["message"] = $db->error;
				}
				else
					$response["result"] = "Success";
			}
            else
                $response["result"] = "Success";
		}

		header("Content-type: application/json");
		echo json_encode($response);
	}
	elseif($action == "ModifyBadge")
	{
		$id = $db->real_escape_string($_POST["badgeID"]);
		$response = array();
		$badgeAction = $_POST["badgeAction"];
		if($badgeAction == "Save")
		{
			$name = $db->real_escape_string($_POST["badgeName"]);
			$db->query("UPDATE PurchasedBadges SET BadgeName = '$name' WHERE BadgeID = $id");
			$response["result"] = "Success";
		}
		elseif($badgeAction == "Transfer")
		{
			$name = $db->real_escape_string($_POST["badgeName"]);
			if(!empty($_POST["newID"]))
			{
				$newID = $db->real_escape_string($_POST["newID"]);
				$result = $db->query("SELECT FirstName, LastName, ParentID FROM People WHERE PeopleID = $newID");
				$row = $result->fetch_array();
				$result->close();
				$purchaserID = is_null($row["ParentID"]) ? $newID : $row["ParentID"];

				$db->query("UPDATE PurchasedBadges SET BadgeName = '$name', PeopleID = $newID, PurchaserID = $purchaserID WHERE BadgeID = $id");
				$response["result"] = "Success";
				$response["newHolder"] = trim($row["FirstName"] . " " . $row["LastName"]);
			}
			else
			{
				$first = $db->real_escape_string($_POST["fName"]);
				$last = $db->real_escape_string($_POST["lName"]);
				$add1 = $db->real_escape_string($_POST["add1"]);
				$add2 = !empty($_POST["add2"]) ? "'" . $db->real_escape_string($_POST["add2"]) . "'" : "NULL";
				$city = $db->real_escape_string($_POST["city"]);
				$state = !empty($_POST["state"]) ? "'" . $db->real_escape_string($_POST["state"]) . "'" : "NULL";
				$zip = !empty($_POST["zip"]) ? "'" . $db->real_escape_string($_POST["zip"]) . "'" : "NULL";
				$country = !empty($_POST["country"]) ? "'" . $db->real_escape_string($_POST["country"]) . "'" : "NULL";
				$phone1 = !empty($_POST["phone1"]) ? "'" . $db->real_escape_string($_POST["phone1"]) . "'" : "NULL";
				$phone1Type = !empty($_POST["phone1Type"]) ? "'" . $db->real_escape_string($_POST["phone1Type"]) . "'" : "NULL";
				$phone2 = !empty($_POST["phone2"]) ? "'" . $db->real_escape_string($_POST["phone2"]) . "'" : "NULL";
				$phone2Type = !empty($_POST["phone2Type"]) ? "'" . $db->real_escape_string($_POST["phone2Type"]) . "'" : "NULL";
				$email = !empty($_POST["email"]) ? $db->real_escape_string($_POST["email"]) : "";
				$heardFrom = !empty($_POST["heardFrom"]) ? $db->real_escape_string($_POST["heardFrom"]) : "";
				$sql = "INSERT INTO People (FirstName, LastName, Address1, Address2, City, State, ZipCode, Country, Phone1, Phone1Type, Phone2, Phone2Type, Email, Password, Registered, BadgeName, HeardFrom) VALUES ('$first', '$last', '$add1', $add2, '$city', $state, $zip, $country, $phone1, $phone1Type, $phone2, $phone2Type, '$email', 'NotYetActivated', NOW(), '$name', '$heardFrom')";
				if($db->query($sql) === false)
				{
					$response["result"] = "Failure";
					$response["message"] = $db->error;
					header("Content-type: application/json");
					echo json_encode($response);
					exit();
				}
				$newID = $db->insert_id;
				if(!empty($email))
					SendPasswordReset($email, $newID, true);
				$response["newPeopleID"] = $newID;

				$db->query("UPDATE PurchasedBadges SET BadgeName = '$name', PeopleID = $newID, PurchaserID = $newID WHERE BadgeID = $id");
				$response["result"] = "Success";
				$response["newHolder"] = trim($first . " " . $last);
			}
		}
		elseif($badgeAction == "ApproveBadge")
		{
			$checkNum = $db->real_escape_string($_POST["checkNum"]);
			$result = $db->query("SELECT PaymentReference FROM PurchasedBadges WHERE BadgeID = $id");
			$row = $result->fetch_array();
			$ref = $row["PaymentReference"];
			$result->close();
			$newRef = $ref . "_#" . $checkNum;

			$db->query("UPDATE PurchasedBadges SET Status = 'Paid', PaymentReference = '$newRef' WHERE BadgeID = $id");
			$db->query("UPDATE PurchaseHistory SET PaymentReference = '$newRef' WHERE PaymentReference = '$ref'");
			$response["result"] = "Success";
		}
		elseif($badgeAction == "Delete")
		{
			$db->query("UPDATE PurchasedBadges SET Status = 'Deleted' WHERE BadgeID = $id");
			$response["result"] = "Success";
		}

		header("Content-type: application/json");
		echo json_encode($response);
	}
	elseif($action == "CompBadge")
	{
		$response = array();
		$badgeName = $db->real_escape_string($_POST["badgeName"]);
		$department = $db->real_escape_string($_POST["department"]);
		if(!empty($_POST["peopleID"]))
		{
			$peopleID = $db->real_escape_string($_POST["peopleID"]);
			$result = $db->query("SELECT ParentID FROM People WHERE PeopleID = $peopleID");
			$row = $result->fetch_array();
			$result->close();
			$purchaserID = is_null($row["ParentID"]) ? $peopleID : $row["ParentID"];
		}
		else
		{
			$first = $db->real_escape_string($_POST["fName"]);
			$last = $db->real_escape_string($_POST["lName"]);
			$add1 = $db->real_escape_string($_POST["add1"]);
			$add2 = !empty($_POST["add2"]) ? "'" . $db->real_escape_string($_POST["add2"]) . "'" : "NULL";
			$city = $db->real_escape_string($_POST["city"]);
			$state = !empty($_POST["state"]) ? "'" . $db->real_escape_string($_POST["state"]) . "'" : "NULL";
			$zip = !empty($_POST["zip"]) ? "'" . $db->real_escape_string($_POST["zip"]) . "'" : "NULL";
			$country = !empty($_POST["country"]) ? "'" . $db->real_escape_string($_POST["country"]) . "'" : "NULL";
			$phone1 = !empty($_POST["phone1"]) ? "'" . $db->real_escape_string($_POST["phone1"]) . "'" : "NULL";
			$phone1Type = !empty($_POST["phone1Type"]) ? "'" . $db->real_escape_string($_POST["phone1Type"]) . "'" : "NULL";
			$phone2 = !empty($_POST["phone2"]) ? "'" . $db->real_escape_string($_POST["phone2"]) . "'" : "NULL";
			$phone2Type = !empty($_POST["phone2Type"]) ? "'" . $db->real_escape_string($_POST["phone2Type"]) . "'" : "NULL";

			// Create an account.
			$email = $db->real_escape_string($_POST["email"]);
			$heardFrom = $db->real_escape_string($_POST["heardFrom"]);
			$sql = "INSERT INTO People (FirstName, LastName, Address1, Address2, City, State, ZipCode, Country, Phone1, Phone1Type, Phone2, Phone2Type, Email, Password, Registered, BadgeName, HeardFrom) VALUES ('$first', '$last', '$add1', $add2, '$city', $state, $zip, $country, $phone1, $phone1Type, $phone2, $phone2Type, '$email', 'NotYetActivated', NOW(), '$badgeName', '$heardFrom')";
			if($db->query($sql) === false)
			{
				$response["result"] = "Failure";
				$response["message"] = $db->error;
				header("Content-type: application/json");
				echo json_encode($response);
				exit();
			}
			$peopleID = $db->insert_id;
			$purchaserID = $peopleID;
			if(!empty($email))
				SendPasswordReset($email, $newID, true);
			$response["newPeopleID"] = $newID;
		}

		$year = date("n") >= 3 ? date("Y") + 1: date("Y");
		$sql = "SELECT CASE WHEN EXISTS (SELECT BadgeNumber FROM PurchasedBadges WHERE BadgeNumber = 150 AND Year = $year) THEN 99999 ELSE 150 END AS Next UNION SELECT (p1.BadgeNumber + 1) as Next FROM PurchasedBadges p1 WHERE NOT EXISTS (SELECT p2.BadgeNumber FROM PurchasedBadges p2 WHERE p2.BadgeNumber = p1.BadgeNumber + 1 AND p2.Year = $year) AND p1.Year = $year HAVING Next >= 150 ORDER BY Next";
		$result = $db->query($sql);
		$row = $result->fetch_array();
		$badgeNumber = $row["Next"];
		$result->close();

		$sql = "INSERT INTO PurchaseHistory (PurchaserID, ItemTypeName, ItemTypeID, Year, Details, PeopleID, Price, Total, Purchased, PaymentSource, PaymentReference) " .
            "VALUES ($purchaserID, 'Badge', 1, $year, '$badgeName', $peopleID, $oneTimeID, 0.00, 0.00, NOW(), 'Comp', 'NoCharge')";
		$db->query($sql);
        $recordID = $db->insert_id;
		$sql = "INSERT INTO PurchasedBadges (Year, PeopleID, PurchaserID, BadgeNumber, BadgeTypeID, BadgeName, Department, Status, OriginalPrice, AmountPaid, PaymentSource, PaymentReference, PromoCodeID, CertificateID, RecordID, Created) VALUES ($year, $peopleID, $purchaserID, $badgeNumber, 1, '$badgeName', '$department', 'Paid', 0.00, 0.00, 'Comp', 'NoCharge', NULL, NULL, $recordID, NOW())";
		$db->query($sql);

		$response["result"] = "Success";
		header("Content-type: application/json");
		echo json_encode($response);
	}
	elseif($action == "GetPickupReportData")
	{
		$type = empty($_POST["type"]) ? "Attendee" : $_POST["type"];
		$year = date("n") >= 3 ? date("Y") + 1: date("Y");
		$sql = "SELECT pb.BadgeID, p.PeopleID, o.OneTimeID, pb.CovidVerified, CASE WHEN p.PeopleID IS NULL THEN o.FirstName ELSE p.FirstName END AS FirstName, CASE WHEN p.PeopleID IS NULL THEN o.LastName ELSE p.LastName END AS LastName, pb.BadgeName, CASE WHEN p.PeopleID IS NULL THEN o.Address1 ELSE p.Address1 END AS Address1, CASE WHEN p.PeopleID IS NULL THEN o.Address2 ELSE p.Address2 END AS Address2, CASE WHEN p.PeopleID IS NULL THEN o.City ELSE p.City END AS City, CASE WHEN p.PeopleID IS NULL THEN o.State ELSE p.State END AS State, CASE WHEN p.PeopleID IS NULL THEN o.ZipCode ELSE p.ZipCode END AS ZipCode, CASE WHEN p.PeopleID IS NULL THEN o.Country ELSE p.Country END AS Country, CASE WHEN p.PeopleID IS NULL THEN o.Phone1 ELSE p.Phone1 END AS Phone1, pb.BadgeNumber, CASE WHEN p.PeopleID IS NULL THEN '' ELSE p.Email END AS Email FROM PurchasedBadges pb LEFT OUTER JOIN People p ON pb.PeopleID = p.PeopleID LEFT OUTER JOIN OneTimeRegistrations o ON pb.OneTimeID = o.OneTimeID WHERE pb.Year = $year AND pb.Status = 'Paid' ";
		if($type == "Attendee")
			$sql .= "AND pb.BadgeTypeID NOT IN (3, 4, 5) AND pb.BadgeNumber >= 150 ";
		else
			$sql .= "AND (pb.BadgeTypeID IN (3, 4, 5) OR pb.BadgeNumber < 150) ";
		$sql .= "ORDER BY LastName";

		$result = $db->query($sql);
		$records = array();
		if($result->num_rows > 0)
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$records[] = $row;

		header("Content-type: application/json");
		echo json_encode($records);
	}
    elseif($action == "GetRelatedPeople") {
        $id = $db->real_escape_string($_POST["id"]);
		$sql = "SELECT p.PeopleID, p.FirstName, p.LastName, p.Phone1, p.Phone1Type, p.Phone2, p.Phone2Type, p.BadgeName FROM People p WHERE p.ParentID = $id";
		$result = $db->query($sql);

		$people = array();
		if($result->num_rows > 0)
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$people[] = $row;

		header("Content-type: application/json");
		echo json_encode($people);
    }
?>