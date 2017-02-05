<?php
	session_start();
	include_once('includes/functions.php');
	if(!DoesUserBelongHere("RegLead"))
		header('Location: /index.php');
	require_once("Stripe/Stripe.php");
	include_once('includes/paypal.php');

	if(isset($_POST["action"]))
	{
		if($_POST["action"] == "SetBadgeName")
		{
			$id = $db->real_escape_string($_POST["id"]);
			$badgeName = $db->real_escape_string($_POST["value"]);
			
			$result = $db->query("SELECT BadgeName FROM PurchasedBadges WHERE BadgeID = $id");
			if($result->num_rows == 0)
			{
				echo "<span class=\"requiredField\">Invalid badge ID.</span>";
				return;
			}
			$result->close();
			$db->query("UPDATE PurchasedBadges SET BadgeName = '$badgeName' WHERE BadgeID = $id");
			echo "<span>Badge Name has been updated to \"" . $_POST["value"] . "\".</span>";
		}
		else if($_POST["action"] == "SetDepartment")
		{
			$id = $db->real_escape_string($_POST["id"]);
			$department = $db->real_escape_string($_POST["value"]);
			
			$result = $db->query("SELECT BadgeName FROM PurchasedBadges WHERE BadgeID = $id");
			if($result->num_rows == 0)
			{
				echo "<span class=\"requiredField\">Invalid badge ID.</span>";
				return;
			}
			$result->close();
			$db->query("UPDATE PurchasedBadges SET Department = '$department' WHERE BadgeID = $id");
			echo "<span>Department has been updated to \"" . $_POST["value"] . "\".</span>";
		}
		else if($_POST["action"] == "SetType")
		{
			$id = $db->real_escape_string($_POST["id"]);
			$type = $db->real_escape_string($_POST["value"]);
			$source = $type == 1 ? "Comp" : "Generated";
			$result = $db->query("SELECT BadgeName FROM PurchasedBadges WHERE BadgeID = $id");
			if($result->num_rows == 0)
			{
				echo "<span class=\"requiredField\">Invalid badge ID.</span>";
				return;
			}
			$result->close();
			$db->query("UPDATE PurchasedBadges SET BadgeTypeID = $type, PaymentSource = '$source' WHERE BadgeID = $id");
			echo "<span>Badge Type has been updated.</span>";
		}
		else if ($_POST["action"] == "CheckPromoCode")
		{
			$id = $db->real_escape_string($_POST["id"]);
			$code = $db->real_escape_string($_POST["value"]);
			$result = $db->query("SELECT PromoCodeID FROM PurchasedBadges WHERE BadgeID = $id");
			$row = $result->fetch_array();
			$result->close();
			if($row["PromoCodeID"] != null)
			{
				echo "<span class=\"requiredField\">This badge has already used a promo code.</span>";
				return;
			}
			
			echo "<span class=\"requiredField\">Has promo code.</span>";
		}
		else if($_POST["action"] == "DeleteBadge")
		{
			$id = $db->real_escape_string($_POST["id"]);
			
			$result = $db->query("SELECT BadgeID FROM PurchasedBadges WHERE BadgeID = $id");
			if($result->num_rows == 0)
			{
				echo "<span class=\"requiredField\">Invalid badge ID.</span>";
				return;
			}
			$result->close();
						
			$db->query("UPDATE PurchasedBadges SET Status = 'Deleted' WHERE BadgeID = $id");			
			echo "<span>Badge has been deleted.</span>";
		}
		else if($_POST["action"] == "SuperAdminDeleteBadge")
		{
			if(DoesUserBelongHere("SuperAdmin")) {
				$id = $db->real_escape_string($_POST["id"]);
				
				$result = $db->query("SELECT BadgeID, BadgeNumber, PurchaserID, OneTimePurchaserID, PeopleID, OneTimeID, Year FROM PurchasedBadges WHERE BadgeID = $id");
				if($result->num_rows == 0)
				{
					echo "<span class=\"requiredField\">Invalid badge ID.</span>";
					return;
				}
				$row = $result->fetch_array();
				$result->close();
				
				$badgeID = $row["BadgeID"];
				$badgeNumber = $row["BadgeNumber"];
				$purchaserID = $row["PurchaserID"];
				$purchaserOneTimeID = $row["OneTimePurchaserID"];
				$peopleID = $row["PeopleID"];
				$oneTimeID = $row["OneTimeID"];
				$year = $row["Year"];
				
				$db->query("DELETE FROM PurchasedBadges WHERE BadgeID = $id");
				$db->query("UPDATE PurchaseHistory SET AmountRefunded = Total, RefundReason = 'Deleted' WHERE PurchaserID " .
					(!empty($purchaserID) ? "= $purchaserID" : ' IS NULL') . " AND PurchaserOneTimeID " .
					(!empty($purchaserOneTimeID) ? "= $purchaserOneTimeID" : ' IS NULL') . " AND PeopleID " . 
					(!empty($peopleID) ? "= $peopleID" : ' IS NULL') . " AND OneTimeID " .
					(!empty($oneTimeID) ? "= $oneTimeID" : ' IS NULL') . " AND Year = $year");
							
				echo "<span>Badge has been purged from database.</span>";
			}
			else
				header('Location: /index.php');
		}
		else if($_POST["action"] == "ApproveBadge")
		{
			$badgeID = $db->real_escape_string($_POST["id"]);
			$checkNum = $db->real_escape_string($_POST["checkNumber"]);
			
			$result = $db->query("SELECT PaymentReference FROM PurchasedBadges WHERE BadgeID = $badgeID");
			$row = $result->fetch_array();
			$ref = $row["PaymentReference"];			
			$result->close();
			$newRef = $ref . "_#" . $checkNum;
			
			$db->query("UPDATE PurchasedBadges SET Status = 'Paid', PaymentReference = '$newRef' WHERE BadgeID = $badgeID");
			$db->query("UPDATE PurchaseHistory SET PaymentReference = '$newRef' WHERE PaymentReference = '$ref'");
			$result = $db->query("SELECT PurchaserID FROM PurchasedBadges WHERE BadgeID = $badgeID");
			$row = $result->fetch_array();
			$peopleID = $row["PurchaserID"];
			$result->close();
			if(!is_null($peopleID))
			{		
				$result = $db->query("SELECT CONCAT(FirstName, ' ', LastName) AS Name, Email FROM People WHERE PeopleID = $peopleID");
				$row = $result->fetch_array();
				$email = $row["Email"];
				$name = $row["Name"];
				$result->close();
				
				if(!empty($email))
				{
					$mail = new PHPMailer;
					$mail->IsSendmail();
					$mail->From = "registration@capricon.org";
					$mail->FromName = "Capricon Registration";
					$mail->AddAddress($email, $name);
					$mail->WordWrap = 70;
					$mail->Subject = "Check for Capricon Registration Received!";
					$mail->Body = "Hello! This email is to let you know that we have received your check, numbered $checkNum, that you mailed in to " . 
						"pay for your convention registration. We've marked your purchase as paid, and we will see you at Capricon!\r\n\r\n" . 
						"Sincerely,\r\nThe Capricon Registration Team";
					$mail->Send();
				}
			}
			echo "<span>Badge has been approved.</span>";
		}
		else if($_POST["action"] == "RefundBadge")
		{
			$id = $db->real_escape_string($_POST["id"]);
			$result = $db->query("SELECT PurchaserID, OneTimePurchaserID, PeopleID, OneTimeID, BadgeTypeID, BadgeName, AmountPaid, PaymentSource, PaymentReference, Year FROM PurchasedBadges WHERE BadgeID = $id");
			if($result->num_rows == 0)
			{
				echo "<span class=\"requiredField\">Invalid badge ID.</span>";
				return;
			}
			$row = $result->fetch_array();
			$result->close();
			
			$peopleID = $row["PurchaserID"];
			$purchaser = !is_null($row["PurchaserID"]) ? $row["PurchaserID"] : "NULL";
			$purchaserOneTime = !is_null($row["OneTimePurchaserID"]) ? $row["OneTimePurchaserID"] : "NULL";
			$person = !is_null($row["PeopleID"]) ? $row["PeopleID"] : "NULL";
			$oneTime = !is_null($row["OneTimeID"]) ? $row["OneTimeID"] : "NULL";
			$badgeTypeID = $row["BadgeTypeID"];
			$badgeName = $db->real_escape_string($row["BadgeName"]);
			$amount = $row["AmountPaid"];
			$source = $row["PaymentSource"];
			$ref = $row["PaymentReference"];
			$year = $row["Year"];
			
			if($source == "Stripe")
			{
				try
				{
					// Perform the refund.
					Stripe::setApiKey($stripeKey);
					$charge = Stripe_Charge::retrieve($ref);
					$refund = $charge->refund(array(
						"amount" => $amount * 100,
						"reason" => "requested_by_customer",
						));
					$destination = "credit card";
				}
				catch(Stripe_CardError $e)
				{
					$obj = $e->getJsonBody();
					$err = $obj['error'];
					echo "<span class=\"requiredField\">The refund attempt failed: " . $err['message'] . "</span>";
					return;
				}
			}
			elseif($source == "PayPal")
			{
				$padata =	'&TRANSACTION=' . urlencode($ref) .
						'&REFUNDTYPE=Partial' .
						'&AMT=' . urlencode($amount) .
						'&CURRENCYCODE=USD' .
						'&NOTE=' . urlencode("Refund for badge '$badgeName'.");
											
				$paypal = new MyPayPal();
				$response = $paypal->PPHttpPost('RefundTransaction', $padata);
				if(!("SUCCESS" == strtoupper($response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($response["ACK"])))
				{
					echo "<span class=\"requiredField\">The refund attempt failed: " . urldecode($response["L_LONGMESSAGE0"]) . "</span>";
					return;
				}
				$destination = "PayPal account";							
			}
			else
			{
				echo "<span class=\"requiredField\">Unable to refund badges that were not paid by Stripe or Paypal.</span>";
				return;			
			}
			
			$db->query("UPDATE PurchasedBadges SET Status = 'Refunded' WHERE BadgeID = $id");
			$db->query("INSERT INTO PurchaseHistory (PurchaserID, PurchaserOneTimeID, ItemTypeName, ItemTypeID, Details, PeopleID, OneTimeID, Price, Total, Year, Purchased, PaymentSource, PaymentReference) " . 
                "VALUES ($purchaser, $purchaserOneTime, 'Badge', $badgeTypeID, 'Refund for: $badgeName', $person, $oneTime, -$amount, -$amount, $year, NOW(), '$source', '$ref')");
			if(!is_null($peopleID))
			{		
				$result = $db->query("SELECT CONCAT(FirstName, ' ', LastName) AS Name, Email FROM People WHERE PeopleID = $peopleID");
				$row = $result->fetch_array();
				$email = $row["Email"];
				$name = $row["Name"];
				$result->close();
				
				if(!empty($email))
				{
					$mail = new PHPMailer;
					$mail->IsSendmail();
					$mail->From = "registration@capricon.org";
					$mail->FromName = "Capricon Registration";
					$mail->AddAddress($email, $name);
					$mail->WordWrap = 70;
					$mail->Subject = "Badge Refund Issued";
					$mail->Body = "Hello! This email is to let you know that we have received a request to refund badge '$badgeName' that you " . 
						"have purchased. The amount of " . sprintf("$%01.2f", $amount) . " was refunded to your $destination. Please allow up " . 
						"to 3 to 5 business days for this to appear in your account.\r\n\r\nIf you have any questions, please contact " . 
						"registration@capricon.org.\r\nSincerely,\r\nThe Capricon Registration Team";
					$mail->Send();
				}
				echo "<span>Badge has been successfully refunded. An email has been sent to $name at $email.</span>";
			}
			else
				echo "<span>Badge has been successfully refunded.</span>";
		}
		elseif($_POST["action"] == "TransferBadge")
		{
			$id = $db->real_escape_string($_POST["id"]);
			$result = $db->query("SELECT BadgeName, PurchaserID, OneTimePurchaserID, PeopleID, OneTimeID, PaymentReference, Year FROM PurchasedBadges WHERE BadgeID = $id");
			if($result->num_rows == 0)
			{
				echo "<span class=\"requiredField\">Invalid badge ID.</span>";
				return;
			}
			$row = $result->fetch_array();
			$originalPurchaser = !is_null($row["PurchaserID"]) ? "= " . $row["PurchaserID"] : "IS NULL";
			$originalPurchaserOneTime = !is_null($row["OneTimePurchaserID"]) ? "= " . $row["OneTimePurchaserID"] : "IS NULL";
			$originalPerson = !is_null($row["PeopleID"]) ? "= " . $row["PeopleID"] : "IS NULL";
			$originalOneTime = !is_null($row["OneTimeID"]) ? "= " . $row["OneTimeID"] : "IS NULL";
			$ref = $row["PaymentReference"];
			$year = $row["Year"];

			$result->close();

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
				$sql = "INSERT INTO OneTimeRegistrations (FirstName, LastName, Address1, City) VALUES ('$first', '$last', 'TRANSFER BADGE', '')";
				$db->query($sql);
				$oneTimeID = $db->insert_id;
				$oneTimePurchaserID = $oneTimeID;
				$badgename = $db->real_escape_string($_POST["badgename"]);
				if(trim($badgename) == "") $badgename = $first . " " . $last;
			}
			
			$sql = "UPDATE PurchasedBadges SET PeopleID = $peopleID, OneTimeID = $oneTimeID, PurchaserID = $purchaserID, OneTimePurchaserID = $oneTimePurchaserID, BadgeName = '$badgename' WHERE BadgeID = $id";
			$db->query($sql);
			$sql = "UPDATE PurchaseHistory SET PeopleID = $peopleID, OneTimeID = $oneTimeID, PurchaserID = $purchaserID, PurchaserOneTimeID = $oneTimePurchaserID, Details = '$badgename' WHERE PeopleID $originalPerson AND OneTimeID $originalOneTime AND PurchaserID $originalPurchaser AND PurchaserOneTimeID $originalPurchaserOneTime AND PaymentReference = '$ref' AND ItemTypeName = 'Badge' AND Year = '$year'";
			$db->query($sql);
			echo "<span>Badge has been successfully transferred.</span>";
		}
		elseif($_POST["action"] == "RolloverBadge")
		{
			$id = $db->real_escape_string($_POST["id"]);
			$result = $db->query("SELECT BadgeName, PurchaserID, OneTimePurchaserID, PeopleID, OneTimeID, Year, BadgeTypeID, BadgeName FROM PurchasedBadges WHERE BadgeID = $id");
			if($result->num_rows == 0)
			{
				echo "<span class=\"requiredField\">Invalid badge ID.</span>";
				return;
			}
			$row = $result->fetch_array();
			$peopleID = $row["PurchaserID"];
			$purchaser = !is_null($row["PurchaserID"]) ? $row["PurchaserID"] : "NULL";
			$purchaserOneTime = !is_null($row["OneTimePurchaserID"]) ? $row["OneTimePurchaserID"] : "NULL";
			$person = !is_null($row["PeopleID"]) ? $row["PeopleID"] : "NULL";
			$oneTime = !is_null($row["OneTimeID"]) ? $row["OneTimeID"] : "NULL";
			$badgeTypeID = $row["BadgeTypeID"];
			$badgeName = $db->real_escape_string($row["BadgeName"]);
			$year = $row["Year"] + 1;
			$result->close();
			
			$sql = "SELECT CASE WHEN EXISTS (SELECT BadgeNumber FROM PurchasedBadges WHERE BadgeNumber = 150 AND Year = $year) THEN 99999 ELSE 150 END AS Next UNION SELECT (p1.BadgeNumber + 1) as Next FROM PurchasedBadges p1 WHERE NOT EXISTS (SELECT p2.BadgeNumber FROM PurchasedBadges p2 WHERE p2.BadgeNumber = p1.BadgeNumber + 1 AND p2.Year = $year) AND p1.Year = $year HAVING Next >= 150 ORDER BY Next";
			$result = $db->query($sql);
			$row = $result->fetch_array();
			$badgeNumber = $row["Next"];
			$result->close();
			
			$sql = "INSERT INTO PurchasedBadges (Year, PeopleID, OneTimeID, PurchaserID, OneTimePurchaserID, BadgeNumber, BadgeTypeID, BadgeName, Status, PaymentSource, paymentReference, Created) VALUES ($year, $person, $oneTime, $purchaser, $purchaserOneTime, $badgeNumber, $badgeTypeID, '$badgeName', 'Paid', 'Rollover', 'NoCharge', NOW())";
			if($db->query($sql) === false)
			{
				echo "<span>Failed to create rollover badge: " . $db->error . "</span>";
				return;
			}
			$db->query("INSERT INTO PurchaseHistory (PurchaserID, PurchaserOneTimeID, ItemTypeName, ItemTypeID, Details, PeopleID, OneTimeID, Year, Purchased, PaymentSource, PaymentReference) VALUES ($purchaser, $purchaserOneTime, 'Badge', $badgeTypeID, '$badgeName', $person, $oneTime, $year, NOW(), 'Rollover', 'NoCharge')");
			$db->query("UPDATE PurchasedBadges SET Status = 'Rolled Over' WHERE BadgeID = $id");
			echo "<span>Badge has been successfully rolled over.</span>";
		}
		elseif($_POST["action"] == "SetBadgeAvailableFrom")
		{
			$id = $db->real_escape_string($_POST["id"]);
			$from = $db->real_escape_string($_POST["from"]);
			$from = str_replace("-", "/", $from);
			if(($moddate = strtotime($from)) === false)
			{
				echo "<span class=\"requiredField\">Date must be in MM/DD/YY format.</span>";
				return;
			}
			$value = "'" . date('Y-m-d', $moddate) . "'";
			if($db->query("UPDATE AvailableBadges SET AvailableFrom = $value WHERE AvailableBadgeID = $id") === false)
				echo "<span class=\"requiredField\">Failed to update badge: " . $db->error . "</span>";
			else
				echo "<span>Badge available from date has been successfully updated.</span>";
		}
		elseif($_POST["action"] == "SetBadgeAvailableTo")
		{
			$id = $db->real_escape_string($_POST["id"]);
			$to = $db->real_escape_string($_POST["to"]);
			$to = str_replace("-", "/", $to);
			if(($moddate = strtotime($to)) === false)
			{
				echo "<span class=\"requiredField\">Date must be in MM/DD/YY format.</span>";
				return;
			}
			$value = "'" . date('Y-m-d', $moddate) . "'";
			if($db->query("UPDATE AvailableBadges SET AvailableTo = $value WHERE AvailableBadgeID = $id") === false)
				echo "<span class=\"requiredField\">Failed to update badge: " . $db->error . "</span>";
			else
				echo "<span>Badge available to date has been successfully updated.</span>";
		}
		elseif($_POST["action"] == "SetBadgePrice")
		{
			$id = $db->real_escape_string($_POST["id"]);
			$price = $db->real_escape_string($_POST["price"]);
			if($db->query("UPDATE AvailableBadges SET Price = $price WHERE AvailableBadgeID = $id") === false)
				echo "<span class=\"requiredField\">Failed to update badge: " . $db->error . "</span>";
			else
				echo "<span>Badge price has been successfully updated.</span>";
		}
		else
			echo "<span class=\"requiredField\">Invalid action request.</span>";
	}
	else
		echo "<span class=\"requiredField\">Invalid action request.</span>";
?>