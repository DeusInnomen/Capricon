<?php
	session_start();
	include_once('includes/functions.php');
	if(empty($_GET["code"]))
		header('Location: /manageMailingLists.php');
	$name = !empty($_GET["name"]) ? $_GET["name"] : (!empty($_SESSION["FullName"]) ? $_SESSION["FullName"] : "");
	$email = !empty($_GET["address"]) ? $_GET["address"] : (!empty($_SESSION["Email"]) ? $_SESSION["Email"] : "");
	$code = $_GET["code"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Manage Mailing Lists</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxmedium">
			<h1>Manage Mailing Lists</h1>
			<h3>Goat Droppings Announcements List</h3>
			<?php
				if($code == 1) { // Successful Subscription
					echo "<p>You have successfully subscribed to the Goat Droppings mailing list!</p>";
				}
				else if($code == 2) { // Successful Unsubscription
					echo "<p>You have successfully unsubscribed from the Goat Droppings mailing list!</p>";
				}
				else if($code == 3) { // Confirmation Request Sent
					echo "<p>You're almost done! We've sent an email to the email address you provded ($email) to verify that you own the address. " .
						 "Check your email inbox for further instructions! Please note it can take up to a couple hours for the system to send this " . 
						 "email to you.</p>";
				}
				else if($code == -1) { // Already On List
					echo "<p>The email address you provided ($email) is already subscribed to Goat Droppings.</p>";
				}
				else if($code == -2) { // Already Unsubscribed
					echo "<p>The email address you provided ($email) is not presently subscribed to Goat Droppings.</p>";
				}
				else if($code == -3) { // Invalid Email
					echo "<p>An invalid email address was provided. Please hit Back on your browser and verify you typed your address correctly.</p>";
				}
				else
					header('Location: /manageMailingLists.php');
			?>
			<p>At any time, you may return to the <a href="manageMailingLists.php">Manage Mailing Lists</a> page to change your subscription settings.</p>
			<div class="goback">
				<a href="/index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>