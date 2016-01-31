<?php
	session_start();
	include_once('includes/functions.php');
	
	if(!isset($_SESSION['PeopleID']))
	{
		echo '{ "success": false, "authenticated": false, "emailInUse": false, "message": "No user is logged in." }';
		return;
	}
	
	if(isset($_POST["peopleID"]) && !DoesUserBelongHere("RegLead") && !DoesUserBelongHere("Ops"))
	{
		echo '{ "success": false, "authenticated": false, "emailInUse": false, "message": "Access denied." }';
		return;
	}
	
	$email = isset($_POST['currentEmail']) ? $_POST['currentEmail'] : $_SESSION["Email"];	
	if(!isset($_POST['setPerms']))
	{
		if(isset($_POST['passwordCurrent']))
		{		
			if(!AuthenticateUser($email, $_POST['passwordCurrent'], true))
			{
				echo '{ "success": false, "authenticated": false, "emailInUse": false, "message": "The password provided was incorrect." }';
				return;
			}
		}
		else
		{
			echo '{ "success": false, "authenticated": false, "emailInUse": false, "message": "The current password was not provided." }';
			return;
		}
	}
	$id = isset($_POST['peopleID']) ? $_POST['peopleID'] : $_SESSION['PeopleID'];	
	
	// Email changes
	$emailchanged = "";
	if(isset($_POST['email']) && $email !== $_POST['email'])
	{
		$newemail = $db->real_escape_string($_POST['email']);
		if(isset($_POST['forceEmail']))
		{
			$db->query("UPDATE People SET Email = '$newemail' WHERE PeopleID = $id");
			$emailchanged = "This user's email has been successfully changed. No verification was sent!";
		}
		else
		{
			$result = $db->query("SELECT Email FROM People WHERE Email = '" . 
				$db->real_escape_string($newemail) . "'");
			$num = $result->num_rows;
			$result->close();
			if($num > 0)
			{
				echo '{ "success": false, "authenticated": true, "emailInUse": true, "message": "This email address is already in use." }';
				return;
			}

			// Generate reset link that expires in 12 hours.
			$code = uniqid('', true);
			$expire = new DateTime();
			$expire->add(new DateInterval('PT12H'));
			$db->query("INSERT INTO ConfirmationLinks (Code, PeopleID, Type, Data, Expires) VALUES ('" . 
				$db->real_escape_string($code) . "', " . $id . ", 'EmailChange', '" . $newemail . 
				"', '" . DateToMySQL($expire) . "')");
			
			// Send email.
			$to = $name . " <" . $email . ">";
			$subject = "Capricon Registration Email changes Request";
			$message = "Hello! A request to changes the email for your Capricon Registration account to " . $newemail .
				" was just received.\r\n\r\nIf this was requested by you, please click on the following " .
				"link to confirm the changes: http://registration.capricon.org/changeemail.php?id=" . $code .
				"\r\n\r\nIf this was not initiated by you, there is nothing you need to do; the above link " .
				"will expire after 12 hours.\r\n\r\nIf you have any questions, don't hesitate to contact " .
				"Phandemonium's IT Director at it@phandemonium.org.\r\n\r\nHave a great day!";
			$message = wordwrap($message, 70, "\r\n");
			$headers = "From: Capricon Registration <registration@capricon.org>\r\n" . 
				"X-Mailer: PHP/" . phpversion();
			mail($to, $subject, $message, $headers);
			
			$emailchanged = " An email has been sent to your current email address to confirm your request " .
				"to changes your email.";
		}
	}
	
	// Password changes
	if(isset($_POST['password']) && strlen($_POST['password']) > 0)
	{
		$pass = $_POST['password'];
		$hash = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 13));
		$db->query("UPDATE People SET Password = '" . $db->real_escape_string($hash) . "' WHERE PeopleID = $id");
	}

	// Personal Information
	$result = $db->query("SELECT FirstName, LastName, Address1, Address2, City, State, ZipCode, Country, " .
		"Phone1, Phone1Type, Phone2, Phone2Type, Email, BadgeName, HeardFrom FROM People WHERE PeopleID = $id");	
	$info = $result->fetch_array();
	$result->close();
	
	$changes = "";
	if($info['FirstName'] != $_POST['firstName'])
		$changes .= ", FirstName = '" . $db->real_escape_string($_POST['firstName']) . "'";
	if($info['LastName'] != $_POST['lastName'])
		$changes .= ", LastName = '" . $db->real_escape_string($_POST['lastName']) . "'";
	if($info['BadgeName'] != $_POST['badgeName'])
		$changes .= ", BadgeName = '" . $db->real_escape_string($_POST['badgeName']) . "'";
	if($info['Address1'] != $_POST['address1'])
		$changes .= ", Address1 = '" . $db->real_escape_string($_POST['address1']) . "'";
	if($info['Address2'] != $_POST['address2'])
		if($_POST['address2'] != '')
			$changes .= ", Address2 = '" . $db->real_escape_string($_POST['address2']) . "'";
		else
			$changes .= ", Address2 = NULL";
	if($info['City'] != $_POST['city'])
		$changes .= ", City = '" . $db->real_escape_string($_POST['city']) . "'";
	if($info['State'] != $_POST['state'])
		if($_POST['state'] != '')
			$changes .= ", State = '" . $db->real_escape_string($_POST['state']) . "'";
		else
			$changes .= ", State = NULL";
	if($info['ZipCode'] != $_POST['zip'])
		if($_POST['zip'] != '')
			$changes .= ", ZipCode = '" . $db->real_escape_string($_POST['zip']) . "'";
		else
			$changes .= ", ZipCode = NULL";
	if($info['Country'] != $_POST['country'])
		$changes .= ", Country = '" . $db->real_escape_string($_POST['country']) . "'";
	if($info['Phone1'] != $_POST['phone1'])
		if($_POST['phone1'] != '')
			$changes .= ", Phone1 = '" . $db->real_escape_string($_POST['phone1']) . "', Phone1Type = '" . 
				$db->real_escape_string($_POST['phone1type']) . "'";
		else
			$changes .= ", Phone1 = NULL, Phone1Type = NULL";
	if($info['Phone2'] != $_POST['phone2'])
		if($_POST['phone2'] != '')
			$changes .= ", Phone2 = '" . $db->real_escape_string($_POST['phone2']) . "', Phone2Type = '" . 
				$db->real_escape_string($_POST['phone2type']) . "'";
		else
			$changes .= ", Phone2 = NULL, Phone2Type = NULL";
	if($info['HeardFrom'] != $_POST['heardFrom'])
		$changes .= ", HeardFrom = '" . $db->real_escape_string($_POST['heardFrom']) . "'";
	
	if(strlen($changes) > 0)
		$db->query("UPDATE People SET " . substr($changes, 2) . " WHERE PeopleID = $id");
	
	// Permissions (for superadmins)
	if(isset($_POST['setPerms']) && DoesUserBelongHere("RegLead"))
	{
		$db->query("DELETE FROM Permissions WHERE PeopleID = $id");
		$result = $db->query("SELECT Permission FROM PermissionDetails");
		$permissions = array();
		while($row = $result->fetch_array())
			$permissions[] = $row["Permission"];
		$result->close();
		
		foreach($permissions as $permission)
			if(isset($_POST['perm' . $permission]))
			{
				$result = $db->query("SELECT ExpireAfterCon FROM PermissionDetails WHERE Permission = '$permission'");
				$row = $result->fetch_array();
				$result->close();
				// Certain positions expire on March 31st after the current convention year.
				if($row["ExpireAfterCon"] == 1)
				{
					$year = date("n") > 3 ? date("Y") + 1: date("Y");
					$db->query("INSERT INTO Permissions (PeopleID, Permission, Expiration) VALUES ($id, '" . 
						$permission . "', '$year-03-31')");
				}
				else
					$db->query("INSERT INTO Permissions (PeopleID, Permission) VALUES ($id, '" . $permission . "')");
			}
	}
	
	echo '{ "success": true, "authenticated": true, "emailInUse": false, "message": ' . 
	'"Your changes have been saved successfully.' . $emailchanged . '" }';
?>