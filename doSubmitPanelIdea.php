<?php
	session_start();
	include_once('includes/functions.php');
	
	if(isset($_POST["id"]))
	{
		$id = $_SESSION['PeopleID'];
		$name = $db->real_escape_string($_SESSION['FullName']);
		$email = $db->real_escape_string($_SESSION['Email']);
	}
	else
	{
		$id = "NULL";
		$name = $db->real_escape_string($_POST["name"]);
		$email = $db->real_escape_string($_POST["email"]);
	}
	$title = $db->real_escape_string($_POST["title"]);
	$desc = $db->real_escape_string($_POST["description"]);
	$participants = $db->real_escape_string($_POST["participants"]);
	$contact = isset($_POST["canContact"]) ? 1 : 0;
	$year = isset($_POST["year"]) ? $_POST["year"] : (date("Y") + 1);

	$sql = "INSERT INTO PanelIdeas (PeopleID, Year, Name, Email, Title, Description, Participants, CanContact, Created) VALUES ($id, $year, " .
		"'$name', '$email', '$title', '$desc', '$participants', $contact, NOW())";
	
	if($db->query($sql))
		echo '{ "success": true, "message": "Your panel idea has been submitted. Thank you!" }';
	else
		echo '{ "success": false, "message": "An error occurred saving your panel idea. Please try again later." }';
?>