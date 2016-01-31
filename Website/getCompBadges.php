<?php
	session_start();
	include_once('includes/functions.php');
	if(!DoesUserBelongHere("RegStaff"))
		header('Location: /index.php');
		
	$year = date("n") >= 3 ? date("Y") + 1: date("Y");
	$sql = "SELECT p.PeopleID, p.FirstName, p.LastName, p.BadgeName, p.Email, CASE WHEN pb.BadgeID IS NOT NULL THEN 1 ELSE 0 END AS HasBadge FROM People p LEFT OUTER JOIN PurchasedBadges pb ON pb.PeopleID = p.PeopleID AND pb.Year = $year ";
		
	$where = "";
	$order = $_POST["sort"];
	if(isset($_POST["email"]) && strlen($_POST["email"]) > 0)
		$where .= "Email LIKE '" . $db->real_escape_string($_POST["email"]) . "%' AND ";

	if(isset($_POST["lastname"]) && strlen($_POST["lastname"]) > 0)
		$where .= "LastName LIKE '" . $db->real_escape_string($_POST["lastname"]) . "%' AND ";

	if(isset($_POST["badgename"]) && strlen($_POST["badgename"]) > 0)
		$where .= "BadgeName LIKE '" . $db->real_escape_string($_POST["badgename"]) . "%' AND ";

	if(strlen($where) > 0) $where = "WHERE " . substr($where, 0, -5) . " ";	
	if(strlen($order) == 0) $order = " LastName, FirstName";
	$sql .= $where . "ORDER BY $order";
	
	$isStaff = isset($_POST["isStaff"]) && $_POST["isStaff"] == 1;
	$isTransfer = isset($_POST["isTransfer"]) && $_POST["isTransfer"] == 1;
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		echo "<p>" . $result->num_rows . " result" . ($result->num_rows == 1 ? "" : "s") . " found. Mark the name you wish to " . ($isTransfer ? "transfer this badge" : "issue a badge") . " to. People with a red 'x' next to their name already have a badge in the system.</p>\r\n";
		echo "<div id=\"results\">\r\n";
		while($row = $result->fetch_array())
		{
			echo "<label>";
			if($row["HasBadge"] == "1")
				echo "<span class=\"requiredField\">&nbsp;x&nbsp;</span>";
			else
				echo "<input type=\"radio\" name=\"id\" value=\"" . $row["PeopleID"] . "\" class=\"people\" />";
			echo $row["FirstName"] . " " . $row["LastName"] . " (" . $row["BadgeName"] . ")&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . 
				$row["Email"] . "</label><br />\r\n";
		}
		$result->close();
		echo "</div>\r\n";
	}
	else
		echo "<p class=\"requiredField\">No records found that matched the above parameters.</p>\r\n";
	echo "<p>If you wish to " . ($isTransfer ? "transfer this badge" : "issue a badge") . " to a non-account holder instead, fill in the following form:</p>\r\n";
	echo "First Name: <input type=\"text\" id=\"firstName\" name=\"firstName\" style=\"width: 150px; margin-right: 30px;\">\r\n";
	echo "Last Name: <input type=\"text\" id=\"lastName\" name=\"lastName\" style=\"width: 150px; margin-right: 30px;\">\r\n";
	echo "Badge Name: <input type=\"text\" id=\"badgeName\" name=\"badgeName\" style=\"width: 150px;\" placeholder=\"Full Name If Blank\"><br>\r\n";
	if($isStaff)
	{
		echo "<p>After picking the search result, or filling in the name fields, enter the department to list then press Create Badge.</p>\r\n";
		echo "Department Name: <input type=\"text\" id=\"department\" name=\"department\" style=\"width: 250px;\">\r\n";
		echo "<input type=\"submit\" id=\"createBadge\" name=\"createBadge\" value=\"Create Badge\" disabled>\r\n";
	}
	elseif($isTransfer)
	{
		echo "<p>After picking the search result, or filling in the name fields, press Transfer Badge.</p>\r\n";
		echo "<input type=\"submit\" id=\"transferBadge\" name=\"transferBadge\" value=\"Transfer Badge\">\r\n";
	}
	else
	{
		echo "<p>After picking the search result, or filling in the name fields, enter the department to be billed then press Create Badge.</p>\r\n";
		echo "Department to Bill: <input type=\"text\" id=\"department\" name=\"department\" style=\"width: 250px;\">\r\n";
		echo "<input type=\"submit\" id=\"createBadge\" name=\"createBadge\" value=\"Create Badge\" disabled>\r\n";
	}
	

?>