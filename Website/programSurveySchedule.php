<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	else
	{
		$year = date("n") >= 3 ? date("Y") + 1: date("Y");
		$result = $db->query("SELECT Arrival, Departure, MaxPanelsTh, PanelStartTh, PanelEndTh, MaxPanelsFr, PanelStartFr, " . 
			"PanelEndFr, MaxPanelsSa, PanelStartSa, PanelEndSa, MaxPanelsSu, PanelStartSu, PanelEndSu, AvailabilityNotes " . 
			"FROM ProgramSurvey WHERE Year = $year AND PeopleID = " . $_SESSION["PeopleID"]);
		
		if($result->num_rows > 0)
		{
			$info = $result->fetch_array();
			$result->close();
		}
		else
		{
			$info = array();
			$info["Arrival"] = "Wed 8am";
			$info["Departure"] = "Mon 8am";
			$info["MaxPanelsTh"] = "0";
			$info["PanelStartTh"] = "15";
			$info["PanelEndTh"] = "0";
			$info["MaxPanelsFr"] = "0";
			$info["PanelStartFr"] = "8";
			$info["PanelEndFr"] = "0";
			$info["MaxPanelsSa"] = "0";
			$info["PanelStartSa"] = "8";
			$info["PanelEndSa"] = "0";
			$info["MaxPanelsSu"] = "0";
			$info["PanelStartSu"] = "8";
			$info["PanelEndSu"] = "15";
			$info["AvailabilityNotes"] = "";
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
			$("select#arriveDate option").each(function() { this.selected = (this.value == "<?php echo substr($info["Arrival"], 0, 3); ?>"); });
			$("select#arriveTime option").each(function() { this.selected = (this.value == "<?php echo substr($info["Arrival"], 4); ?>"); });
			$("select#departDate option").each(function() { this.selected = (this.value == "<?php echo substr($info["Departure"], 0, 3); ?>"); });
			$("select#departTime option").each(function() { this.selected = (this.value == "<?php echo substr($info["Departure"], 4); ?>"); });
			$("select#maxPanelsTh option").each(function() { this.selected = (this.value == "<?php echo $info["MaxPanelsTh"]; ?>"); });
			$("select#panelStartTh option").each(function() { this.selected = (this.value == "<?php echo $info["PanelStartTh"]; ?>"); });
			$("select#panelEndTh option").each(function() { this.selected = (this.value == "<?php echo $info["PanelEndTh"]; ?>"); });
			$("select#maxPanelsFr option").each(function() { this.selected = (this.value == "<?php echo $info["MaxPanelsFr"]; ?>"); });
			$("select#panelStartFr option").each(function() { this.selected = (this.value == "<?php echo $info["PanelStartFr"]; ?>"); });
			$("select#panelEndFr option").each(function() { this.selected = (this.value == "<?php echo $info["PanelEndFr"]; ?>"); });
			$("select#maxPanelsSa option").each(function() { this.selected = (this.value == "<?php echo $info["MaxPanelsSa"]; ?>"); });
			$("select#panelStartSa option").each(function() { this.selected = (this.value == "<?php echo $info["PanelStartSa"]; ?>"); });
			$("select#panelEndSa option").each(function() { this.selected = (this.value == "<?php echo $info["PanelEndSa"]; ?>"); });
			$("select#maxPanelsSu option").each(function() { this.selected = (this.value == "<?php echo $info["MaxPanelsSu"]; ?>"); });
			$("select#panelStartSu option").each(function() { this.selected = (this.value == "<?php echo $info["PanelStartSu"]; ?>"); });
			$("select#panelEndSu option").each(function() { this.selected = (this.value == "<?php echo $info["PanelEndSu"]; ?>"); });
			$(":submit").click(function () {
				$("#continue").val(this.name == "saveContinue" ? "continue" : "");
			});
			$("#surveyForm").submit(function () {
				$("#surveyForm :input").prop("readonly", true);
				$("#accountSaveMessage").removeClass("goodMessage");
				$("#accountSaveMessage").removeClass("errorMessage");
				$("#accountSaveMessage").html("&nbsp;");
				$.post("doProgramSurveyFunctions.php", $(this).serialize(), function(result) {
					if(result.success)
					{
						if(result.doRedirect)
							window.location = "programSurveyInterests.php";
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
					<h1>Programming Survey -- Part 2</h1>
					<p>What are your estimated arrival and departure times at Capricon? (Days are relative to the week of Capricon.)</p>
					<label for="arriveDate" class="fieldLabelShort" >Arrival: </label>
					<select id="arriveDate" name="arriveDate" style="width: 20%">
						<option value="Wed">Wednesday</option>
						<option value="Thu">Thursday</option>
						<option value="Fri">Friday</option>
						<option value="Sat">Saturday</option>
						<option value="Sun">Sunday</option>
					</select>&nbsp;&nbsp;
					<select id="arriveTime" name="arriveTime" style="width: 10%">
						<option value="8am">8am</option><option value="9am">9am</option><option value="10am">10am</option>
						<option value="11am">11am</option><option value="12pm">12pm</option><option value="1pm">1pm</option>
						<option value="2pm">2pm</option><option value="3pm">3pm</option><option value="4pm">4pm</option>
						<option value="5pm">5pm</option><option value="6pm">6pm</option><option value="7pm">7pm</option>
						<option value="8pm">8pm</option><option value="9pm">9pm</option><option value="10pm">10pm</option>
					</select>&nbsp;&nbsp;
					<label for="departDate" class="fieldLabelShort" >Depart: </label>
					<select id="departDate" name="departDate" style="width: 20%">
						<option value="Fri">Friday</option>
						<option value="Sat">Saturday</option>
						<option value="Sun">Sunday</option>
						<option value="Mon">Monday</option>
					</select>&nbsp;&nbsp;
					<select id="departTime" name="departTime" style="width: 10%">
						<option value="8am">8am</option><option value="9am">9am</option><option value="10am">10am</option>
						<option value="11am">11am</option><option value="12pm">12pm</option><option value="1pm">1pm</option>
						<option value="2pm">2pm</option><option value="3pm">3pm</option><option value="4pm">4pm</option>
						<option value="5pm">5pm</option><option value="6pm">6pm</option><option value="7pm">7pm</option>
						<option value="8pm">8pm</option><option value="9pm">9pm</option><option value="10pm">10pm</option>
					</select><br><br>					
					<h3>Your Panel Availabilities</h3>
					<div class="headertitle">Thursday</div>
					<label for="maxPanelsTh" class="fieldLabelShort" >What is the maximum number of panels you would consider? </label>
					<select id="maxPanelsTh" name="maxPanelsTh" style="width: 12%">
						<option value="0">None</option><option value="1">1</option>
						<option value="2">2</option><option value="3">3</option><option value="4">4</option>
						<option value="5">5</option><option value="6">6</option><option value="7">7</option>
						<option value="8">8</option><option value="9">9</option><option value="10">10</option>
					</select><br>
					<label for="panelStartTh" class="fieldLabelShort" >Earliest panel start time? </label>
					<select id="panelStartTh" name="panelStartTh" style="width: 10%">
						<option value="15">3pm</option><option value="16">4pm</option><option value="17">5pm</option>
						<option value="18">6pm</option><option value="19">7pm</option><option value="20">8pm</option>
						<option value="21">9pm</option><option value="22">10pm</option><option value="23">11pm</option>
						<option value="0">12am</option>
					</select>&nbsp;&nbsp;
					<label for="panelEndTh" class="fieldLabelShort" >Latest panel end time? </label>
					<select id="panelEndTh" name="panelEndTh" style="width: 10%">
						<option value="15">3pm</option><option value="16">4pm</option><option value="17">5pm</option>
						<option value="18">6pm</option><option value="19">7pm</option><option value="20">8pm</option>
						<option value="21">9pm</option><option value="22">10pm</option><option value="23">11pm</option>
						<option value="0">12am</option>
					</select><br>
					<div class="headertitle">Friday</div>
					<label for="maxPanelsFr" class="fieldLabelShort" >What is the maximum number of panels you would consider? </label>
					<select id="maxPanelsFr" name="maxPanelsFr" style="width: 12%">
						<option value="0">None</option><option value="1">1</option>
						<option value="2">2</option><option value="3">3</option><option value="4">4</option>
						<option value="5">5</option><option value="6">6</option><option value="7">7</option>
						<option value="8">8</option><option value="9">9</option><option value="10">10</option>
					</select><br>
					<label for="panelStartFr" class="fieldLabelShort" >Earliest panel start time? </label>
					<select id="panelStartFr" name="panelStartFr" style="width: 10%">
						<option value="8">8am</option><option value="9">9am</option><option value="10">10am</option>
						<option value="11">11am</option><option value="12">12pm</option><option value="13">1pm</option>
						<option value="14">2pm</option><option value="15">3pm</option><option value="16">4pm</option>
						<option value="17">5pm</option><option value="18">6pm</option><option value="19">7pm</option>
						<option value="20">8pm</option><option value="21">9pm</option><option value="22">10pm</option>
						<option value="23">11pm</option><option value="0">12am</option>
					</select>&nbsp;&nbsp;
					<label for="panelEndFr" class="fieldLabelShort" >Latest panel end time? </label>
					<select id="panelEndFr" name="panelEndFr" style="width: 10%">
						<option value="8">8am</option><option value="9">9am</option><option value="10">10am</option>
						<option value="11">11am</option><option value="12">12pm</option><option value="13">1pm</option>
						<option value="14">2pm</option><option value="15">3pm</option><option value="16">4pm</option>
						<option value="17">5pm</option><option value="18">6pm</option><option value="19">7pm</option>
						<option value="20">8pm</option><option value="21">9pm</option><option value="22">10pm</option>
						<option value="23">11pm</option><option value="0">12am</option>
					</select><br>
					<div class="headertitle">Saturday</div>
					<label for="maxPanelsSa" class="fieldLabelShort" >What is the maximum number of panels you would consider? </label>
					<select id="maxPanelsSa" name="maxPanelsSa" style="width: 12%">
						<option value="0">None</option><option value="1">1</option>
						<option value="2">2</option><option value="3">3</option><option value="4">4</option>
						<option value="5">5</option><option value="6">6</option><option value="7">7</option>
						<option value="8">8</option><option value="9">9</option><option value="10">10</option>
					</select><br>
					<label for="panelStartSa" class="fieldLabelShort" >Earliest panel start time? </label>
					<select id="panelStartSa" name="panelStartSa" style="width: 10%">
						<option value="8">8am</option><option value="9">9am</option><option value="10">10am</option>
						<option value="11">11am</option><option value="12">12pm</option><option value="13">1pm</option>
						<option value="14">2pm</option><option value="15">3pm</option><option value="16">4pm</option>
						<option value="17">5pm</option><option value="18">6pm</option><option value="19">7pm</option>
						<option value="20">8pm</option><option value="21">9pm</option><option value="22">10pm</option>
						<option value="23">11pm</option><option value="0">12am</option>
					</select>&nbsp;&nbsp;
					<label for="panelEndSa" class="fieldLabelShort" >Latest panel end time? </label>
					<select id="panelEndSa" name="panelEndSa" style="width: 10%">
						<option value="8">8am</option><option value="9">9am</option><option value="10">10am</option>
						<option value="11">11am</option><option value="12">12pm</option><option value="13">1pm</option>
						<option value="14">2pm</option><option value="15">3pm</option><option value="16">4pm</option>
						<option value="17">5pm</option><option value="18">6pm</option><option value="19">7pm</option>
						<option value="20">8pm</option><option value="21">9pm</option><option value="22">10pm</option>
						<option value="23">11pm</option><option value="0">12am</option>
					</select><br>
					<div class="headertitle">Sunday</div>
					<label for="maxPanelsSu" class="fieldLabelShort" >What is the maximum number of panels you would consider? </label>
					<select id="maxPanelsSu" name="maxPanelsSu" style="width: 12%">
						<option value="0">None</option><option value="1">1</option>
						<option value="2">2</option><option value="3">3</option><option value="4">4</option>
						<option value="5">5</option><option value="6">6</option><option value="7">7</option>
						<option value="8">8</option><option value="9">9</option><option value="10">10</option>
					</select><br>
					<label for="panelStartSu" class="fieldLabelShort" >Earliest panel start time? </label>
					<select id="panelStartSu" name="panelStartSu" style="width: 10%">
						<option value="8">8am</option><option value="9">9am</option><option value="10">10am</option>
						<option value="11">11am</option><option value="12">12pm</option><option value="13">1pm</option>
						<option value="14">2pm</option>
					</select>&nbsp;&nbsp;
					<label for="panelEndSu" class="fieldLabelShort" >Latest panel end time? </label>
					<select id="panelEndSu" name="panelEndSu" style="width: 10%">
						<option value="8">8am</option><option value="9">9am</option><option value="10">10am</option>
						<option value="11">11am</option><option value="12">12pm</option><option value="13">1pm</option>
						<option value="14">2pm</option><option value="15">3pm</option>
					</select><br>
					<br>
					<label for="availabilityNotes" class="fieldLabelShort" >Is there anything regarding your time availability that we should be aware of that wasn't covered above?</label><br />
					<input type="text" name="availabilityNotes" id="availabilityNotes" maxlength="200" style="width: 98%;" value="<?php echo $info["AvailabilityNotes"]; ?>" /><br />
					<br><br>
					<input type="submit" name="saveOnly" value="Save Information" /> 
					<input style="float: right;" type="submit" name="saveContinue" value="Save Information and Continue" /><br />
					<input type="hidden" name="task" id="task" value="Page2" />
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