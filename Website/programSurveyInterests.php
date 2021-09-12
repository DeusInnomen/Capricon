<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	else
	{
		$year = date("n") >= 3 ? date("Y") + 1: date("Y");
		$result = $db->query("SELECT Interests, WillingAutograph, WillingReading, WillingYA, WillingKids, ProgramIdeas, ProgramIdeaTitle, " .
			"ProgramIdeaPanelists, OverdonePrograms FROM ProgramSurvey WHERE Year = $year AND PeopleID = " . $_SESSION["PeopleID"]);
		
		if($result->num_rows > 0)
		{
			$info = $result->fetch_array();
			$result->close();
		}
		else
		{
			$info = array();
			$info["Interests"] = "";
			$info["WillingAutograph"] = "0";
			$info["WillingReading"] = "0";
			$info["WillingYA"] = "0";
			$info["WillingKids"] = "0";
			$info["ProgramIdeas"] = "";
			$info["ProgramIdeaTitle"] = "";
			$info["ProgramIdeaPanelists"] = "";
			$info["OverdonePrograms"] = "";
		}
		
		$result = $db->query("SELECT InterestID, Interest FROM SurveyInterests ORDER BY InterestID");
		$interests = array();
		while($row = $result->fetch_array())
			$interests[$row["InterestID"]] = $row["Interest"];
		$result->close();

	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Programming Survey</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
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
				var intValue = "";
				var intText = "";
				$("input:checkbox:checked").each(function () {
					if($(this).attr("id").substring(0, 3) == "int")
					{
						intValue += "|" + $(this).attr("id").substring(3);
						var label = $("label[for='" + $(this).attr("id") + "']");
						intText += ", " + label.text();
					}
				});
				if(intValue.length > 0) intValue = intValue.substring(1);
				if(intText.length > 0) intText = intText.substring(2);
				$("#interestValues").val(intValue);
				$("#interestText").val(intText);
				$("#surveyForm :input").prop("readonly", true);
				$("#programIdeas").prop("readonly", true);
				$("#overdonePrograms").prop("readonly", true);
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
					$("#programIdeas").prop("readonly", false);
					$("#overdonePrograms").prop("readonly", false);
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
					<h1>Programming Survey -- Part 3</h1>
					<div class="headertitle">Areas of Interest</div>
					<label class="fieldLabelShort" >From which of the following areas would you consider being a panelist? (Check All That Apply)</label><br />
					<div id="interestsTable">
					<table>
					<?
					$col = 0;
					foreach($interests as $key => $value)
					{
						if($col == 0)
							echo "<tr>";
						echo '<td><label for="int' . $key . '" class="fieldLabelShort"><input type="checkbox" name="int' . $key .
							'" id="int' . $key . '" ' . (strpos("|" . $info["Interests"] . "|", "|" . $key . "|") === false ? "" : "checked ") . "/>$value</label></td>";
						if($col == 1)
							echo "</tr>";
						echo "\r\n					";
						$col++;
						if($col == 2)
							$col = 0;
					}
					?></table></div><br>
					<label for="willingAutograph"><input type="checkbox" name="willingAutograph" id="willingAutograph" <?php echo ($info["WillingAutograph"] == "1" ? "checked" : ""); ?> />I am willing to do an autograph session.</label></br />
					<label for="willingReading"><input type="checkbox" name="willingReading" id="willingReading" <?php echo ($info["WillingReading"] == "1" ? "checked" : ""); ?> />I am willing to do a reading.</label></br />
					<label for="willingYA"><input type="checkbox" name="willingYA" id="willingYA" <?php echo ($info["WillingYA"] == "1" ? "checked" : ""); ?> />I am willing to do Teen / Young Adult Programming.</label></br />
					<label for="willingKids"><input type="checkbox" name="willingKids" id="willingKids" <?php echo ($info["WillingKids"] == "1" ? "checked" : ""); ?> />I am willing to do Children's Programming.</label></br />
					<div class="headertitle">Panel and Program Ideas</div>
					<label for="programIdeas" class="fieldLabelShort" >Are there any program items that you have always wanted to do, but never had the chance? Tell us about it / them.</label><br />
					<textarea id="programIdeas" name="programIdeas" maxlength="500" rows="4" style="width: 98%;"><?php echo htmlspecialchars($info["ProgramIdeas"]); ?></textarea><br />
					<label for="programIdeaTitle" class="fieldLabelShort" >What would you title this program or panel?</label><br />
					<input type="text" name="programIdeaTitle" id="website" maxlength="100" style="width: 98%;" value="<?php echo $info["ProgramIdeaTitle"]; ?>" /><br />
					<label for="programIdeaPanelists" class="fieldLabelShort" >Who would you recommend as possible panelists or participants? (Include contact information if you can, please.)</label><br />
					<input type="text" name="programIdeaPanelists" id="programIdeaPanelists" maxlength="200" style="width: 98%;" value="<?php echo $info["ProgramIdeaPanelists"]; ?>" /><br />
					<label for="overdonePrograms" class="fieldLabelShort" >Are there any program items / topics that you have done way too many times and don't wish to do anymore? Tell us about it / them.</label><br />
					<textarea id="overdonePrograms" name="overdonePrograms" maxlength="500" rows="4" style="width: 98%;"><?php echo htmlspecialchars($info["OverdonePrograms"]); ?></textarea><br /><br>
					<input type="submit" name="saveOnly" value="Save Information" /> 
                    <input style="float: right;" type="submit" name="saveContinue" value="Save Information and Return to Menu" /><br />
					<input type="hidden" name="task" id="task" value="Page2" />
					<input type="hidden" name="interestValues" id="interestValues" value="" />
					<input type="hidden" name="interestText" id="interestText" value="" />
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