<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("RegLead"))
		header('Location: /index.php');
		
	$year = date("n") >= 3 ? date("Y") + 1: date("Y");
    $sql  = "SELECT pb.BadgeID, pb.BadgeNumber, CASE WHEN pb.PeopleID IS NULL THEN CONCAT(ot.FirstName, ' ', ot.LastName) ";
    $sql .= "ELSE CONCAT(p.FirstName, ' ', p.LastName) END AS Name, pb.BadgeName, ";
	$sql .= "CASE WHEN pb.PeopleID IS NULL THEN ot.LastName ELSE p.LastName END AS LastName, ";
	$sql .= "CASE WHEN pb.PeopleID IS NULL THEN ot.FirstName ELSE p.FirstName END AS FirstName, pb.Department ";
	$sql .= "FROM PurchasedBadges pb LEFT OUTER JOIN People p ON p.PeopleID = pb.PeopleID ";
    $sql .= "LEFT OUTER JOIN OneTimeRegistrations ot ON ot.OneTimeID = pb.OneTimeID ";
    $sql .= "WHERE pb.Year = $year AND pb.Department IS NOT NULL AND pb.PaymentSource = 'Comp' ORDER BY Department, LastName";
	
	$badges = array();
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_array())
			$badges[] = $row;
		$result->close();
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Issue Complimentary Badges</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<style>
		#regList {
			clear: both;
			width: 100%;
			max-height: 415px;
			overflow: auto;
		}
	</style>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript">
		function resetFields() {
			$("#searchLastName").val("");
			$("#searchBadgeName").val("");
			$("#regList").html("");
		}
		
		$(document).ready(function() {
			$(document).keypress(function(e){
				if (e.which == 13){
					$("#doSearch").click();
					return false;
				}
			});
			$("#search_form").submit(function () {
				$("#regList").html("");
				var lastname = $("#searchLastName").val();
				var badgename = $("#searchBadgeName").val();
				var sort = $("#sort").val();
				if($("#sortDesc").is(':checked')) sort += " DESC";
				$.post("getCompBadges.php", { lastname: lastname, badgename: badgename, sort: sort, isStaff: 0 }, function(result) {
					$("#regList").html(result);
				});
				return false;
			});
			$("#actionForm").submit(function() {
				var department = $("#department").val();
				var peopleID = $("input[name=id]:checked").val();
				var firstname = $("#firstName").val();
				var lastname = $("#lastName").val();
				var badgename = $("#badgeName").val();
				if(peopleID == null && firstname == "")
				{
					$("#actionMessage").html("<p style=\"font-weight: bold;\">You must either pick a person from the results list or fill in at least the First Name to save the badge.</p>");
					return false;
				}
				$.post("doIssueCompBadge.php", { department: department, peopleID: peopleID, firstname: firstname, lastname: lastname, badgename: badgename }, function(result) {
					location.reload();
				});
				return false;
			});
		});
		
		$(document).on("keyup", "#firstName,#lastName,#badgeName", function () {
			$(".people").prop("checked", false);
		});
		
		$(document).on("click", ".people", function() {
			$("#firstName").val("");
			$("#lastName").val("");
			$("#badgeName").val("");
		});
		
		$(document).on("keyup", "#department", function () {
			if($("#department").val() == "")
				$("#createBadge").attr("disabled", true);
			else
				$("#createBadge").removeAttr("disabled");
		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Issue Complimentary Badges</h1>
			<div class="standardTable">
			<?php
				if(!empty($badges))
				{
					echo "<table>\r\n";
					echo "<tr><th>Name</th><th>Badge Name</th><th>Badge #</th><th>Department to Bill</th></tr>\r\n";
					foreach($badges as $badge)
						echo "<tr><td>" . $badge["Name"] . "</td><td>" . $badge["BadgeName"] . "</td><td>" . $badge["BadgeNumber"] . "</td><td>" . 
							$badge["Department"] . "</td></tr>\r\n";
					echo "</table>\r\n";
				}
				else
					echo "<p class=\"requiredField\">No complimentary badges appear to have been issued for this convention year.</p>\r\n"; ?>
			</div>
			<p>To issue a complimentary badge, search for the recipient below and select them, or enter a name to issue a badge to 
			someone without an account.</p>
			<form id="search_form" method="post">
				<div style="width: 50%; float: left;">
					<label>Sort By: <select id="sort" name="sort" style="width: 27%;">
						<option value="LastName">Last Name</option>
						<option value="FirstName">First Name</option>
						<option value="BadgeName">Badge Name</option>
					</select></label><label><input type="checkbox" id="sortDesc" name="sortDesc" />Descending</label><br />
					<input type="Submit" value="Clear" onclick="resetFields(); return false;" /><input type="Submit" id="doSearch" name="doSearch" value="Search" />
				</div>
				<div style="width: 50%; float: left;">
					<label for="searchLastName">Last Name: <input type="text" id="searchLastName" /></label><br />
					<label for="searchBadgeName">Badge Name: <input type="text" id="searchBadgeName" /></label><br />
					<br />
				</div>
			</form>
			<form id="actionForm" method="post">
			<div id="regList"></div>
			</form>
			<div class="clearfix"></div>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>