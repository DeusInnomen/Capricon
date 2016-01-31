<?php
	session_start();
	include_once('includes/functions.php');
	
	if(!isset($_SESSION['PeopleID']))
	{
		echo '{ "success": false, "doRedirect": false, "message": "No user is logged in." }';
		return;
	}
	
	$result = $db->query("SELECT ArtistID FROM ArtistDetails WHERE PeopleID = " . $_SESSION["PeopleID"]);
	if($result->num_rows > 0)
	{
		$row = $result->fetch_array();
		$artistID = $row["ArtistID"];
		$result->close();
	}
	else
		$artistID = "";
		
	if($artistID == "")
	{
		$db->query("INSERT INTO ArtistDetails (PeopleID, DisplayName, LegalName, IsPro, IsEAP, CanPhoto, Website, ArtType, Notes) " .
			"VALUES (" . $_SESSION["PeopleID"] . ", '" . $db->real_escape_string($_POST["displayName"]) . "', '" .
			$db->real_escape_string($_POST["legalName"]) . "', " . (isset($_POST["isPro"]) ? "1" : "0") . ", " . 
			(isset($_POST["isEAP"]) ? "1" : "0") . ", " . (isset($_POST["canPhoto"]) ? "1" : "0") . ", '" . 
			$db->real_escape_string($_POST["website"]) . "', '" . $db->real_escape_string($_POST["artType"]) . "', '" . 
			$db->real_escape_string($_POST["notes"]) . "')");
	}
	else
	{
		$db->query("UPDATE ArtistDetails SET " . 
			"DisplayName = '" . $db->real_escape_string($_POST["displayName"]) . "', " .
			"LegalName = '" . $db->real_escape_string($_POST["legalName"]) . "', " .
			"IsPro = " . (isset($_POST["isPro"]) ? "1" : "0") . ", " .
			"IsEAP = " . (isset($_POST["isEAP"]) ? "1" : "0") . ", " .
			"CanPhoto = " . (isset($_POST["canPhoto"]) ? "1" : "0") . ", " .
			"Website = '" . $db->real_escape_string($_POST["website"]) . "', " .
			"ArtType = '" . $db->real_escape_string($_POST["artType"]) . "', " .
			"Notes = '" . $db->real_escape_string($_POST["notes"]) . "' WHERE PeopleID = " . $_SESSION["PeopleID"]);			
	}
	echo '{ "success": true, "doRedirect": ' . (!empty($_POST["continue"]) ? 'true' : 'false') . 
		', "message": "Your details have been updated successfully." }';	
?>
