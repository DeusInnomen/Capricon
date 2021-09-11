<?php
	session_start();
	include_once('includes/functions.php');
	
	DoCleanup();

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
		
	if(isset($_POST['email']))
	{
		$email = strtolower($_POST['email']);
		$result = $db->query("SELECT Email FROM People WHERE Email = '" . 
			$db->real_escape_string($email) . "'");
		$num = $result->num_rows;
		$result->close();
		if($num > 0)
		{
			echo '{ "success": false, "emailInUse": true, "message": "This email address is already in use." }';
			return;
		}
		if(isset($_POST['checkOnly']))
		{
			echo '{ "success": true, "emailInUse": false, "message": "This email address is not in use." }';
			return;
		}
		
		// Delete any current pending account.
		$db->query("DELETE FROM PendingAccounts WHERE Email = '" . $db->real_escape_string($email) . "'");
		
		// Gather remaining fields.
		$data = uniqid('', true);
		$fname = $db->real_escape_string($_POST['firstName']);
		$lname = $db->real_escape_string($_POST['lastName']);
		$badgename = $db->real_escape_string($_POST['badgeName']);
		if(empty($badgename)) $badgename = $fname . " " . $lname;
		$add1 = $db->real_escape_string($_POST['address1']);
		$add2 = strlen($_POST['address2']) > 0 ? "'" . $db->real_escape_string($_POST['address2']) . "'" : "NULL";
		$city = $db->real_escape_string($_POST['city']);
		$state = $db->real_escape_string($_POST['state']);
		$zip = $db->real_escape_string($_POST['zip']);
		$country = strlen($_POST['country']) > 0 ? "'" . $db->real_escape_string($_POST['country']) . "'" : "NULL";
		$phone1 = strlen($_POST['phone1']) > 0 ? "'" . $db->real_escape_string($_POST['phone1']) . "'" : "NULL";
		$phone1type = $phone1 !== "NULL" ? "'" . $db->real_escape_string($_POST['phone1type']) . "'" : "NULL";
		$phone2 = strlen($_POST['phone2']) > 0 ? "'" . $db->real_escape_string($_POST['phone2']) . "'" : "NULL";
		$phone2type = $phone2 !== "NULL" ? "'" . $db->real_escape_string($_POST['phone2type']) . "'" : "NULL";
		$heardFrom = $db->real_escape_string($_POST['heardFrom']);
		$pass = $_POST['password'];
		$hash = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 13));
		$interests = "";
		if(isset($_POST['intGophers']))	$interests .= "|Gophers";
		if(isset($_POST['intProgram']))	$interests .= "|Program";
		if(isset($_POST['intDealer']))	$interests .= "|Dealer";
		if(isset($_POST['intArtShow']))	$interests .= "|ArtShow";
		if(isset($_POST['intAds']))		$interests .= "|Ads";
		if(isset($_POST['intParties']))	$interests .= "|Anime";
		if(!empty($interests)) $interests = substr($interests, 1);
		
		$expire = new DateTime();
		$expire->add(new DateInterval('PT72H'));
		
		$sql = "INSERT INTO PendingAccounts (PendingID, FirstName, LastName, BadgeName, Address1, Address2, City, State, " .
			"ZipCode, Country, Phone1, Phone1Type, Phone2, Phone2Type, HeardFrom, Interests, Email, Password, Expires) " .
			"VALUES ('$data', '$fname', '$lname', '$badgename', '$add1', $add2, '$city', '$state', '$zip', $country, $phone1, " .
			"$phone1type, $phone2, $phone2type, '$heardFrom', '$interests', '$email', '$hash', '" . DateToMySQL($expire) . "')";
		
		$db->query($sql);
		
		// Send activation email.
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->SMTPAuth = true;
		$mail->Port = 587;
		$mail->Host = $smtpServer;
		$mail->Username = $smtpUser;
		$mail->Password = $smtpPass;
		$mail->From = "registration@capricon.org";
		$mail->FromName = "Capricon Registration";
		$mail->AddAddress($email, $fname . " " . $lname);
		$mail->WordWrap = 70;
		$mail->Subject = "Capricon Registration Account Activation";
		$mail->Body = "Hello! A Capricon Registration account was created for you at this email address." .
			"\r\n\r\nIf this was requested by you, please click on the following link to finish activating " . 
			"your account: https://registration.capricon.org/activate.php?id=" . $data . "\r\n\r\nIf this " . 
			"was not initiated by you, there is nothing you need to do; the above link will expire after " . 
			"72 hours.\r\n\r\nIf you have any questions, don't hesitate to contact Phandemonium's IT " . 
			"Director at it@phandemonium.org or Capricon Registration at registration@capricon.org.\r\n\r\n" . 
			"Have a great day!";
		$mail->Send();
		
		echo '{ "success": true, "emailInUse": false, "message": "Account created and pending activation." }';
	}
	else
	{
		echo '{ "success": false, "emailInUse": false, "message": "An unknown error has occurred sending your information." }';
	}
?>