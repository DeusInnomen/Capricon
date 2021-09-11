<?php
	session_start();
	include_once('includes/functions.php');

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	function AddHangingFees($showMessage = true) {
		global $db;

        $mailInFee = 5.00;

		$year = date("n") >= 3 ? date("Y") + 1: date("Y");
		$result = $db->query("SELECT ap.ArtistAttendingID, ad.IsEAP, ap.FeesWaived, p.IsCharity, ap.ShippingPref, ap.ArtistNumber FROM ArtistPresence ap INNER JOIN ArtistDetails ad ON " .
			"ad.ArtistID = ap.ArtistID INNER JOIN People p ON ad.PeopleID = p.PeopleID WHERE ap.Year = $year AND ad.PeopleID = " . $_SESSION["PeopleID"]);
		$row = $result->fetch_array();
		$attendingID = $row["ArtistAttendingID"];
		$isEAP = $row["IsEAP"];
        $waiveFees = ($row["WaiveFees"] == 1 || $row["IsCharity"] == 1 || $row["ArtistNumber"] == 1);
        $isMailIn = ($row["ShippingPref"] != null);
		$result->close();

		$db->query("DELETE FROM ShoppingCart WHERE PurchaserID = " . $_SESSION["PeopleID"] . " AND ItemTypeName = 'HangingFees'");

        if($waiveFees) {
            if($showMessage)
                echo '{ "success": true, "message": "Fees do not apply to this exhibit." }';
            return;
        }

		$perms = UserPermissions();
		if($isEAP || in_array("artistgoh", $perms)) {
            if($showMessage)
                echo '{ "success": true, "message": "No fees required at this time." }';
			return;
		}
		$result = $db->query("SELECT MinimumBid FROM ArtSubmissions WHERE ArtistAttendingID = $attendingID AND IsPrintShop = 0 AND FeesPaid = 0");
		$fees = $isMailIn ? $mailInFee : 0.0;
		$count = 0;
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
			{
				$count++;
				if($row["MinimumBid"] != null && $row["MinimumBid"] < 100.0)
					$fees += 0.5;
				else
					$fees += 1.0;
			}
			$result->close();

			$db->query("INSERT INTO ShoppingCart (ItemTypeName, ItemTypeID, ItemDetail, PurchaserID, Price, Created) VALUES (" .
				"'HangingFees', $attendingID, '$count Piece" . ($count == 1 ? "" : "s") . " of Art " . ($isMailIn ? " and Mail-In Fee" : "") . "', " . $_SESSION["PeopleID"] .
				", $fees, NOW())");
            if($showMessage)
                echo '{ "success": true, "message": "Fees successfully added to the shopping cart." }';
		}
		else if($showMessage)
			echo '{ "success": true, "message": "No fees required at this time." }';
	}

	if(!isset($_SESSION['PeopleID']))
	{
		echo '{ "success": false, "message": "No user is logged in." }';
		return;
	}

	$task = $_POST["task"];
	$year = date("n") >= 3 ? date("Y") + 1: date("Y");

	if($task == "SubmitRequest")
	{
		$result = $db->query("SELECT ap.ArtistAttendingID, ap.isAttending, ap.AgentName, ap.AgentContact, ap.ShippingPref, ap.ShippingAddress, ap.NumTables, ap.NeedsElectricity, ap.NumGrid, ap.HasPrintShop, ap.Status, ad.DisplayName FROM ArtistPresence ap INNER JOIN ArtistDetails ad ON ad.ArtistID = ap.ArtistID WHERE ap.Year = $year AND ad.PeopleID = " . $_SESSION["PeopleID"]);
		$details = $result->fetch_array();
		$attendingID = $details["ArtistAttendingID"];
		$displayName = !empty($details["DisplayName"]) ? $details["DisplayName"] : $_SESSION["FullName"];
		$status = $details["Status"];
		$result->close();

		$handling = "";
		$needs = "";
		$handling = "The old handling method was \"";
		if($details["isAttending"] == 1)
			$handling .= "Attending In Person\". ";
		elseif($details["AgentName"] != null)
			$handling .= "Handled by Agent " . $details["AgentName"] . "\". ";
		else
			$handling .= "Shipping via " . $details["ShippingPref"] . "\". ";

		$needsElectricity = isset($_POST["needsElectricity"]) ? "1" : "0";
		if($needsElectricity != $details["NeedsElectricity"])
			$needs .= $needsElectricity == "1" ? "The artist now requires Electricity.\r\n" : "The artist no longer requires Electricity.\r\n";
		$numTables = $db->real_escape_string($_POST["numTables"]);
		if($numTables != $details["NumTables"])
			$need .= "The artist now requires $numTables 1.5' by 6' table" . ($numTables == 1 ? "" : "s") . "\r\n";
		$numGrid = $db->real_escape_string($_POST["numGrid"]);
		if($numGrid != $details["NumGrid"])
			$need .= "The artist now requires $numGrid 4' by 7' gridwall section" . ($numGrid == 1 ? "" : "s") . "\r\n";
		$hasPrintShop = isset($_POST["hasPrintShop"]) ? "1" : "0";
		if($hasPrintShop != $details["HasPrintShop"])
			$needs .= $hasPrintShop == "1" ? "The artist now has Print Shop items.\r\n" : "The artist no longer has Print Shop items.\r\n";
		$notes = $db->real_escape_string($_POST["notes"]);

		if($_POST["delivery"] == "isAttending")
		{
			$isAttending = "1";
			$agentName = "NULL";
			$agentContact = "NULL";
			$shippingPref = "NULL";
			$shippingAddress = "NULL";
			if(!empty($handling) && $details["isAttending"] == 0)
				$handling .= "The new handling method is \"Attending in Person\".";
			else
				$handling = "";
		}
		elseif($_POST["delivery"] == "mailingIn")
		{
			$isAttending = "0";
			$agentName = "NULL";
			$agentContact = "NULL";
			$shippingPref = "'" . $db->real_escape_string($_POST["shippingPref"]) . "'";
			$shippingAddress = "'" . $db->real_escape_string($_POST["shippingAddress"]) . "'";
			if(!empty($handling) && $details["ShippingPref"] != null)
				$handling .= "The new handling method is \"Shipping via " . $_POST["shippingPref"] . "\".";
			else
				$handling = "";
		}
		else
		{
			$isAttending = "0";
			$agentName = "'" . $db->real_escape_string($_POST["agentName"]) . "'";
			$agentContact = "'" . $db->real_escape_string($_POST["agentContact"]) . "'";
			$shippingPref = "NULL";
			$shippingAddress = "NULL";
			if(!empty($handling) && $details["AgentName"] != null)
				$handling .= "The new handling method is \"Handled by Agent " . $_POST["agentName"] . "\".";
			else
				$handling = "";
		}

		if($details["Status"] == "Approved" && ($handling != "" || $needs != ""))
		{
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->SMTPAuth = true;
			$mail->Port = 587;
			$mail->Host = $smtpServer;
			$mail->Username = $smtpUser;
			$mail->Password = $smtpPass;
			$mail->From = "artshow@capricon.org";
			$mail->FromName = "Capricon Art Show";
			$mail->AddAddress("artshow@capricon.org", "Capricon Art Show");
			$mail->WordWrap = 70;
			$mail->Subject = "An Artist Updated Their Handling Preference";
			$body = "Hello! This email is to let you know that an artist ($displayName) has " .
				"updated their exhibit details after you previously approved their application to exhibit.\r\n\r\n";
			if($handling != "")
				$body .= "The handling method has changed. $handling\r\n";
			if($needs != "")
				$body .= $needs . "\r\n";
			$body .= "\r\nFor more information, please check the Registration website.";
			$mail->Body = $body;
			$mail->Send();
		}

		if($attendingID == "")
		{
			$result = $db->query("SELECT ArtistID FROM ArtistDetails WHERE PeopleID = " . $_SESSION["PeopleID"]);
			$row = $result->fetch_array();
			$artistID = $row["ArtistID"];
			$result->close();

			$perms = UserPermissions();
			if(in_array("artistgoh", $perms))
				$number = "1";
			else
			{
				$result = $db->query("SELECT IFNULL(MAX(ArtistNumber), 1) + 1 AS ArtistNumber FROM ArtistPresence WHERE Year = $year");
				$row = $result->fetch_array();
				$number = $row["ArtistNumber"];
				$result->close();
			}

			if($db->query("INSERT INTO ArtistPresence (ArtistID, Year, ArtistNumber, IsAttending, AgentName, AgentContact, " .
				"ShippingPref, ShippingAddress, NeedsElectricity, NumTables, NumGrid, HasPrintShop, Notes) VALUES ($artistID, $year, $number, $isAttending, $agentName, $agentContact, $shippingPref, $shippingAddress, $needsElectricity, $numTables, $numGrid, $hasPrintShop, '$notes')"))
				echo '{ "success": true, "message": "Your request has been successfully submitted." }';
			else
				echo '{ "success": false, "message": "Failed to save your request: ' . $db->real_escape_string($db->error) . '" }';
		}
		else
		{
			if($db->query("UPDATE ArtistPresence SET IsAttending = $isAttending, AgentName = $agentName, AgentContact = $agentContact, ShippingPref = $shippingPref, ShippingAddress = $shippingAddress, NeedsElectricity = $needsElectricity, NumTables = $numTables, NumGrid = $numGrid, HasPrintShop = $hasPrintShop, Notes = '$notes' WHERE ArtistAttendingID = $attendingID"))
				echo '{ "success": true, "message": "Your request has been updated successfully." }';
			else
				echo '{ "success": false, "message": "Failed to update your request: ' . $db->real_escape_string($db->error) . '" }';
		}
	}
	elseif($task == "AddFees")
	{
		AddHangingFees();
	}
	elseif($task == "AddArtShowItem")
	{
        if(!empty($_POST["attendID"])) {
            $attendingID = $_POST["attendID"];
            $skipFees = true;
        }
        else
        {
            $result = $db->query("SELECT ap.ArtistAttendingID FROM ArtistPresence ap INNER JOIN ArtistDetails ad ON " .
                "ad.ArtistID = ap.ArtistID WHERE ap.Year = $year AND ad.PeopleID = " . $_SESSION["PeopleID"]);
            $row = $result->fetch_array();
            $attendingID = $row["ArtistAttendingID"];
            $result->close();
            $skipFees = false;
        }
		$result = $db->query("SELECT IFNULL(MAX(s.ShowNumber), 0) + 1 AS ShowNumber FROM ArtSubmissions s INNER JOIN " .
			"ArtistPresence ap ON ap.ArtistAttendingID = s.ArtistAttendingID WHERE ap.Year = $year");
		$row = $result->fetch_array();
		$showNumber = $row["ShowNumber"];
		$result->close();

		$title = $db->real_escape_string($_POST["showItemTitle"]);
		$isOriginal = (isset($_POST["showItemIsOriginal"]) ? 1 : 0);
		$media = $db->real_escape_string($_POST["showItemOriginalMedia"]);
		$printNumber = !empty($_POST["showItemPrintNumber"]) ? "'" . $db->real_escape_string($_POST["showItemPrintNumber"]) . "'" : "NULL";
		$printMaxNumber = !empty($_POST["showItemMaxPrintNumber"]) ? "'" . $db->real_escape_string($_POST["showItemMaxPrintNumber"]) . "'" : "NULL";
		$minimumBid = !empty($_POST["showItemMinimumBid"]) ? $db->real_escape_string($_POST["showItemMinimumBid"]) : "NULL";
		$minimumBid = str_replace("$", "", $minimumBid);
		$quickSale = !empty($_POST["showItemQuickSale"]) ? $db->real_escape_string($_POST["showItemQuickSale"]) : "NULL";
		$quickSale = str_replace("$", "", $quickSale);
		$notes = $db->real_escape_string($_POST["showItemNotes"]);
		$sql = "INSERT INTO ArtSubmissions (ArtistAttendingID, ShowNumber, Title, Notes, IsPrintShop, IsOriginal, OriginalMedia, " .
			"PrintNumber, PrintMaxNumber, MinimumBid, QuickSalePrice, FeesPaid, Category) VALUES ($attendingID, $showNumber, '$title', '$notes', " .
			"0, $isOriginal, '$media', $printNumber, $printMaxNumber, $minimumBid, $quickSale, 0, 'Not Sold')";
		if($db->query($sql))
		{
            if(!$skipFees) {
                // If we have hanging fees in the cart already, update them.
                $result = $db->query("SELECT * FROM ShoppingCart WHERE PurchaserID = " . $_SESSION["PeopleID"] . " AND ItemTypeName = 'HangingFees'");
                if($result->num_rows > 0)
                {
                    $result->close();
                    AddHangingFees(false);
                }
            }
			echo '{ "success": true, "message": "Your item have been successfully added." }';
		}
		else
			echo '{ "success": false, "message": "Failed to add your item. Check to make sure everything is written correctly." }';
	}
	elseif($task == "DeleteArtShowItem")
	{
		$id = $db->real_escape_string($_POST["id"]);
		$db->query("DELETE FROM ArtSubmissions WHERE ArtID = $id");
		echo '{ "success": true, "message": "Your item have been successfully removed." }';
	}
	elseif($task == "AddPrintShopItem")
	{
        if(!empty($_POST["attendID"])) {
            $attendingID = $_POST["attendID"];
            $skipFees = true;
        }
        else
        {
            $result = $db->query("SELECT ap.ArtistAttendingID FROM ArtistPresence ap INNER JOIN ArtistDetails ad ON " .
                "ad.ArtistID = ap.ArtistID WHERE ap.Year = $year AND ad.PeopleID = " . $_SESSION["PeopleID"]);
            $row = $result->fetch_array();
            $attendingID = $row["ArtistAttendingID"];
            $result->close();
            $skipFees = false;
        }
		$result = $db->query("SELECT IFNULL(MAX(s.ShowNumber), 0) + 1 AS ShowNumber FROM ArtSubmissions s INNER JOIN " .
			"ArtistPresence ap ON ap.ArtistAttendingID = s.ArtistAttendingID WHERE ap.Year = $year");
		$row = $result->fetch_array();
		$showNumber = $row["ShowNumber"];
		$result->close();

		$title = $db->real_escape_string($_POST["printItemTitle"]);
		$media = $db->real_escape_string($_POST["printItemOriginalMedia"]);
		$quantity = $db->real_escape_string($_POST["printItemQuantity"]);
		$salePrice = !empty($_POST["printItemSalePrice"]) ? $db->real_escape_string($_POST["printItemSalePrice"]) : "NULL";
		//$canPhoto = (isset($_POST["printItemCanPhoto"]) ? 1 : 0);
		$notes = $db->real_escape_string($_POST["printItemNotes"]);

		if($db->query("INSERT INTO ArtSubmissions (ArtistAttendingID, ShowNumber, Title, Notes, IsPrintShop, OriginalMedia, " .
			"QuickSalePrice, QuantitySent, QuantitySold) VALUES ($attendingID, $showNumber, '$title', '$notes', 1, '$media', " .
			"$salePrice, $quantity, 0)"))
		{
            if(!$skipFees) {
                // If we have hanging fees in the cart already, update them.
                $result = $db->query("SELECT * FROM ShoppingCart WHERE PurchaserID = " . $_SESSION["PeopleID"] . " AND ItemTypeName = 'HangingFees'");
                if($result->num_rows > 0)
                {
                    $result->close();
                    AddHangingFees();
                }
            }
			// If they're adding Print Shop items, set HasPrintShop to 1/true automatically.
			$db->query("UPDATE ArtistPresence SET HasPrintShop = 1 WHERE ArtistAttendingID = $attendingID");

			echo '{ "success": true, "message": "Your item have been successfully added." }';
		}
		else
			echo '{ "success": false, "message": "Failed to add your item. Check to make sure everything is written correctly." }';
	}
	elseif($task == "DeletePrintShopItem")
	{
		$id = $db->real_escape_string($_POST["id"]);
		$db->query("DELETE FROM ArtSubmissions WHERE ArtID = $id");
		echo '{ "success": true, "message": "Your item have been successfully removed." }';
	}
	elseif($task == "UpdatePermissions")
	{
		$toAdd = explode("|", $db->real_escape_string($_POST["add"]));
		$sentTo = "";
		foreach($toAdd as $id)
		{
			$db->query("INSERT INTO Permissions (PeopleID, Permission) VALUES ($id, 'Artist')");
			if($_POST["sendEmail"] == "true")
			{
				$result = $db->query("SELECT CONCAT(FirstName, ' ', LastName) AS Name, Email FROM People WHERE PeopleID = $id " .
					"AND Email != ''");
				if($result->num_rows > 0)
				{
					$row = $result->fetch_array();
					$name = $row["Name"];
					$email = $row["Email"];
					$result->close();

					$mail = new PHPMailer;
					$mail->isSMTP();
					$mail->SMTPAuth = true;
					$mail->Port = 587;
					$mail->Host = $smtpServer;
					$mail->Username = $smtpUser;
					$mail->Password = $smtpPass;
					$mail->From = "artshow@capricon.org";
					$mail->FromName = "Capricon Art Show";
					$mail->AddAddress($email, $name);
					$mail->WordWrap = 70;
					$mail->Subject = "Welcome, Artist, From the Capricon Art Show!";
					$mail->Body = "Hello! This email is to let you know that you have been given access to the Art Show section " .
						"of the Capricon Registration site. This means that you will be able to fill in your information about " .
						"yourself and your art, then request permission to show and sell your art at the Art Show for the upcoming " .
						"Capricon.\r\n\r\nTo get started, please go to the URL at https://registration.capricon.org/artistInformation.php " .
						"to fill in your Artist Information. You will then be taken to a page where you can request to exhibit art at " .
						"the next Capricon.\r\n\r\nOnce we receive your request, we will contact you once it's approved or if we " .
						"have any questions.\r\n\r\nThank you so much for considering Capricon for displaying and selling your " .
						"art!\r\n\r\nSincerely,\r\nThe Capricon Art Show Team";
					$mail->Send();
					$sentTo .= ", $name";
				}
			}
		}

		$toRemove = explode("|", $db->real_escape_string($_POST["remove"]));
		foreach($toRemove as $id)
			$db->query("DELETE FROM Permissions WHERE PeopleID = $id AND Permission = 'Artist'");

        echo '{ "success": true, "message": "Permissions successfully updated.' . (strlen($sentTo) > 0 ? " The following people were notified: " . substr($sentTo, 2) . "." : "") . '" }';
	}
	elseif($task == "FindPeople")
	{
		$sql = "SELECT PeopleID, FirstName, LastName, BadgeName, Email FROM People ";
		$where = "WHERE Email != '' ";
		$order = $_POST["sort"];
		if(isset($_POST["email"]) && strlen($_POST["email"]) > 0)
			$where .= "AND Email LIKE '" . $db->real_escape_string($_POST["email"]) . "%' ";

		if(isset($_POST["lastname"]) && strlen($_POST["lastname"]) > 0)
			$where .= "AND LastName LIKE '" . $db->real_escape_string($_POST["lastname"]) . "%' ";

		if(isset($_POST["badgename"]) && strlen($_POST["badgename"]) > 0)
			$where .= "AND BadgeName LIKE '" . $db->real_escape_string($_POST["badgename"]) . "%' ";

		if(strlen($order) == 0) $order = "  LastName, FirstName";
		$sql .= $where . "ORDER BY $order";

		$result = $db->query($sql);
		if($result->num_rows > 0)
		{
			echo "<div id=\"results\">\r\n";
			while($row = $result->fetch_array())
			{
				echo "<label><input type=\"checkbox\" name=\"id\" value=\"" . $row["PeopleID"] . "\" />" . $row["FirstName"] . " " . $row["LastName"] . " (" .
					$row["BadgeName"] . ")&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $row["Email"] . "</label><br />\r\n";
			}
			$result->close();
			echo "</div>\r\n";
		}
		else
			echo "<p class=\"requiredField\">No records found that matched the above parameters.</p>\r\n";
	}
	elseif($task == "UpdateStatus")
	{
		$requests = $db->real_escape_string($_POST["requests"]);
		$action = $db->real_escape_string($_POST["action"]);
		$reason = $db->real_escape_string($_POST["reason"]);
		$clearReason = $db->real_escape_string($_POST["clearReason"]);
        $waiveFees = $db->real_escape_string($_POST["clearReason"]);
        $isGOH = $db->real_escape_string($_POST["isGOH"]);
		if($action == "Delete")
		{
			$db->query("DELETE FROM ArtSubmissions WHERE ArtistAttendingID in ($requests)");
			$db->query("DELETE FROM ArtistPresence WHERE ArtistAttendingID in ($requests)");
			echo '{ "success": true, "message": "Requests successfully deleted." }';
			return;
		}

		$sql = "UPDATE ArtistPresence SET ";
		if($waiveFees == "true")
			$sql .= "WaiveFees = 1, ";
        if($isGOH == "true")
            $sql .= "ArtistNumber = 1, ";
		if($clearReason == "true")
			$sql .= "StatusReason = NULL" . ($action != "NoChange" ? ", " : "");
		elseif(!empty($reason))
			$sql .= "StatusReason = '$reason'" . ($action != "NoChange" ? ", " : "");
		if($action != "NoChange")
			$sql .= "Status = '$action' ";
		$sql .= "WHERE ArtistAttendingID IN ($requests)";

		$db->query($sql);

		if($_POST["sendEmail"] == "true" && ($action != "NoChange" || (!empty($reason) && $clearReason != "true")))
		{
			$result = $db->query("SELECT CONCAT(p.FirstName, ' ', p.LastName) AS Name, p.Email, ap.Status, ap.StatusReason, ap.NeedsElectricity, ap.NumTables, ap.NumGrid, ap.HasPrintShop, ap.FeesWaived " .
				"FROM People p INNER JOIN ArtistDetails ad ON p.PeopleID = ad.PeopleID INNER JOIN ArtistPresence ap ON ap.ArtistID = ad.ArtistID WHERE ap.ArtistAttendingID IN ($requests) "
                . "AND p.Email != ''");
			if($result->num_rows > 0)
			{
				while($row = $result->fetch_array())
				{
					$name = $row["Name"];
					$email = $row["Email"];
					$thisStatus = $row["Status"];
					$thisReason = $row["StatusReason"];

					$mail = new PHPMailer;
					$mail->isSMTP();
					$mail->SMTPAuth = true;
					$mail->Port = 587;
					$mail->Host = $smtpServer;
					$mail->Username = $smtpUser;
					$mail->Password = $smtpPass;
					$mail->From = "artshow@capricon.org";
					$mail->FromName = "Capricon Art Show";
					$mail->AddAddress($email, $name);
					$mail->WordWrap = 70;
					if($action == "Approved")
					{
                        if($row["FeesWaived"] == 1)
                            $feesNote = "";
                        else
                            $feesNote = " Please note that there is a nominal Hanging Fee that needs to be paid on anything shown in the "
                                . "Art Show (not the Print Shop) based on the minimum bid. See the Art Submission page for details.";
                        $exhibit  = "Number of 1.5' by 6' Tables Requested: " . $row["NumTables"] . "\r\n";
                        $exhibit .= "Number of 4' Wide by 7' Tall Gridwall Requested: " . $row["NumGrid"] . "\r\n";
                        $exhibit .= "Electricity Needed for Exhibit: " . ($row["NeedsElectricity"] == 1 ? "Yes" : "No") . "\r\n";
                        $exhibit .= "Expecting to Bring Print Shop Items: " . ($row["HasPrintShop"] == 1 ? "Yes" : "No") . "\r\n";
						$mail->Subject = "Capricon Art Show Application Approved";
						$mail->Body = "Hello! This email is to let you know that your request to show art at the next Capricon Art "
							. "Show has been approved! " . (!empty($thisReason) ? "\r\n\r\nAdditionally, the following reason was given "
							. "with the approval: \"$thisReason\"." : "") . "\r\n\r\nYour exhibit was approved with the following requested features:\r\n\r\n$exhibit\r\n"
                            . "You may now begin to fill in artwork that you will be bringing or sending to the Art Show by going to https://registration.capricon.org/artistSubmissions.php "
							. "and adding pieces being shown in the main show or to be sold in the Print Shop.$feesNote If you have any questions, please feel free to "
							. "contact us at artshow@capricon.org with your questions.\r\n\r\nThank you so much for considering Capricon for "
							. "displaying and selling your art!\r\n\r\nSincerely,\r\nThe Capricon Art Show Team";
					}
					elseif($action == "Rejected")
					{
						$mail->Subject = "Capricon Art Show Application Rejected";
						$mail->Body = "Hello! This email is to let you know that your request to show art at the next Capricon Art " .
							"Show has been unfortunately rejected! " . (!empty($thisReason) ? "\r\n\r\nAdditionally, the following reason was given " .
							"with the rejection: \"$thisReason\"." : "") . "\r\n\r\nPlease feel free to contact us at " .
							"artshow@capricon.org if you have any questions. You may also update your request at " .
							"https://registration.capricon.org/artistRequest.php if need be.\r\n\r\nSincerely,\r\nThe " .
							"Capricon Art Show Team";
					}
					else
					{
						$mail->Subject = "Update to your Capricon Art Show Application";
						$mail->Body = "Hello! This email is to let you know that your request to show art at the next Capricon Art " .
							"Show has been updated. The status presently is \"$thisStatus\", and the following reason has been given: " .
							"\"$thisReason\".\r\n\r\nPlease feel free to contact us at artshow@capricon.org if you have any questions. " .
							"You may also update your request at https://registration.capricon.org/artistRequest.php if need be.\r\n\r\n" .
							"Thank you so much for considering Capricon for displaying and selling your art!\r\n\r\nSincerely,\r\nThe " .
							"Capricon Art Show Team";
					}
					$mail->Send();
				}
				$result->close();
			}
		}

		echo '{ "success": true, "message": "Requests successfully updated." }';
	}
    elseif($task == "AddCharityToShow")
    {
		$artistID = $db->real_escape_string($_POST["id"]);
        $result = $db->query("SELECT ArtistAttendingID FROM ArtistPresnce WHERE ArtistID = $artistID AND Year = $year");
    	if($result->num_rows == 0)
        {
			$result = $db->query("SELECT IFNULL(MAX(ArtistNumber), 1) + 1 AS ArtistNumber FROM ArtistPresence WHERE Year = $year");
			$row = $result->fetch_array();
			$number = $row["ArtistNumber"];
			$result->close();

			$db->query("INSERT INTO ArtistPresence (ArtistID, Year, ArtistNumber, IsAttending, AgentName, AgentContact, " .
				"ShippingPref, ShippingAddress, NeedsElectricity, NumTables, NumGrid, HasPrintShop, Notes, Status, StatusReason, FeesWaivedReason) " .
                "VALUES ($artistID, $year, $number, 1, NULL, NULL, NULL, NULL, 0, 1, 0, 0, '', 'Approved', '', 'Charity')");
            echo '{ "success": true, "message": "Charity added to the art show." }';
        }
        else
        {
            $result->close();
            echo '{ "success": false, "message": "This charity is already part of this year\'s art show." }';
        }
    }
    elseif($task == "AddNewCharity")
    {
        $name = $db->real_escape_string($_POST["name"]);
        $db->query("INSERT INTO People (FirstName, LastName, Address1, City, Country, Email, Password, BadgeName, Registered, IsCharity) VALUES " .
            "('Charity', 'Auction', '555 Main Street', 'Chicago', 'USA', '', '', '$name', NOW(), 1)");
        $peopleID = $db->insert_id;
        $db->query("INSERT INTO Permissions (PeopleID, Permission, Expiration) VALUES ($peopleID, 'Artist', NULL)");
        $db->query("INSERT INTO ArtistDetails (PeopleID, DisplayName, LegalName, IsPro, IsEAP, CanPhoto, Website, ArtType, Notes) VALUES " .
            "($peopleID, '$name', '$name', 0, 0, 1, '', '', 'Charity')");
		echo '{ "success": true, "message": "Charity successfully added." }';
    }
    elseif($task == "EditArtShowItem")
    {
        $artID = $_POST["artID"];
        $title = $db->real_escape_string($_POST["showItemTitle"]);
		$isOriginal = (isset($_POST["showItemIsOriginal"]) ? 1 : 0);
		$media = $db->real_escape_string($_POST["showItemOriginalMedia"]);
		$printNumber = !empty($_POST["showItemPrintNumber"]) ? "'" . $db->real_escape_string($_POST["showItemPrintNumber"]) . "'" : "NULL";
		$printMaxNumber = !empty($_POST["showItemMaxPrintNumber"]) ? "'" . $db->real_escape_string($_POST["showItemMaxPrintNumber"]) . "'" : "NULL";
		$minimumBid = !empty($_POST["showItemMinimumBid"]) ? $db->real_escape_string($_POST["showItemMinimumBid"]) : "NULL";
		$minimumBid = str_replace("$", "", $minimumBid);
		$notes = $db->real_escape_string($_POST["showItemNotes"]);

        if($db->query("UPDATE ArtSubmissions SET Title = '$title', Notes = '$notes', IsOriginal = $isOriginal, OriginalMedia = '$media', PrintNumber = $printNumber, PrintMaxNumber = $printMaxNumber, MinimumBid = $minimumBid WHERE ArtID = $artID"))
        {
            echo '{ "success": true, "message": "Item successfully edited." }';
        }
        else
        {
            echo '{ "success": false, "message": "Failed to edit item: ' . $db->error . '" }';
        }
    }
    elseif($task == "EditPrintShopItem")
    {
        $artID = $_POST["artID"];
		$title = $db->real_escape_string($_POST["printItemTitle"]);
		$media = $db->real_escape_string($_POST["printItemOriginalMedia"]);
		$quantity = $db->real_escape_string($_POST["printItemQuantity"]);
		$salePrice = !empty($_POST["printItemSalePrice"]) ? $db->real_escape_string($_POST["printItemSalePrice"]) : "NULL";
		$notes = $db->real_escape_string($_POST["printItemNotes"]);

        if($db->query("UPDATE ArtSubmissions SET Title = '$title', Notes = '$notes', OriginalMedia = '$media', QuickSalePrice = $salePrice, QuantitySent = $quantity WHERE ArtID = $artID"))
        {
            echo '{ "success": true, "message": "Item successfully edited." }';
        }
        else
        {
            echo '{ "success": false, "message": "Failed to edit item: ' . $db->error . '" }';
        }

    }
	else
		echo '{ "success": false, "message": "Unknown request submitted." }';
?>
