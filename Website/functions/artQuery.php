<?php
	session_start();
	if($_SERVER["HTTPS"] != "on") exit();

	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/includes/functions.php");

	// Add authentication handler here.

	if(empty($_POST["action"])) exit();

	$action = $db->real_escape_string($_POST["action"]);

	if($action == "GetArtists")
	{
		$sql = "SELECT DISTINCT p.PeopleID, a.ArtistID, p.FirstName, p.LastName, p.Email, p.Phone1, p.Phone1Type, p.IsCharity, a.DisplayName FROM People p INNER JOIN Permissions m ON p.PeopleID = m.PeopleID AND m.Permission = 'Artist' LEFT OUTER JOIN ArtistDetails a ON p.PeopleID = a.PeopleID ";

		if(!empty($_POST["id"]))
		{
			$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));
			$sql .= "INNER JOIN ArtistPresence ap ON ap.ArtistID = a.ArtistID WHERE ap.Year = $year AND ArtistNumber = " .
				$db->real_escape_string($_POST["id"]);
		}
		else
		{
			if(!empty($_POST["whereField"]) && !empty($_POST["whereTerm"]))
                $sql .= "WHERE " . $db->real_escape_string($_POST["whereField"]) . " " . (!empty($_POST["whereSimilar"]) ? "LIKE" : "=") . " '". (!empty($_POST["whereSimilar"]) ? "%" : "") . $db->real_escape_string($_POST["whereTerm"]) . (!empty($_POST["whereSimilar"]) ? "%" : "") . "'";
            elseif(!empty($_POST["withInventory"]) && $_POST["withInventory"] == "true")
                $sql .= "LEFT OUTER JOIN (SELECT ad.ArtistID, COUNT(*) FROM ArtSubmissions ar JOIN ArtistPresence ap ON ar.ArtistAttendingID = ap.ArtistAttendingID JOIN ArtistDetails ad ON ad.ArtistID = ap.ArtistID WHERE ap.Year = 2018 GROUP BY ad.ArtistID) ai ON ai.ArtistID = a.ArtistID WHERE ai.ArtistID IS NOT NULL ";
		}

		$sql .= " ORDER BY " . (!empty($_POST["order"]) ? $db->real_escape_string($_POST["order"]) : "LastName");
		$result = $db->query($sql);

		$people = array();
		if($result->num_rows > 0)
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$people[] = $row;

		header("Content-type: application/json");
		echo json_encode($people);
	}
	elseif($action == "GetArtist")
	{
		$id = $db->real_escape_string($_POST["PeopleID"]);
		if($id == null)
		{
			header("Content-type: application/json");
			echo json_encode(array("error" => "An invalid request was sent."));
			exit();
		}

		$artistInfo = array();
		$result = $db->query("SELECT DISTINCT ad.ArtistID, ad.PeopleID, ad.DisplayName, ad.LegalName, ad.IsPro, ad.IsEAP, ad.Website, ad.ArtType, ad.Notes, p.IsCharity FROM ArtistDetails ad INNER JOIN People p ON p.PeopleID = ad.peopleID WHERE ad.PeopleID = $id");
		if($result->num_rows > 0)
		{
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$artistID = $row["ArtistID"];
			$artistInfo["details"] = $row;
			$result->close();
			$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));
		    $sql = "SELECT ArtistAttendingID, ArtistNumber, IsAttending, AgentName, AgentContact, ShippingPref, ShippingAddress, ShippingCost, ShippingPrepaid, ShippingDetails, NeedsElectricity, NumTables, NumGrid, HasPrintShop, Notes, Status, StatusReason, LocationCode, FeesWaivedReason FROM ArtistPresence WHERE ArtistID = $artistID AND Year = $year";
			$artistInfo["debug"] = $sql;
			$result = $db->query($sql);
			if($result->num_rows > 0)
			{
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$result->close();

				$result = $db->query("SELECT BadgeID FROM PurchasedBadges WHERE PeopleID = $id AND Year = $year");
				if($result->num_rows > 0)
				{
					$result->close();
					$row["HasBadge"] = 1;
				}
				else
					$row["HasBadge"] = 0;

				$artistInfo["presence"] = $row;
			}
			else
				$artistInfo["presence"] = null;
		}
		else
		{
			$artistInfo["details"] = null;
			$artistInfo["presence"] = null;
		}

		header("Content-type: application/json");
		echo json_encode($artistInfo);
	}
	elseif($action == "SaveArtist")
	{
		$response = array();
		$artist = json_decode($_POST["artist"], true);
		if($artist == null)
		{
			header("Content-type: application/json");
			echo json_encode(array("error" => "An invalid request was sent."));
			exit();
		}
		$displayName = $db->real_escape_string($artist["DisplayName"]);
		$legalName = $db->real_escape_string($artist["LegalName"]);
		$isPro = $db->real_escape_string($artist["IsPro"]);
		$isEAP = $db->real_escape_string($artist["IsEAP"]);
		$website = $db->real_escape_string($artist["Website"]);
		$artType = $db->real_escape_string($artist["ArtType"]);
		$notes = $db->real_escape_string($artist["Notes"]);
		if(!empty($artist["ArtistID"]))
		{
			$artistID = $db->real_escape_string($artist["ArtistID"]);
			$db->query("UPDATE ArtistDetails SET DisplayName = '$displayName', LegalName = '$legalName', IsPro = $isPro, IsEAP = $isEAP, Website = '$website', ArtType = '$artType', Notes = '$notes' WHERE ArtistID = $artistID");
		}
		else
		{
			$peopleID = $db->real_escape_string($artist["PeopleID"]);
			$sql = "INSERT INTO ArtistDetails (PeopleID, DisplayName, LegalName, IsPro, IsEAP, Website, ArtType, Notes) VALUES ($peopleID, '$displayName', '$legalName', $isPro, $isEAP, '$website', '$artType', '$notes')";
			$db->query($sql);
			$artistID = $db->insert_id;
			$response["artistID"] = $artistID;
		}

		$presence = json_decode($_POST["presence"], true);
		$isAttending = $db->real_escape_string($presence["IsAttending"]);
		$agentName = !empty($presence["AgentName"]) ? "'" . $db->real_escape_string($presence["AgentName"]) . "'" : "NULL";
		$agentContact = !empty($presence["AgentContact"]) ? "'" . $db->real_escape_string($presence["AgentContact"]) . "'" : "NULL";
		$shippingPref = !empty($presence["ShippingPref"]) ? "'" . $db->real_escape_string($presence["ShippingPref"]) . "'" : "NULL";
		$shippingAddress = !empty($presence["ShippingAddress"]) ? "'" . $db->real_escape_string($presence["ShippingAddress"]) . "'" : "NULL";
		$shippingCost = !empty($presence["ShippingCost"]) ? $db->real_escape_string($presence["ShippingCost"]) : "NULL";
		$shippingPrepaid = !empty($presence["ShippingPrepaid"]) ? $db->real_escape_string($presence["ShippingPrepaid"]) : "NULL";
		$shippingDetails = $db->real_escape_string($presence["ShippingDetails"]);
		$needsElectricity = $db->real_escape_string($presence["NeedsElectricity"]);
		$numTables = $db->real_escape_string($presence["NumTables"]);
		$numGrid = $db->real_escape_string($presence["NumGrid"]);
		$hasPrintShop = $db->real_escape_string($presence["HasPrintShop"]);
		$notes = $db->real_escape_string($presence["Notes"]);
		$status = $db->real_escape_string($presence["Status"]);
		$statusReason = $db->real_escape_string($presence["StatusReason"]);
		$location = $db->real_escape_string($presence["LocationCode"]);
		if(!empty($presence["ArtistAttendingID"]))
		{
			$attendID = $db->real_escape_string($presence["ArtistAttendingID"]);
			$sql = "UPDATE ArtistPresence SET IsAttending = $isAttending, AgentName = $agentName, AgentContact = $agentContact, ShippingPref = $shippingPref, ShippingAddress = $shippingAddress, ShippingCost = $shippingCost, ShippingPrepaid = $shippingPrepaid, ShippingDetails = '$shippingDetails', NeedsElectricity = $needsElectricity, NumTables = $numTables, NumGrid = $numGrid, HasPrintShop = $hasPrintShop, Notes = '$notes', Status = '$status', StatusReason = '$statusReason', LocationCode = '$location' WHERE ArtistAttendingID = $attendID";
			$db->query($sql);
			$response["update"] = $sql;
		}
		else
		{
			$result = $db->query("SELECT IFNULL(MAX(ArtistNumber), 1) + 1 AS ArtistNumber FROM ArtistPresence");
			$row = $result->fetch_array();
			$number = $row["ArtistNumber"];
			$result->close();
			$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));
			$sql = "INSERT INTO ArtistPresence (ArtistID, Year, ArtistNumber, IsAttending, AgentName, AgentContact, ShippingPref, ShippingAddress, ShippingDetails, NeedsElectricity, NumTables, NumGrid, HasPrintShop, Notes, Status, StatusReason, LocationCode) VALUES ($artistID, $year, $number, $isAttending, $agentName, $agentContact, $shippingPref, $shippingAddress, '$shippingDetails', $needsElectricity, $numTables, $numGrid, $hasPrintShop, '$notes', '$status', '$statusReason', '$location')";
			$db->query($sql);
			$attendID = $db->insert_id;
			$response["artistAttendingID"] = $attendID;
			$response["artistNumber"] = $number;
		}

		$db->query("UPDATE ArtSubmissions SET LocationCode = '$location' WHERE ArtistAttendingID = $attendID AND " .
			"IFNULL(LocationCode, '') = '' AND IsPrintShop = 0");

		$response["result"] = "Success";
		header("Content-type: application/json");
		echo json_encode($response);
	}
	elseif($action == "GetInventory")
	{
		$id = !empty($_POST["AttendID"]) ? $db->real_escape_string($_POST["AttendID"]) : null;
		$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));

		$showPieces = array();
		$sql = "SELECT ar.ArtistAttendingID, ArtID, ShowNumber, Title, ar.Notes, IsOriginal, OriginalMedia, PrintNumber, PrintMaxNumber, MinimumBid, ar.LocationCode, Category, FeesPaid, PurchaserBadgeID, pb.BadgeNumber AS PurchaserNumber, pb.BadgeName AS PurchaserName, FinalSalePrice, CheckedIn, Claimed, Auctioned, DisplayName, LegalName, ArtistNumber FROM ArtSubmissions ar JOIN ArtistPresence ap ON ar.ArtistAttendingID = ap.ArtistAttendingID JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID LEFT OUTER JOIN PurchasedBadges pb ON ar.PurchaserBadgeID = pb.BadgeID AND pb.Year = $year WHERE IsPrintShop = 0 AND ap.Year = $year ";
		if($id != null) $sql .= "AND ap.ArtistAttendingID = $id ";
		$sql .= "ORDER BY ArtistNumber, ar.LocationCode";
		$result = $db->query($sql);
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$showPieces[] = $row;

			$result->close();
		}

		$printShopPieces = array();
		$sql = "SELECT ar.ArtistAttendingID, ArtID, ShowNumber, Title, ar.Notes, OriginalMedia, QuantitySent, QuickSalePrice, ar.LocationCode, Category, QuantitySold, CheckedIn, DisplayName, LegalName, ArtistNumber, Claimed FROM ArtSubmissions ar JOIN ArtistPresence ap ON ar.ArtistAttendingID = ap.ArtistAttendingID JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID WHERE IsPrintShop = 1 AND ap.Year = $year ";
		if($id != null) $sql .= "AND ap.ArtistAttendingID = $id ";
		$sql .= "ORDER BY ArtistNumber, ar.LocationCode";
		$result = $db->query($sql);
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$printShopPieces[] = $row;
			$result->close();
		}

		$response = array();
		$response["result"] = "Success";
		$response["showPieces"] = count($showPieces) > 0 ? $showPieces : null;
		$response["printShopPieces"] = count($printShopPieces) > 0 ? $printShopPieces : null;

		header("Content-type: application/json");
		echo json_encode($response);
	}
	elseif($action == "NewShowItem" || $action == "UpdateShowItem")
	{
		$response = array();
		$id = $db->real_escape_string($_POST["id"]);
		$item = json_decode($_POST["item"], true);

		$title = $db->real_escape_string($item["Title"]);
		$notes = $db->real_escape_string($item["Notes"]);
		$isOriginal = $db->real_escape_string($item["IsOriginal"]);
		$media = $db->real_escape_string($item["OriginalMedia"]);
		$printNumber = !empty($item["PrintNumber"]) ? "'" . $db->real_escape_string($item["PrintNumber"]) . "'" : "NULL";
		$printMaxNumber = !empty($item["PrintMaxNumber"]) ? "'" . $db->real_escape_string($item["PrintMaxNumber"]) . "'": "NULL";
		$minimumBid = !empty($item["MinimumBid"]) ? $db->real_escape_string($item["MinimumBid"]) : "NULL";
		$quickSale = !empty($item["QuickSalePrice"]) ? $db->real_escape_string($item["QuickSalePrice"]) : "NULL";
		$category = !empty($item["Category"]) ? "'" . $db->real_escape_string($item["Category"]) . "'" : "NULL";
		$location = !empty($item["LocationCode"]) ? "'" . $db->real_escape_string($item["LocationCode"]) . "'" : "NULL";
		$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));

		if($action == "NewShowItem")
		{
			$result = $db->query("SELECT IFNULL(MAX(s.ShowNumber), 0) + 1 AS ShowNumber FROM ArtSubmissions s INNER JOIN " .
				"ArtistPresence ap ON ap.ArtistAttendingID = s.ArtistAttendingID WHERE ap.Year = $year");
			$row = $result->fetch_array();
			$showNumber = $row["ShowNumber"];
			$result->close();
			if($db->query("INSERT INTO ArtSubmissions (ArtistAttendingID, ShowNumber, Title, Notes, IsPrintShop, IsOriginal, OriginalMedia, " .
				"PrintNumber, PrintMaxNumber, MinimumBid, QuickSalePrice, FeesPaid, Category, LocationCode) VALUES ($id, $showNumber, " .
				"'$title', '$notes', 0, $isOriginal, '$media', $printNumber, $printMaxNumber, $minimumBid, $quickSale, 0, $category, " .
				"$location)"))
			{
				$response["Result"] = "Success";
				$response["ArtID"] = $db->insert_id;
				$response["ShowNumber"] = $showNumber;
			}
			else
			{
				$response["Result"] = "Failure";
				$response["Message"] = $db->error;
			}
		}
		else
		{
			$artID = $item["ArtID"];
			$feesPaid = $item["FeesPaid"];
			$auctioned = $db->real_escape_string($item["Auctioned"]);
			$purchaser = "NULL";
			if(!empty($item["PurchaserNumber"]))
			{
				$result = $db->query("SELECT BadgeID FROM PurchasedBadges WHERE BadgeNumber = " . $db->real_escape_string($item["PurchaserNumber"]) .
					" AND Year = $year");
				if($result->num_rows > 0)
				{
					$row = $result->fetch_array();
					$purchaser = $row["BadgeID"];
					$result->close();
				}
			}
			$finalPrice = !empty($item["FinalSalePrice"]) ? $item["FinalSalePrice"] : "NULL";
			$checkedIn = $item["CheckedIn"];
			$claimed = $item["Claimed"];
			if($db->query("UPDATE ArtSubmissions SET Title = '$title', Notes = '$notes', IsOriginal = $isOriginal, OriginalMedia = '$media', PrintNumber = $printNumber, PrintMaxNumber = $printMaxNumber, MinimumBid = $minimumBid, QuickSalePrice = $quickSale, FeesPaid = $feesPaid, Category = $category, LocationCode = $location, PurchaserBadgeID = $purchaser, FinalSalePrice = $finalPrice, Auctioned = $auctioned, CheckedIn = $checkedIn, Claimed = $claimed WHERE ArtID = $artID"))
			{
				$response["Result"] = "Success";
			}
			else
			{
				$response["Result"] = "Failure";
				$response["Message"] = $db->error;
			}
		}

		header("Content-type: application/json");
		echo json_encode($response);
	}
	elseif($action == "NewShopItem" || $action == "UpdateShopItem")
	{
		$response = array();
		$id = $db->real_escape_string($_POST["id"]);
		$item = json_decode($_POST["item"], true);

		$title = $db->real_escape_string($item["Title"]);
		$notes = $db->real_escape_string($item["Notes"]);
		$media = $db->real_escape_string($item["OriginalMedia"]);
		$quantitySent = $db->real_escape_string($item["QuantitySent"]);
		$salePrice = $db->real_escape_string($item["QuickSalePrice"]);
		$category = !empty($item["Category"]) ? "'" . $db->real_escape_string($item["Category"]) . "'" : "NULL";
		$location = !empty($item["LocationCode"]) ? "'" . $db->real_escape_string($item["LocationCode"]) . "'" : "NULL";

		if($action == "NewShopItem")
		{
			$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));
			$result = $db->query("SELECT IFNULL(MAX(s.ShowNumber), 0) + 1 AS ShowNumber FROM ArtSubmissions s INNER JOIN " .
				"ArtistPresence ap ON ap.ArtistAttendingID = s.ArtistAttendingID WHERE ap.Year = $year");
			$row = $result->fetch_array();
			$showNumber = $row["ShowNumber"];
			$result->close();
			$sql = "INSERT INTO ArtSubmissions (ArtistAttendingID, ShowNumber, Title, Notes, IsPrintShop, OriginalMedia, QuickSalePrice, QuantitySent, QuantitySold, Category, LocationCode, CheckedIn) VALUES ($id, $showNumber, '$title', '$notes', 1, '$media', $salePrice, $quantitySent, 0, $category, $location, 0)";

			if($db->query($sql))
			{
				$response["Result"] = "Success";
				$response["ArtID"] = $db->insert_id;
				$response["ShowNumber"] = $showNumber;
			}
			else
			{
				$response["Result"] = "Failure";
				$response["Message"] = $db->error;
			}
		}
		else
		{
			$artID = $item["ArtID"];
			$checkedIn = $item["CheckedIn"];
			$quantitySold = $item["QuantitySold"];
			$sql = "UPDATE ArtSubmissions SET Title = '$title', Notes = '$notes', OriginalMedia = '$media', QuickSalePrice = $salePrice, Category = $category, LocationCode = $location, QuantitySent = $quantitySent, QuantitySold = $quantitySold, CheckedIn = $checkedIn WHERE ArtID = $artID";
			if($db->query($sql))
			{
				$response["Result"] = "Success";
			}
			else
			{
				$response["Result"] = "Failure";
				$response["Message"] = $db->error;
			}
		}

		header("Content-type: application/json");
		echo json_encode($response);
	}
	elseif($action == "PayHangingFees")
	{
		$attendingID = $db->real_escape_string($_POST["id"]);
		$source = $db->real_escape_string($_POST["source"]);

		if($source != "Waived")
		{
			$ref = $db->real_escape_string($_POST["reference"]);
			$price = $db->real_escape_string($_POST["fees"]);
			$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));
			$result = $db->query("SELECT ad.PeopleID FROM ArtistDetails ad JOIN ArtistPresence ap ON ad.ArtistID = ap.ArtistID WHERE ap.ArtistAttendingID = $attendingID");
			$row = $result->fetch_array();
			$peopleID = $row["PeopleID"];
			$result->close();

			$db->query("INSERT INTO PurchaseHistory (PurchaserID, PeopleID, ItemTypeName, ItemTypeID, Details, Price, Total, Year, Purchased, PaymentSource, PaymentReference) " .
                "VALUES ($peopleID, $peopleID, 'HangingFees', $attendingID, 'Hanging Fees', $price, $price, $year, NOW(), '$source', '$ref')");
		}
        else
        {
    		$reason = !empty($_POST["reason"]) ? $db->real_escape_string($_POST["reason"]) : "No Reason Provided";
            $db->query("UPDATE ArtistPresence SET FeesWaivedReason = '$reason' WHERE ArtistAttendingID = $attendingID");
        }
		$db->query("UPDATE ArtSubmissions SET FeesPaid = 1 WHERE ArtistAttendingID = $attendingID AND IsPrintShop = 0");

		$response = array();
		$response["Result"] = "Success";
		header("Content-type: application/json");
		echo json_encode($response);
	}
	elseif($action == "GetPrintShopList")
	{
		$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));
		$printShopPieces = array();
		$result = $db->query("SELECT ArtID, ar.ArtistAttendingID, ShowNumber, Title, ar.Notes, DisplayName AS ArtistName, OriginalMedia, QuantitySent, QuickSalePrice, ar.LocationCode, Category, QuantitySold, CheckedIn, IsCharity FROM ArtSubmissions ar JOIN ArtistPresence ap ON ar.ArtistAttendingID = ap.ArtistAttendingID JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID JOIN People p ON p.PeopleID = ad.PeopleID WHERE Year = $year AND IsPrintShop = 1 AND CheckedIn = 1");
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$printShopPieces[] = $row;
			$result->close();
		}

		header("Content-type: application/json");
		echo json_encode($printShopPieces);
	}
	elseif($action == "RecordPrintShopSales")
	{
		$response = array();
		try
		{
			$ref = $db->real_escape_string($_POST["reference"]);
			$purchaser = !empty($_POST["purchaser"]) ? $db->real_escape_string($_POST["purchaser"]) : "NULL";
			$onetime = !empty($_POST["onetime"]) ?  $db->real_escape_string($_POST["onetime"]) : "NULL";
			$source = $db->real_escape_string($_POST["source"]);
			$price = $db->real_escape_string($_POST["price"]);
            $tax = $db->real_escape_string($_POST["tax"]);
            $total = $db->real_escape_string($_POST["total"]);
			$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));
			$items = json_decode($_POST["items"], true);

			$sql = "INSERT INTO PurchaseHistory (PurchaserID, PeopleID, PurchaserOneTimeID, OneTimeID, ItemTypeName, Details, Price, Tax, Total, Year, Purchased, PaymentSource, PaymentReference) " .
                "VALUES ($purchaser, $purchaser, $onetime, $onetime, 'PrintShop', 'Print Shop Sales', $price, $tax, $total, $year, NOW(), '$source', '$ref')";
			if($db->query($sql) === false)
			{
				$response["Result"] = "Failure";
				$response["Message"] = $db->error;
				header("Content-type: application/json");
				echo json_encode($response);
				exit();
			}
            $recordID = $db->insert_id;
			foreach($items as $item)
			{
				$id = $db->real_escape_string($item["ArtID"]);
                $price = $db->real_escape_string($item["QuickSalePrice"]);
				$db->query("UPDATE ArtSubmissions SET QuantitySold = QuantitySold + 1 WHERE ArtID = $id");
                $db->query("INSERT INTO ArtSales (RecordID, ArtID, Price) VALUES ($recordID, $id, $price)");
			}

			$response["Result"] = "Success";
		}
		catch (Exception $e)
		{
			$response["Result"] = "Failure";
			$response["Message"] = $e->getMessage();
		}
		header("Content-type: application/json");
		echo json_encode($response);
	}
	elseif($action == "ModifyPermission")
	{
		$id = $db->real_escape_string($_POST["id"]);
		$mod = $_POST["modification"];
		if($mod == "Add")
		{
			$result = $db->query("SELECT PermissionID FROM Permissions WHERE PeopleID = $id AND Permission = 'Artist'");
			if($result->num_rows == 0)
				$db->query("INSERT INTO Permissions (PeopleID, Permission) VALUES ($id, 'Artist')");
			else
				$result->close();
		}
		else if($mod == "Delete")
			$db->query("DELETE FROM Permissions WHERE PeopleID = $id AND Permission = 'Artist'");
		$response = array();
		$response["Result"] = "Success";
		header("Content-type: application/json");
		echo json_encode($response);
	}
	elseif($action == "DeleteArtItems")
	{
		$ids = $db->real_escape_string($_POST["ids"]);
		$db->query("DELETE FROM ArtSubmissions WHERE ArtID IN ($ids)");
		$response = array();
		$response["Result"] = "Success";
		header("Content-type: application/json");
		echo json_encode($response);
	}
	elseif($action == "GetPeopleForPickup")
	{
		$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));
		$sql = "SELECT p.PeopleID, o.OneTimeID, BadgeID, BadgeNumber, CASE WHEN pb.PeopleID IS NULL THEN o.FirstName ELSE p.FirstName END AS FirstName, CASE WHEN pb.PeopleID IS NULL THEN o.LastName ELSE p.LastName END AS LastName, pb.BadgeName, COUNT(ArtID) AS TotalPieces, SUM(FinalSalePrice) AS TotalDue FROM ArtSubmissions a JOIN ArtistPresence ap ON a.ArtistAttendingID = ap.ArtistAttendingID JOIN PurchasedBadges pb ON a.PurchaserBadgeID = pb.BadgeID LEFT OUTER JOIN People p ON pb.PeopleID = p.PeopleID LEFT OUTER JOIN OneTimeRegistrations o ON pb.OneTimeID = o.OneTimeID WHERE Claimed = 0 AND ap.Year = $year ";
		if(!empty($_POST["id"]))
			$sql .= "AND BadgeNumber = " . $db->real_escape_string($_POST["id"]) . " ";
		elseif(!empty($_POST["lastName"]))
			$sql .= "AND (p.LastName LIKE '" . $db->real_escape_string($_POST["lastName"]) . "%' OR o.LastName LIKE '" . $db->real_escape_string($_POST["lastName"]) . "%') ";
		$sql .= "GROUP BY p.PeopleID, o.OneTimeID, BadgeNumber, FirstName, LastName, BadgeName ORDER BY LastName";
		$people = array();
		$result = $db->query($sql);
		if($result->num_rows > 0)
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$people[] = $row;

		header("Content-type: application/json");
		echo json_encode($people);
	}
	elseif($action == "GetItemsForPickup")
	{
		$id = $db->real_escape_string($_POST["id"]);
		$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));

		$items = array();
		$sql = "SELECT ar.ArtistAttendingID, ArtID, ShowNumber, Title, ar.Notes, IsOriginal, OriginalMedia, PrintNumber, PrintMaxNumber, MinimumBid, ar.LocationCode, Category, FeesPaid, PurchaserBadgeID, pb.BadgeNumber AS PurchaserNumber, FinalSalePrice, CheckedIn, Claimed, Auctioned, DisplayName, LegalName, ArtistNumber, IsCharity FROM ArtSubmissions ar JOIN ArtistPresence ap ON ar.ArtistAttendingID = ap.ArtistAttendingID JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID LEFT OUTER JOIN PurchasedBadges pb ON ar.PurchaserBadgeID = pb.BadgeID AND pb.Year = $year JOIN People p ON p.PeopleID = ad.PeopleID WHERE Claimed = 0 AND PurchaserBadgeID = $id ";
		$sql .= "ORDER BY ar.LocationCode, ShowNumber";
		$result = $db->query($sql);
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$items[] = $row;

			$result->close();
		}

		header("Content-type: application/json");
		echo json_encode($items);
	}
	elseif($action == "SellAuctionItems")
	{
		$price = $db->real_escape_string($_POST["price"]);
		$tax = $db->real_escape_string($_POST["tax"]);
		$total = $db->real_escape_string($_POST["total"]);
		$pieces = $db->real_escape_string($_POST["pieces"]);
		$message = $pieces . " Piece" . ($pieces == 1 ? "" : "s") . " of Art";
		$id = $db->real_escape_string($_POST["id"]);
		$source = $db->real_escape_string($_POST["source"]);
		$ref = $db->real_escape_string($_POST["reference"]);

		$sql = "SELECT pb.PeopleID, pb.OneTimeID,CASE WHEN pb.PeopleID IS NULL THEN o.FirstName ELSE p.FirstName END AS FirstName, CASE WHEN pb.PeopleID IS NULL THEN o.LastName ELSE p.LastName END AS LastName, p.Email FROM PurchasedBadges pb LEFT OUTER JOIN People p ON pb.PeopleID = p.PeopleID LEFT OUTER JOIN OneTimeRegistrations o ON pb.OneTimeID = o.OneTimeID WHERE pb.BadgeID = $id";
		$result = $db->query($sql);
		$row = $result->fetch_array();
		$peopleID = !empty($row["PeopleID"]) ? $row["PeopleID"] : "NULL";
		$onetimeID = !empty($row["OneTimeID"]) ? $row["OneTimeID"] : "NULL";
		$fname = $row["FirstName"];
		$lname = $row["LastName"];
		$email = !empty($row["Email"]) ? $row["Email"] : null;
		$result->close();

		$ref = $db->real_escape_string($_POST["reference"]);
		$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));

		$db->query("INSERT INTO PurchaseHistory (PeopleID, PurchaserID, OneTimeID, PurchaserOneTimeID, ItemTypeName, Details, Price, Tax, Total, Year, Purchased, PaymentSource, PaymentReference) " .
            "VALUES ($peopleID, $peopleID, $onetimeID, $onetimeID, 'Auction Sales', '$message', $price, $tax, $total, $year, NOW(), '$source', '$ref')");
        $recordID = $db->insert_id;

        $sql = "SELECT ArtID, FinalSalePrice FROM ArtSubmissions art JOIN ArtistPresence ap ON ap.ArtistAttendingID = art.ArtistAttendingID WHERE Year = $year AND PurchaserBadgeID = $id AND Claimed = 0";
        $result = $db->query($sql);
        $items = array();
        while($row = $result->fetch_array())
            $items[] = $row;
        $result->close();
        foreach($items as $item) {
            $artID = $item["ArtID"];
            $price = $item["FinalSalePrice"];
            $db->query("INSERT INTO ArtSales (RecordID, ArtID, Price) VALUES ($recordID, $artID, $price)");
        }
		$db->query("UPDATE ArtSubmissions SET Claimed = 1 WHERE PurchaserBadgeID = $id AND Claimed = 0");

		$response = array();
		$response["Result"] = "Success";
		header("Content-type: application/json");
		echo json_encode($response);
	}
	elseif($action == "GetArtistCheckout")
	{
		$id = !empty($_POST["id"]) ? $db->real_escape_string($_POST["id"]) : null;
		$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));

		$inventory = array();
		$sql = "SELECT ar.ArtistAttendingID, ArtID, ShowNumber, Title, ar.Notes, IsOriginal, OriginalMedia, PrintNumber, PrintMaxNumber, MinimumBid, ar.LocationCode, Category, FeesPaid, PurchaserBadgeID, pb.BadgeNumber AS PurchaserNumber, pb.BadgeName AS PurchaserName, FinalSalePrice, CheckedIn, Claimed, Auctioned, DisplayName, LegalName, ArtistNumber, IsPrintShop, QuickSalePrice, QuantitySent, QuantitySold, ShippingCost, ShippingPrepaid, ShippingDetails, IsEAP FROM ArtSubmissions ar JOIN ArtistPresence ap ON ar.ArtistAttendingID = ap.ArtistAttendingID JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID LEFT OUTER JOIN PurchasedBadges pb ON ar.PurchaserBadgeID = pb.BadgeID AND pb.Year = $year WHERE ap.Year = $year ";
		if($id != null) $sql .= "AND ap.ArtistAttendingID = $id ";
		$sql .= "ORDER BY ArtistNumber, ar.LocationCode";
		$result = $db->query($sql);
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$inventory[] = $row;

			$result->close();
		}
		header("Content-type: application/json");
		echo json_encode($inventory);
	}
	elseif($action == "MarkArtistPickup")
	{
		$id = !empty($_POST["id"]) ? $db->real_escape_string($_POST["id"]) : null;
        $values = !empty($_POST["values"]) ? $db->real_escape_string($_POST["values"]) : null;
        if(!empty($id) && !empty($values))  {
            $toUpdate = explode(";", $values);
            foreach($toUpdate as $value) {
                $values = explode("~", $value);
                $sql = "UPDATE ArtSubmissions SET Claimed = " . $values[1] . " WHERE ArtId = " . $values[0];
                $db->query($sql);
            }
        }
        $response = array();
        $response["Result"] = "Success";
        header("Content-type: application/json");
        echo json_encode($response);
	}
	elseif($action == "GetInventoryList")
	{
		$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 4 ? date("Y") + 1: date("Y"));

		$sql = "SELECT ap.ArtistNumber, ad.DisplayName, p.LastName, p.Email, ap.LocationCode, IFNULL(art1.ArtShowPieces, 0) AS ArtShowPieces, IFNULL(art2.PrintShopPieces, 0) AS PrintShopPieces FROM ArtistPresence ap JOIN ArtistDetails ad ON ad.ArtistID = ap.ArtistID JOIN People p ON p.PeopleID = ad.PeopleID LEFT OUTER JOIN (SELECT ap.ArtistNumber, COUNT(art.ArtID) AS ArtShowPieces FROM ArtSubmissions art LEFT OUTER JOIN ArtistPresence ap ON ap.ArtistAttendingID = art.ArtistAttendingID WHERE ap.Year = $year AND art.IsPrintShop = 0 GROUP BY ap.ArtistNumber) AS art1 ON ap.ArtistNumber = art1.ArtistNumber LEFT OUTER JOIN (SELECT ap.ArtistNumber, SUM(art.QuantitySent - art.QuantitySold) AS PrintShopPieces FROM ArtSubmissions art LEFT OUTER JOIN ArtistPresence ap ON ap.ArtistAttendingID = art.ArtistAttendingID WHERE ap.Year = $year AND art.IsPrintShop = 1 GROUP BY ap.ArtistNumber) AS art2 ON ap.ArtistNumber = art2.ArtistNumber WHERE ap.Year = $year ORDER BY p.LastName";
		$inventory = array();
		$result = $db->query($sql);
		while($row = $result->fetch_array())
			$inventory[] = $row;
		$result->close();
		header("Content-type: application/json");
		echo json_encode($inventory);
	}
	elseif($action == "GetUsers")
	{
		$year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 3 ? date("Y") + 1: date("Y"));
		$sql = "SELECT p.PeopleID, NULL AS OneTimeID, p.FirstName, p.LastName, p.Address1, p.Address2, p.City, p.State, p.ZipCode, p.Country, p.Phone1, p.Phone1Type, p.Phone2, p.Phone2Type, p.Email, p.Registered, pb.BadgeName, pb.BadgeNumber, p.Banned, p.ParentID, p.HeardFrom, p.LastChanged, CONCAT(par.FirstName, ' ', par.LastName) AS ParentName, par.Phone1 AS ParentContact FROM People p LEFT OUTER JOIN People par ON par.PeopleID = p.ParentID LEFT OUTER JOIN PurchasedBadges pb ON p.PeopleID = pb.PeopleID AND pb.Year = $year WHERE p.IsCharity = 0 UNION SELECT NULL AS PeopleID, o.OneTimeID, o.FirstName, o.LastName, o.Address1, o.Address2, o.City, o.State, o.ZipCode, o.Country, o.Phone1, o.Phone1Type, o.Phone2, o.Phone2Type, NULL AS Email, o.LastChanged AS Registered, pb.BadgeName, pb.BadgeNumber, 0 AS Banned, NULL AS ParentID, '' AS HeardFrom, o.LastChanged, NULL AS ParentName, NULL AS ParentContact FROM OneTimeRegistrations o LEFT OUTER JOIN PurchasedBadges pb ON o.OneTimeID = pb.OneTimeID AND pb.Year = $year ";

		$result = $db->query($sql);
		$people = array();
		if($result->num_rows > 0)
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$people[] = $row;

		header("Content-type: application/json");
		echo json_encode($people);
	}
    elseif($action == "GetWaivedFeesReport")
    {
        $year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 3 ? date("Y") + 1: date("Y"));
		$sql = "SELECT DisplayName, FeesWaivedReason FROM ArtistPresence ap JOIN ArtistDetails ad ON ad.ArtistID = ap.ArtistID WHERE ap.Year = $year AND FeesWaivedReason IS NOT NULL";

		$result = $db->query($sql);
		$records = array();
		if($result->num_rows > 0)
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$records[] = $row;

		header("Content-type: application/json");
		echo json_encode($records);
    }
    elseif($action == "GetArtistsSummaryList")
    {
        $year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 3 ? date("Y") + 1: date("Y"));
		$sql = "SELECT DisplayName, LegalName, Address1, IFNULL(Address2, '') AS Address2, City, State, ZipCode, Country, StartDate, EndDate, Location FROM ArtistDetails ad JOIN ArtistPresence ap ON ad.ArtistID = ap.ArtistID JOIN People p ON ad.PeopleID = p.PeopleID JOIN ConventionDetails cd ON cd.Year = ap.Year WHERE ap.Year = $year AND ap.Status = 'Approved' ORDER BY DisplayName";

		$result = $db->query($sql);
		$records = array();
		if($result->num_rows > 0)
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$records[] = $row;

		header("Content-type: application/json");
		echo json_encode($records);
    }
    elseif($action == "EnterFinalBid")
    {
        $year = !empty($_POST["year"]) ? $db->real_escape_string($_POST["year"]) : (date("n") >= 3 ? date("Y") + 1: date("Y"));
        $pieceNum = $db->real_escape_string($_POST["pieceNum"]);
        $badgeNum = $db->real_escape_string($_POST["badgeNum"]);
        $amount = $db->real_escape_string($_POST["amount"]);
        $auction = $db->real_escape_string($_POST["auction"]);

		$result = $db->query("SELECT s.ArtID FROM ArtSubmissions s JOIN ArtistPresence ap ON s.ArtistAttendingID = ap.ArtistAttendingID WHERE ap.Year = $year AND s.ShowNumber = $pieceNum");
		if($result->num_rows == 0)
		{
            echo '{ "success": false, "message": "Piece number ' . $pieceNum . ' not recognized." }';
            return;
        }
        $row = $result->fetch_array();
		$id = $row["ArtID"];
		$result->close();

        if(empty($badgeNum) || empty($amount)) {
            $db->query("UPDATE ArtSubmissions SET FinalSalePrice = NULL, PurchaserBadgeID = NULL, Auctioned = 0 WHERE ArtID = $id");
            echo '{ "success": true, "message": "Piece ' . $pieceNum . ' has had its bid information cleared." }';
        }
		else {
            $result = $db->query("SELECT BadgeID, BadgeName FROM PurchasedBadges WHERE BadgeNumber = $badgeNum AND Year = $year");
            if($result->num_rows == 0)
            {
                echo '{ "success": false, "message": "Badge number ' . $badgeNum . ' not recognized." }';
                return;
            }
            $row = $result->fetch_array();
            $purchaser = $row["BadgeID"];
            $name = $row["BadgeName"];
            $result->close();

            if($db->query("UPDATE ArtSubmissions SET FinalSalePrice = $amount, PurchaserBadgeID = $purchaser, Auctioned = $auction WHERE ArtID = $id"))
            {
                if($auction == 1)
                    echo '{ "success": true, "message": "Piece ' . $pieceNum . ' went to Auction for $' . $amount . ', final bid by Badge ' . $badgeNum . ' ' . $name . '." }';
                else
                    echo '{ "success": true, "message": "Piece ' . $pieceNum . ' sold to Badge ' . $badgeNum . ' ' . $name . ' for $' . $amount . '." }';
            }
            else
            {
                echo '{ "success": false, "message": "Failed to write to database!" }';
            }
        }
	}
?>
