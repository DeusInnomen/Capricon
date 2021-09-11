<?php
	session_start();
	include_once('includes/functions.php');
	$return = isset($_GET["return"]) ? urldecode($_GET["return"]) : "index.php";
	$message = "Please enter your email address and password to log in.";
	if(isset($_GET["resend"]))
	{
		$email = $db->real_escape_string($_GET["resend"]);
		SendActivationEmail($email);
		$message = "An activation email has been sent to your email address. Please check for an email " .
			"from registration@capricon.org and click the link within to activate your account before " .
			"logging in.";
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$.validator.messages.required = "";
			$("#login_form").validate({
				rules: {
					email: {
						required: true,
						email: true
					},
					pass: "required"
				}, submitHandler: function(form) {
					$("#login_form :input").prop("readonly", true);
					$("#loginMessage p").removeClass("errMsg");
					$("#loginMessage p").html("Logging in...<br /><br />");
					var email = $("#email").val();
					var pass = $("#pass").val();
					$.post("auth.php", { email: email, password: pass }, function(result) {
						if(result.success)
						{
							$("#loginMessage p").html(result.message);
							//$("#login_form").fadeOut(500);
							//setTimeout(function() {
							//	window.location = "<?php echo $return; ?>";
							//	}, 500);
							window.location = "<?php echo $return; ?>";
						}
						else
						{
							$("#loginMessage p").html(result.message);
							$("#loginMessage p").addClass("errMsg");
							$("#login_form :input").prop("readonly", false);
						}
					}, 'json');
					return false;
				}
			});
		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxnarrow">
		<?php
			if(isset($_SESSION['PeopleID']))
			{ ?>
				<p>Logged in as <?php echo $_SESSION['FullName']; ?> (<?php echo $_SESSION['BadgeName'] ?>)</p><br>
				<p style="text-align: center;"><a href="logout.php">Log Out</a></p>	<?php
			}
			else
			{ ?>
			<div id="loginMessage"><p><?php echo $message; ?></p></div>
			<form id="login_form" class="login_form" method="post">
				<label for="email" class="fieldLabelShort">Email: </label><input type="email" name="email" id="email" /><br>
				<label for="pass" class="fieldLabelShort">Password: </label><input type="password" name="pass" id="pass" /><br>
				<input type="submit" value="Login" />
			</form>	<?php
			} ?>
			<hr />
			<p>Have an account but forgot your password? <a href="forgotpassword.php">Click here</a> to
			reset your password.</p>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>