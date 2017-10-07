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
		$result = $db->query("SELECT ArtistID, DisplayName, LegalName, IsPro, IsEAP, CanPhoto, Website, ArtType, Notes FROM " .
			"ArtistDetails WHERE PeopleID = " . $_SESSION["PeopleID"]);
		
		if($result->num_rows > 0)
		{
			$info = $result->fetch_array();
			$result->close();
		}
		else
		{
			$info = array();
			$info["ArtistID"] = "";
			$info["DisplayName"] = $_SESSION["FullName"];
			$info["LegalName"] = $_SESSION["FullName"];
			$info["IsPro"] = false;
			$info["IsEAP"] = false;
			$info["CanPhoto"] = false;
			$info["Website"] = "";
			$info["ArtType"] = "";
			$info["Notes"] = "";
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Artist Information</title>
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
			$("#detailsForm").submit(function () {
				$("#detailsForm :input").prop("readonly", true);
				$("#notes").prop("readonly", true);
				$("#accountSaveMessage").removeClass("goodMessage");
				$("#accountSaveMessage").removeClass("errorMessage");
				$("#accountSaveMessage").html("&nbsp;");
				$.post("doSaveArtistDetail.php", $(this).serialize(), function(result) {
					if(result.success)
					{
						if(result.doRedirect)
							window.location = "artistExhibitDetails.php";
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
					$("#detailsForm :input").prop("readonly", false);
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
		<div class="centerboxnarrow">
			<div style="margin-bottom: 30px;">
				<form id="detailsForm" class="accountForm" method="post" action="">
					<h1>Artist Information</h1>
					<label for="displayName" class="fieldLabelShort" >Display Name or Alias: </label><br />
					<input type="text" name="displayName" id="displayName" placeholder="Required" style="width: 98%;" value="<?php echo $info["DisplayName"]; ?>" /><br />
					<label for="legalName" class="fieldLabelShort" >Legal Name (Used for Payment Checks): </label><br />
					<input type="text" name="legalName" id="legalName" placeholder="Required" style="width: 98%;" value="<?php echo $info["LegalName"]; ?>" /><br />
					<label for="isPro"><input type="checkbox" name="isPro" id="isPro" <?php echo ($info["IsPro"] ? "checked" : ""); ?> />I am a Professional Artist</label></br />
					<label for="isEAP"><input type="checkbox" name="isEAP" id="isEAP" <?php echo ($info["IsEAP"] ? "checked" : ""); ?> />I wish to register with the Emerging Artist Program</label> -- <a href="http://capricon.org/capricon37/wp-content/uploads/2016/11/Cap37EAPflyer.pdf" target="_blank">More Info</a></br />
					<label for="canPhoto"><input type="checkbox" name="canPhoto" id="canPhoto" <?php echo ($info["CanPhoto"] ? "checked" : ""); ?> >The convention has permission to photograph my artwork.</label><br /><br />
					<label for="website" class="fieldLabelShort">Website: </label><br />
					<input type="url" name="website" id="website" style="width: 98%;" value="<?php echo $info["Website"]; ?>" /><br />
					<label for="artType" class="fieldLabelShort">Type of Art: </label><br />
					<input type="text" name="artType" id="artType" style="width: 98%;" value="<?php echo $info["ArtType"]; ?>" /><br />
					<label for="notes" class="fieldLabelShort">Additional Notes: </label><br />
					<textarea id="notes" name="notes" maxlength="500" rows="4" style="width: 98%;"><?php echo $info["Notes"]; ?></textarea><br /><br />
					<input type="submit" name="saveOnly" value="Save Information" /> 
					<input style="float: right;" type="submit" name="saveContinue" value="Save Information and Continue" /><br />
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