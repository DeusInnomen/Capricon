<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("RegLead") && !DoesUserBelongHere("Ops"))
		header('Location: index.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Manage All Accounts</title>
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
			$("#email").val("");
			$("#lastname").val("");
			$("#badgename").val("");
		}
		$(document).ready(function() {
			$(document).keypress(function(e){
				if (e.which == 13){
					$("#doSearch").click();
					return false;
				}
			});
			$("#search_form").submit(function () {
				var email = $("#email").val();
				var lastname = $("#lastname").val();
				var badgename = $("#badgename").val();
				var sort = $("#sort").val();
				if($("#sortDesc").is(':checked')) sort += " DESC";
				$.post("adminAccountSearch.php", { email: email, lastname: lastname, badgename: badgename, sort: sort }, function(result) {
					$("#resultBlock").html(result);
				});
				return false;
			});
			$("#actionForm").submit(function() {
				if($("#actionForm :input:checkbox:checked").length == 0) 
					return false;
				
				var ids = "";
				$("#actionForm :input:checkbox:checked").each(function() {
					ids += "|" + $(this).val();
				});
				ids = ids.substring(1);
				$('<form action="bulkPermissions.php" method="POST"><input type="hidden" name="IDs" value="' + 
					ids + '"></form>').submit();
				return false;
			});
		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Account Management</h1>
			<p>To edit a user, fill in one or more of the search boxes below then press Submit. Leave blank to list all users.</p>
			<form id="search_form" method="post">
				<div style="width: 50%; float: left;">
					<label for="email">Email: <input type="text" id="email" /></label><br />
					<label>Sort By: <select id="sort" name="sort" style="width: 27%;">
						<option value="LastName" selected>Last Name</option>
						<option value="FirstName">First Name</option>
						<option value="Email">Email Address</option>
						<option value="PeopleID">ID Number</option>
						<option value="BadgeName">Badge Name</option>
					</select></label><label><input type="checkbox" id="sortDesc" name="sortDesc" />Descending</label>
					<input type="Submit" value="Clear" onclick="resetFields(); return false;" /><input type="Submit" id="doSearch" name="doSearch" value="Search" />
				</div>
				<div style="width: 50%; float: left;">
					<label for="lastname">Last Name: <input type="text" id="lastname" /></label><br />
					<label for="badgename">Badge Name: <input type="text" id="badgename" /></label><br />
					<br />
				</div>
			</form>
			<form id="actionForm" method="post">
			<div id="resultBlock"></div>
			<?php if(DoesUserBelongHere("SuperAdmin")) { ?> <input type="submit" value="Bulk Assign Permissions" /> <?php } ?>
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