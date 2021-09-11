<?php
	session_start();
	include_once('includes/functions.php');
	if(isset($_POST['id']) && isset($_POST['password']))
	{
		DoCleanup();
		
		$data = $db->real_escape_string($_POST['id']);
		$result = $db->query("SELECT PeopleID FROM ConfirmationLinks WHERE Code = '$data' AND Type = 'PWReset'");
		if($result->num_rows > 0)
		{
			$row = $result->fetch_array();			
			$id = $row["PeopleID"];			
			$result->close();
			
			$pass = $_POST['password'];
			$hash = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 13));
			$db->query("UPDATE People SET Password = '$hash' WHERE PeopleID = " . $id);
			$db->query("DELETE FROM ConfirmationLinks WHERE Code = '$data'");
			
			// Destroy session information, forcing them to log out.
			$_SESSION = array();
			session_destroy();

			echo '{ "success": true }';
			return;
		}
		
		$result = $db->query("SELECT PeopleID, Data FROM ConfirmationLinks WHERE Code = '$data' AND Type = 'Promote'");
		if($result->num_rows > 0)
		{
			$row = $result->fetch_array();			
			$id = $row["PeopleID"];		
			$email = $row["Data"];
			$result->close();
			
			$pass = $_POST['password'];
			$hash = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 13));
			$db->query("UPDATE People SET Password = '$hash', Email = '$email', ParentID = NULL WHERE PeopleID = " . $id);
			$db->query("DELETE FROM ConfirmationLinks WHERE Code = '$data'");
			
			// Destroy session information, forcing them to log out.
			$_SESSION = array();
			session_destroy();

			echo '{ "success": true }';
			return;
		}
		else
			echo '{ "success": false }';
	}
	else
		echo '{ "success": false }';
?>