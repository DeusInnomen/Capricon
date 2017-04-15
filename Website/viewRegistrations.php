<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("RegStaff"))
		header('Location: /index.php');
	$year = isset($_GET['year']) ? $_GET['year'] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- View Registrations</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<style>
		#resultsBlock {
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
			$("#lastname").val("");
			$("#badgename").val("");
		}
		$(document).ready(function() {
		    $.post("getRegistrations.php", { lastname: "", badgename: "", sort: "Purchased", year: "<?php echo $year; ?>" }, function(result) {
				$("#regList").html(result);
			});
			$(document).keypress(function(e){
				if (e.which == 13){
					$("#doSearch").click();
					return false;
				}
			});
			$("#search_form").submit(function () {
				$("#regList").html("");
				var lastname = $("#lastname").val();
				var badgename = $("#badgename").val();
				var sort = $("#sort").val();
				if($("#sortDesc").is(':checked')) sort += " DESC";
				$.post("getRegistrations.php", { lastname: lastname, badgename: badgename, sort: sort, year: "<?php echo $year; ?>" }, function(result) {
					$("#regList").html(result);
				});
				return false;
			});
		});
        function updateList(start)
        {
			var lastname = $("#lastname").val();
			var badgename = $("#badgename").val();
			var sort = $("#sort").val();
			if($("#sortDesc").is(':checked')) sort += " DESC";
			$.post("getRegistrations.php", { lastname: lastname, badgename: badgename, sort: sort, start: start, year: "<?php echo $year; ?>" }, function(result) {
				$("#regList").html(result);
			});
        }
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>View Registrations</h1>
			<p>To view specific registrations, fill in one or more of the search boxes below then press Submit. Leave blank to list all registrations. Badges in a status other than "Paid" will have red backgrounds and will not be printed.</p>
			<form id="search_form" method="post">
				<div style="width: 50%; float: left;">
					<label>Sort By: <select id="sort" name="sort" style="width: 27%;">
						<option value="Purchased" selected>Purchased</option>
						<option value="LastName">Last Name</option>
						<option value="FirstName">First Name</option>
						<option value="BadgeNumber">Badge Number</option>
						<option value="BadgeName">Badge Name</option>
					</select></label><label><input type="checkbox" id="sortDesc" name="sortDesc" />Descending</label><br />
					<input type="Submit" value="Clear" onclick="resetFields(); return false;" /><input type="Submit" id="doSearch" name="doSearch" value="Search" />
				</div>
				<div style="width: 50%; float: left;">
					<label for="lastname">Last Name: <input type="text" id="lastname" /></label><br />
					<label for="badgename">Badge Name: <input type="text" id="badgename" /></label><br />
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