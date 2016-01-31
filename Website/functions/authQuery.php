<?php
	session_start();
	if($_SERVER["HTTPS"] != "on") exit();

	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/includes/functions.php");

	// I may handle more security after Cap 34 by putting in an authentication token to verify the usage of the token comes from
	// the same address that logged in.
	
	if(empty($_POST["action"])) exit();
	
	$action = $db->real_escape_string($_POST["action"]);
	$app = $db->real_escape_string($_POST["app"]);

	if($action == "Login")
	{
		$email = $db->real_escape_string($_POST["email"]);
		$pass = $db->real_escape_string($_POST["pass"]);
		$response = array();
		
		$result = $db->query("SELECT PeopleID, Password FROM People WHERE Email = '$email'");
		if($result->num_rows == 0)
		{
			$response["success"] = false;
			$response["message"] = "Invalid username or password.";
			header("Content-type: application/json");
			echo json_encode($response);
			exit();
		}
		$row = $result->fetch_array();
		$hash = $row["Password"];
		$id = $row["PeopleID"];
		$result->close();
		
		if(!password_verify($pass, $hash))
		{
			$response["success"] = false;
			$response["message"] = "Invalid username or password.";
			header("Content-type: application/json");
			echo json_encode($response);
			exit();
		}
		
		$perms = UserPermissions($id);
		$hasPermission = false;
		if($app = "Registration")
			$hasPermission = (in_array("regstaff", $perms) || !in_array("reglead", $perms) || !in_array("ops", $perms) || !in_array("superadmin", $perms));
		if($app = "ArtShow")
			$hasPermission = (in_array("artshowstaff", $perms) || !in_array("artshowlead", $perms) || !in_array("superadmin", $perms));
			
		if($hasPermission)
		{
			// Handle any token system here.
			$response["success"] = true;
			$response["message"] = "Logon successful.";
			header("Content-type: application/json");
			echo json_encode($response);
			exit();
		}
		else
		{
			$response["success"] = false;
			$response["message"] = "You do not have permission to use this application.";
			header("Content-type: application/json");
			echo json_encode($response);
			exit();
		}
	}
	
?>