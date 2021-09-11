<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("Program"))
		header('Location: index.php');

	$year = isset($_GET["year"]) ? $_GET["year"] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
	$conYear = $year - 1980;
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=Capricon' . $conYear . 'PanelIdeas' . date("Ymd") . '.csv');

	$output = fopen('php://output', 'w');
	fputcsv($output, array('Name', 'Email', 'Title', 'Description', 'Participants', 'Can Contact', 'Submitted'));
	
	$result = $db->query("SELECT Name, Email, Title, Description, Participants, CanContact, Created FROM PanelIdeas WHERE Year = $year");
	while($row = $result->fetch_array(MYSQLI_ASSOC))
		fputcsv($output, $row);
?>
