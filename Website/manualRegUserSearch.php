<?php
	session_start();
	include_once('includes/functions.php');
	if(!DoesUserBelongHere("RegLead"))
		header('Location: index.php');
		
	$terms = $_POST["terms"];
	if($terms != '')
	{
		$sql = "SELECT PeopleID, FirstName, LastName, Address1, Address2, City, State, ZipCode, Country, Phone1, Phone1Type, Email, HeardFrom FROM People WHERE IsCharity = 0 AND ";
		if(strpos($terms, "@") === false)
		{
			if(strpos($terms, " ") === false)
			{
				$sql .= "LastName LIKE '" . $db->real_escape_string($terms) . "%' AND Email <> ''";
			}
			else
			{
				$nameTerms = explode(" ", $terms, 2);
				$sql .= "FirstName LIKE '" . $db->real_escape_string($nameTerms[0]) . "%' AND LastName LIKE '" . 
					$db->real_escape_string($nameTerms[1]) . "%' AND Email <> ''";
			}
		}
		else
		{
			$sql .= "Email = '" . $db->real_escape_string($terms) . "'";
		}

		$result = $db->query($sql);
		if($result->num_rows > 0)
		{
			if($result->num_rows == 1)
			{
				echo "<p><b>Member found!</b> Please review the address information below to ensure this is the right member " . 
					"before proceeding. If necessary, make corrections to the address before proceeding.</p>\r\n";
				$row = $result->fetch_array();
				$disabled = "disabled";
				$id = $row["PeopleID"];
				$fName = $row["FirstName"];
				$lName = $row["LastName"];
				$address1 = $row["Address1"];
				$address2 = $row["Address2"];
				$city = $row["City"];
				$state = $row["State"];
				$zip = $row["ZipCode"];
				$country = $row["Country"];
				$phone = $row["Phone1"];
				$phoneType = $row["Phone1Type"];
				$email = $row["Email"];
				$heard = $row["HeardFrom"];
				$makeAccount = "";
				$result->close();
			}
			else
			{
				echo "<p><b>More than one member found!</b> Please select the intended member, or \"Enter As New Registration\" if none " .
					"are a match.</p>\r\n";
				echo "<ul>\r\n";
				while($row = $result->fetch_array())
				{
					echo "<li><a href=\"#\" onclick=\"$('#searchTerm').val('" . $row["Email"] . "'); $('#checkForCurrentForm').submit();\">" . 
						$row["FirstName"] . " " . $row["LastName"] . " (" . $row["Email"] . ")</li>\r\n";
				}
				$result->close();
				echo "<li><a href=\"#\" onclick=\"$('#searchTerm').val(''); $('#checkForCurrentForm').submit();\">Enter as New Registration</li>\r\n";
				echo "</ul>\r\n";
				return;
			}
		}
		else
		{
			echo "<p>No records found that matched the above parameters. If you think this is in error, correct the " .
				"search terms and submit again. Otherwise, to continue with a new member fill in the fields below. " . 
				"If the attendee requested no account, please leave the Email field blank.</p>\r\n";
			$disabled = "";
			$id = "";
			$address1 = "";
			$address2 = "";
			$city = "";
			$state = "";
			$zip = "";
			$country = "";
			$phone = "";
			$phoneType = "Home";
			$heard = "";
			if(strpos($terms, "@") === false)
			{
				$fName = $nameTerms[0];
				$lName = $nameTerms[1];
				$email = "";
			}
			else
			{
				$fName = "";
				$lName = "";
				$email = $terms;
			}
		}
	}
	else
	{
		echo "<p>To enter a brand new registration, fill in the fields below. If the attendee requested no account, " . 
			"please leave the Email field blank.</p>\r\n";
		$disabled = "";
		$id = "";
		$address1 = "";
		$address2 = "";
		$city = "";
		$state = "";
		$zip = "";
		$country = "";
		$phone = "";
		$phoneType = "Home";
		$heard = "";
		if(strpos($terms, "@") === false)
		{
			$name = $nameTerms[0] . " " . $nameTerms[1];
			$email = "";
		}
		else
		{
			$fName = "";
			$lName = "";
			$email = $terms;
		}
	}
	echo "<form id=\"addressInfo\" method=\"post\">\r\n";
	echo "<input type=\"hidden\" id=\"peopleID\" name=\"peopleID\" value=\"$id\" />\r\n";
	echo "<label>First Name: <input type=\"text\" id=\"addFName\" name=\"addFName\" value=\"$fName\" style=\"width: 30%\" /></label> ";
	echo "<label>Last Name: <input type=\"text\" id=\"addLName\" name=\"addLName\" value=\"$lName\" style=\"width: 30%\" /></label><br />\r\n";
	echo "<label>Address (Line 1): <input type=\"text\" id=\"addAddress1\" name=\"addAddress1\" value=\"$address1\" style=\"width: 80%\" /></label><br />\r\n";
	echo "<label>Address (Line 2): <input type=\"text\" id=\"addAddress2\" name=\"addAddress2\" value=\"$address2\" style=\"width: 80%\" /></label><br />\r\n";
	echo "<label>City: <input type=\"text\" id=\"addCity\" name=\"addCity\" value=\"$city\" style=\"width: 25%\" /></label>\r\n";
	echo "<label style=\"margin-left: 10px;\">State: <input type=\"text\" id=\"addState\" name=\"addState\" value=\"$state\" style=\"width: 5%\" /></label>\r\n";
	echo "<label style=\"margin-left: 10px;\">Zip Code: <input type=\"text\" id=\"addZip\" name=\"addZip\" value=\"$zip\" style=\"width: 10%\" /></label>\r\n";
	echo "<label style=\"margin-left: 10px;\">Country: <input type=\"text\" id=\"addCountry\" name=\"addCountry\" value=\"$country\" style=\"width: 15%\" /></label><br \>\r\n";
	echo "<label>Phone #: <input type=\"text\" id=\"addPhone\" name=\"addPhone\" value=\"$phone\" style=\"width: 20%\"  /></label>\r\n";
	echo '<select id="addPhoneType" name="addPhoneType" >' . "\r\n"; 
	echo '<option value="Home"' . ($phoneType == "Home" ? " selected" : "") . '>Home</option>' . "\r\n"; 
	echo '<option value="Mobile"' . ($phoneType == "Mobile" ? " selected" : "") . '>Mobile</option>' . "\r\n"; 
	echo '<option value="Work"' . ($phoneType == "Work" ? " selected" : "") . '>Work</option>' . "\r\n"; 
	echo '<option value="Other"' . ($phoneType == "Other" ? " selected" : "") . '>Other</option></select>' . "\r\n";
	echo "<label style=\"margin-left: 30px;\">Email: <input type=\"text\" id=\"email\" name=\"email\" value=\"$email\" style=\"width: 30%\" placeholder=\"Leave blank to not create an account.\" /></label><br />\r\n";
	echo "<label>How They Heard About Us: <input type=\"text\" id=\"addHeard\" name=\"addHeard\" value=\"$heard\" style=\"width: 65%\" /></label><br />\r\n";
	echo "<label><input type=\"submit\" id=\"addGood\" name=\"addGood\" onclick=\"$('#searchSubmit').attr('readonly', true); $('#enterReg').show(); $('#addGood').attr('readonly', true); return false;\" value=\"The Information Listed is Correct\" />\r\n";
	echo "</form>\r\n";
?>