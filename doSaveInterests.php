<?php
	session_start();
	include_once('includes/functions.php');

	if(!isset($_SESSION['PeopleID']))
	{
		echo '{ "success": false, "message": "No user is logged in." }';
		return;
	}

	$id = $_SESSION['PeopleID'];

	$db->query("DELETE FROM PeopleInterests WHERE PeopleID = $id");
	$query = '';
	if(isset($_POST['intGophers']))	$query .= ", ($id, 'Gophers')";
	if(isset($_POST['intProgram']))	$query .= ", ($id, 'Program')";
	if(isset($_POST['intDealer']))	$query .= ", ($id, 'Dealer')";
	if(isset($_POST['intAds']))		$query .= ", ($id, 'Ads')";
	if(isset($_POST['intAnime']))	$query .= ", ($id, 'Anime')";
	if(isset($_POST['intArtShow']))	$query .= ", ($id, 'ArtShow')";
	if(isset($_POST['intFilms']))	$query .= ", ($id, 'Films')";
	if(isset($_POST['intGaming']))	$query .= ", ($id, 'Gaming')";
	if(isset($_POST['intParties']))	$query .= ", ($id, 'Parties')";
	if(isset($_POST['intEvents']))	$query .= ", ($id, 'Events')";
	if(strlen($query) > 0)
		$db->query("INSERT INTO PeopleInterests (PeopleID, Interest) VALUES " . substr($query, 2));

	if(isset($_POST['intArtShow']))
	{
		$result = $db->query("SELECT PermissionID FROM Permissions WHERE PeopleID = $id AND Permission = 'Artist'");
		if($result->num_rows == 0)
			$db->query("INSERT INTO Permissions (PeopleID, Permission) VALUES ($id, 'Artist')");
        else
	        $result->close();
    }
    else
        $db->query("DELETE FROM Permissions WHERE PeopleID = $id AND Permission = 'Artist'");

    if(isset($_POST['intDealer']))
	{
		$result = $db->query("SELECT PermissionID FROM Permissions WHERE PeopleID = $id AND Permission = 'Dealer'");
		if($result->num_rows == 0)
            $db->query("INSERT INTO Permissions (PeopleID, Permission) VALUES ($id, 'Dealer')");
        else
	        $result->close();
    }
    else
        $db->query("DELETE FROM Permissions WHERE PeopleID = $id AND Permission = 'Dealer'");

	echo '{ "success": true, "message": "Your interests have been saved." }';
?>
