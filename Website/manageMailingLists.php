<?php
	session_start();
	include_once('includes/functions.php');
	$name = !empty($_GET["name"]) ? $_GET["name"] : (!empty($_SESSION["FullName"]) ? $_SESSION["FullName"] : "");
	$email = !empty($_GET["address"]) ? $_GET["address"] : (!empty($_SESSION["Email"]) ? $_SESSION["Email"] : "");
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
			<p>Goat Droppings is the official Phandemonium announcement list. We typically send out a few announcements
			per year in order to keep our membership up to date on information related to Capricon and other related
			activities.</p>
			<form method="post" action="http://scripts.dreamhost.com/add_list.cgi">
			<table><input type="hidden" name="list" value="goatdroppings" />
			<input type="hidden" name="domain" value="capricon.org" />
			<tr><td>Your Name (Optional)</td><td><input type="text" name="name" value="<?php echo $name; ?>" /></td></tr>
			<tr><td>E-mail</td><td><input type="text" name="email" value="<?php echo $email; ?>" /></td></tr>
			<tr><td><input type="submit" name="submit" value="Subscribe to Goat Droppings" /></td>
			<td><input type="submit" name="unsub" value="Unsubscribe from Goat Droppings" /></td><td></td></tr>
			</table>
			</form>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>