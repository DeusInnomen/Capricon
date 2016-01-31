<?php
	session_start();
	include_once('includes/functions.php');
	require_once("Stripe/Stripe.php");
	include_once('includes/paypal.php');

	function HandleCart()
	{
		global $db, $stripeKey, $smtpPass;
		Stripe::setApiKey($stripeKey);
		
		$token = isset($_POST["stripeToken"]) ? $_POST["stripeToken"] : "";
		$method = isset($_POST["method"]) ? $_POST["method"] : "";
		
		// PayPal will call this page directly using these two GET values.
		if(isset($_GET["token"]) && isset($_GET["PayerID"]))
		{
			$token = $_GET["token"];
			$pid = $_GET["PayerID"];
			$method = "PayPal";
			
			$orders = getShoppingCart();
			$total = 0;
			$num = 0;
			
			$padata =	'&TOKEN=' . urlencode($token) .
					'&PAYERID=' . urlencode($pid) .
					'&PAYMENTREQUEST_0_PAYMENTACTION=SALE';
					
			foreach($orders as $order)
			{
				$num++;	
				$price = $order["Price"];
				if(isset($order["PromoCode"])) 
				{
					$price -= $order["Discount"];
					if($price < 0) $price = 0;
				}
				if(isset($order["CertificateCode"])) 
				{
					if($order["Badges"] > 0)
						$price = 0;
					else
						$price -= $order["CurrentValue"];
					if($price < 0) $price = 0;
				}

				$padata .=	'&L_PAYMENTREQUEST_0_NAME' . $num . '=' . urlencode($order["Item"]).
						'&L_PAYMENTREQUEST_0_DESC' . $num . '=' . urlencode($order["Details"]).
						'&L_PAYMENTREQUEST_0_AMT' . $num . '=' . urlencode($price).
						'&L_PAYMENTREQUEST_0_QTY' . $num . '=1';
				$total += $order["Price"];		
			}		
			
			$padata .=	'&PAYMENTREQUEST_0_ITEMAMT=' . urlencode(sprintf("%01.2f", $total) ).
					'&PAYMENTREQUEST_0_AMT=' . urlencode(sprintf("%01.2f", $total)) .
					'&PAYMENTREQUEST_0_CURRENCYCODE=USD';
					
			$paypal = new MyPayPal();
			$response = $paypal->PPHttpPost('DoExpressCheckoutPayment', $padata);
			if("SUCCESS" == strtoupper($response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($response["ACK"]))
			{
				$ref = $response["PAYMENTINFO_0_TRANSACTIONID"];
			}else{
				return array("success" => false, "message" => "The charge failed: " . urldecode($response["L_LONGMESSAGE0"]));
			}
		}
			
		$id = $_SESSION["PeopleID"];
		$result = $db->query("SELECT Email, CONCAT(FirstName, ' ', LastName) AS Name FROM People WHERE PeopleID = $id");
		$row = $result->fetch_array();
		$email = $row["Email"];
		$name = $row["Name"];
		$result->close();
		
		$count = 0;
		$total = 0.0;
		$result = $db->query("SELECT sc.Price, pc.Code AS PromoCode, pc.Discount, gc.CertificateCode, gc.CurrentValue, gc.Badges " .
			"FROM ShoppingCart sc LEFT OUTER JOIN PromoCodes pc ON pc.CodeID = sc.PromoCodeID LEFT OUTER JOIN GiftCertificates gc " . 
			"ON gc.CertificateID = sc.CertificateID WHERE sc.PurchaserID = $id");
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
			{
				$price = $row["Price"];
				if(isset($row["PromoCode"])) 
				{
					$price -= $row["Discount"];
					if($price < 0) $price = 0;
				}
				if(isset($row["CertificateCode"])) 
				{
					if($row["Badges"] > 0)
						$price = 0;
					else
						$price -= $row["CurrentValue"];
					if($price < 0) $price = 0;
				}
				$total += $price;
				$count++;
			}
			$result->close();
		}
		
		if($count == 0)
			return array("success" => false, "message" => "There were no entries in the cart");
		
		$isPending = false;
		if($total > 0)
		{
			if($method == "Mail")
			{
				$isPending = true;
				$source = "Check";
				$ref = strtoupper(uniqid());
			}
			elseif($method == "PayPal")
			{
				$source = "PayPal";
			}
			else // Credit Card
			{
				if($token == "")
					return array("success" => false, "message" => "No payment information was provided.");
					
				try
				{
					// Perform the charge.
					$charge = Stripe_Charge::create(array(
						"amount" => $total * 100,
						"currency" => "usd",
						"card" => $token,
						"description" => "Purchases for $name (#$id)")
						);
					$source = "Stripe";
					$ref = $db->real_escape_string($charge["id"]);
				}
				catch(Stripe_CardError $e)
				{
					$obj = $e->getJsonBody();
					$err  = $obj['error'];
					return array("success" => false, "message" => "The charge was declined: " . $err['message']);
				}
			}
		}
		else
		{
			$source = "No Charge";
			$ref = strtoupper(uniqid());
		}
		
		// If we got this far, we're ready to handle the cart's transactions.
		$items = array();
		$result = $db->query("SELECT sc.ItemTypeName, sc.ItemTypeID, sc.ItemDetail, sc.PeopleID, sc.Price, sc.PromoCodeID, sc.CertificateID, pc.Code AS PromoCode, pc.UsesLeft, pc.Discount, gc.CertificateCode, gc.CurrentValue, gc.Badges FROM ShoppingCart sc LEFT OUTER JOIN PromoCodes pc ON pc.CodeID = sc.PromoCodeID LEFT OUTER JOIN GiftCertificates gc ON gc.CertificateID = sc.CertificateID WHERE sc.PurchaserID = $id");
		while($row = $result->fetch_array())
			$items[] = $row;
		$result->close();
		
		$certs = array();
		$certificates = 0;
		$discounts = 0;
		$originalTotal = 0;
		
		$messages = "";
		$purchases = array();
		foreach($items as $item)
		{
			$type = $item["ItemTypeName"];
			$recipientID = $item["PeopleID"];
			$originalPrice = $item["Price"];
			$price = $originalPrice;
			$promoID = "NULL";
			if(isset($item["PromoCodeID"]))
			{
				$promoID = $item["PromoCodeID"];
				$price -= $item["Discount"];
				if($price < 0) $price == 0;
				if($item["UsesLeft"] > 0)
					$db->query("UPDATE PromoCodes SET UsesLeft = UsesLeft - 1 WHERE CodeID = $promoID");
			}
			$certID = "NULL";
			if(isset($item["CertificateID"]))
			{
				$certID = $item["CertificateID"];
				if($item["Badges"] > 0)
				{
					$price = 0;
					$db->query("UPDATE GiftCertificates SET Badges = Badges - 1, Redeemed = NOW() WHERE CertificateID = $certID");
				}
				else
				{
					if(!isset($certs[$certID]))
						$certs[$certID] = $item["CurrentValue"];
					$discount = $certs[$certID] > $price ? $price : $certs[$certID];
					$price -= $discount;
					$certs[$certID] -= $discount;					
					$db->query("UPDATE GiftCertificates SET CurrentValue = CurrentValue - $discount, Redeemed = NOW() " . 
						"WHERE CertificateID = $certID");
					$message .= "<li>UPDATE GiftCertificates SET CurrentValue = CurrentValue - $discount, Redeemed = NOW() " . 
						"WHERE CertificateID = $certID</li>";
				}
			}
			$originalTotal += $originalPrice;
			$discounts += ($originalPrice - $price);

			switch($type)
			{
				case "Badge":
					$availableBadgeID = $item["ItemTypeID"];
					$result = $db->query("SELECT ab.Year, ab.BadgeTypeID, bt.Description FROM AvailableBadges ab INNER JOIN BadgeTypes bt ON " .
						"bt.BadgeTypeID = ab.BadgeTypeID WHERE ab.AvailableBadgeID = $availableBadgeID");
					$row = $result->fetch_array();
					$year = $row["Year"];
					$capriconYear = $year - 1980;
					$description = $row["Description"];
					$badgeTypeID = $row["BadgeTypeID"];
					$result->close();
					
					$result = $db->query("SELECT CONCAT(FirstName, ' ', LastName) AS Name, Email FROM People WHERE PeopleID = $recipientID");
					$row = $result->fetch_array();
					$recipientName = $row["Name"];
					$recipientEmail = $row["Email"];
					$result->close();
					
					$sql = "SELECT CASE WHEN EXISTS (SELECT BadgeNumber FROM PurchasedBadges WHERE BadgeNumber = 150 AND Year = $year) THEN 99999 ELSE 150 END AS Next UNION SELECT (p1.BadgeNumber + 1) as Next FROM PurchasedBadges p1 WHERE NOT EXISTS (SELECT p2.BadgeNumber FROM PurchasedBadges p2 WHERE p2.BadgeNumber = p1.BadgeNumber + 1 AND p2.Year = $year) AND p1.Year = $year HAVING Next >= 150 ORDER BY Next";
					$result = $db->query($sql);
					$row = $result->fetch_array();
					$badgeNumber = $row["Next"];
					$badgeName = $db->real_escape_string($item["ItemDetail"]);
					$result->close();
					
					$sql = "INSERT INTO PurchasedBadges (Year, PeopleID, PurchaserID, BadgeNumber, BadgeTypeID, BadgeName, Status, " . 
						"OriginalPrice, AmountPaid, PaymentSource, PaymentReference, PromoCodeID, CertificateID, Created) VALUES ($year, " .
						"$recipientID, $id, $badgeNumber, $badgeTypeID, '$badgeName', '" . ($isPending ? "Pending" : "Paid") . 
						"', $originalPrice, $price, '$source', '$ref', $promoID, $certID, NOW())";
					$db->query($sql);
					
					$sql = "INSERT INTO PurchaseHistory (PurchaserID, ItemTypeName, ItemTypeID, Details, PeopleID, Price, Year, Purchased, " .
						"PaymentSource, PaymentReference) VALUES ($id, 'Badge', $badgeTypeID, '$badgeName', $recipientID, $price, $year, NOW(), " . 
						"'$source', '$ref')";
					$db->query($sql);
					
					$message .= "<li>$year $description " . (!stripos($description, "badge") ? "Badge " : "") .
						"for '$recipientName' " . sprintf("$%01.2f", $price) . " Badge Name: $badgeName</li>";
					$purchases[] = "$year $description " . (!stripos($description, "badge") ? "Badge " : "") .
						"for '$recipientName' " . sprintf("$%01.2f", $price) . " Badge Name: $badgeName";
						
					if($recipientEmail != "" && $recipientEmail != $email)
					{
						// Send an email to people with a full account who received a badge from a different person.
						$mail = new PHPMailer;
						$mail->isSMTP();
						$mail->SMTPAuth = true;
						$mail->Port = 587;
						$mail->Host = "mail.capricon.org";
						$mail->Username = "outgoing@capricon.org";
						$mail->Password = $smtpPass;
						$mail->From = "registration@capricon.org";
						$mail->FromName = "Capricon Registration";
						$mail->AddAddress($recipientEmail, $name);
						$mail->WordWrap = 70;
						$mail->Subject = "You Have Received a Capricon $capriconYear Badge!";
						$mail->Body = "Hello! This email serves as a notification that a Capricon $capriconYear badge has been " . 
							"purchased for you by " . $_SESSION["FullName"] . ". No further action is required by you. The badge will " .
							"appear in your Account Management screen on the Capricon Registration System website.\r\n\r\nThanks for " .
							"being a member of Phandemonium!\r\n";
						$mail->Send();
						
					}
					break;
				
				case "GiftCertificate":
					$certificates++;
					
					if(!$isPending)
					{
						$certCode = strtoupper(bin2hex(openssl_random_pseudo_bytes(7)));
						$db->query("INSERT INTO GiftCertificates (CertificateCode, PurchaserID, Purchased, OriginalValue, CurrentValue) " .
							"VALUES ('$certCode', $id, CURDATE(), $price, $price)");
						$certID = $db->insert_id;
						
						$db->query("INSERT INTO PurchaseHistory (PurchaserID, ItemTypeName, Price, Purchased, PaymentSource, PaymentReference) " .
							"VALUES ($id, 'GiftCertificate', $price, NOW(), '$source', '$ref')");
						
						$message .= "<li>Gift Certificate for " . sprintf("$%01.2f", $price) . "; Certificate Code: $certCode</li>";
						$purchases[] = "Gift Certificate for " . sprintf("$%01.2f", $price) . "; Certificate Code: $certCode";
						
						// We send a separate email with the certificate code:	
						$mail = new PHPMailer;
						$mail->isSMTP();
						$mail->SMTPAuth = true;
						$mail->Port = 587;
						$mail->Host = "mail.capricon.org";
						$mail->Username = "outgoing@capricon.org";
						$mail->Password = $smtpPass;
						$mail->From = "registration@capricon.org";
						$mail->FromName = "Capricon Registration";
						$mail->AddAddress($email, $name);
						$mail->WordWrap = 70;
						$mail->Subject = "You Have Received a Capricon Gift Certificate!";
						$mail->Body = "Hello! A gift certificate that can be used for Capricon purchases has been issued to you. " .
							"To use this certificate, enter the following code at checkout:\r\n\r\n$certCode\r\n\r\nThis code is " .
							"worth " . sprintf("$%01.2f", $price) . " and never expires.\r\n\r\nPlease save this email for your " . 
							"records. If you forget the code, or lose this email, the certificate will also be listed in the \"" . 
							"View or Purchase Gift Certificates\" screen in the Capricon Registration System.\r\n\r\nThanks for " .
							"being a member of Phandemonium!\r\n";
							
						$pdf = produceGiftCertificatePDF($certID);
						$mail->AddStringAttachment($pdf->Output('', 'S'), 'Certificate.pdf', 'base64', 'application/pdf');
						$mail->Send();
					}
					else
					{
						$message .= "<li>Gift Certificate for " . sprintf("$%01.2f", $price) . "; Certificate Not Yet Issued</li>";
						$purchases[] = "Gift Certificate for " . sprintf("$%01.2f", $price) . "; Certificate Not Yet Issued";
					}
					break;
					
				case "Catan":
					$availableBadgeID = $item["ItemTypeID"];
					$result = $db->query("SELECT ab.Year, ab.BadgeTypeID, bt.Description FROM AvailableBadges ab INNER JOIN BadgeTypes bt ON " .
						"bt.BadgeTypeID = ab.BadgeTypeID WHERE ab.AvailableBadgeID = $availableBadgeID");
					$row = $result->fetch_array();
					$year = $row["Year"];
					$badgeTypeID = $row["BadgeTypeID"];
					$description = $row["Description"];
					$result->close();
					
					$result = $db->query("SELECT CONCAT(FirstName, ' ', LastName) AS Name, BadgeName, Email FROM People WHERE PeopleID = $recipientID");
					$row = $result->fetch_array();
					$recipientName = $row["Name"];
					$result->close();
					
					$db->query("INSERT INTO PurchaseHistory (PurchaserID, PeopleID, ItemTypeName, ItemTypeID, Price, Year, Purchased, " .
							"PaymentSource, PaymentReference) VALUES ($id, $recipientID, 'Catan', $badgeTypeID, $price, $year, NOW()), " . 
							"'$source', '$ref'");

					$message .= "<li>$description for '$recipientName'</li>";
					$purchases[] = "$description for '$recipientName'";

					break;
					
				case "HangingFees":
					$details = $item["ItemDetail"];
					$attendingID = $item["ItemTypeID"];
					$db->query("UPDATE ArtSubmissions SET FeesPaid = 1 WHERE ArtistAttendingID = $attendingID AND IsPrintShop = 0");
					
					$result = $db->query("SELECT Year FROM ArtistPresence WHERE ArtistAttendingID = $attendingID");
					$row = $result->fetch_array();
					$year = $row["Year"];
					$result->close();
					
					$db->query("INSERT INTO PurchaseHistory (PurchaserID, PeopleID, ItemTypeName, ItemTypeID, Details, Price, Year, Purchased, " .
							"PaymentSource, PaymentReference) VALUES ($id, $id, 'HangingFees', $attendingID, '$details', $price, $year, NOW(), " . 
							"'$source', '$ref')");

					$message .= "<li>Art Show Hanging Fees for $details for '$recipientName'</li>";
					$purchases[] = "Art Show Hanging Fees for $details for '$recipientName'";

					break;
				
			}
		}
		
		$db->query("DELETE FROM ShoppingCart WHERE PurchaserID = $id");
		$postMessage = "Your purchase has been completed. ";
		if(strlen($ref) > 0) $postMessage .= "Your reference number for this purchase is '$ref'. Please keep it for your records. ";
		$postMessage .= "A copy of your purchase information has been sent to your email address.";
		if($certificates > 0) $postMessage .= " The information for your gift certificate" . ($certificates == 1 ? " has" : "s have") .
			"  been sent to your email in a separate message.";
		$postMessage .= "You have purchased the following items:<br /><ol>$message</ol>";
		
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->SMTPAuth = true;
		$mail->Port = 587;
		$mail->Host = "mail.capricon.org";
		$mail->Username = "outgoing@capricon.org";
		$mail->Password = $smtpPass;
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
			"\r\nDiscounts:      " . sprintf("-$%01.2f", $discounts) . "\r\n";
		$message .= "Total:          " . sprintf("$%01.2f", $total) . "\r\n\r\n";
		if($certificates > 0) $message .= "The information for your gift certificate" . ($certificates == 1 ? " has" : "s have") .
			" been sent to your email in a separate message.\r\n";				
		if($isPending)
		{
			$message .= "\r\nImportant! Attached to this email is your registration form. Please print this out and " .
				"follow the directions on it to send your payment in to Capricon.\r\n";
			$pdf = produceOrderPDF($purchases, $originalTotal, $total);
			$mail->AddStringAttachment($pdf->Output('', 'S'), 'RegistrationForm.pdf', 'base64', 'application/pdf');
		}

		$message .= "\r\nThanks for being a member of Phandemonium!\r\n";
		$mail->Body = $message;
		$mail->Send();
		
		return array("success" => true, "message" => $postMessage);
	}
	
	$results = handleCart();
	$success = $results["success"];
	$message = $results["message"];
 ?>
<html>
<head>
	<title>Capricon Registration Site -- Processing</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css">
</head>
<body>
	<form action="shoppingCart.php" method="post" name="results">
		<input type="hidden" name="processed" value="true" />
		<input type="hidden" name="success" value="<?php echo $success; ?>" />
		<input type="hidden" name="message" value="<?php echo $message; ?>" />
	</form>		
	<div class="centeredMessage"><h2>Processing your shopping cart, please wait...</h2></div>
	<script type="text/javascript">
		document.results.submit();
	</script>
</body>