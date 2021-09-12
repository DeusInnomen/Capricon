<?php
	session_start();
	include_once('includes/functions.php');
	

	if(!isset($_SESSION['PeopleID']))
	{
		echo '{ "success": false, "doRedirect": false, "message": "No user is logged in." }';
		return;
	}
	
	$result = $db->query("SELECT DealerID FROM DealerDetails WHERE PeopleID = " . $_SESSION["PeopleID"]);
	if($result->num_rows > 0)
	{
		$row = $result->fetch_array();
		$dealerID = $row["DealerID"];
		$result->close();
	}
	else
	{
		$dealerID = "";
	}

	$msg = 'There was an error.';
	
	$displayName = $db->real_escape_string($_POST["displayName"]);
	$legalName = $db->real_escape_string($_POST["legalName"]);
	$companyName = $db->real_escape_string($_POST["companyName"]);
	$address1 = $db->real_escape_string($_POST["address1"]);
	$address2 = $db->real_escape_string($_POST["address2"]);
	$city = $db->real_escape_string($_POST["city"]);
	$state = $db->real_escape_string($_POST["state"]);
	$zipCode = $db->real_escape_string($_POST["zipCode"]);
	$country = $db->real_escape_string($_POST["country"]);
	$workPhone = $db->real_escape_string($_POST["workPhone"]);
	$workEmail = $db->real_escape_string($_POST["workEmail"]);
	$ilTaxNum = $db->real_escape_string($_POST["ilTaxNum"]);
	$website = $db->real_escape_string($_POST["website"]);
	$merchType = $db->real_escape_string($_POST["merchType"]);
	$notes = $db->real_escape_string($_POST["notes"]);

	if($dealerID == "")
	{
		$sql = "INSERT INTO DealerDetails (PeopleID, DisplayName, LegalName, CompanyName, Address1, Address2, City, State, ZipCode, Country, WorkPhone, WorkEmail, ILTaxNum, Website, MerchType, Notes) VALUES (" . 
			$_SESSION["PeopleID"] . ", '" . 
			$displayName . "', '" .
			$legalName . "', '" . 
			$companyName . "', '" . 
			$address1 . "', '" . 
			$address2 . "', '" . 
			$city . "', '" . 
			$state . "', '" . 
			$zipCode . "', '" . 
			$country . "', '" . 
			$workPhone . "', '" . 
			$workEmail . "', '" . 
			$ilTaxNum . "', '" . 
			$website . "', '" . 
			$merchType . "', '" . 
			$notes . "')";
		$insert_row = $db->query($sql);
		if($insert_row){
			//$msg = 'Success! ID of last inserted record is : ' .$db->insert_id; 
			$msg = 'Success! Your record has been created.';
		}else{
			//die('Error : ('. $db->errno .') '. $db->error);
			//$msg = 'Error : ('. $db->errno .') '. $db->error;
			echo '{ "success": false, "doRedirect": false, "message": "There was an error saving the data. Please contact dealers@capricon.org with details." }';
			return;
		}
	}
	else
	{
		$sql = "UPDATE DealerDetails SET " . 
			"DisplayName = '" . $displayName . "', " .
			"LegalName = '" . $legalName . "', " .
			"CompanyName = '" . $companyName . "', " .
			"Address1 = '" . $address1 . "', " .
			"Address2 = '" . $address2 . "', " .
			"City = '" . $city . "', " .
			"State = '" . $state . "', " .
			"ZipCode = '" . $zipCode . "', " .
			"Country = '" . $country . "', " .
			"WorkPhone = '" . $workPhone . "', " .
			"WorkEmail = '" . $workEmail . "', " .
			"ILTaxNum = '" . $ilTaxNum . "', " .
			"Website = '" . $website . "', " .
			"MerchType = '" . $merchType . "', " .
			"Notes = '" . $notes . 
			"' WHERE PeopleID = " . $_SESSION["PeopleID"];
		$results = $db->query($sql);
		if($results){
			$msg = 'Your details have been updated successfully.';
		}else{
			//print 'Error : ('. $mysqli->errno .') '. $mysqli->error;
			echo '{ "success": false, "doRedirect": false, "message": "There was an error updating the data. Please contact dealers@capricon.org with details." }';
			return;
		}
	}

	echo '{ "success": true, "doRedirect": ' . (!empty($_POST["continue"]) ? 'true' : 'false') . ', "message": "Status: ' .$msg. '" }';		

?>
