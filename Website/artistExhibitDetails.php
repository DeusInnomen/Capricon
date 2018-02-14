<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("Artist"))
		header('Location: index.php');
	else
	{
		$year = date("n") >= 3 ? date("Y") + 1: date("Y");
		$capriconYear = $year - 1980;
		$result = $db->query("SELECT ArtistID FROM ArtistDetails WHERE PeopleID = " . $_SESSION["PeopleID"]);
		if($result->num_rows == 0)
			header('Location: artistInformation.php');
		else
		{
			$result->close();
			
			$result = $db->query("SELECT ap.ArtistAttendingID, ap.ArtistNumber, ap.IsAttending, ap.AgentName, ap.AgentContact, ap.ShippingPref, ap.ShippingAddress, ap.NeedsElectricity, ap.NumTables, ap.NumGrid, ap.HasPrintShop, ap.Status, ap.StatusReason FROM ArtistPresence ap INNER JOIN ArtistDetails ad ON ad.ArtistID = ap.ArtistID WHERE ap.Year = $year AND ad.PeopleID = " . $_SESSION["PeopleID"]);
			
			if($result->num_rows > 0)
			{
				$request = $result->fetch_array();
				$result->close();
			}
			else
			{
				$request = array();
				$request["ArtistAttendingID"] = "";
				$request["IsAttending"] = true;
				$request["AgentName"] = null;
				$request["AgentContact"] = null;
				$request["ShippingPref"] = null;
				$request["ShippingAddress"] = null;
				$request["NeedsElectricity"] = false;
				$request["NumTables"] = 0;
				$request["NumGrid"] = 0;
				$request["HasPrintShop"] = false;
				$request["Status"] = "";
			}
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Request Art Show Space</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(":submit").click(function () {
				if(this.name == "updateInfo") window.location = "artistInformation.php";
			});
			$("select#shippingPref option").each(function() { this.selected = (this.value == "<?php echo $request["ShippingPref"]; ?>"); });
			$("#requestForm :radio").click(function () {
				var method = $("#requestForm input[name=delivery]:checked").val();
				if(method == "isAttending")
				{
					$("#shippingInfo").children().prop("disabled", true);
					$("#agentInfo").children().prop("disabled", true);
				}
				else if(method == "mailingIn") 
				{
					$("#shippingInfo").children().prop("disabled", false);
					$("#agentInfo").children().prop("disabled", true);
				}
				else
				{
					$("#shippingInfo").children().prop("disabled", true);
					$("#agentInfo").children().prop("disabled", false);
				}
			});
			$("#requestForm").submit(function () {
				if(($("#numTables").val() == "0" || $("#numTables").val() == "") && ($("#numGrid").val() == "0" || $("#numGrid").val() == ""))
				{
					$("#accountSaveMessage").addClass("errorMessage");
					$("#accountSaveMessage").html("You must declare how many tables and/or gridwall you need.");
					return false;
				}
				if(!$("#policiesRead").is(":checked"))
				{
					$("#accountSaveMessage").addClass("errorMessage");
					$("#accountSaveMessage").html("You must check the box indicating you've read the Art Show Policies first.");
					return false;
				}
				$("#requestForm :input").prop("readonly", true);
				$("#notes").prop("readonly", true);
				$("#accountSaveMessage").removeClass("goodMessage");
				$("#accountSaveMessage").removeClass("errorMessage");
				$("#accountSaveMessage").html("&nbsp;");
				$.post("doArtistFunctions.php", $(this).serialize(), function(result) {
					if(result.success)
					{
						$("#accountSaveMessage").addClass("goodMessage");
						$("#accountSaveMessage").html(result.message);
					}
					else
					{
						$("#accountSaveMessage").addClass("errorMessage");
						$("#accountSaveMessage").html(result.message);
					}
					$("#requestForm :input").prop("readonly", false);
					$("#notes").prop("readonly", false);
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
		<h1>Exhibiting Details for Capricon <?php echo $capriconYear; ?></h1>
			<div style="margin-bottom: 30px;">
				<form id="requestForm" class="accountForm" method="post" action="">
					<input type="hidden" id="task" name="task" value="SubmitRequest">
		<?php if($request["Status"] != "") { ?>
					<div class="headertitle">Request Status</div>
					<p>Your request for space in the Art Show is <span style="font-size: 1.2em; font-weight: bold;"><?php echo $request["Status"]; ?></span>.<br />
					<?php if(isset($request["StatusReason"])) echo 'Reason Given for Status: <span style="font-weight: bold;">' . $request["StatusReason"] . "</span><br />"; ?>
					<?php if($request["Status"] == "Approved") echo '<br />You may now submit information about your pieces being shown at the <a href="artistSubmissions.php">Artwork Inventory</a> page.<br />'; ?>
					</p>
					<?php } ?>
					<div class="headertitle">About My Artwork</div>
					<label for="needsElectricity"><input type="checkbox" name="needsElectricity" id="needsElectricity" <?php echo ($request["NeedsElectricity"] ? "checked" : ""); ?> />Some of my work will require electricity.</label></br />
					<label for="hasPrintShop"><input type="checkbox" name="hasPrintShop" id="hasPrintShop" <?php echo ($request["HasPrintShop"] ? "checked" : ""); ?> />I have items to sell in the Print Shop.</label></br />
					<label for="numTables">Anticipated # of 1.5' by 6' Tables Needed: <input type="number" name="numTables" id="numTables" min="0" max="9" value="<?php echo $request["NumTables"]; ?>" /></label></br />
					<label for="numGrid">Anticipated # of 4' Wide by 7' Tall Gridwall Panels Needed: <input type="number" name="numGrid" id="numGrid" min="0" max="9" value="<?php echo $request["NumGrid"]; ?>" /></label></br />
					<span style="font-style: italic; font-size: 0.9em;">A reminder: this is a gallery-style show. You are reserving auction display space only, not vendor's space.</span>
					<div class="headertitle">Method of Delivery and Handling</div>
					<label for="isAttending" class="fieldLabelShort"><input type="radio" name="delivery" id="isAttending" value="isAttending" 
						<?php echo (isset($request["IsAttending"]) ? "checked" : ""); ?> />I will be attending the convention in person.</label><br /><br />
					<label for="mailingIn" class="fieldLabelShort"><input type="radio" name="delivery" id="mailingIn" value="mailingIn"
						<?php echo (isset($request["ShippingPref"]) ? "checked" : ""); ?> />I will be shipping the pieces to Capricon:</label><br />
					<div id="shippingInfo">
						<label for="shippingPref" class="fieldLabelShort" >Preferred Shipping Method: </label><br />
						<select id="shippingPref" name="shippingPref" style="width: 25%" <?php echo (!isset($request["ShippingPref"]) ? "disabled" : ""); ?> >
							<option value="USPS" selected>USPS</option>
							<option value="UPS" selected>UPS</option>
							<option value="FedEx" selected>FedEx</option>
							<option value="DHL" selected>DHL</option>
						</select><br />
						<label for="shippingAddress" class="fieldLabelShort" >Shipping Address: </label><br />
						<input type="text" name="shippingAddress" id="shippingAddress" style="width: 98%;" 
							value="<?php echo $request["ShippingAddress"]; ?>" <?php echo (!isset($request["ShippingPref"]) ? "disabled" : ""); ?> /><br /><br />
					</div>
					<label for="agenting" class="fieldLabelShort"><input type="radio" name="delivery" id="agenting" value="agenting"
						<?php echo (isset($request["AgentName"]) ? "checked" : ""); ?> />I will be authorizing an Agent to operate on my behalf:</label><br />
					<div id="agentInfo">
						<label for="agentName" class="fieldLabelShort" >Agent Name: </label><br />
						<input type="text" name="agentName" id="agentName" style="width: 98%;" value="<?php echo $request["AgentName"]; ?>" <?php echo (!isset($request["AgentName"]) ? "disabled" : ""); ?> /><br />
						<label for="agentContact" class="fieldLabelShort" >Contact Info for Agent (Phone or Email): </label><br />
						<input type="text" name="agentContact" id="agentContact" style="width: 98%;" value="<?php echo $request["AgentContact"]; ?>" <?php echo (!isset($request["AgentName"]) ? "disabled" : ""); ?> /><br />
					</div>
					<div class="headertitle">Additional Notes</div>
					<textarea id="notes" name="notes" maxlength="500" rows="4" style="width: 98%;" ></textarea><br /><br />
					<label style="font-weight: bold;"><input type="checkbox" name="policiesRead" id="policiesRead" <?php echo (($request["Status"] != "Pending" && $request["Status"] != "") ? "checked" : ""); ?> /> I acknowledge that I have read the <a href="http://capricon.org/docs/Capricon-Art-Policies.pdf" target="_new">Art Show's Policies</a>. </label><br />
					<input type="submit" name="saveRequest" value="Save Request" /> 
					<input type="submit" name="updateInfo" value="Update Artist Information">
					<br />
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