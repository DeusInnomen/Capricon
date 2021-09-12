<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("DealerStaff"))
		header('Location: /index.php');
		
	$year = date("n") >= 3 ? date("Y") + 1: date("Y");
	$result = $db->query("SELECT dp.DealerPresenceID, dd.DisplayName, dd.CompanyName, p.Email, dd.LegalName, dd.MerchType, dp.NeedsElectricity, dp.DealerItemCostID AS DealerItemCostID, dp.Notes, dp.Status, dp.StatusReason, di.Name AS TableRequest FROM DealerPresence dp INNER JOIN DealerDetails dd ON dp.DealerID = dd.DealerID INNER JOIN People p ON dd.PeopleID = p.PeopleID INNER JOIN DealerItemCosts dic ON dp.DealerItemCostID = dic.DealerItemCostID INNER JOIN DealerItems di ON dic.DealerItemID = di.DealerItemID WHERE dp.Year = $year ORDER BY dd.DisplayName");
	
	$requests = array();
	while($row = $result->fetch_array())
		$requests[] = $row;
	$result->close();
	
?>
<!--/*
loop through all records
if status is null or pending or waitlist, show record
else
	if pendwaitonly is checked and this is not (null or pending or waitlist), hide record, else show
*/
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Manage Dealer Requests</title>
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
			$("#dealerRequestForm table :input:checkbox").click(function () {
				if($("#dealerRequestForm table :input:checkbox:checked").length > 0)
				{
					$("#dealerRequestForm :input[type!='checkbox']").removeAttr("disabled");
					$("#clearReason").removeAttr("disabled");
				}
				else
				{
					$("#dealerRequestForm :input[type!='checkbox']").attr("disabled", "disabled");
					$("#clearReason").attr("disabled", "disabled");
				}
			});
			$("#showPendingOnly").click(function(e) {
				var pendingOnly = $("#showPendingOnly").is(":checked");
				$("#dealerRequestForm tr").each(function() {
					if($(this).attr("status") == null || $(this).attr("status") == "Pending" || $(this).attr("status") == "Waitlist")
						$(this).show();
					else
						if(pendingOnly) $(this).hide(); else $(this).show();
				});
			});
		});
		
		function doUpdate() {
			var requests = "";
			$("#dealerRequestForm :input[name=select]:checkbox:checked").each(function() {
				requests += ", " + $(this).attr("id");
			});
			if(requests.length > 0) requests = requests.substring(2);
			
			var action = $("#action").val();
			if(action == "Delete")
				if(!confirm("Are you sure you wish to delete these requests?"))
					return;
			
			var reason = $("#reason").val();
			var clearReason = $("#clearReason").is(":checked");
			var sendEmail = $("#sendEmail").is(":checked");
			$("#dealerRequestForm :input").prop("readonly", true);

			$.post("doDealerFunctions.php", { task: "UpdateStatus", requests: requests, action: action, reason: reason, clearReason: clearReason, sendEmail: sendEmail }, function(result) {
					$("#dealerRequestForm :input").removeProp("readonly");
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
			<h1>View Dealer Requests</h1>
			<p>The following is a list of dealers who have expressed interest in vending at the next Capricon's Dealers Room.</p>
			<div class="standardTable">
				<label for="showPendingOnly"><input type="checkbox" id="showPendingOnly" name="showPendingOnly" checked> Show Pending Requests Only</label>
				<label for="sendEmail" style="margin-left: 30px;"><input type="checkbox" id="sendEmail" name="sendEmail"> Send Email After Update (When Possible)</label>
				<form id="dealerRequestForm" method="post">
				<table>
					<tr><th>Select</th><th>Display Name</th><th>Company Name</th><th>Email</th><th>Power?</th><th># Tables</th><th>Status</th><th>Reason</th></tr>

					<?php
					foreach($requests as $request)
					{
						echo "<tr class=\"masterTooltip\" title=\"Legal Name: " . $request["LegalName"] . "<br>Merchandise Type: " . $request["MerchType"];
						if(!empty($request["StatusReason"]))
							echo "<br>Status Reason: " . $request["StatusReason"];
						echo "<br>Notes: " . $request["Notes"] . "\" status=\"" . $request["Status"] . "\" " . 
							($request["Status"] == "Pending" ? "" : "style=\"display: none;\"") . ">";
						
						echo "<td style=\"text-align: center;\"><input type=\"checkbox\" name=\"select\" id=\"" . 
							$request["DealerPresenceID"] . "\"/></td>";
						echo "<td>" . $request["DisplayName"] . "</td>";
						echo "<td>" . $request["CompanyName"] . "</td>";
						echo "<td>" . $request["Email"] . "</td>";
						echo "<td>" . ($request["NeedsElectricity"] == 1 ? "Yes" : "No") . "</td>";
						echo "<td>" . $request["TableRequest"] . "</td>";
						echo "<td>" . $request["Status"] . "</td>";
						echo "<td>" . $request["StatusReason"] . "</td>";
						echo "</tr>\r\n";
					} ?>

				</table>
				<br />
				With Selected: <select id="action" name="action" style="width: 165px;" disabled>
					<option value="Approved" selected>Approve Requests</option>
					<option value="Rejected">Reject Requests</option>
					<option value="Waitlist">Waitlist Requests</option>
					<option value="NoChange">Update Reason Only</option>
					<option value="Delete">Delete Requests</option>
				</select><label for="reason" style=" margin-left: 15px;">Reason: <input type="text" id="reason" name="reason" style="width: 40%;" maxlength="100" placeholder="Optional" disabled></label>
				<label for="clearReason"><input type="checkbox" id="clearReason" name="clearReason" disabled> Clear Reason</label><br /><br />
				<input type="submit" id="updateStatus" name="updateStatus" value="Apply Action to Requests" onclick="doUpdate(); return false;" disabled><br />
				<span id="updateMessage" style="font-size: 1.05em; font-weight: bold;">&nbsp;</span>
				<p style="font-style:italic;">Hover over any entry above to see additional information.</p>
				<br />
				</form>
			</div>
			<div class="goback">
				<a href="/index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>