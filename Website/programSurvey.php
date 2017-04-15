<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	else
	{
		$canCopy = false;
		//$year = date("n") >= 3 ? date("Y") + 1: date("Y");
		$year = date("Y") + 1;
		$result = $db->query("SELECT Website, Biography, DayJob, Expertise, PreferredContact, Ethnicity, Gender, Age, Orientation FROM ProgramSurvey WHERE " . 
		"Year = $year AND PeopleID = " . $_SESSION["PeopleID"]);
		
		if($result->num_rows > 0)
		{
			$info = $result->fetch_array();
			$result->close();
		}
		else
		{
			$result = $db->query("SELECT SurveyID FROM ProgramSurvey WHERE Year != $year AND PeopleID = " . $_SESSION["PeopleID"]);
			if($result->num_rows > 0)
			{
				$canCopy = true;
				$result->close();
			}
			
			$info = array();
			$info["PreferredContact"] = "Email";
			$info["Website"] = "";
			$info["Biography"] = "";
			$info["DayJob"] = "";
			$info["Expertise"] = "";
            $info["Accessibility"] = "";
            $info["Ethnicity"] = "";
            $info["Gender"] = "";
            $info["Age"] = "NoAnswer";
            $info["Orientation"] = "";
		}
		
		$result = $db->query("SELECT ExpertiseID, Expertise FROM SurveyExpertise ORDER BY ExpertiseID");
		$expertise = array();
		while($row = $result->fetch_array())
			$expertise[] = $row;
		$result->close();
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
			$("select#preferredContact option").each(function() { this.selected = (this.value == "<?php echo $info["PreferredContact"]; ?>"); });
			$("select#age option").each(function() { this.selected = (this.value == "<?php echo $info["Age"]; ?>"); });
			$(":submit").click(function () {
				if(this.name != "doTransfer")
					$("#continue").val(this.name == "saveContinue" ? "continue" : "");
			});
			$("#surveyForm").submit(function () {
				var expValue = "";
				var expText = "";
				$("input:checkbox:checked").each(function () {
					expValue += "|" + $(this).attr("id").substring(3);
					var label = $("label[for='" + $(this).attr("id") + "']");
					expText += ", " + label.text();
				});
				if(expValue.length > 0) expValue = expValue.substring(1);
				if(expText.length > 0) expText = expText.substring(2);
				$("#expertiseValues").val(expValue);
				$("#expertiseText").val(expText);
				$("#surveyForm :input").prop("readonly", true);
				$("#biography").prop("readonly", true);
				$("#accountSaveMessage").removeClass("goodMessage");
				$("#accountSaveMessage").removeClass("errorMessage");
				$("#accountSaveMessage").html("&nbsp;");
				$.post("doProgramSurveyFunctions.php", $(this).serialize(), function(result) {
					if(result.success)
					{
						if(result.doRedirect)
							window.location = "/programSurveyInterests.php";
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
					$("#biography").prop("readonly", false);
				}, 'json');
				return false;
			});
		});

		function transferSurvey()
		{
			$.post("doProgramSurveyFunctions.php", { task: "Transfer" }, function(result) {
				window.location = "/programSurvey.php";
			}, 'json');
			return false;
		}
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxmedium">
			<div style="margin-bottom: 30px;">
				<form id="surveyForm" class="accountForm" method="post" action="">
					<h1>Programming Survey -- Part 1</h1>
					<p>To make Capricon a success, we need you as a programming participant and your programming ideas. If you are interested in participating in programming, please fill out the following survey.</p>
					<p>We'll contact you closer to the Con to share the panels we're developing and secure your input and interest in participating.</p>
					<p>Capricon policy is for all panelists to purchase a membership prior to the convention.  Participants who take part in the requisite number of convention activities may be eligible for membership reimbursements.</p>
					<?php if($canCopy) { ?>
					<div class="headertitle">Transfer Last Survey?</div>
					<p>I see you have filled out the Programming Survey in a previous year! Would you like to start by copying your answers over from the most recent year? You still should go through this and the following screens to ensure none of your answers have changed (and often the interests options change year-to-year to fit the theme), but at least this will save you a lot of time! Note: this option goes away once you save any answers to the survey.<p>
					<input type="submit" name="doTransfer" id="doTransfer" value="Transfer From Last Survey" onclick="transferSurvey(); return false;">
					<?php } ?>
					<div class="headertitle">Basic Information</div>
					<label for="preferredContact" class="fieldLabelShort" >Preferred Method of Contact: </label>
					<select id="preferredContact" name="preferredContact" style="width: 30%">
						<option value="Email">Email Address</option>
						<option value="Home">Home Phone</option>
						<option value="Mobile">Mobile Phone</option>
						<option value="Mail">Mailing Address</option>
					</select><br />
					<p>Please be sure that your preferred method of contact is filled in on the <a href="manageAccount.php" target="_new">Manage Your Account</a> page! (Link opens in a new window.)</p>
					<label for="website" class="fieldLabelShort" >Your Website (Personal or Professional): </label><br />
					<input type="text" name="website" id="website" maxlength="100" style="width: 98%;" value="<?php echo $info["Website"]; ?>" /><br />
					<label for="biography" class="fieldLabelShort" >Brief Biography: </label><br />
					<textarea id="biography" name="biography" maxlength="500" rows="4" style="width: 98%;"><?php echo htmlspecialchars($info["Biography"]); ?></textarea>
					<p style="font-style: italic;">We use KonOpas to provide a portable version of the Programming Guide that's friendly to mobile devices and computers alike. The biography you list here will be listed in KonOpas linked to your name so that people may learn more about you.</p>
					<label for="dayjob" class="fieldLabelShort" >Day Job: </label><br />
					<input type="text" name="dayjob" id="dayjob" maxlength="100" style="width: 98%;" value="<?php echo $info["DayJob"]; ?>" /><br />
                    <label for="accessibility" class="fieldLabelShort">Do you have any accessibility issues that we should know about?</label><br />
                    <textarea id="accessibility" name="accessibility" maxlength="200" rows="2" style="width: 98%;"><?php echo htmlspecialchars($info["Accessibility"]); ?></textarea><br />
					<label class="fieldLabelShort" >Areas of Expertise: </label><br /> 
					<div id="expertiseTable">
					<table>
					<?
					$col = 0;
					foreach($expertise as $record)
					{
						if($col == 0)
							echo "<tr>";						
						echo '<td><label for="exp' . $record["ExpertiseID"] . '" class="fieldLabelShort"><input type="checkbox" name="exp' . $record["ExpertiseID"] .
							'" id="exp' . $record["ExpertiseID"] . '" ' . (strpos("|" . $info["Expertise"] . "|", "|" . $record["ExpertiseID"] . "|") === false ? "" : "checked ") . "/>" . $record["Expertise"] . "</label></td>";
						if($col == 2)
							echo "</tr>";
						echo "\r\n					";
						$col++;
						if($col == 3)
							$col = 0;
					}
                    ?></table></div><br>
                    <p>Capricon is committed to diverse panelist representation on our program items. To help us do that, please consider filling in the following OPTIONAL items of demographic information. All answers will be kept strictly confidential.</p>
					<label for="ethnicity" class="fieldLabelShort" >Race/Ethnicity: </label><br />
					<input type="text" name="ethnicity" id="ethnicity" maxlength="100" style="width: 98%;" value="<?php echo $info["Ethnicity"]; ?>" /><br />
                    <label for="gender" class="fieldLabelShort">Gender: </label><br />
                    <input type="text" name="gender" id="gender" maxlength="100" style="width: 98%;" value="<?php echo $info["Gender"]; ?>" /><br />
                    <label for="age" class="fieldLabelShort">Age Range: </label>
                    <select id="age" name="age" style="width: 30%">
                        <option value="NoAnswer">No Answer</option>
                        <option value="Under18">Under 18 Years</option>
                        <option value="YoungAdult">18 to 24 Years</option>
                        <option value="Adult">25 Years and Over</option>
                    </select><br />
                    <label for="orientation" class="fieldLabelShort">Sexual Orientation: </label><br />
                    <input type="text" name="orientation" id="orientation" maxlength="100" style="width: 98%;" value="<?php echo $info["Orientation"]; ?>" /><br /><br />
					<input type="submit" name="saveOnly" value="Save Information" /> 
					<input style="float: right;" type="submit" name="saveContinue" value="Save Information and Continue" /><br />
					<input type="hidden" name="task" id="task" value="Page1" />
					<input type="hidden" name="expertiseValues" id="expertiseValues" value="" />
					<input type="hidden" name="expertiseText" id="expertiseText" value="" />
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