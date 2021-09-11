<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_GET['id']))
		header('Location: index.php');
	else
	{
		$result = $db->query("SELECT PeopleID, Data FROM ConfirmationLinks WHERE Code = '" .
			$db->real_escape_string($_GET['id']) . "' AND Type = 'EmailChange'");
		if($result->num_rows > 0)
		{
			$row = $result->fetch_array();
			$id = $row['PeopleID'];
			$newemail = $row['Data'];
			$result->close();			

			$db->query("UPDATE People SET Email = '$newemail' WHERE PeopleID = $id");
			$db->query("DELETE FROM ConfirmationLinks WHERE Code = '" . $_GET['id'] . "'");
			
			// Destroy session information, forcing them to log out.
			$_SESSION = array();
			session_destroy();
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Change Email</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			setTimeout(function() { window.location = "index.php"; }, 5000);
		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxnarrow">
			<p>Your email address has been successfully changed. For security reasons, you have been
			automatically logged out. You will be returned to the main menu in 5 seconds.</p>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>