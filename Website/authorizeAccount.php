<?php
	session_start();
	if(!isset($_GET['id']))
		header('Location: /index.php');
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	$data = $db->real_escape_string($_GET['id']);
	
	DoCleanup();
	
	$result = $db->query("SELECT cl.PeopleID, TRIM(CONCAT(p.FirstName, ' ', p.LastName)) AS Name, Data FROM ConfirmationLinks cl JOIN People p ON cl.PeopleID = p.PeopleID WHERE cl.Code = '$data' AND cl.Type = 'Authorize'");
	if($result->num_rows > 0)
	{
		$row = $result->fetch_array();
		$id = $row["PeopleID"];
		$name = $row["Name"];
		$email = $row["Data"];
		$result->close();
		
		if($email == $_SESSION['Email'])
		{
			$db->query("UPDATE People SET ParentID = $id WHERE PeopleID = " . $_SESSION["PeopleID"]);
			$db->query("DELETE FROM ConfirmationLinks WHERE Code = '$data' and Type = 'Authorize'");
			$message = "You have successfully given permission to $name to purchase badges and other convention items for you!";
		}
		else
		{
			$message = 'Sorry, an invalid ID was provided.<br /><br />If you continue to have problems, please contact ' . 
				'<a href="mailto:it@phandemonium.org?Subject=Account Authorization Problems">Phandemonium IT</a> ' . 
				'for further assistance.';
		}
	}
	else
	{
			$message = 'Sorry, an invalid ID was provided.<br /><br />If you continue to have problems, please contact ' . 
				'<a href="mailto:it@phandemonium.org?Subject=Account Authorization Problems">Phandemonium IT</a> ' . 
				'for further assistance.';
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Authorize Account</title>
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
		<div class="centerboxnarrow">
			<p><?php echo $message; ?></p>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>