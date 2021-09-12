<?php
	session_start();
	include_once('includes/functions.php');
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	if(!isset($_SESSION["PeopleID"]))
	{
		echo '{ "success": false, "message": "No user is logged in." }';
		return;
	}
	
	if(isset($_GET["id"]))
	{
		$code = $db->real_escape_string($_GET["id"]);
		$result = $db->query("SELECT PeopleID, Data FROM ConfirmationLinks WHERE Code = '$code' AND Type = 'Authorize'");
		if($result->num_rows == 0)
		{
			echo '{ "success": false }';
			return;
		}
		
		$row = $result->fetch_array();
		$result->close();
		$id = $row["PeopleID"];
		$email = $row["Data"];
		if($email != $_SESSION["Email"])
		{
			echo '{ "success": false }';
			return;
		}
		
		$db->query("DELETE FROM ConfirmationLinks WHERE Code = '$code'");
		$db->query("UPDATE People SET ParentID = " . $_SESSION["PeopleID"] . " WHERE PeopleID = $id");
		
	}	
	elseif(isset($_POST["action"]) && $_POST["action"] == "AddNew")
	{		
		$parentID = $_SESSION["PeopleID"];
		$result = $db->query("SELECT Address1, Address2, City, State, ZipCode, Country FROM People WHERE PeopleID = $parentID");
		$row = $result->fetch_array();
		$result->close();	
		$address1 = $row["Address1"];
		$address2 = isset($row["Address2"]) ? "'" . $row["Address2"] . "'" : "NULL";
		$city = $row["City"];
		$state = isset($row["State"]) ? "'" . $row["State"] . "'" : "NULL";
		$zip = isset($row["ZipCode"]) ? "'" . $row["ZipCode"] . "'" : "NULL";
		$country = $row["Country"];
		
		$fname = $db->real_escape_string($_POST['firstName']);
		$lname = $db->real_escape_string($_POST['lastName']);
		$phone1 = strlen($_POST['phone1']) > 0 ? "'" . $db->real_escape_string($_POST['phone1']) . "'" : "NULL";
		$phone1type = $phone1 !== "NULL" ? "'" . $db->real_escape_string($_POST['phone1type']) . "'" : "NULL";
		$phone2 = strlen($_POST['phone2']) > 0 ? "'" . $db->real_escape_string($_POST['phone2']) . "'" : "NULL";
		$phone2type = $phone2 !== "NULL" ? "'" . $db->real_escape_string($_POST['phone2type']) . "'" : "NULL";
		$badgeName = strlen($_POST["badgeName"]) > 0 ? $db->real_escape_string($_POST["badgeName"]) : $fname . ' ' . $lname;
		
		$db->query("INSERT INTO People (FirstName, LastName, Address1, Address2, City, State, ZipCode, Country, Phone1, Phone1Type, Phone2, " .
			"Phone2Type, Email, Password, Registered, BadgeName, ParentID) VALUES ('$fname', '$lname', '$address1', $address2, '$city', " .
			"$state, $zip, '$country', $phone1, $phone1type, $phone2, $phone2type, '', '', NOW(), '$badgeName', $parentID)");
		
		echo '{ "success": true, "message": "The sub account has been successfully created." }';
	}
	elseif(isset($_POST["action"]) && $_POST["action"] == "Promote")
	{
		$id = $_POST["PeopleID"];
		$email = $db->real_escape_string($_POST["email"]);
		
		$result = $db->query("SELECT FirstName, LastName FROM People WHERE PeopleID = $id");
		$row = $result->fetch_array();
		$result->close();	
		$name = $row["FirstName"] . ' ' . $row["LastName"];
		
		// Generate reset link that expires in 72 hours.
		$data = uniqid('', true);
		$expire = new DateTime();
		$expire->add(new DateInterval('PT72H'));
		$db->query("INSERT INTO ConfirmationLinks (Code, PeopleID, Data, Type, Expires) VALUES ('$data', $id, '$email', 'Promote', '" . 
			DateToMySQL($expire) . "')");
		
		// Send email.
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->SMTPAuth = true;
		$mail->Port = 587;
		$mail->Host = $smtpServer;
		$mail->Username = $smtpUser;
		$mail->Password = $smtpPass;
		$mail->From = "registration@capricon.org";
		$mail->FromName = "Capricon Registration";
		$mail->AddAddress($email, $name);
		$mail->WordWrap = 70;
		$mail->Subject = "Capricon Registration Account Activation";
		$mail->Body = "Hello! " . $_SESSION["FullName"] . ", who formerly had you as a part of their account, wishes " . 
			"to give you full control of your account.\r\n\r\nTo complete this action, please click on the following " .
			"link to choose a password for your account: https://registration.capricon.org/resetpassword.php?id=" . $data .
			"\r\n\r\nYour email address is your login ID. The above link will expire after 72 hours.\r\n\r\n" . 
			"If you have any questions, don't hesitate to contact Phandemonium's IT Director at it@phandemonium.org." . 
			"\r\n\r\nHave a great day!";
		$mail->Send();
		echo '{ "success": true, "message": "The request has been sent to the provided email address." }';
	}
	elseif(isset($_POST["action"]) && $_POST["action"] == "Update")
	{
		$id = $_POST["PeopleID"];
		$fname = $db->real_escape_string($_POST['firstName']);
		$lname = $db->real_escape_string($_POST['lastName']);
		$phone1 = strlen($_POST['phone1']) > 0 ? "'" . $db->real_escape_string($_POST['phone1']) . "'" : "NULL";
		$phone1type = $phone1 !== "NULL" ? "'" . $db->real_escape_string($_POST['phone1type']) . "'" : "NULL";
		$phone2 = strlen($_POST['phone2']) > 0 ? "'" . $db->real_escape_string($_POST['phone2']) . "'" : "NULL";
		$phone2type = $phone2 !== "NULL" ? "'" . $db->real_escape_string($_POST['phone2type']) . "'" : "NULL";
		$badgeName = strlen($_POST["badgeName"]) > 0 ? $db->real_escape_string($_POST["badgeName"]) : $fname . ' ' . $lname;
		
		$db->query("UPDATE People SET FirstName = '$fname', LastName = '$lname', Phone1 = $phone1, Phone1Type = $phone1type, Phone2 = " . 
			"$phone2, Phone2Type = $phone2type, BadgeName = '$badgeName' WHERE PeopleID = $id");
		echo '{ "success": true, "message": "The sub account has been successfully updated." }';
	}
	elseif(isset($_POST["action"]) && $_POST["action"] == "Remove")
	{
		$id = $_POST["PeopleID"];
		if($id != $_SESSION["PeopleID"])
		{
			$result = $db->query("SELECT Email FROM People WHERE PeopleID = $id AND ParentID = " . $_SESSION["PeopleID"]);
			$row = $result->fetch_array();
			$result->close();
			if(strlen($row["Email"]) == 0)
			{
				echo '{ "success": false, "message": "Sub accounts cannot be deleted." }';
				return;
			}
		}
		$db->query("UPDATE People SET ParentID = NULL WHERE PeopleID = $id");
		echo '{ "success": true, "message": "The authorization has been successfully removed." }';
	}
	elseif(isset($_POST["action"]) && $_POST["action"] == "Authorize")
	{
		$id = $_SESSION["PeopleID"];
		$email = $db->real_escape_string($_POST["requestEmail"]);
		
		// Generate reset link that expires in 72 hours.
		$data = uniqid('', true);
		$expire = new DateTime();
		$expire->add(new DateInterval('PT72H'));
		$db->query("INSERT INTO ConfirmationLinks (Code, PeopleID, Data, Type, Expires) VALUES ('$data', $id, '$email', 'Authorize', '" . 
			DateToMySQL($expire) . "')");
	
		// Find out if this person already has an account. If they do not, tailor the message differently.
		$targetID = 0;
		$result = $db->query("SELECT PeopleID FROM People WHERE Email = '$email'");
		if($result->num_rows > 0)
		{
			$row = $result->fetch_array();
			$result->close();
			$targetID = $row["PeopleID"];
		}
	
		// Send email.
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->SMTPAuth = true;
		$mail->Port = 587;
		$mail->Host = $smtpServer;
		$mail->Username = $smtpUser;
		$mail->Password = $smtpPass;
		$mail->From = "registration@capricon.org";
		$mail->FromName = "Capricon Registration";
		$mail->AddAddress($email, $name);
		$mail->WordWrap = 70;
		$mail->Subject = "Capricon Registration Authorization Request";
		$mail->Body = "Hello! " . $_SESSION["FullName"] . " has submitted a request to allow you to make purchases for " . 
			"them at the Capricon Registration System website. This does not perform any actual charges, but instead " . 
			"gives you the ability to make purchases for them, such as badges.\r\n"; 
		if($targetID > 0)
			$mail->Body .= "To complete this action, please click on the following link to confirm the request: " . 
				"https://registration.capricon.org/authorizeAccount.php?id=" . $data . "\r\n\r\n";
		else
			$mail->Body .= "To complete this action, you will need to create an account on the website. To do this, " . 
				"head to https://registration.capricon.org/register.php to begin the process. Once you have created " . 
				"your account, you can authorize the request at that time by coming back to this email and clicking " .
				"the following link: https://registration.capricon.org/authorizeAccount.php?id=" . $data . "\r\n\r\n";
		$mail->Body .= "Please note that the link will expire after 72 hours. You can also\r\n\r\nIf you have any questions, don't " . 
			"hesitate to contact Phandemonium's IT Director at it@phandemonium.org.\r\n\r\nHave a great day!";
		$mail->Send();
		echo '{ "success": true, "message": "The request has been sent to the provided email address." }';
	}
	else
	{
		echo '{ "success": false, "message": "Invalid action" }';
	}
?>