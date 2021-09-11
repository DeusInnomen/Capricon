<?php
	session_start();
	include_once('includes/functions.php');	
	
	DoCleanup();

	if(isset($_POST['email']) && isset($_POST['password']))
	{
		if(AuthenticateUser($_POST['email'], $_POST['password']))
		{
			echo '{ "success": true, "message": "Logged in as ' . addslashes($_SESSION['FullName']) . ' (' . addslashes($_SESSION['BadgeName']) . '). Redirecting shortly..." }';
		}
		else
		{
			echo '{ "success": false, "message": "Unknown email or invalid password provided." }';
		}
	}

?>