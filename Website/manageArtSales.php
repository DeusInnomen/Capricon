<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("superadmin"))
		header('Location: index.php');

		
	$year = date("n") >= 3 ? date("Y") + 1: date("Y");
	$result = $db->query("SELECT * from ArtSubmissions");
	
	$people = array();
	while($row = $result->fetch_array())
		$people[] = $row;
	$result->close();	
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Sales Debug</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<?
	/*
	<script type="text/javascript">
		$(document).ready(function() {
			$(".standardTable tr").click(function(e) {
				if(e.target.type !== "checkbox")
					$(":checkbox", this).trigger("click");
			}); 
			$("#search_form").submit(function () {
				var email = $("#email").val();
				var lastname = $("#lastname").val();
				var badgename = $("#badgename").val();
				var sort = $("#sort").val();
				if($("#sortDesc").is(':checked')) sort += " DESC";
				$.post("doArtistFunctions.php", { task: "FindPeople", email: email, lastname: lastname, badgename: badgename, sort: sort }, function(result) {
					$("#resultBlock").html(result);					
				});
				return false;
			});
			$("#actionForm").on("click", ":input:checkbox", function () {
				if($("#actionForm :input:checkbox:checked").length > 0)
					$("#givePermissions").removeAttr("disabled");
				else
					$("#givePermissions").attr("disabled", "disabled");
			});
			$("#actionForm").submit(function() {
				if($("#actionForm :input:checkbox:checked").length == 0) 
					return false;
				
				$("#givePermissions").attr("disabled", "disabled");
				var toAdd = "";
				$("#actionForm :input:checkbox:checked").each(function() {
					toAdd += "|" + $(this).val();
				});
				toAdd = toAdd.substring(1);
				var sendEmail = $("#sendNotices").is(":checked");
				
				$.post("doArtistFunctions.php", { task: "UpdatePermissions", add: toAdd, remove: "", sendEmail: sendEmail }, function(result) {
					if(result.success)
						location.reload();
					else
					{
						$("#givePermissions").removeProp("disabled");
						$("#updateFromSearchMessage").html(result.message);
					}
				}, 'json');
				return false;
			});
		});
		function updateArtistList() {
			var toAdd = "";
			$("#artistForm :input:checkbox:checked").each(function() {
				if($(this).attr("current") == "0")
					toAdd += "|" + $(this).attr("id");
			});
			if(toAdd.length > 0) toAdd = toAdd.substring(1);

			var toRemove = "";
			$("#artistForm :input:checkbox:not(:checked)").each(function() {
				if($(this).attr("current") == "1")
					toRemove += "|" + $(this).attr("id");
			});
			if(toRemove.length > 0) toRemove = toRemove.substring(1);
			
			if (toAdd.length == 0 && toRemove.length == 0)
				$("#updateMessage").html("No changes were made.");
			else
			{
				var sendEmail = $("#sendNotices").is(":checked");
				$("#updateArtists").prop("disabled", true);
				$("#updateMessage").html("&nbsp;");
				$.post("doArtistFunctions.php", { task: "UpdatePermissions", add: toAdd, remove: toRemove, sendEmail: sendEmail }, function(result) {
					$("#updateArtists").removeProp("disabled");
					$("#artistForm :input:checkbox").each(function() {
						$(this).attr("current", ($(this).is(":checked") ? "1" : "0"));
					});
					$("#updateMessage").html(result.message);
				}, 'json');
			}
		}
		function resetFields() {
			$("#email").val("");
			$("#lastname").val("");
			$("#badgename").val("");
		}

	</script>
	*/
	?>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Art Sales Debug</h1>
			<p>These buttons control running sales functions as if this were the POS system</p>
			<p>Then we can force test Invoicing as well.</p>
			<div class="standardTable">
				<form id="artistForm" method="post">
				<table>
					<tr><th>Name</th><th>Display Name</th></th><th>Artist?</th><th>Email</th><th>Applied?</th><th>Inventories</th></tr>
<?php
					foreach($people as $person)
					{
						echo "<tr><td>" . $person["Name"] . "</td><td>" . $person["DisplayName"] . "</td><td style=\"text-align: center;\"><input type=\"checkbox\" id=\"" . 
                            $person["PeopleID"] . "\" current=\"" . $person["Artist"] . "\" " .
                            ($person["Artist"] == 1 ? "checked" : "") . "/></td><td>" . $person["Email"] . "</td><td style=\"text-align: center;\"><input type=\"checkbox\" " .
                            ($person["Applied"] == 1 ? "checked" : "") . " disabled /></td><td style=\"text-align: center;\">" . 
                            ($person["Applied"] == 1 && DoesUserBelongHere("ArtShowLead") ? "<a href=\"artistSubmissions.php?attendID=" . $person["ArtistAttendingID"] . 
                            "\">Manage Inventory</a>" : "") . "</td></tr>\r\n";
					} ?>
				</table>
				<br />
				</form>
				<label for="sendNotices"><input type="checkbox" id="sendNotices" name="sendNotices"> Send Email Notification to People
				Gaining the Artist Permission (When Possible)</label><br />
				<input type="submit" id="updateArtists" onclick="updateArtistList(); return false;" value="Update Artist List"><br/>
				<span id="updateMessage" style="font-size: 1.05em; font-weight: bold;">&nbsp;</span>
			</div>			
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>