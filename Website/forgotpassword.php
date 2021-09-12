<?php
	session_start();
	include_once('includes/functions.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Forgot Password</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#reset_form").submit(function() {
				$("#reset_form :input").prop("readonly", true);
				$("#statusMessage p").removeClass("errMsg");
				var email = $("#email").val();
				$.post("reminders.php", { email: email, type: "Password" }, function(result) {
					if(result.success)
					{
						$("#statusMessage p").html("A reminder email has been sent to the provided address. " + 
							"Check your email for further instructions.");
						$("#reset_form").fadeOut(500);
					}
					else
					{
						$("#statusMessage p").html("The email address provided was not recognized.");
						$("#statusMessage p").addClass("errMsg");
						$("#reset_form :input").prop("readonly", false);
					}
				}, 'json');
				return false;
			});
		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxnarrow">
			<div id="statusMessage"><p>Please enter your email address:</p></div>
			<form id="reset_form" method="post">
				<label>Email Address: <input type="email" name="email" id="email" /></label><br />
				<input type="submit" value="Submit" />
			</form>
			<hr />
			<p>Return to the <a href="index.php">Main Menu</a>.</p>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>