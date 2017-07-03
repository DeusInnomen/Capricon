<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	else
	{
		$year = date("n") >= 3 ? date("Y") + 1: date("Y");
		$result = $db->query("SELECT PanelistToAvoid, Accessibility, AdditionalInfo, CanShareInfo FROM ProgramSurvey WHERE " . 
			"Year = $year AND PeopleID = " . $_SESSION["PeopleID"]);
		
		if($result->num_rows > 0)
		{
			$info = $result->fetch_array();
			$result->close();
		}
		else
		{
			$info = array();
			$info["PanelistToAvoid"] = "";
			$info["Accessibility"] = "";
			$info["AdditionalInfo"] = "";
			$info["CanShareInfo"] = "0";
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Programming Survey</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(":submit").click(function () {
				$("#continue").val(this.name == "saveContinue" ? "continue" : "");
			});
			$("#surveyForm").submit(function () {
				$("#surveyForm :input").prop("readonly", true);
				$("#panelistToAvoid").prop("readonly", true);
				$("#additionalInfo").prop("readonly", true);
				$("#accountSaveMessage").removeClass("goodMessage");
				$("#accountSaveMessage").removeClass("errorMessage");
				$("#accountSaveMessage").html("&nbsp;");
				$.post("doProgramSurveyFunctions.php", $(this).serialize(), function(result) {
					if(result.success)
					{
						if(result.doRedirect)
							window.location = "index.php";
						else
						{
							$("#accountSaveMessage").addClass("goodMessage");
							$("#accountSaveMessage").html(result.message);
						}
					}
					else
					{
						$("#accountSaveMessage").addClass("errorMessage");
						$("#accountSaveMessage").html(result.message);
					}
					$("#surveyForm :input").prop("readonly", false);
					$("#panelistToAvoid").prop("readonly", false);
					$("#additionalInfo").prop("readonly", false);
				}, 'json');
				return false;
			});			
		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxmedium">
			<div style="margin-bottom: 30px;">
				<form id="surveyForm" class="accountForm" method="post" action="">
					<h1>Programming Survey -- Part 4</h1>
					<label for="panelistToAvoid" class="fieldLabelShort" >Please do not program me on panels with the following people:</label><br />
					<textarea id="panelistToAvoid" name="panelistToAvoid" maxlength="500" rows="4" style="width: 98%;"><?php echo htmlspecialchars($info["PanelistToAvoid"]); ?></textarea><br />
					<label for="accessibility" class="fieldLabelShort" >Do you have any accessibility issues that we should know about?</label><br />
					<textarea id="accessibility" name="accessibility" maxlength="200" rows="2" style="width: 98%;"><?php echo htmlspecialchars($info["Accessibility"]); ?></textarea><br />
					<label for="additionalInfo" class="fieldLabelShort" >Is there anything else you would like to share or alert us to regarding your participation in programming or panels?</label><br />
					<textarea id="additionalInfo" name="additionalInfo" maxlength="500" rows="4" style="width: 98%;"><?php echo htmlspecialchars($info["AdditionalInfo"]); ?></textarea><br />
					<label for="canShareInfo"><input type="checkbox" name="canShareInfo" id="canShareInfo" <?php echo ($info["CanShareInfo"] ? "checked" : ""); ?> />Check here to give us permission to share your contact information with any co-panelists.</label></br />
					<p>Hit "Save Information" below to save this final page, then you're done! Thank you for volunteering with Capricon programming!</p>
					<input type="submit" name="saveOnly" value="Save Information" /> 
					<input style="float: right;" type="submit" name="saveContinue" value="Save Information and Return to Menu" /><br />
					<input type="hidden" name="task" id="task" value="Page4" />
					<input type="hidden" name="continue" id="continue" value="">
					<span id="accountSaveMessage">&nbsp;</span><br />
				</form>
			</div>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>