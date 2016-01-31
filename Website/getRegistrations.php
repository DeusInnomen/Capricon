<?php
	session_start();
	include_once('includes/functions.php');
	if(!DoesUserBelongHere("RegStaff"))
		header('Location: /index.php');
	
	$showLinks = DoesUserBelongHere("RegLead");
	$step = 20;

	$thisYear = date("n") >= 3 ? date("Y") + 1: date("Y");
    $sql  = "SELECT SQL_CALC_FOUND_ROWS pb.BadgeID, pb.BadgeNumber, CASE WHEN pb.PeopleID IS NULL THEN CONCAT(ot.FirstName, ' ', ot.LastName) ELSE CONCAT(p.FirstName, ' ', p.LastName) END AS Name, pb.BadgeName, pb.Created AS Purchased, CASE WHEN pb.PeopleID IS NULL THEN ot.LastName ELSE p.LastName END AS LastName, CASE WHEN pb.PeopleID IS NULL THEN ot.FirstName ELSE p.FirstName END AS FirstName, pb.PaymentSource, pb.PaymentReference, pc.Code, pb.Status FROM PurchasedBadges pb LEFT OUTER JOIN People p ON p.PeopleID = pb.PeopleID LEFT OUTER JOIN OneTimeRegistrations ot ON ot.OneTimeID = pb.OneTimeID LEFT OUTER JOIN PromoCodes pc ON pc.CodeID = pb.PromoCodeID ";
    $where = "WHERE pb.Year = $thisYear ";
	
	$order = $_POST["sort"];
	$start = isset($_POST["start"]) ? $_POST["start"] : 0;
	if(isset($_POST["lastname"]) && strlen($_POST["lastname"]) > 0)
		$where .= "AND (p.LastName LIKE '" . $db->real_escape_string($_POST["lastname"]) . "%' OR " .
            "ot.LastName LIKE '%" . $db->real_escape_string($_POST["lastname"]) . "%')";

	if(isset($_POST["badgename"]) && strlen($_POST["badgename"]) > 0)
		$where .= "AND pb.BadgeName LIKE '" . $db->real_escape_string($_POST["badgename"]) . "%' ";

	if(strlen($order) == 0) $order = "  Purchased, Name";
	$sql .= $where . "ORDER BY $order LIMIT $start, $step";
	
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		$currentRows = $result->num_rows;
		echo "<table>\r\n";
		echo "<tr><th>Name</th><th>Badge Name</th><th>Badge #</th><th>Purchased</th><th>Paid With</th><th>Reference #</th><th>Promo Code</th><th>Status</th></tr>\r\n";
		while($row = $result->fetch_array())
		{
			if($showLinks)
			{
				$link1 = "<a href=\"editBadge.php?id=" . $row["BadgeID"] . "\">";
				if(empty($row["BadgeName"])) $link1 .= "[Blank Badge]";
				$link2 = "</a>";
			}
			else
			{
				$link1 = "";
				$link2 = "";
			}
			$fade = $row["Status"] != "Paid" ? " style=\"background-color: #FF6666;\"" : "";
			echo "<tr" . $fade . "><td>" . $row["Name"] . "</td><td>$link1" . $row["BadgeName"] . "$link2</td><td>" . $row["BadgeNumber"] . "</td><td>" . date("m/d/Y", strtotime($row["Purchased"])) . "</td><td>" . $row["PaymentSource"] . "</td><td>" . $row["PaymentReference"] . "</td><td>" . $row["Code"] . "</td><td>" . $row["Status"] . "</td></tr>\r\n";
		}
		$result->close();
		echo "</table>\r\n";
		
		$result = $db->query("SELECT FOUND_ROWS() AS TotalRows");
		$row = $result->fetch_array();
		$totalRows = $row["TotalRows"];
		$result->close();
		echo "<div style=\"margin: 5px 0px 0px 0px;\">Showing records <b>" . ($start + 1) . "</b> through <b>" . ($start + $currentRows) . "</b> out of <b>" . $totalRows . "</b> badges.</div><br />\r\n";
		echo "<input type=\"submit\" name=\"GoBack\" value=\"Last $step Badges\" " . ($start == 0 ? "disabled" : "") . " onclick=\"updateList(" . ($start == 0 ? 0 : $start - $step) . "); return false;\" />";
		echo "<input type=\"submit\" name=\"GoFwd\" value=\"Next $step Badges\" " . ($start + $step >= $totalRows ? "disabled" : "") . " onclick=\"updateList(" . ($start + $step >= $totalRows ? $start : $start + $step) . "); return false;\" /><br />\r\n";
	}
	else
		echo "<p class=\"requiredField\">No badges appear to have been purchased that meet the above parameters.</p>\r\n";
?>