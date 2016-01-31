<?php
	session_start();
	include_once('includes/functions.php');

	$year = date("Y") + 1;
	$conYear = $year - 1980;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Programming Ideas</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#ideaForm').validate({
				rules: {
					name: "required",
					title: "required",
					description: "required",
					participants: "required",
				},
				messages: {
					name: "<span style=\"font-size: 0.9em; color: #FF0000;\">Name required.</span>",
					title: "<span style=\"font-size: 0.9em; color: #FF0000;\">Title required.</span>",
					description: "<span style=\"font-size: 0.9em; color: #FF0000;\">Please provide a detailed description.</span>",
					participants: "<span style=\"font-size: 0.9em; color: #FF0000;\">Please provide a list of suggested participants.</span>"
				},
				submitHandler: function (e) {
					$("#ideaForm :input").prop("readonly", true);
					$("#message").html("&nbsp;");
					$.post("doSubmitPanelIdea.php", $('#ideaForm').serialize(), function(result) {
						if(result.success)
						{
							$(".content").scrollTop(0);
							$("#ideaForm").fadeOut(500);
							$("#results").html("Your panel idea has been submitted. Thank you! Refresh the page if you have another idea to submit.");
						}
						else
						{
							$("#message").html(result.message);
							$("#ideaForm :input").prop("readonly", false);
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
		<div class="centerboxmedium">
			<h1>I Have a Idea for a Panel!</h1>
			<div style="font-size: 1.05em;" class="goodMessage" id="results">&nbsp;</div>
			<form id="ideaForm" class="ideaForm" method="post" action="">
			<p>Capricon takes pride in its wide range of panels that our programming participants generously agree to organize and put on 
			each year. We are always open to your suggestions - this helps make sure we have the types of panels our attendees wish to see! 
			If you have a great idea for a panel that you would like to see (or even participate in) at the next Capricon, we would love to 
			hear it.</p>
			<p>Ideas submitted at this time will be considered for Capricon <?php echo $conYear; ?> in <?php echo $year; ?>.</p>
			<p>Please fill out the following form with your idea, and we'll make sure it gets to our Programming team. Thank you very
			much for your help!</p>
<?php
			if(isset($_SESSION["PeopleID"]))
				echo '			<input type="hidden" name="id" id="id" value="' . $_SESSION["PeopleID"] . '" />' . "\r\n";
			else
			{
				echo '			<label for="name" class="fieldLabelShort">Your Name: </label><br />' . "\r\n";
				echo '			<input type="text" name="name" id="name" placeholder="Required" style="width: 70%;" maxlength="100" value="" /><br />' . "\r\n";
				echo '			<label for="email" class="fieldLabelShort">Email Address: </label><br />' . "\r\n";
				echo '			<input type="text" name="email" id="email" placeholder="Preferred, But Not Required" style="width: 70%;" maxlength="100" value="" /><br />' . "\r\n";
			} 
?>			<label for="title" class="fieldLabelShort">Panel Idea Title: </label><br />
			<input type="text" name="title" id="title" placeholder="Required" style="width: 70%;" maxlength="100" value="" /><br />
			<label for="description" class="fieldLabelShort">Panel Idea Description: </label><br />
			<textarea id="description" name="description" maxlength="500" rows="4" style="width: 98%;" placeholder="Be as descriptive as possible!"></textarea><br />
			<label for="participants" class="fieldLabelShort">Who Would You Suggest As Panelists For This Panel? </label><br />
			<textarea id="participants" name="participants" maxlength="500" rows="4" style="width: 98%;" placeholder="Provide the names of people you think should be on this panel."></textarea><br /><br />
			<label for="canContact" class="fieldLabelShort">Check This Box To Give Us Permission To Contact You For More Info: <input type="checkbox" name="canContact" id="canContact" /></label><br />
			<br />
			<input type="submit" value="Submit Panel Idea" /><br />
			<div style="font-size: 1.05em;" class="errorMessage" id="message">&nbsp;</div></form>
			<br /><br />
			<div class="goback">
				<a href="/index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>