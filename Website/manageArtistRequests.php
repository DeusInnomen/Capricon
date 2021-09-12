<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("ArtShowStaff"))
		header('Location: index.php');
		
	$year = date("n") >= 3 ? date("Y") + 1: date("Y");
	$result = $db->query("SELECT ap.ArtistAttendingID, ad.DisplayName, p.Email, ad.LegalName, ad.IsPro, ad.ArtType, ap.IsAttending, ap.AgentName, ap.AgentContact, ap.ShippingPref, " 
        . "ap.ShippingAddress, ap.NeedsElectricity, ap.NumTables, ap.NumGrid, ap.HasPrintShop, ap.Notes, ap.Status, ap.StatusReason, ad.IsEAP, ap.FeesWaived FROM ArtistPresence ap " 
        . "INNER JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID INNER JOIN People p ON ad.PeopleID = p.PeopleID WHERE ap.Year = $year ORDER BY ad.DisplayName");
	
	$requests = array();
	while($row = $result->fetch_array())
		$requests[] = $row;
	$result->close();
	
	$result = $db->query("SELECT ars.ArtistAttendingID, COUNT(ars.ArtID) AS Pieces FROM ArtSubmissions ars JOIN ArtistPresence ap ON ars.ArtistAttendingID = ap.ArtistAttendingID WHERE ap.Year = $year GROUP BY ap.ArtistAttendingID");
	$pieces = array();
	while($row = $result->fetch_array())
		$pieces[$row["ArtistAttendingID"]] = $row["Pieces"];
	$result->close();

    $result = $db->query("SELECT ArtistAttendingID FROM ArtistPresence WHERE Year = $year AND ArtistNumber = 1");
    if($result->num_rows > 0) {
        $hasGOH = true;
        $result->close();
    }
    else
        $hasGOH = false;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Manage Artist Requests</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
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
			$("#artistRequestForm table :input[name=select]:checkbox").click(function () {
				if($("#artistRequestForm table :input[name=select]:checkbox:checked").length > 0)
				{
					$("#artistRequestForm :input[type!='checkbox']").removeAttr("disabled");
					$("#clearReason").removeAttr("disabled");
					$("#waiveFees").removeAttr("disabled");
					$("#isGOH").removeAttr("disabled");
				}
				else
				{
					$("#artistRequestForm :input[type!='checkbox']").attr("disabled", "disabled");
					$("#clearReason").attr("disabled", "disabled");
					$("#waiveFees").attr("disabled", "disabled");
					$("#isGOH").attr("disabled", "disabled");
				}
			});
			$("#showPendingOnly").click(function(e) {
				var pendingOnly = $("#showPendingOnly").is(":checked");
				$("#artistRequestForm tr").each(function() {
					if($(this).attr("status") == null || $(this).attr("status") == "Pending")
						$(this).show();
					else
						if(pendingOnly) $(this).hide(); else $(this).show();
				});
			});
		});

		function doUpdate() {
			var requests = "";
			$("#artistRequestForm :input[name=select]:checkbox:checked").each(function() {
				requests += ", " + $(this).attr("id");
			});
			if(requests.length > 0) requests = requests.substring(2);

			var action = $("#action").val();
			if(action == "Delete")
				if(!confirm("Are you sure you wish to delete these requests? All submitted art will also be deleted."))
					return;

			var reason = $("#reason").val();
			var clearReason = $("#clearReason").is(":checked");
			var waiveFees = $("#waiveFees").is(":checked");
			var sendEmail = $("#sendEmail").is(":checked");
            <?php if(!$hasGOH) { ?>
            var isGOH = $("#isGOH").is(":checked");
            if ($("#artistRequestForm table :input[name=select]:checkbox:checked").length > 1 && isGOH) {
                $("#updateMessage").html("You should only have one box checked when declaring who the GOH is.");
                return false;
            }
            <?php } else { ?>
            var isGOH = false;
            <?php } ?>
			$("#artistRequestForm :input").prop("readonly", true);

			$.post("doArtistFunctions.php", { task: "UpdateStatus", requests: requests, action: action, reason: reason, clearReason: clearReason, sendEmail: sendEmail, waiveFees: waiveFees, isGOH: isGOH }, function(result) {
					$("#artistRequestForm :input").removeProp("readonly");
					if(result.success)
						location.reload();
					else
						$("#updateMessage").html(result.message);
			}, 'json');
		}
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Manage Artist Requests</h1>
			<p>The following is a list of artists who have expressed interest in showing at the next Capricon's Art Show.</p>
			<div class="standardTable">
				<label for="showPendingOnly"><input type="checkbox" id="showPendingOnly" name="showPendingOnly" checked> Show Pending Requests Only</label>
				<label for="sendEmail" style="margin-left: 30px;"><input type="checkbox" id="sendEmail" name="sendEmail"> Send Email After Update (When Possible)</label>
				<form id="artistRequestForm" method="post">
				<table>
					<tr><th>Select</th><th>Display Name</th><th>Email</th><th>Power?</th><th># Tables</th><th># Grid</th><th>EAP?</th><th>Prints?</th><th>Status</th><th>Pieces</th></tr>
