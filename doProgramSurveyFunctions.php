<?php
	session_start();
	include_once('includes/functions.php');

	DoCleanup();

	if(!isset($_SESSION['PeopleID']))
	{
		echo '{ "success": false, "message": "No user is logged in." }';
		return;
	}

	$task = $_POST["task"];
	//$year = date("n") >= 3 ? date("Y") + 1: date("Y");
	$year = date("Y") + 1;

	$result = $db->query("SELECT SurveyID FROM ProgramSurvey WHERE Year = $year AND PeopleID = " . $_SESSION["PeopleID"]);
	if($result->num_rows > 0)
	{
		$info = $result->fetch_array();
		$surveyID = $info["SurveyID"];
		$result->close();
	}
	elseif($task == "Transfer")
	{
		$sql = "INSERT INTO ProgramSurvey (PeopleID, Year, PreferredContact, Website, Biography, DayJob, Expertise, ExpertiseText, Ethnicity, Gender, Age, Orientation, Arrival, Departure, MaxPanelsTh, PanelStartTh, PanelEndTh, MaxPanelsFr, " .
                "PanelStartFr, PanelEndFr, MaxPanelsSa, PanelStartSa, PanelEndSa, MaxPanelsSu, PanelStartSu, PanelEndSu, AvailabilityNotes, Interests, InterestsText, WillingAutograph, WillingReading, WillingYA, WillingKids, ProgramIdeas, " .
                "ProgramIdeaTitle, ProgramIdeaPanelists, OverdonePrograms, PanelistToAvoid, Accessibility, AdditionalInfo, CanShareInfo, Created) " .
                "SELECT PeopleID, $year, PreferredContact, Website, Biography, DayJob, Expertise, ExpertiseText, Ethnicity, Gender, Age, Orientation, Arrival, Departure, MaxPanelsTh, PanelStartTh, PanelEndTh, MaxPanelsFr, PanelStartFr, PanelEndFr, " .
                "MaxPanelsSa, PanelStartSa, PanelEndSa, MaxPanelsSu, PanelStartSu, PanelEndSu, AvailabilityNotes, Interests, InterestsText, WillingAutograph, WillingReading, WillingYA, WillingKids, ProgramIdeas, " .
                "ProgramIdeaTitle, ProgramIdeaPanelists, OverdonePrograms, PanelistToAvoid, Accessibility, AdditionalInfo, CanShareInfo, NOW() FROM ProgramSurvey " .
                "WHERE PeopleID = " . $_SESSION["PeopleID"] . " ORDER BY Created DESC LIMIT 1";
		$db->query($sql);
		echo '{ "success": true, "doRedirect": true, "message": "' . $sql . '" }';
		return;
	}
	else
	{
		$sql = "INSERT INTO ProgramSurvey (PeopleID, Year, Created) VALUES (" . $_SESSION["PeopleID"] . ", $year, NOW())";
		$db->query($sql);
		$surveyID = $db->insert_id;

		$sql = "SELECT Interest FROM PeopleInterests WHERE PeopleID = " . $_SESSION["PeopleID"] . " AND Interest = 'Program'";
		$result = $db->query($sql);
		if($result->num_rows == 0)
		{
			$sql = "INSERT INTO PeopleInterests (PeopleID, Interest) VALUES (" . $_SESSION["PeopleID"] . ", 'Program')";
			$db->query($sql);
		}
		else
			$result->close();
	}

	if($task == "Page1")
	{
		$valid = true;
		$contact = $db->real_escape_string($_POST["preferredContact"]);
		if($contact == "Home")
		{
			$result = $db->query("SELECT IFNULL(Phone1, '') AS Phone1, IFNULL(Phone1Type, 'Other') AS Phone1Type, " .
				"IFNULL(Phone2, '') AS Phone2, IFNULL(Phone2Type, 'Other') AS Phone2Type FROM People WHERE PeopleID = " .
				$_SESSION["PeopleID"]);
			$row = $result->fetch_array();
			$result->close();
			if($row["Phone1Type"] != "Home" && $row["Phone2Type"] != "Home")
				$valid = false;
			elseif($row["Phone1Type"] == "Home" && $row["Phone1"] == '')
				$valid = false;
			elseif($row["Phone2Type"] == "Home" && $row["Phone2"] == '')
				$valid = false;
			if(!$valid)
				echo '{ "success": false, "doRedirect": false, "message": "You do not have a Home phone set." }';
		}
		elseif($contact == "Mobile")
		{
			$result = $db->query("SELECT IFNULL(Phone1, '') AS Phone1, IFNULL(Phone1Type, 'Other') AS Phone1Type, " .
				"IFNULL(Phone2, '') AS Phone2, IFNULL(Phone2Type, 'Other') AS Phone2Type FROM People WHERE PeopleID = " .
				$_SESSION["PeopleID"]);
			$row = $result->fetch_array();
			$result->close();
			if($row["Phone1Type"] != "Mobile" && $row["Phone2Type"] != "Mobile")
				$valid = false;
			elseif($row["Phone1Type"] == "Mobile" && $row["Phone1"] == '')
				$valid = false;
			elseif($row["Phone2Type"] == "Mobile" && $row["Phone2"] == '')
				$valid = false;
			if(!$valid)
				echo '{ "success": false, "doRedirect": false, "message": "You do not have a Mobile phone set." }';
		}

		if($valid)
		{
			$sql = "UPDATE ProgramSurvey SET Website = '" . $db->real_escape_string($_POST["website"]) . "', Biography = '" .
				$db->real_escape_string($_POST["biography"]) . "', DayJob = '" . $db->real_escape_string($_POST["dayjob"]) .
				"', Expertise = '" . $db->real_escape_string($_POST["expertiseValues"]) . "', ExpertiseText = '" .
				$db->real_escape_string($_POST["expertiseText"]) . "', PreferredContact = '$contact', Accessibility = '" .
			    $db->real_escape_string($_POST["accessibility"]) . "', Ethnicity = '" . $db->real_escape_string($_POST["ethnicity"]) .
                "', Gender = '" . $db->real_escape_string($_POST["gender"]) . "', Age = '" . $db->real_escape_string($_POST["age"]) . 
                "', Orientation = '" . $db->real_escape_string($_POST["orientation"]) . "' WHERE SurveyID = $surveyID";
			$db->query($sql);
			echo '{ "success": true, "doRedirect": ' . (!empty($_POST["continue"]) ? 'true' : 'false') .
				', "message": "Your survey answers have been updated successfully." }';
		}

	}
	/*elseif($task == "Page2")
	{
		$sql = "UPDATE ProgramSurvey SET Arrival = '" . $db->real_escape_string($_POST["arriveDate"]) . " " .
			$db->real_escape_string($_POST["arriveTime"]) . "', Departure = '" . $db->real_escape_string($_POST["departDate"]) .
			" " . $db->real_escape_string($_POST["departTime"]) .
			"', MaxPanelsTh = " . $db->real_escape_string($_POST["maxPanelsTh"]) .
			", PanelStartTh = " . $db->real_escape_string($_POST["panelStartTh"]) .
			", PanelEndTh = " . $db->real_escape_string($_POST["panelEndTh"]) .
			", MaxPanelsFr = " . $db->real_escape_string($_POST["maxPanelsFr"]) .
			", PanelStartFr = " . $db->real_escape_string($_POST["panelStartFr"]) .
			", PanelEndFr = " . $db->real_escape_string($_POST["panelEndFr"]) .
			", MaxPanelsSa = " . $db->real_escape_string($_POST["maxPanelsSa"]) .
			", PanelStartSa = " . $db->real_escape_string($_POST["panelStartSa"]) .
			", PanelEndSa = " . $db->real_escape_string($_POST["panelEndSa"]) .
			", MaxPanelsSu = " . $db->real_escape_string($_POST["maxPanelsSu"]) .
			", PanelStartSu = " . $db->real_escape_string($_POST["panelStartSu"]) .
			", PanelEndSu = " . $db->real_escape_string($_POST["panelEndSu"]) .
			", AvailabilityNotes = '" . $db->real_escape_string($_POST["availabilityNotes"]) . "' WHERE SurveyID = $surveyID";
		$db->query($sql);
		echo '{ "success": true, "doRedirect": ' . (!empty($_POST["continue"]) ? 'true' : 'false') .
			', "message": "Your survey answers have been updated successfully." }';
	}*/
	elseif($task == "Page2") // Former Page3
	{
		$sql = "UPDATE ProgramSurvey SET Interests = '" . $db->real_escape_string($_POST["interestValues"]) . "', InterestsText = '" .
			$db->real_escape_string($_POST["interestText"]) . "', WillingAutograph = " . (isset($_POST["willingAutograph"]) ? "1" : "0") .
			", WillingReading = " . (isset($_POST["willingReading"]) ? "1" : "0") . ", WillingYA = " .
			(isset($_POST["willingYA"]) ? "1" : "0") . ", WillingKids = " . (isset($_POST["willingKids"]) ? "1" : "0") .
			", ProgramIdeas = '" . $db->real_escape_string($_POST["programIdeas"]) . "', ProgramIdeaTitle = '" .
			$db->real_escape_string($_POST["programIdeaTitle"]) . "', ProgramIdeaPanelists = '" .
			$db->real_escape_string($_POST["programIdeaPanelists"]) . "', OverdonePrograms = '" .
			$db->real_escape_string($_POST["overdonePrograms"]) . "' WHERE SurveyID = $surveyID";
		$db->query($sql);
		echo '{ "success": true, "doRedirect": ' . (!empty($_POST["continue"]) ? 'true' : 'false') .
			', "message": "Your survey answers have been updated successfully." }';
	}
	/*elseif($task == "Page4")
	{
		$sql = "UPDATE ProgramSurvey SET PanelistToAvoid = '" . $db->real_escape_string($_POST["panelistToAvoid"]) . "', Accessibility = '" .
			$db->real_escape_string($_POST["accessibility"]) . "', AdditionalInfo = '" . $db->real_escape_string($_POST["additionalInfo"]) .
			"', CanShareInfo = " . (isset($_POST["canShareInfo"]) ? "1" : "0") . " WHERE SurveyID = $surveyID";
		$db->query($sql);
		echo '{ "success": true, "doRedirect": ' . (!empty($_POST["continue"]) ? 'true' : 'false') .
			', "message": "Your survey answers have been updated successfully." }';
	}*/
	elseif($task != "Transfer")
		echo '{ "success": false, "doRedirect": false, "message": "Invalid request." }';

?>