<?php
	session_start();
	include_once('includes/functions.php');
	
	DoCleanup();
	
	if(isset($_POST["action"]) && $_POST["action"] == "MarkPaid")
	{
		$ids = $db->real_escape_string($_POST["badges"]);
		
		$sql = "SELECT DISTINCT PaymentReference FROM PurchasedBadges WHERE BadgeID IN ($ids)";
		$result = $db->query($sql);
		
		$refs = "";
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
			{
				$refs .= ", '" . $row["PaymentReference"] . "'";
			}
			$result->close();
			$refs = substr($refs, 2);
		}
		else
		{
			echo '{ "success": false, "message": "An error occurred attempting to get the payment references for the badges." }';
			return;
		}

		$sql = "UPDATE PurchasedBadges SET Status = 'Paid' WHERE PaymentReference IN ($refs)";
		$db->query($sql);
		
		$notified = "";
		$sql = "SELECT DISTINCT p.FirstName, p.LastName, p.Email, pb.PaymentReference FROM People p " . 
			"INNER JOIN PurchasedBadges pb ON p.PeopleID = pb.PeopleID WHERE pb.PeopleID IS NOT NULL " . 
			"AND pb.PaymentReference IN ($refs)";
		$result = $db->query($sql);
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
			{
				$email = $row["Email"];
				$first = $row["FirstName"];
				$last = $row["LastName"];				
				$check = substr($row["PaymentReference"], strpos($row["PaymentReference"], "_") + 2);
				
				$mail = new PHPMailer;
				$mail->isSMTP();
				$mail->SMTPAuth = true;
				$mail->Port = 587;
				$mail->Host = "mail.capricon.org";
				$mail->Username = "outgoing@capricon.org";
				$mail->Password = $smtpPass;
				$mail->From = "registration@capricon.org";
				$mail->FromName = "Capricon Registration";
				$mail->AddAddress($email, $first . " " . $last);
				$mail->WordWrap = 70;
				$mail->Subject = "Capricon Registration Payment Cleared";
				$mail->Body = "Hello, $first! A payment you have made with a check (#$check) has been deposited " .
					"and has cleared Capricon's bank. This email serves as notice that any badges paid for by " . 
					"this check have been activated. Thank you for your patience, and we look forward to seeing " . 
					"you at Capricon!\r\n\r\nIf you have any questions, don't hesitate to contact Capricon " . 
					"Registration at registration@capricon.org or Phandemonium's IT Director at it@phandemonium.org." . 
					"\r\n\r\nHave a great day!";
				$mail->Send();
			
				$notified = " An email was sent to notify those who have Registration Accounts.";
			}
			$result->close();
		}		
		echo '{ "success": true, "message": "The badges were updated successfully.' . $notified . '" }';
	}
	else
		echo '{ "success": false, "message": "An error occurred attempting to update the badges." }';
	
?>