<?php
					foreach($requests as $request)
					{
						echo "<tr class=\"masterTooltip\" title=\"Legal Name: " . $request["LegalName"] . "<br>Is Professional? " . 
							($request["IsPro"] == 1 ? "Yes" : "No") . "<br>Art Type: " . $request["ArtType"];
						if($request["AgentName"] !== null)
							echo "<br>Agent Contact: " . $request["AgentContact"];
						elseif($request["ShippingPref"] !== null)
							echo "<br>Shipping Address: " . $request["ShippingAddress"];
						if(!empty($request["StatusReason"]))
							echo "<br>Status Reason: " . $request["StatusReason"];
						echo "<br>Notes: " . $request["Notes"]; 
                        if($request["FeesWaived"] == 1)
                            echo "<br>Note: Fees Have Been Waived For This Exhibit";
                        echo "\" status=\"" . $request["Status"] . "\" " . 
							($request["Status"] == "Pending" ? "" : "style=\"display: none;\"") . ">";
						echo "<td style=\"text-align: center;\"><input type=\"checkbox\" name=\"select\" id=\"" . 
							$request["ArtistAttendingID"] . "\"/></td><td>" . $request["DisplayName"] . "</td><td>";
						//if($request["IsAttending"] == 1)
						//	echo "Attending In Person";
						//elseif($request["AgentName"] !== null)
						//	echo "Agent: " . $request["AgentName"];
						//else
						//	echo "Shipping via " . $request["ShippingPref"];
						echo $request["Email"];
						echo "</td><td>" . ($request["NeedsElectricity"] == 1 ? "Yes" : "No") . "</td><td>" . $request["NumTables"] . "</td><td>" . $request["NumGrid"] . "</td><td style=\"text-align: center;\"><input type=\"checkbox\" . " . ($request["IsEAP"] == 1 ? "checked" : "") . " disabled/></td><td style=\"text-align: center;\"><input type=\"checkbox\" . " . ($request["HasPrintShop"] == 1 ? "checked" : "") . " disabled/></td><td>" . $request["Status"] . "</td><td>" . (isset($pieces[$request["ArtistAttendingID"]]) ? "<b>" . $pieces[$request["ArtistAttendingID"]] . "</b>" : "0") . "</td></tr>\r\n";
					} ?>
				</table>
				With Selected: <select id="action" name="action" style="width: 165px;" disabled>
					<option value="Approved" selected>Approve Requests</option>
					<option value="Rejected">Reject Requests</option>
					<option value="NoChange">Update Reason Only</option>
					<option value="Delete">Delete Requests</option>
				</select><label for="reason" style=" margin-left: 15px;">Reason: <input type="text" id="reason" name="reason" style="width: 40%;" maxlength="100" placeholder="Optional" disabled></label>
				<label for="clearReason"><input type="checkbox" id="clearReason" name="clearReason" disabled> Clear Reason</label><br />
                <label for="waiveFees"><input type="checkbox" id="waiveFees" name="waiveFees" disabled>Waive Fees for Checked Requests</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php if(!$hasGOH) { ?><label for="isGOH"><input type="checkbox" id="isGOH" name="isGOH" disabled>This is the GOH (Set as Artist #1)</label><?php } ?><br />
                <br />
				<input type="submit" id="updateStatus" name="updateStatus" value="Apply Action to Requests" onclick="doUpdate(); return false;" disabled><br />
				<span id="updateMessage" style="font-size: 1.05em; font-weight: bold;">&nbsp;</span>
				<p style="font-style:italic;">Hover over any entry above to see additional information.</p>
				<br />
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