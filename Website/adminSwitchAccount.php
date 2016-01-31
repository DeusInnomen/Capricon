<?php
	session_start();
	include_once('includes/functions.php');
	if(!DoesUserBelongHere("SuperAdmin"))
		header('Location: /index.php');
	elseif(!isset($_GET["id"]))
		header('Location: /manageAllAccounts.php');

	$result = $db->query("SELECT FirstName, LastName, BadgeName, Email FROM People WHERE PeopleID = " . $_GET["id"]);
	if($result->num_rows > 0)
	{
		$row = $result->fetch_array();
		$name = trim($row['FirstName'] . " " . $row['LastName']);
		$badge = trim($row['BadgeName']);
		$email = trim($row['Email']);
		$result->close();
		
		$_SESSION['PeopleID'] = $_GET["id"];
		$_SESSION['FullName'] = $name;
		$_SESSION['BadgeName'] = $badge;
		$_SESSION['Email'] = $email;
		header('Location: /index.php');
	}
	else
		header('Location: /manageAllAccounts.php');
?>