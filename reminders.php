<?php
	session_start();
	include_once('includes/functions.php');

	// Maintenance: Delete any expired links.
	$db->query("DELETE FROM ConfirmationLinks WHERE Expires IS NOT NULL AND Expires < NOW()");
	$db->query("DELETE FROM PendingAccounts WHERE Expires < NOW()");

	if(isset($_POST['type']) && $_POST['type'] == 'Password')
	{
		if(isset($_POST['email']))
		{
			if(SendPasswordReset($_POST['email']))
				echo '{ "success": true }';
			else
				echo '{ "success": false }';
		}
		elseif(isset($_POST['id']))
		{
			// See if this is a real ID for this type of reminder.
			$result = $db->query("SELECT PeopleID FROM ConfirmationLinks WHERE Code = '" .
				$db->real_escape_string($_POST['id']) . "' AND Type IN ('PWReset', 'Promote')");
			if($result->num_rows > 0)
			{
				$result->close();
				echo '{ "success": true }';
			}
			else
				echo '{ "success": false }';
		}
		else
			echo '{ "success": false }';
	}
	else
		echo '{ "success": false }';
?>