<?php
	session_start();
	include_once('includes/functions.php');
	require_once("Stripe/Stripe.php");
	
	function GetNextBadgeNumber()
	{
		global $db;
		$year = date("n") >= 3 ? date("Y") + 1: date("Y");
		$sql = "SELECT IFNULL(MAX(BadgeNumber), 149) + 1 AS Next FROM PurchasedBadges WHERE BadgeNumber >= 150 AND Year = $year";
		$result = $db->query($sql);
		$row = $result->fetch_array();
		$badgeNumber = $row["Next"];
		$result->close();
		return $badgeNumber;
	}
	
	if($_POST["action"] == "ValidateCode")
	{
		$code = $db->real_escape_string($_POST["code"]);
		$code = trim($code);
		
		// Check to see if the code is a Promotional Code.
		$result = $db->query("SELECT CodeID, Year, Discount, Expiration, UsesLeft FROM PromoCodes WHERE Code = '$code'");
		if($result->num_rows > 0)
		{
			$row = $result->fetch_array();
			$result->close();
			$id = $row["CodeID"];
			$value = $row["Discount"];
			
			// Check to see if the code expired.
			$thisYear = date("n") >= 3 ? date("Y") + 1: date("Y");
			if($thisYear != $row["Year"])
			{
				echo '{ "success": false, "message": "The provided code has expired." }';
				return;
			}
			
			// Check to make sure the code has any uses left.
			$uses = $row["UsesLeft"];
			if($uses == 0)
			{
				echo '{ "success": false, "message": "The provided code has been used up." }';
				return;
			}
				
			echo '{ "success": true, "message": "This code is valid for $' . $value . ' off.", "isFreeBadge": false, "value": "' . $value . 
				'", "codeType": "promo", "codeID": ' . $id . ' }';
			return;
		}
		
		$result = $db->query("SELECT CertificateID, CurrentValue, Badges FROM GiftCertificates WHERE CertificateCode = '$code'");
		if($result->num_rows > 0)
		{
			$row = $result->fetch_array();
			$result->close();
			$id = $row["CertificateID"];
			$value = $row["CurrentValue"];
			$badges = $row["Badges"];
			
			if($value == 0 && $badges == 0)
			{
				echo '{ "success": false, "message": "The provided code has been used up." }';
				return;
			}
			elseif($badges > 0)
			{				
				echo '{ "success": true, "message": "This certificate is valid for up to ' . $badges . ' free badge' . ($badges == 1 ? "" : "s") . '.", ' . 
					'"isFreeBadge": true, "value": ' . $badges . ', "codeType": "cert", "codeID": ' . $id . ' }';
			}
			else
			{
				echo '{ "success": true, "message": "This certificate is valid for up to $' . $value . ' off", ' . 
					'"isFreeBadge": false, "value": ' . $value . ', "codeType": "cert", "codeID": ' . $id . ' }';
			}
		}
		else
			echo '{ "success": false, "message": "An invalid code was provided: ' . $code . '." }';
	}
	else if($_POST["action"] == "ProcessRegistration")
	{		
		parse_str($_POST["user"], $user);
		parse_str($_POST["badges"], $badges);
		parse_str($_POST["payment"], $payment);
		$total = $payment["amount"];
		if($total > 0 && $payment["method"] == "credit")
		{
			Stripe::setApiKey("sk_live_ZEyQTepq0r6gqhPfaI7ClUBg");

			$token = $payment["stripeToken"];
			$source = "Stripe";
			
			try
			{
				// Perform the charge.
				$charge = Stripe_Charge::create(array(
					"amount" => $total * 100,
					"currency" => "usd",
					"card" => $token,
					"description" => "Purchases for " . $user["addName"] . " (Manual Entry)")
				);
				$source = "Stripe";
				$ref = $db->real_escape_string($charge["id"]);
			}
			catch(Stripe_CardError $e)
			{
				$obj = $e->getJsonBody();
				$err  = $obj['error'];
				echo '{ "success": false, "message": "The charge was declined: ' . $err['message'] . '" }';
				return;
			}
		}
		else if($payment["method"] == "check")
		{
			$source = "Check";
			$ref = strtoupper(uniqid()) . "_#" . $payment["checkNumber"];
		}
		else
		{
			$source = "Cash";
			$ref = strtoupper(uniqid());		
		}
		
		$creationMessage = "";
		$emailSentMessage = "";
		$first = $db->real_escape_string(trim($user["addFName"]));
		$last = $db->real_escape_string(trim($user["addLName"]));
		$name = $first . " " . $last;
		$badgeName = $db->real_escape_string($badges["mainBadge"]);
		if($badgeName == '') $badgeName = $name;
		$address2 = !empty($user["addAddress2"]) ? "'" . $db->real_escape_string($user["addAddress2"]) . "'" : "NULL"; 
		
		$result = $db->query("SELECT Description FROM BadgeTypes WHERE BadgeTypeID IN (1, 2) ORDER BY BadgeTypeID");
		$row = $result->fetch_array();
		$descriptionNormal = $row["Description"];
		$row = $result->fetch_array();
		$descriptionKIT = $row["Description"];
		$result->close();
		
		$peopleID = "NULL";
		$oneTimePurchaserID = "NULL";
		
		if($user["peopleID"] == "")
		{
			$sql = "INSERT INTO People (FirstName, LastName, Address1, Address2, City, State, ZipCode, Country, Phone1, Phone1Type, Email, Password, " . 
				"Registered, BadgeName, HeardFrom) VALUES ('$first', '$last', '" . $db->real_escape_string($user["addAddress1"]) . "', $address2, '" . 
				$db->real_escape_string($user["addCity"]) . "', '" . $db->real_escape_string($user["addState"]) . "', '" . 
				$db->real_escape_string($user["addZip"]) . "', '" . $db->real_escape_string($user["addCountry"]) . "', '" . 
				$db->real_escape_string($user["addPhone"]) . "', '" . $db->real_escape_string($user["addPhoneType"]) . 
				"', '" . $db->real_escape_string($user["email"]) . "', '', NOW(), '" . $badgeName . "', '" . 
				$db->real_escape_string($user["heardFrom"]) . "')";
			$db->query($sql);
			$peopleID = $db->insert_id;
			if($db->real_escape_string($user["email"]) != "")
				SendPasswordReset($user["email"], $peopleID, true);
			$creationMessage = "Account #$peopleID was created for the member. ";
		}
		else
		{
			$sql = "UPDATE People SET FirstName = '$first', LastName = '$last', Address1 = '" . 
				$db->real_escape_string($user["addAddress1"]) . "', Address2 = $address2, City = '" . 
				$db->real_escape_string($user["addCity"]) . 
				"', State = '" . $db->real_escape_string($user["addState"]) . 
				"', ZipCode = '" . $db->real_escape_string($user["addZip"]) . 
				"', Country = '" . $db->real_escape_string($user["addCountry"]) . 
				"', Phone1 = '" . $db->real_escape_string($user["addPhone"]) . 
				"', Phone2 = '" . $db->real_escape_string($user["addPhoneType"]) . 
				"', Email = '" . $db->real_escape_string($user["email"]) . 
				"', LastChanged = NOW(), BadgeName = '" . $badgeName . 
				"', HeardFrom = '" . $db->real_escape_string($user["heardFrom"]) . 
				"' WHERE PeopleID = " . $user["peopleID"];
			$db->query($sql);
			$peopleID = $user["peopleID"];
		}
		
		$purchases = array();
		$year = date("n") >= 3 ? date("Y") + 1: date("Y");
		$price = $badges["badgePrice"];		
		$promoID = $badges["codeType"] == "promo" ? $badges["codeID"] : "NULL";
		$certID = $badges["codeType"] == "cert" ? $badges["codeID"] : "NULL";
		$originalTotal = $price;
		
		$badgeNumber = GetNextBadgeNumber();
		$sql = "INSERT INTO PurchasedBadges (Year, PeopleID, OneTimeID, PurchaserID, OneTimePurchaserID, BadgeNumber, BadgeTypeID, BadgeName, " . 
			"Status, OriginalPrice, AmountPaid, PaymentSource, PaymentReference, PromoCodeID, CertificateID, Created) VALUES ($year, " .
			"$peopleID, $oneTimePurchaserID, $peopleID, $oneTimePurchaserID, $badgeNumber, 1, '$badgeName', " . 
			"'Paid', $price, $price, '$source', '$ref', $promoID, $certID, NOW())";
		$db->query($sql);
		$sql = "INSERT INTO PurchaseHistory (PurchaserID, PurchaserOneTimeID, ItemTypeName, ItemTypeID, Details, PeopleID, OneTimeID, Price, Year, PaymentSource, PaymentReference, Purchased) VALUES ($peopleID, $oneTimePurchaserID, 'Badge', 1, '$badgeName', $peopleID, $oneTimePurchaserID, $price, $year, '$source', '$ref', NOW())";
		$db->query($sql);
		
		$purchases[] = "$year $descriptionNormal " . (!stripos($descriptionNormal, "badge") ? "Badge " : "") .
			"for '$name' " . sprintf("$%01.2f", $price) . " Badge Name: $badgeName";
		
		if(!empty($badges["addlFName1"]))
		{
			$recipientFirst = $db->real_escape_string(trim($badges["addlFName1"]));
			$recipientLast = $db->real_escape_string(trim($badges["addlLName1"]));
			$recipientName = $recipientFirst . " " . $recipientLast;
			$recipientBadge = empty($badges["addlBadge1"]) ? $recipientName : $db->real_escape_string($badges["addlBadge1"]);
			$badgeType = isset($badges["addlKid1"]) ? "2" : "1";
			$description = isset($badges["addlKid1"]) ? $descriptionKIT : $descriptionNormal;
			$badgePrice = isset($badges["addlKid1"]) ? 0 : $price;
			$originalTotal += $badgePrice;
			$badgeNumber = GetNextBadgeNumber();
			
			$sql = "INSERT INTO OneTimeRegistrations (FirstName, LastName, Address1, Address2, City, State, ZipCode, Phone1, Phone1Type) " .
				"VALUES ('$recipientFirst', '$recipientLast', '" . $db->real_escape_string($user["addAddress1"]) . "', $address2, '" . 
				$db->real_escape_string($user["addCity"]) . "', '" . $db->real_escape_string($user["addState"]) . "', '" . 
				$db->real_escape_string($user["addZip"]) . "', '" . $db->real_escape_string($user["addPhone"]) . "', '" . 
				$db->real_escape_string($user["addPhoneType"]) . "')";
			$db->query($sql);
			$addlOneTimeID = $db->insert_id;

			$sql = "INSERT INTO PurchasedBadges (Year, PeopleID, OneTimeID, PurchaserID, OneTimePurchaserID, BadgeNumber, BadgeTypeID, BadgeName, " . 
				"Status, OriginalPrice, AmountPaid, PaymentSource, PaymentReference, PromoCodeID, CertificateID, Created) VALUES ($year, " .
				"NULL, $addlOneTimeID, $peopleID, $oneTimePurchaserID, $badgeNumber, $badgeType, '$recipientBadge', " . 
				"'Paid', $price, $price, '$source', '$ref', $promoID, $certID, NOW())";
			$db->query($sql);
			$sql = "INSERT INTO PurchaseHistory (PurchaserID, PurchaserOneTimeID, ItemTypeName, ItemTypeID, Details, PeopleID, OneTimeID, Price, Year, PaymentSource, PaymentReference, Purchased) VALUES ($peopleID, $oneTimePurchaserID, 'Badge', $badgeType, '$recipientBadge', NULL, $addlOneTimeID, $price, $year, '$source', '$ref', NOW())";
			$db->query($sql);
			
			$purchases[] = "$year $description " . (!stripos($description, "badge") ? "Badge " : "") .
				"for '$recipientName' " . sprintf("$%01.2f", $badgePrice) . " Badge Name: $recipientBadge";
		}

		if(!empty($badges["addlFName2"]))
		{
			$recipientFirst = $db->real_escape_string(trim($badges["addlFName2"]));
			$recipientLast = $db->real_escape_string(trim($badges["addlLName2"]));
			$recipientName = $recipientFirst . " " . $recipientLast;
			$recipientBadge = empty($badges["addlBadge2"]) ? $recipientName : $db->real_escape_string($badges["addlBadge2"]);
			$badgeType = isset($badges["addlKid2"]) ? "2" : "1";
			$description = isset($badges["addlKid2"]) ? $descriptionKIT : $descriptionNormal;
			$badgePrice = isset($badges["addlKid2"]) ? 0 : $price;
			$originalTotal += $badgePrice;
			$badgeNumber = GetNextBadgeNumber();
			
			$sql = "INSERT INTO OneTimeRegistrations (FirstName, LastName, Address1, Address2, City, State, ZipCode, Phone1, Phone1Type) " .
				"VALUES ('$recipientFirst', '$recipientLast', '" . $db->real_escape_string($user["addAddress1"]) . "', $address2, '" . 
				$db->real_escape_string($user["addCity"]) . "', '" . $db->real_escape_string($user["addState"]) . "', '" . 
				$db->real_escape_string($user["addZip"]) . "', '" . $db->real_escape_string($user["addPhone"]) . "', '" . 
				$db->real_escape_string($user["addPhoneType"]) . "')";
			$db->query($sql);
			$addlOneTimeID = $db->insert_id;

			$sql = "INSERT INTO PurchasedBadges (Year, PeopleID, OneTimeID, PurchaserID, OneTimePurchaserID, BadgeNumber, BadgeTypeID, BadgeName, " . 
				"Status, OriginalPrice, AmountPaid, PaymentSource, PaymentReference, PromoCodeID, CertificateID, Created) VALUES ($year, " .
				"NULL, $addlOneTimeID, $peopleID, $oneTimePurchaserID, $badgeNumber, $badgeType, '$recipientBadge', " . 
				"'Paid', $price, $price, '$source', '$ref', $promoID, $certID, NOW())";
			$db->query($sql);
			$sql = "INSERT INTO PurchaseHistory (PurchaserID, PurchaserOneTimeID, ItemTypeName, ItemTypeID, Details, PeopleID, OneTimeID, Price, Year, PaymentSource, PaymentReference, Purchased) VALUES ($peopleID, $oneTimePurchaserID, 'Badge', $badgeType, '$recipientBadge', NULL, $addlOneTimeID, $price, $year, '$source', '$ref', NOW())";
			$db->query($sql);
			
			$purchases[] = "$year $description " . (!stripos($description, "badge") ? "Badge " : "") .
				"for '$recipientName' " . sprintf("$%01.2f", $badgePrice) . " Badge Name: $recipientBadge";
		}

		if(!empty($badges["addlFName3"]))
		{
			$recipientFirst = $db->real_escape_string(trim($badges["addlFName3"]));
			$recipientLast = $db->real_escape_string(trim($badges["addlLName3"]));
			$recipientName = $recipientFirst . " " . $recipientLast;
			$recipientBadge = empty($badges["addlBadge3"]) ? $recipientName : $db->real_escape_string($badges["addlBadge3"]);
			$badgeType = isset($badges["addlKid3"]) ? "2" : "1";
			$description = isset($badges["addlKid3"]) ? $descriptionKIT : $descriptionNormal;
			$badgePrice = isset($badges["addlKid3"]) ? 0 : $price;
			$originalTotal += $badgePrice;
			$badgeNumber = GetNextBadgeNumber();
			
			$sql = "INSERT INTO OneTimeRegistrations (FirstName, LastName, Address1, Address2, City, State, ZipCode, Phone1, Phone1Type) " .
				"VALUES ('$recipientFirst', '$recipientLast', '" . $db->real_escape_string($user["addAddress1"]) . "', $address2, '" . 
				$db->real_escape_string($user["addCity"]) . "', '" . $db->real_escape_string($user["addState"]) . "', '" . 
				$db->real_escape_string($user["addZip"]) . "', '" . $db->real_escape_string($user["addPhone"]) . "', '" . 
				$db->real_escape_string($user["addPhoneType"]) . "')";
			$db->query($sql);
			$addlOneTimeID = $db->insert_id;

			$sql = "INSERT INTO PurchasedBadges (Year, PeopleID, OneTimeID, PurchaserID, OneTimePurchaserID, BadgeNumber, BadgeTypeID, BadgeName, " . 
				"Status, OriginalPrice, AmountPaid, PaymentSource, PaymentReference, PromoCodeID, CertificateID, Created) VALUES ($year, " .
				"NULL, $addlOneTimeID, $peopleID, $oneTimePurchaserID, $badgeNumber, $badgeType, '$recipientBadge', " . 
				"'Paid', $price, $price, '$source', '$ref', $promoID, $certID, NOW())";
			$db->query($sql);
			$sql = "INSERT INTO PurchaseHistory (PurchaserID, PurchaserOneTimeID, ItemTypeName, ItemTypeID, Details, PeopleID, OneTimeID, Price, Year, PaymentSource, PaymentReference, Purchased) VALUES ($peopleID, $oneTimePurchaserID, 'Badge', $badgeType, '$recipientBadge', NULL, $addlOneTimeID, $price, $year, '$source', '$ref', NOW())";
			$db->query($sql);
			
			$purchases[] = "$year $description " . (!stripos($description, "badge") ? "Badge " : "") .
				"for '$recipientName' " . sprintf("$%01.2f", $badgePrice) . " Badge Name: $recipientBadge";
		}

		if(!empty($badges["addlFName4"]))
		{
			$recipientFirst = $db->real_escape_string(trim($badges["addlFName4"]));
			$recipientLast = $db->real_escape_string(trim($badges["addlLName4"]));
			$recipientName = $recipientFirst . " " . $recipientLast;
			$recipientBadge = empty($badges["addlBadge4"]) ? $recipientName : $db->real_escape_string($badges["addlBadge4"]);
			$badgeType = isset($badges["addlKid4"]) ? "2" : "1";
			$description = isset($badges["addlKid4"]) ? $descriptionKIT : $descriptionNormal;
			$badgePrice = isset($badges["addlKid4"]) ? 0 : $price;
			$originalTotal += $badgePrice;
			$badgeNumber = GetNextBadgeNumber();
			
			$sql = "INSERT INTO OneTimeRegistrations (FirstName, LastName, Address1, Address2, City, State, ZipCode, Phone1, Phone1Type) " .
				"VALUES ('$recipientFirst', '$recipientLast', '" . $db->real_escape_string($user["addAddress1"]) . "', $address2, '" . 
				$db->real_escape_string($user["addCity"]) . "', '" . $db->real_escape_string($user["addState"]) . "', '" . 
				$db->real_escape_string($user["addZip"]) . "', '" . $db->real_escape_string($user["addPhone"]) . "', '" . 
				$db->real_escape_string($user["addPhoneType"]) . "')";
			$db->query($sql);
			$addlOneTimeID = $db->insert_id;

			$sql = "INSERT INTO PurchasedBadges (Year, PeopleID, OneTimeID, PurchaserID, OneTimePurchaserID, BadgeNumber, BadgeTypeID, BadgeName, " . 
				"Status, OriginalPrice, AmountPaid, PaymentSource, PaymentReference, PromoCodeID, CertificateID, Created) VALUES ($year, " .
				"NULL, $addlOneTimeID, $peopleID, $oneTimePurchaserID, $badgeNumber, $badgeType, '$recipientBadge', " . 
				"'Paid', $price, $price, '$source', '$ref', $promoID, $certID, NOW())";
			$db->query($sql);
			$sql = "INSERT INTO PurchaseHistory (PurchaserID, PurchaserOneTimeID, ItemTypeName, ItemTypeID, Details, PeopleID, OneTimeID, Price, Year, PaymentSource, PaymentReference, Purchased) VALUES ($peopleID, $oneTimePurchaserID, 'Badge', $badgeType, '$recipientBadge', NULL, $addlOneTimeID, $price, $year, '$source', '$ref', NOW())";
			$db->query($sql);
			
			$purchases[] = "$year $description " . (!stripos($description, "badge") ? "Badge " : "") .
				"for '$recipientName' " . sprintf("$%01.2f", $badgePrice) . " Badge Name: $recipientBadge";
		}

		if(!empty($badges["addlFName5"]))
		{
			$recipientFirst = $db->real_escape_string(trim($badges["addlFName5"]));
			$recipientLast = $db->real_escape_string(trim($badges["addlLName5"]));
			$recipientName = $recipientFirst . " " . $recipientLast;
			$recipientBadge = empty($badges["addlBadge5"]) ? $recipientName : $db->real_escape_string($badges["addlBadge5"]);
			$badgeType = isset($badges["addlKid5"]) ? "2" : "1";
			$description = isset($badges["addlKid5"]) ? $descriptionKIT : $descriptionNormal;
			$badgePrice = isset($badges["addlKid5"]) ? 0 : $price;
			$originalTotal += $badgePrice;
			$badgeNumber = GetNextBadgeNumber();
			
			$sql = "INSERT INTO OneTimeRegistrations (FirstName, LastName, Address1, Address2, City, State, ZipCode, Phone1, Phone1Type) " .
				"VALUES ('$recipientFirst', '$recipientLast', '" . $db->real_escape_string($user["addAddress1"]) . "', $address2, '" . 
				$db->real_escape_string($user["addCity"]) . "', '" . $db->real_escape_string($user["addState"]) . "', '" . 
				$db->real_escape_string($user["addZip"]) . "', '" . $db->real_escape_string($user["addPhone"]) . "', '" . 
				$db->real_escape_string($user["addPhoneType"]) . "')";
			$db->query($sql);
			$addlOneTimeID = $db->insert_id;

			$sql = "INSERT INTO PurchasedBadges (Year, PeopleID, OneTimeID, PurchaserID, OneTimePurchaserID, BadgeNumber, BadgeTypeID, BadgeName, " . 
				"Status, OriginalPrice, AmountPaid, PaymentSource, PaymentReference, PromoCodeID, CertificateID, Created) VALUES ($year, " .
				"NULL, $addlOneTimeID, $peopleID, $oneTimePurchaserID, $badgeNumber, $badgeType, '$recipientBadge', " . 
				"'Paid', $price, $price, '$source', '$ref', $promoID, $certID, NOW())";
			$db->query($sql);
			$sql = "INSERT INTO PurchaseHistory (PurchaserID, PurchaserOneTimeID, ItemTypeName, ItemTypeID, Details, PeopleID, OneTimeID, Price, Year, PaymentSource, PaymentReference, Purchased) VALUES ($peopleID, $oneTimePurchaserID, 'Badge', $badgeType, '$recipientBadge', NULL, $addlOneTimeID, $price, $year, '$source', '$ref', NOW())";
			$db->query($sql);
			
			$purchases[] = "$year $description " . (!stripos($description, "badge") ? "Badge " : "") .
				"for '$recipientName' " . sprintf("$%01.2f", $badgePrice) . " Badge Name: $recipientBadge";
		}
		
		if(!empty($user["email"]))
		{
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->SMTPAuth = true;
			$mail->Port = 587;
			$mail->Host = "mail.capricon.org";
			$mail->Username = "outgoing@capricon.org";
			$mail->Password = $smtpPass;
			$mail->From = "registration@capricon.org";
			$mail->FromName = "Capricon Registration";
			$mail->AddAddress($user["email"], $name);
			$mail->WordWrap = 70;
			$mail->Subject = "Receipt for your Capricon Purchase";
			
			$message = "Hello! This email acts as your receipt for purchases you've made at the Capricon Registration " .
				"System website. Your reference number for this order is '$ref'.\r\n\r\nPurchases:\r\n\r\n";
			foreach($purchases as $purchase)
				$message .= $purchase . "\r\n";
			$message .= "\r\n"; 
			
			$discounts = $originalTotal - $total;
			if($discounts > 0)
			{
				$message .= "Original Total: " . sprintf("$%01.2f", $originalTotal) . 
					"\r\nDiscounts:      " . sprintf("-$%01.2f", $discounts) . "\r\n";
				$message .= ($badges["codeType"] == "promo" ? "Promo Code" : "Gift Certificate") . " used: " .
					$badges["codeValue"] . "\r\n";
			}
			$message .= "Total:          " . sprintf("$%01.2f", $total) . "\r\n\r\n";

			$message .= "\r\nThanks for being a member of Phandemonium!\r\n";
			$mail->Body = $message;
			$mail->Send();
			$emailSentMessage = "An email with the order details was sent to the member. ";
		}
		
		echo '{ "success": true, "message": "Registration(s) entered successfully. ' . $creationMessage . $emailSentMessage . 
			'The reference number for the order is: ' . $ref . '." }';
	}
	
?>