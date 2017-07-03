<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("ArtShowStaff"))
		header('Location: index.php');
	
	$year = date("n") >= 3 ? date("Y") + 1: date("Y");
	$result = $db->query("SELECT p.PeopleID, p.LastName, CONCAT(p.FirstName, ' ', p.LastName) AS Name, ad.DisplayName, CASE WHEN m.Permission IS NULL THEN 0 ELSE 1 END AS Artist, p.Email, " . 
        "CASE WHEN ap.ArtistAttendingID IS NOT NULL THEN 1 ELSE 0 END AS Applied, ap.ArtistAttendingID FROM People p LEFT OUTER JOIN Permissions m ON p.PeopleID = m.PeopleID AND " . 
        "m.Permission = 'Artist' LEFT OUTER JOIN ArtistDetails ad ON p.PeopleID = ad.PeopleID LEFT OUTER JOIN ArtistPresence ap ON ad.ArtistID = ap.ArtistID AND ap.Year = $year " . 
        "WHERE p.PeopleID IN (SELECT PeopleID FROM PeopleInterests WHERE Interest = 'ArtShow') " . 
        "UNION SELECT p.PeopleID, p.LastName, CONCAT(p.FirstName, ' ', p.LastName) AS Name, ad.DisplayName, 1 AS Artist, p.Email, CASE WHEN ap.ArtistAttendingID IS NOT NULL THEN 1 ELSE 0 END AS Applied, " . 
        "ap.ArtistAttendingID FROM People p INNER JOIN Permissions m ON p.PeopleID = m.PeopleID AND m.Permission = 'Artist' LEFT OUTER JOIN ArtistDetails ad ON p.PeopleID = ad.PeopleID " . 
        "LEFT OUTER JOIN ArtistPresence ap ON ad.ArtistID = ap.ArtistID AND ap.Year = $year ORDER BY LastName");
	
	$people = array();
	while($row = $result->fetch_array())
		$people[] = $row;
	$result->close();	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Manage Artists</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
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
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Manage Artists</h1>
			<p>The following is a list of people who have expressed interest in the Art Show or are presently considered Artists
			by the Registration system.</p>
			<p>To authorize/deauthorize people as Artists, check or uncheck the "Artist?" box as appropriate then click the button
			below the list. <?php if(DoesUserBelongHere("ArtShowLead")) { ?>If the artist has applied for this year's
            show, the "Manage Inventory" link will let you view and update their art inventory.<?php } ?></p>
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
			<h1>Find Specific Person</h1>
			<p>In the event you suspect an artist has an account but has not indicated interest in the Art Show on their account,
			you may use this form to search for them. Artists added this way will receive an email if that option is checked above.</p>
			<form id="search_form" method="post">
				<div style="width: 50%; float: left;">
					<label for="email">Email: <input type="text" id="email" /></label><br />
					<label>Sort By: <select id="sort" name="sort" style="width: 27%;">
						<option value="LastName" selected>Last Name</option>
						<option value="FirstName">First Name</option>
						<option value="Email">Email Address</option>
						<option value="PeopleID">ID Number</option>
						<option value="BadgeName">Badge Name</option>
					</select></label><label><input type="checkbox" id="sortDesc" name="sortDesc" />Descending</label>
					<input type="Submit" value="Clear" onclick="resetFields(); return false;" /><input type="Submit" value="Search" />
				</div>
				<div style="width: 50%; float: left;">
					<label for="lastname">Last Name: <input type="text" id="lastname" /></label><br />
					<label for="badgename">Badge Name: <input type="text" id="badgename" /></label><br />
					<br />
				</div>
			</form>
			<form id="actionForm" method="post">
			<div id="resultBlock"></div>
			<input id="givePermissions" name="givePermissions" type="submit" value="Give Artist Permission" disabled/>
			</form>
			<span class="clearfix" id="updateFromSearchMessage" style="font-size: 1.05em; font-weight: bold;">&nbsp;</span>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>