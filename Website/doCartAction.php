<?php
	session_start();
	include_once('includes/functions.php');
	
	DoCleanup();
	$return = isset($_GET["return"]) ? urldecode($_GET["return"]) : (isset($_POST["return"]) ? urldecode($_POST["return"]) : "index.php");
	
	if(isset($_GET["entry"]))
	{
		if($_GET["entry"] == "clear")
		{
			if(isset($_SESSION["PeopleID"]))
				$db->query("DELETE FROM ShoppingCart WHERE PurchaserID = " . $_SESSION["PeopleID"]);
			header('Location: ' . $return);
		}
	}
	elseif(isset($_POST["entry"]))
	{
		if($_POST["entry"] == "AddItem")
		{
			$id = $db->real_escape_string($_POST["recipient"]);
			$availableBadgeID = $_POST["type"];
			$badgeName = $db->real_escape_string($_POST["badgeName"]);
			
			// Get item's category type and year.
			$result = $db->query("SELECT bt.CategoryID, ab.Year FROM BadgeTypes bt INNER JOIN AvailableBadges ab ON " .
				"bt.BadgeTypeID = ab.BadgeTypeID WHERE ab.AvailableBadgeID = " . $availableBadgeID);
			if($result->num_rows > 0)
			{
				$row = $result->fetch_array();
				$categoryID = $row["CategoryID"];
				$year = $row["Year"];
				$result->close();
			}
			else
			{
				$year = date("n") >= 3 ? date("Y") + 1: date("Y");
				if(!empty($badgeName))
					$categoryID = "1";
				else
					$categoryID = "2";
				file_put_contents(".ab_debug", date("Ymd Hms") . ": Failed: abID = '$availableBadgeID', id = '$id', badgeName = '$badgeName'\n", FILE_APPEND);
			}
			
			// Track whether this person is a staff member of the convention.
			$perms = UserPermissions($id);
			$isStaff = (in_array("concom", $perms) || in_array("boardstaff", $perms) || in_array("boardmember", $perms));
			
			if($categoryID == "1")
			{
				// If this person is a ConCom Member, Board Staff or Board Member, they already get a badge.
				if($isStaff)
				{
					echo '{ "success": false, "message": "Notice: ' . ($id == $_SESSION["PeopleID"] ? "You do " : "This person does ") . 
						'not need a badge, ' . ($id == $_SESSION["PeopleID"] ? "you " : "they ") . 'already work for the convention." }';
					return;
				}
				
				// Reject the Kid-in-Tow badge for account owners.
				$result = $db->query("SELECT bt.Name FROM BadgeTypes bt INNER JOIN AvailableBadges ab ON " . 
					"ab.BadgeTypeID = bt.BadgeTypeID WHERE ab.AvailableBadgeID = $availableBadgeID " . 
					"AND bt.Name = 'kid-in-tow'");
				if($result->num_rows > 0)
				{
					$result->close();
					$result = $db->query("SELECT PeopleID FROM People WHERE PeopleID = $id AND Email != '' AND Password != ''");
					if($result->num_rows > 0)
					{
						$result->close();
						echo '{ "success": false, "message": "Notice: ' . ($id == $_SESSION["PeopleID"] ? "You are " : "This person is ") . 
							' not eligible for the \'Kid in Tow\' badge." }';
						return;
					}
				}
				
				// Check to see if this person has a badge in the cart already.
				$result = $db->query("SELECT CONCAT(p.FirstName, ' ', p.LastName) AS Name FROM ShoppingCart sc INNER JOIN " . 
					"People p ON p.PeopleID = sc.PeopleID WHERE sc.ItemTypeName = 'Badge' AND sc.PurchaserID = " . $_SESSION["PeopleID"] . 
					" AND sc.PeopleID = $id");
				if($result->num_rows > 0)
				{
					$row = $result->fetch_array();
					echo '{ "success": false, "message": "You already have a badge for ' . $row["Name"] . ' in the cart." }';
					$result->close();
					return;
				}
				
				// Check to see if this person has a badge for this year already.
				$result = $db->query("SELECT bt.Description, pb.Created, pb.Year FROM PurchasedBadges pb INNER JOIN BadgeTypes bt ON " .
					"bt.BadgeTypeID = pb.BadgeTypeID WHERE pb.PeopleID = $id AND pb.Year = $year");
				if($result->num_rows > 0)
				{
					$row = $result->fetch_array();
					$purchased = date("F d, Y", strtotime($row["Created"]));
					echo '{ "success": false, "message": "You already have a \'' . $row["Description"] . '\' badge purchased for ' .
						$row["Year"] . ' on ' . $purchased . '." }';
					$result->close();
					return;
				}
				
				// Sanity check.
				$result = $db->query("SELECT BadgeTypeID, Price FROM AvailableBadges WHERE AvailableBadgeID = $availableBadgeID");
				if($result->num_rows == 0)
				{
					echo '{ "success": false, "message": "Unknown badge type ID." }';
					return;
				}
				
				$row = $result->fetch_array();
				$badgeTypeID = $row["BadgeTypeID"];
				$price = $row["Price"];
				$result->close();			
				
				$db->query("INSERT INTO ShoppingCart (ItemTypeName, ItemTypeID, ItemDetail, PurchaserID, PeopleID, Price, Created) VALUES (" . 
				"'Badge', $availableBadgeID, '$badgeName', " . $_SESSION["PeopleID"] . ", $id, $price, NOW())");
			}
			elseif($categoryID == "2")
			{
				// Catan requires the purchaser to have a badge. We check for existing badges (if they're not staff)
				// and the cart for a badge first.
				if(!$isStaff)
				{
					$result = $db->query("SELECT bt.Description, pb.Created, ab.Year FROM PurchasedBadges pb INNER JOIN BadgeTypes bt ON bt.BadgeTypeID = " .
						"pb.BadgeTypeID INNER JOIN AvailableBadges ab ON ab.BadgeTypeID = pb.BadgeTypeID WHERE pb.PeopleID = $id AND " .
						"ab.Year = (SELECT CASE WHEN MONTH(CURDATE()) >= 3 THEN YEAR(CURDATE()) + 1 ELSE YEAR(CURDATE()) END AS Year) " .
						"AND bt.CategoryID = 1");
					if($result->num_rows == 0)
					{
						$result = $db->query("SELECT sc.CartID FROM ShoppingCart sc INNER JOIN AvailableBadges ab ON " .
						"ab.AvailableBadgeID = sc.ItemTypeID INNER JOIN BadgeTypes bt ON ab.BadgeTypeID = bt.BadgeTypeID " .
						"WHERE sc.ItemTypeName = 'Badge' AND sc.PeopleID = $id AND bt.CategoryID = 1");
						if($result->num_rows == 0)
						{
							echo '{ "success": false, "message": "You must have a convention badge purchased or in the cart to ' .
								'register for the Catan tournament." }';
							return;
						}
						$result->close();						
					}
				}
				
				// Check for an existing pre-reg.
				$result = $db->query("SELECT ph.RecordID FROM PurchaseHistory ph INNER JOIN BadgeTypes bt ON bt.BadgeTypeID = " .
					"ph.ItemTypeID INNER JOIN AvailableBadges ab ON ab.BadgeTypeID = ph.BadgeTypeID WHERE ph.PeopleID = $id AND " .
					"ab.Year = (SELECT CASE WHEN MONTH(CURDATE()) >= 3 THEN YEAR(CURDATE()) + 1 ELSE YEAR(CURDATE()) END AS Year) " .
					"AND ph.ItemTypeName = 'Catan' AND bt.CategoryID = 2");
				if($result->num_rows > 0)
				{
					$result->close();
					echo '{ "success": false, "message": "You have already pre-registered this person for the selected tournament." }';
					return;
				}

				$result = $db->query("SELECT CartID FROM ShoppingCart WHERE ItemTypeID = $availableBadgeID AND PeopleID = $id");
				if($result->num_rows > 0)
				{
					$result->close();
					echo '{ "success": false, "message": "This tournament pre-registration is already in the cart for this person." }';
					return;
				}
								
				$db->query("INSERT INTO ShoppingCart (ItemTypeName, ItemTypeID, PurchaserID, PeopleID, Price, Created) VALUES (" . 
					"'Catan', $availableBadgeID, " . $_SESSION["PeopleID"] . ", $id, 0.00, NOW())");
			}
			echo '{ "success": true, "message": "Successfully added item to the cart." }';
		}
		elseif($_POST["entry"] == "CartRemoveItem")
		{
			$id = $db->real_escape_string($_POST["recipient"]);
			$targets = $_POST["type"];
			if(strlen($targets) > 0)
				$db->query("DELETE FROM ShoppingCart WHERE PurchaserID = $id AND CartID IN ($targets)");
			else
				$db->query("DELETE FROM ShoppingCart WHERE PurchaserID = $id");
				
			echo '{ "success": true, "message": "Successfully removed item(s) from the cart." }';
		}
		elseif($_POST["entry"] == "CartAddCode")
		{
			$id = $db->real_escape_string($_POST["recipient"]);
			$code = $db->real_escape_string($_POST["type"]);
			$targets = "";
			if(strpos($code, "|"))
			{
				$targets = substr($code, strpos($code, "|") + 1);
				$code = substr($code, 0, strpos($code, "|"));
			}
			$code = trim($code);
			
			// Check to see if the code is a Promotional Code.
			$result = $db->query("SELECT CodeID, Year, Discount, Expiration, UsesLeft FROM PromoCodes WHERE Code = '$code'");
			if($result->num_rows > 0)
			{
				$row = $result->fetch_array();
				$result->close();
				$codeID = $row["CodeID"];
				
				// Check to see if the code expired.
				$thisYear = date("n") >= 3 ? date("Y") + 1: date("Y");
				if($thisYear != $row["Year"])
				{
					echo '{ "success": false, "message": "The provided code has expired." }';
					return;
				}
				if(isset($row["Expiration"]))
				{
					$expiration = new DateTime($row["Expiration"]);
					if($expiration < new DateTime(date("F d, Y")))
					{
						echo '{ "success": false, "message": "The provided code has expired." }';
						return;
					}
				}
				
				// Check to make sure the code has any uses left.
				$uses = $row["UsesLeft"];
				if($uses == 0)
				{
					echo '{ "success": false, "message": "The provided code has been used up." }';
					return;
				}
				
				// Apply the code to the shopping cart. Skip any entries that are already free (e.g. Kids-in-Tow badges).
				$sql = "UPDATE ShoppingCart SET PromoCodeID = $codeID WHERE PurchaserID = $id AND Price > 0";
				if(strlen($targets) > 0)
					$sql .= " AND CartID IN ($targets)";
				if($uses > 0)
					$sql .= " LIMIT $uses";
				$db->query($sql);
				
				echo '{ "success": true, "message": "Successfully applied the promotional code to the cart." }';
				return;
			}
			
			$result = $db->query("SELECT CertificateID, CurrentValue, Badges FROM GiftCertificates WHERE " . 
				"CertificateCode = '$code' AND (Recipient IS NULL OR Recipient = '" . $_SESSION["Email"] . "')");
			if($result->num_rows > 0)
			{
				$row = $result->fetch_array();
				$result->close();
				$certID = $row["CertificateID"];
				$value = $row["CurrentValue"];
				$maxBadges = $row["Badges"];
				
				if($value == 0 && $maxBadges == 0)
				{
					echo '{ "success": false, "message": "The provided code has been used up." }';
					return;
				}
				
				// See if we can use all of the certificate or only part of it.
				$result = $db->query("SELECT COUNT(PurchaserID) AS Badges, SUM(Price) AS Total FROM ShoppingCart WHERE PurchaserID = $id " . 
				"AND Price > 0" . (strlen($targets) > 0 ? " AND CartID IN ($targets)" : ""));
				$row = $result->fetch_array();
				$result->close();
				$badges = $row["Badges"];
				$total = $row["Total"];
				
				if($maxBadges > 0 && $badges > $maxBadges)
				{
					// Apply to the first X badges, where X is the number left on the certificate.
					$sql = "UPDATE ShoppingCart SET CertificateID = $certID WHERE PurchaserID = $id AND ItemTypeName = 'Badge' AND Price > 0";
					if(strlen($targets) > 0)
						$sql .= " AND CartID IN ($targets)";
					$sql .= " LIMIT $maxBadges";
					$message = "Certificate applied to $badges badge" . ($badges == 1 ? "" : "s") . ".";
				}
				elseif($value > 0 && $total > $value)
				{
					// This requires manually going through the cart until we figure out when the certificate runs out.
					$result = $db->query("SELECT CartID, Price FROM ShoppingCart WHERE PurchaserID = $id AND Price > 0" .
						(strlen($targets) > 0 ? " AND CartID IN ($targets)" : ""));
					$newTargets = "";
					while($row = $result->fetch_array())
					{
						$value -= $row["Price"];
						$newTargets .= ", " . $row["CartID"];
						if($value <= 0) break;
					}
					$newTargets = substr($newTargets, 2);
					
					$sql = "UPDATE ShoppingCart SET CertificateID = $certID WHERE PurchaserID = $id AND Price > 0 AND CartID " . 
						"IN ($newTargets)";
					$message = "Successfully applied the gift certificate to the cart.";
				}
				else
				{
					// Apply the code to the entire cart. Only apply free badge certificates to badges.
					$sql = "UPDATE ShoppingCart SET CertificateID = $certID WHERE PurchaserID = $id AND Price > 0";
					if($maxBadges > 0) $sql .= " AND ItemTypeName = 'Badge'";
						
					if(strlen($targets) > 0)
						$sql .= " AND CartID IN ($targets)";
					$message = "Successfully applied the gift certificate to the cart.";
				}
				
				$db->query($sql);
				echo '{ "success": true, "message": "' . $message . '" }';
				return;
			}
			else
				echo '{ "success": false, "message": "An invalid code was provided: ' . $code . '." }';
		}
		elseif($_POST["entry"] == "CartRemoveCode")
		{
			$id = $db->real_escape_string($_POST["recipient"]);
			$targets = $db->real_escape_string($_POST["type"]);
			
			$sql = "UPDATE ShoppingCart SET PromoCodeID = NULL, CertificateID = NULL WHERE PurchaserID = $id AND Price > 0";
			if(strlen($targets) > 0)
				$sql .= " AND CartID IN ($targets)";
			$db->query($sql);
			
			echo '{ "success": true, "message": "Successfully removed codes from the cart." }';
			return;
		}
		elseif($_POST["entry"] == "AddCert")
		{
			$value = isset($_POST["amount"]) ? $_POST["amount"] : 0;
			if($value <= 0)
			{
				echo '{ "success": false, "message": "The gift certificate must have a value above $0." }';
				return;
			}
			$db->query("INSERT INTO ShoppingCart (ItemTypeName, PurchaserID, Price, Created) VALUES (" . 
				"'GiftCertificate', " . $_SESSION["PeopleID"] . ", $value, NOW())");
			echo '{ "success": true, "message": "Successfully added item to the cart." }';
		}
		else
			echo '{ "success": false, "message": "Unknown action provided." }';
	}
	else
		header('Location: ' . $return);
?>