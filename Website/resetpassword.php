<?php
	session_start();
	if(!isset($_GET['id']))
		header('Location: index.php');
	else
	{
		include_once('includes/functions.php');
		$id = $_GET['id'];
		$result = PostToURL("https://registration.capricon.org/reminders.php", array ('id' => $id, 'type' => 'Password'));
		$values = json_decode($result, true);
		if(!$values["success"])
			header('Location: index.php');		
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Reset Password</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
	<link rel="stylesheet" type="text/css" href="includes/jquery.validate.password.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
	<script type="text/javascript" src="includes/jquery.validate.password.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#password").keyup(function () { $(this).valid();	});
			$("#passwordconfirm").keyup(function () { $(this).valid(); });
			$.validator.messages.required = "";
			$("#reset_form").validate({
				rules: {
					password: "required",
					passwordconfirm: {
						required: true,
						equalTo: "#password"
					}
				},
				submitHandler: function(form) {
					$("#reset_form :input").prop("readonly", true);
					var id = '<?php echo $_GET['id']; ?>';
					var pass = $("#password").val();
					$.post("doResetPassword.php", { id: id, password: pass }, function(result) {
						if(result.success)
						{
							$("#statusMessage p").html("Your password has been successfully reset. For security reasons, " + 
								"you have been automatically logged out. You will be returned to the main menu in 5 seconds.");
							$("#reset_form").fadeOut(500);						
							setTimeout(function() { window.location = "index.php"; }, 5000);
						}
						else
						{
							$("#statusMessage p").html('An unexpected error has occurred. Unable to reset your password ' + 
								'at this time. Please try again shortly using the link in your email. If problems persist, ' + 
								'contact Phandemonium IT at <a href="mailto:it@phandemonium.org?Subject=Password Reset Problem">' + 
								'it@phandemonium.org</a>. Sorry for the inconvenience!');
							$("#statusMessage p").addClass("errMsg");
						}
					}, 'json');
					return false;
				},
				messages: {
					password: "The password is not strong enough. It must be rated 'Good' or 'Strong' to be accepted.",
					passwordconfirm: {
						equalTo: "Both passwords must match each other exactly."
					}
				},
				errorPlacement: function(error, element) {
					error.appendTo("#errorMessage");
				}
			});
		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxnarrow">
			<div id="statusMessage"><p>Please enter your new password below:</p></div>
			<form id="reset_form" class="reset_form" method="post">
				<label>Password: <input type="password" name="password" id="password" /></label><br />
				<label>Retype: <input type="password" name="passwordconfirm" id="passwordconfirm" /></label><br />
				Strength:<div class="password-meter">
					<div class="password-meter-message"></div>
					<div class="password-meter-bg"><div class="password-meter-bar">&nbsp;</div></div>
				</div><br />
				<div id="errorMessage"></div>
				<input type="submit" value="Submit" />
			</form>
			<hr />
			<p>Return to the <a href="/index.php">Main Menu</a>.</p>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>