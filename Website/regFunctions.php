<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("RegUser"))
		header('Location: main.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System</title>
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#search_form").submit(function () {				
				if($.trim($("#email").val()).length == 0 && $.trim($("#lastname").val()).length == 0 && 
					$.trim($("#badgename").val()).length == 0)
				{
					$("#results").html("<span class=\"requiredField\">Please enter at least one value before searching.</span>");
					return false;
				}
				var email = $("#email").val();
				var lastname = $("#lastname").val();
				var badgename = $("#badgename").val();
				$.post("accountSearch.php", { email: email, lastname: lastname, badgename: badgename }, function(result) {
					$("#results").html(result);
				});
				return false;
			});
		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxmedium">
			<h1>Account Management</h1>
			<p>To edit a user, fill in one or more of the search boxes below then press Submit.</p>
			<form id="search_form" method="post">
				<div style="width: 48%; float: left;">
					<label for="email">Email: <input type="text" id="email" /></label><br />
				</div>
				<div style="width: 48%; float: left;">
					<label for="lastname">Last Name: <input type="text" id="lastname" /></label><br />
					<label for="badgename">Badge Name: <input type="text" id="badgename" /></label><br />
				</div>
				<input type="Submit" value="Clear" onclick="resetFields(); return false;" /><input type="Submit" value="Search" />
			</form>
			<div id="results">&nbsp;</div>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>