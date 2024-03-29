<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("Program"))
		header('Location: index.php');

	$year = isset($_GET["year"]) ? $_GET["year"] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
	$conYear = $year - 1980;

	if(isset($_GET["startDate"]))
	{
		if(($startDate = strtotime($_GET["startDate"])) === false)
		{
			echo "Invalid start date provided.";
			return;
		}
		$range = "AND s.Created >= '" . date("Y-m-d", strtotime($_GET["startDate"])) . " 00:00:00'";
	}
	else
		$range = "";

	if($_GET["downloadType"] == "EmailOnly")
	{
		$fields = array('PeopleID', 'Email');
		$sql = "SELECT p.PeopleID, p.Email FROM ProgramSurvey s JOIN People p ON s.PeopleID = p.PeopleID WHERE s.Year = $year $range";
	}
	elseif($_GET["downloadType"] == "EmailAndNames")
	{
		$fields = array('PeopleID', 'First Name', 'Last Name', 'Email');
		$sql = "SELECT p.PeopleID, p.FirstName, p.LastName, p.Email FROM ProgramSurvey s JOIN People p ON s.PeopleID = p.PeopleID " .
			"WHERE s.Year = $year $range ORDER BY p.LastName";
	}
	elseif($_GET["downloadType"] == "AllResponses")
	{
		if(isset($_GET["preferredOnly"]))
		{
			$contactQuery = "CASE WHEN s.PreferredContact = 'Email' THEN p.Email WHEN s.PreferredContact = 'Home' THEN CASE WHEN " .
				"p.Phone1Type = 'Home' THEN p.Phone1 ELSE p.Phone2 END WHEN s.PreferredContact = 'Mobile' THEN CASE WHEN " .
				"p.Phone1Type = 'Mobile' THEN p.Phone1 ELSE p.Phone2 END WHEN s.PreferredContact = 'Mail' THEN " .
				"CONCAT(p.Address1, ', ', p.Address2, ', ', p.City, ', ', p.State, ' ', p.ZipCode, ' ', p.Country) END AS Contact";
			$fields = array('PeopleID', 'First Name', 'Last Name', 'Preferred Contact', 'Contact', 'Website', 'Biography', 'Day Job', 'Expertise', 'Ethnicity', 
                'Gender', 'Age', 'Orientation', 'Interests', 'Autographs', 'Young Adult', 'Children\'s', 'Program Ideas', 'Program Idea Title', 
                'Program Idea Panelists', 'Overdone Programs', 'Accessibility', 'Created');
		}
		else
		{
			$contactQuery = "p.Email, CASE WHEN p.Phone1Type = 'Home' THEN p.Phone1 WHEN p.Phone2Type = 'Home' THEN p.Phone2 ELSE '' " .
				"END AS HomePhone, CASE WHEN p.Phone1Type = 'Mobile' THEN p.Phone1 WHEN p.Phone2Type = 'Mobile' THEN p.Phone2 ELSE '' " .
				"END AS MobilePhone, CONCAT(p.Address1, ', ', p.Address2, ', ', p.City, ', ', p.State, ' ', p.ZipCode, ' ', p.Country) " .
				"AS Address";
			$fields = array('PeopleID', 'First Name', 'Last Name', 'Preferred Contact', 'Email', 'Home Phone', 'Mobile Phone', 'Address', 'Website',
				'Biography', 'Day Job', 'Expertise', 'Ethnicity', 'Gender', 'Age', 'Orientation', 'Interests', 'Autographs', 'Young Adult', 'Children\'s', 
                'Program Ideas', 'Program Idea Title', 'Program Idea Panelists', 'Overdone Programs', 'Accessibility', 'Created');
		}
		$sql = "SELECT p.PeopleID, p.FirstName, p.LastName, s.PreferredContact, $contactQuery, s.Website, s.Biography, s.DayJob, s.ExpertiseText, Ethnicity, Gender, Age, Orientation, " .
			"InterestsText, CASE WHEN WillingAutograph = 1 THEN 'Yes' ELSE 'No' END AS WillingAutograph, CASE WHEN WillingYa = 1 THEN 'Yes' ELSE 'No' END AS WillingYa, " .
			"CASE WHEN WillingKids = 1 THEN 'Yes' ELSE 'No' END AS WillingKids, ProgramIdeas, ProgramIdeaTitle, ProgramIdeaPanelists, OverdonePrograms, Accessibility, " .
			"Created FROM ProgramSurvey s JOIN People p ON " .
			"s.PeopleID = p.PeopleID WHERE s.Year = $year $range ORDER BY p.LastName";
	}
	else
	{
		echo "Invalid request received.";
		return;
	}

	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=Capricon' . $conYear . 'ProgramSurveys' . date("Ymd") . '.csv');
	$output = fopen('php://output', 'w');
	fputcsv($output, $fields);

	$result = $db->query($sql);
	while($row = $result->fetch_array(MYSQLI_ASSOC))
	{
		foreach($row as &$record)
			$record = str_replace(array("\r\n", "\r", "\n"), ' ', $record);
		unset($record);
		fputcsv($output, $row);
	}
?>
