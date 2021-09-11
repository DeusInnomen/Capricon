<?php
	session_start();
	if($_SERVER["HTTPS"] != "on") exit();

	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/includes/functions.php");

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	// Add authentication handler here.
	
	if(empty($_POST["action"])) exit();
	
	$action = $db->real_escape_string($_POST["action"]);
	
	if($action == "AvailableItems")
	{
		$sql = "SELECT ab.AvailableBadgeID, bt.Description, bt.CategoryID, bc.CategoryName, ab.Year, ab.Price, ab.AvailableTo, ab.BadgeTypeID FROM AvailableBadges ab JOIN BadgeTypes bt ON bt.BadgeTypeID = ab.BadgeTypeID JOIN BadgeCategory bc ON bt.CategoryID = bc.CategoryID WHERE ab.AvailableFrom <= CURDATE() AND ab.AvailableTo >= CURDATE()";
		$result = $db->query($sql);
		
		$items = array();
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				$items[] = $row;
		}
		
		header("Content-type: application/json");
		echo json_encode($items);
	}
	elseif($action == "CheckCode")
	{
		$response = array();
		$code = $db->real_escape_string($_POST["code"]);
		$result = $db->query("SELECT CodeID, Year, Discount, Expiration, UsesLeft FROM PromoCodes WHERE Code = '$code'");
		if($result->num_rows > 0)
		{
			$row = $result->fetch_array();
			$result->close();
			
			// Check to see if the code expired.
			$thisYear = date("n") >= 3 ? date("Y") + 1: date("Y");
			if($thisYear != $row["Year"])
			{
				$response["Result"] = "Failure";
				$response["Reason"] = "The provided code has expired.";
				header("Content-type: application/json");
				echo json_encode($response);
				exit();
			}
			if(isset($row["Expiration"]))
			{
				$expiration = new DateTime($row["Expiration"]);
				if($expiration < new DateTime(date("F d, Y")))
				{
					$response["Result"] = "Failure";
					$response["Reason"] = "The provided code has expired.";
					header("Content-type: application/json");
					echo json_encode($response);
					exit();
				}
			}
			
			// Check to make sure the code has any uses left.
			$uses = $row["UsesLeft"];
			if($uses == 0)
			{
				$response["Result"] = "Failure";
				$response["Reason"] = "The provided code has been used up.";
				header("Content-type: application/json");
				echo json_encode($response);
				exit();
			}
			
			$response["Result"] = "Success";
			$response["Discount"] = $row["Discount"];
			header("Content-type: application/json");
			echo json_encode($response);
			exit();
		}
		
		$result = $db->query("SELECT CertificateID, CurrentValue, Badges FROM GiftCertificates WHERE CertificateCode = '$code'");
		if($result->num_rows > 0)
		{
			$row = $result->fetch_array();
			$result->close();
			$certID = $row["CertificateID"];
			$value = $row["CurrentValue"];
			$maxBadges = $row["Badges"];

			if($value == 0 && $maxBadges == 0)
			{
				$response["Result"] = "Failure";
				$response["Reason"] = "The provided code has been used up.";
				header("Content-type: application/json");
				echo json_encode($response);
				exit();
			}
						
			$response["Result"] = "Success";
			if($value > 0)
				$response["Value"] = $value;
			else
				$response["FreeBadges"] = $maxBadges;
			header("Content-type: application/json");
			echo json_encode($response);
			exit();
		}
		else
		{
			$response["Result"] = "Failure";
			$response["Reason"] = "An invalid code was provided.";
			header("Content-type: application/json");
			echo json_encode($response);
			exit();
		}
	}
	elseif($action == "RecordPurchase")
	{
		$response = array();
		try
		{
			$ref = $db->real_escape_string($_POST["reference"]);
			$purchaser = !empty($_POST["purchaser"]) ? $db->real_escape_string($_POST["purchaser"]) : "NULL";
			$onetime = "NULL";
			$source = $db->real_escape_string($_POST["source"]);
			$code = !empty($_POST["code"]) ? $db->real_escape_string($_POST["code"]) : null;
			$promoID = "NULL";
			$certID = "NULL";
			if($code != null)
			{
				$result = $db->query("SELECT CodeID, UsesLeft FROM PromoCodes WHERE Code = '$code'");
				if($result->num_rows > 0)
				{
					$row = $result->fetch_array();
					$promoID = $row["CodeID"];
					$usesLeft = $row["UsesLeft"];
					$result->close();
				}
				else
				{
					$result = $db->query("SELECT CertificateID, CurrentValue, Badges FROM GiftCertificates WHERE CertificateCode = '$code'");
					if($result->num_rows > 0)
					{
						$row = $result->fetch_array();
						$certID = $row["CertificateID"];
						$currentValue = $row["CurrentValue"];
						$badgesLeft = $row["Badges"];
						$result->close();
					}
				}				
			}
			
			$email = null;
			$name = "";
			if($purchaser != "NULL")
			{
				$sql = "SELECT CONCAT(FirstName, ' ', LastName) AS Name, Email FROM People WHERE PeopleID = $purchaser";
				$result = $db->query($sql);
				$row = $result->fetch_array();
				$name = $row["Name"];
				$email = $row["Email"];
				$result->close();
			}
			
			$items = json_decode($_POST["items"], true);
			
			$badgeNums = array();
			$originalTotal = 0;
			$totalSpent = 0;
			$discountUsed = 0;
			$badgesFree = 0;
			$purchases = array();

			foreach($items as $item)
			{
				if($item["CategoryID"] == 1) // Badge
				{
					$thisYear = date("n") >= 3 ? date("Y") + 1: date("Y");
					$year = !empty($item["Year"]) ? $db->real_escape_string($item["Year"]) : $thisYear;
					$recipientPeopleID = !empty($item["RecipientPeopleID"]) ? $item["RecipientPeopleID"] : "NULL";
					$recipientOneTimeID = !empty($item["RecipientOneTimeID"]) ? $item["RecipientOneTimeID"] : "NULL";
					$badgeTypeID = $item["BadgeTypeID"];
					$originalPrice = $item["Price"];
					$price = $item["Price"] - $item["Discount"];
					$badgeName = $db->real_escape_string($item["Details"]);
					$description = $item["Description"];
					
					$originalTotal += $originalPrice;
					$totalSpent += $price;
					$discountUsed += $item["Discount"];
					if($badgeTypeID != 2 && $item["Price"] == $item["Discount"])
						$badgesFree++;
					
					if($recipientPeopleID != "NULL")
						$sql = "SELECT CONCAT(FirstName, ' ', LastName) AS Name FROM People WHERE PeopleID = $recipientPeopleID";
					else
						$sql = "SELECT CONCAT(FirstName, ' ', LastName) AS Name FROM OneTimeRegistrations WHERE OneTimeID = $recipientOneTimeID";
					$result = $db->query($sql);
					$row = $result->fetch_array();
					$recipientName = $row["Name"];
					$result->close();

					$sql = "INSERT INTO PurchaseHistory (PurchaserID, PurchaserOneTimeID, ItemTypeName, ItemTypeID, Details, PeopleID, OneTimeID, Price, Total, Year, Purchased, PaymentSource, PaymentReference) " . 
                        "VALUES ($purchaser, $onetime, 'Badge', $badgeTypeID, '$badgeName', $recipientPeopleID, $recipientOneTimeID, $price, $price, $year, NOW(), '$source', '$ref')";
					if($db->query($sql) === false)
					{
						$response["Result"] = "Failure";
						$response["Message"] = $db->error;
						$response["Debug"] = $sql;
						header("Content-type: application/json");
						echo json_encode($response);
						exit();
					}
                    $recordID = $db->insert_id;

					$sql = "SELECT CASE WHEN EXISTS (SELECT BadgeNumber FROM PurchasedBadges WHERE BadgeNumber = 150 AND Year = $year) THEN 99999 ELSE 150 END AS Next UNION SELECT (p1.BadgeNumber + 1) as Next FROM PurchasedBadges p1 WHERE NOT EXISTS (SELECT p2.BadgeNumber FROM PurchasedBadges p2 WHERE p2.BadgeNumber = p1.BadgeNumber + 1 AND p2.Year = $year) AND p1.Year = $year HAVING Next >= 150 ORDER BY Next";
					$result = $db->query($sql);
					$row = $result->fetch_array();
					$badgeNumber = $row["Next"];
					$result->close();
					
					$sql = "INSERT INTO PurchasedBadges (Year, PeopleID, OneTimeID, PurchaserID, OneTimePurchaserID, BadgeNumber, BadgeTypeID, BadgeName, Status, OriginalPrice, AmountPaid, PaymentSource, PaymentReference, PromoCodeID, CertificateID, RecordID, Created, PickedUp, PickUpTime) VALUES ($year, $recipientPeopleID, $recipientOneTimeID, $purchaser, $onetime, $badgeNumber, $badgeTypeID, '$badgeName', 'Paid', $originalPrice, $price, '$source', '$ref', $promoID, $certID, $recordID, NOW(), 1, NOW())";
					if($db->query($sql) === false)
					{
						$response["Result"] = "Failure";
						$response["Message"] = $sql; //$db->error;
						$response["Debug"] = $sql;
						header("Content-type: application/json");
						echo json_encode($response);
						exit();
					}
					
					$purchases[] = "$year $description " . (!stripos($description, "badge") ? "Badge " : "") .
						"for '$recipientName' " . sprintf("$%01.2f", $price) . " Badge Name: $badgeName";
					
					if($year == $thisYear)
						$badgeNums[$recipientPeopleID != "NULL" ? $recipientPeopleID : "!" . $recipientOneTimeID] = $badgeNumber;
					
				}
				elseif($item["CategoryID"] == 2) // Catan
				{
					$year = !empty($_POST["Year"]) ? $db->real_escape_string($_POST["Year"]) : (date("n") >= 3 ? date("Y") + 1: date("Y"));
					$recipientPeopleID = !empty($item["RecipientPeopleID"]) ? $item["RecipientPeopleID"] : "NULL";
					$recipientOneTimeID = !empty($item["RecipientOneTimeID"]) ? $item["RecipientOneTimeID"] : "NULL";
					$badgeTypeID = $item["TypeID"];
					$description = $item["Description"];
					
					$sql = "INSERT INTO PurchaseHistory (PurchaserID, PurchaserOneTimeID, ItemTypeName, ItemTypeID, PeopleID, OneTimeID, Price, Total, Year, Purchased, PaymentSource, PaymentReference) " . 
                        "VALUES ($purchaser, $onetime, 'Catan', $badgeTypeID, $recipientPeopleID, $recipientOneTimeID, $price, $price, $year, NOW(), '$source', '$ref')";
					if($db->query($sql) === false)
					{
						$response["Result"] = "Failure";
						$response["Message"] = $db->error;
						header("Content-type: application/json");
						echo json_encode($response);
						exit();
					}
					$purchases[] = "$description for '$recipientName'";
				}
				elseif($item["CategoryID"] == 3) // Miscellaneous Charge
				{
					$year = !empty($_POST["Year"]) ? $db->real_escape_string($_POST["Year"]) : (date("n") >= 3 ? date("Y") + 1: date("Y"));
					$details = $db->real_escape_string($item["Details"]);
					$price = $db->real_escape_string($item["Price"]);
					$totalSpent += $price;
					$originalTotal += $price;
					
					$sql = "INSERT INTO PurchaseHistory (PurchaserID, PurchaserOneTimeID, ItemTypeName, PeopleID, OneTimeID, Details, Price, Total, Year, Purchased, PaymentSource, PaymentReference) " . 
                        "VALUES ($purchaser, $onetime, 'Miscellaneous Charge', $purchaser, $onetime, '$details', $price, $price, $year, NOW(), '$source', '$ref')";
					if($db->query($sql) === false)
					{
						$response["Result"] = "Failure";
						$response["Message"] = $db->error;
						header("Content-type: application/json");
						echo json_encode($response);
						exit();
					}
					$purchases[] = "$details " . sprintf("$%01.2f", $price);
				}
			}
			
			// Update any promo codes that were used.
			if($promoID != "NULL")
			{
				if(usesLeft > 0)
					$db->query("UPDATE PromoCodes SET UsesLeft = UsesLeft - 1 WHERE CodeID = $promoID");
			}
			
			// Update any gift certificate that was used.
			if($certID != "NULL")
			{
				if($badgesLeft > 0)
					$badgesLeft -= $badgesFree;
				if($currentValue > 0)
					$currentValue -= $discountUsed;				
				$db->query("UPDATE GiftCertificates SET Redeemed = NOW(), CurrentValue = $currentValue, Badges = $badgesLeft WHERE CertificateID = $certID");
			}
			
			if(!empty($email))
			{
				$mail = new PHPMailer;
				$mail->IsSendmail();
				$mail->From = "registration@capricon.org";
				$mail->FromName = "Capricon Registration";
				$mail->AddAddress($email, $name);
				$mail->WordWrap = 70;
				$mail->Subject = "Receipt for your Capricon Purchase";
				
				$message = "Hello! This email acts as your receipt for purchases you've made at the Capricon Registration " .
					"System website. Your reference number for this order is '$ref'.\r\n\r\nPurchases:\r\n\r\n";
				foreach($purchases as $purchase)
					$message .= $purchase . "\r\n";
				$message .= "\r\n"; 
				if($discounts > 0) $message .= "Original Price: " . sprintf("$%01.2f", $originalTotal) . 
					"\r\nDiscounts:      " . sprintf("-$%01.2f", $discountUsed) . "\r\n";
				$message .= "Total:          " . sprintf("$%01.2f", $totalSpent) . "\r\n\r\n";
				$message .= "\r\nThanks for being a member of Phandemonium!\r\n";
				$mail->Body = $message;
				$mail->Send();
			}
			
			$response["Result"] = "Success";
			if(count($badgeNums) > 0) $response["BadgeNumbers"] = json_encode($badgeNums);
		}
		catch (Exception $e)
		{
			$response["Result"] = "Failure";
			$response["Message"] = $e->getMessage();
		}
		header("Content-type: application/json");
		echo json_encode($response);
	}
	
?>