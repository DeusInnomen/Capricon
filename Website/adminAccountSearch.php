<?php
	session_start();
	include_once('includes/functions.php');
	if(!DoesUserBelongHere("RegLead"))
		header('Location: index.php');

	$sql = "SELECT PeopleID, FirstName, LastName, BadgeName, Email FROM People ";
	$where = "WHERE IsCharity = 0 AND Email != '' ";
	$order = $_POST["sort"];
	if(isset($_POST["email"]) && strlen($_POST["email"]) > 0)
		$where .= "AND Email LIKE '" . $db->real_escape_string($_POST["email"]) . "%' ";

	if(isset($_POST["lastname"]) && strlen($_POST["lastname"]) > 0)
		$where .= "AND LastName LIKE '" . $db->real_escape_string($_POST["lastname"]) . "%' ";

	if(isset($_POST["badgename"]) && strlen($_POST["badgename"]) > 0)
		$where .= "AND BadgeName LIKE '" . $db->real_escape_string($_POST["badgename"]) . "%' ";

	if(strlen($order) == 0) $order = "  LastName, FirstName";
	$sql .= $where . "ORDER BY $order";
	
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		echo "<p>" . $result->num_rows . " result" . ($result->num_rows == 1 ? "" : "s") . " found. Click on a name below to manage their account.</p>\r\n";
		echo "<div id=\"results\">\r\n";
		while($row = $result->fetch_array())
		{
			echo "<label>";
			if(DoesUserBelongHere("SuperAdmin")) echo "<input type=\"checkbox\" name=\"id\" value=\"" . $row["PeopleID"] . "\" />";
			echo $row["FirstName"] . " " . $row["LastName"] . " (" . $row["BadgeName"] . ")&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . 
				$row["Email"] . "</label><span style=\"float: right;\"><a href=\"manageAccountAdmin.php?id=" . $row["PeopleID"] . 
				"\">Edit Account</a></span><br />\r\n";
		}
		$result->close();
		echo "</div>\r\n";
	}
	else
		echo "<p class=\"requiredField\">No records found that matched the above parameters.</p>\r\n";
?>