<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("DealerLead"))
    header('Location: index.php');
else
{
    $year = date("n") >= 3 ? date("Y") + 1: date("Y");

    $dealers = array();
    $result = $db->query("SELECT dp.DealerPresenceID, d.CompanyName, IFNULL(d.LegalName, d.CompanyName) AS LegalName, dp.NumTables, dp.ElectricalNeeded, dp.AddedDetails, dp.Status, " 
        . "dp.StatusReason, IFNULL(i.Status, 'Not Created') AS InvoiceStatus, il.Amount AS InvoiceAmount, i.Created AS InvoiceCreated, i.Sent AS InvoiceSent, i.Fulfilled AS InvoiceFulfilled, "
        . "i.Cancelled AS InvoiceCancelled, IFNULL(b.Badges, 0) AS Badges FROM DealerPresence dp JOIN Dealer d ON dp.DealerID = d.DealerID LEFT OUTER JOIN (SELECT i1.InvoiceID, i1.RelatedRecordID, " 
        . "i1.Status, i1.Created, i1.Sent, i1.Fulfilled, i1.Cancelled FROM Invoice i1 LEFT OUTER JOIN Invoice i2 ON i1.RelatedRecordID = i2.RelatedRecordID AND i1.Created < i2.Created AND " 
        . "i1.InvoiceType = 'Dealer' WHERE i2.RelatedRecordID IS NULL) i ON i.RelatedRecordID = dp.DealerPresenceID LEFT OUTER JOIN (SELECT InvoiceID, SUM(Price) + SUM(Tax) AS Amount FROM " 
        . "InvoiceLine GROUP BY InvoiceID) il ON i.InvoiceID = il.InvoiceID LEFT OUTER JOIN (SELECT DealerPresenceID, COUNT(DealerBadgeID) AS Badges FROM DealerBadges GROUP BY DealerPresenceID) b " 
        . "ON b.DealerPresenceID = dp.DealerPresenceID WHERE dp.Year = $year ORDER BY FIELD(dp.Status, 'Pending', 'Waitlist', 'Approved', 'Rejected'), d.CompanyName ASC");
	while($row = $result->fetch_array())
		$dealers[] = $row;
	$result->close();

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Current Dealer Applications</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css" />
    <link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
    <link rel="icon" href="includes/favicon.png" />
    <link rel="shortcut icon" href="includes/favicon.ico" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
    <script type="text/javascript" src="includes/global.js"></script>
    <script type="text/javascript">
		$(document).ready(function() {
			$(".standardTable tr").click(function(e) {
				if(e.target.type !== "checkbox")
					$(":checkbox", this).trigger("click");
			});
			$("#dealersForm table :input:checkbox").click(function () {
				if($("#dealersForm table :input:checkbox:checked").length > 0)
				{
					$("#dealersForm :input[type!='checkbox']").removeAttr("disabled");
					$("#clearReason").removeAttr("disabled");
					$("#noEmail").removeAttr("disabled");
				}
				else
				{
					$("#dealersForm :input[type!='checkbox']").attr("disabled", "disabled");
					$("#clearReason").attr("disabled", "disabled");
					$("#noEmail").attr("disabled", "disabled");
				}
            });
        });

    	function doUpdate() {
			var requests = "";
			$("#dealersForm :input[name=select]:checkbox:checked").each(function() {
				requests += "," + $(this).attr("id");
			});
			if(requests.length > 0) requests = requests.substring(1);
			
			var status = $("#action").val();
			var reason = $("#reason").val();
			var message = $("#message").val();
			var clearReason = $("#clearReason").is(":checked");
			var noEmail = $("#noEmail").is(":checked");
			$("#dealersForm :input").prop("readonly", true);
			$("#clearReason").attr("disabled", "disabled");
			$("#noEmail").attr("disabled", "disabled");

			$.post("doDealerFunctions.php", { task: "UpdateDealerApplications", requests: requests, status: status, reason: reason, clearReason: clearReason, noEmail: noEmail, message: message }, function(result) {
					$("#dealersForm :input").removeProp("readonly");
					$("#clearReason").removeAttr("disabled");
					$("#noEmail").removeAttr("disabled");
					if(result.success)
						location.reload();
					else
						$("#saveMessage").html(result.message);
			}, 'json');
		}
    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxwide">
            <div style="margin-bottom: 30px;">
                <form id="dealersForm" class="accountForm" method="post" action="">
                    <h1>Current Dealer Applications</h1>
                    <h3>Pending Applications</h3>
                    <div class="standardTable">
                        <table>
                            <tr><th>Select</th><th>Company Name</th><th># Tables</th><th>Electric?</th><th>Status</th><th>Reason</th></tr><?php
                            foreach($dealers as $dealer)
                            {
                                if($dealer["Status"] == "Pending" || $dealer["Status"] == "Waitlist") {
                                    echo "<tr class=\"masterTooltip\" title=\"Legal Name: " . $dealer["LegalName"] . "<br>Added Details:<br>" . $dealer["AddedDetails"] . "\">";
                                    echo "<td style=\"text-align: center;\"><input type=\"checkbox\" name=\"select\" id=\"" . $dealer["DealerPresenceID"] . "\"/></td>";
                                    echo "<td>" . $dealer["CompanyName"] . "</td>";
                                    echo "<td>" . $dealer["NumTables"] . "</td>";
                                    echo "<td>" . ($dealer["ElectricalNeeded"] == 1 ? "Yes" : "No") . "</td>";
                                    echo "<td>" . $dealer["Status"] . "</td>";
                                    echo "<td>" . $dealer["StatusReason"] . "</td>";
                                    echo "</tr>\r\n";
                                }
                            } ?>
                        </table>
                    </div>
                    <hr />
                    <h3>Completed Applications</h3>
                    <div class="standardTable">
                        <table>
                            <tr><th>Select</th><th>Company Name</th><th># Tables</th><th>Electric?</th><th>Status</th><th>Reason</th><th>Invoice Status</th><th>As Of</th></tr><?php
                            foreach($dealers as $dealer)
                            {
                                if($dealer["Status"] == "Approved" || $dealer["Status"] == "Rejected") {
                                    echo "<tr class=\"masterTooltip\" title=\"Legal Name: " . $dealer["LegalName"] . "<br>Extra Badges: " . $dealer["Badges"] . "<br>Added Details:<br>" . $dealer["AddedDetails"];
                                    if(!empty($dealer["InvoiceStatus"])){
                                        echo "<br><br>Invoice Amount: " . sprintf("$%01.2f", $dealer["InvoiceAmount"]);
                                        if(!empty($dealer["InvoiceSent"]))
                                            echo "<br>Invoice Sent: " . (!empty($dealer["InvoiceSent"]) ? date("F d, Y g:i a", strtotime($dealer["InvoiceSent"])) : "Not Yet");
                                        if(!empty($dealer["InvoiceFulfilled"]))
                                            echo "<br>Invoice Paid: " . (!empty($dealer["InvoiceFulfilled"]) ? date("F d, Y g:i a", strtotime($dealer["InvoiceFulfilled"])) : "Not Yet");
                                        if(!empty($dealer["InvoiceCancelled"]))
                                            echo "<br>Invoice Cancelled: " . (!empty($dealer["InvoiceCancelled"]) ? date("F d, Y g:i a", strtotime($dealer["InvoiceCancelled"])) : "Not Yet");
                                    }
                                    echo "\">";
                                    echo "<td style=\"text-align: center;\"><input type=\"checkbox\" name=\"select\" id=\"" . $dealer["DealerPresenceID"] . "\"/></td>";
                                    echo "<td>" . $dealer["CompanyName"] . "</td>";
                                    echo "<td>" . $dealer["NumTables"] . "</td>";
                                    echo "<td>" . ($dealer["ElectricalNeeded"] == 1 ? "Yes" : "No") . "</td>";
                                    echo "<td>" . $dealer["Status"] . "</td>";
                                    echo "<td>" . $dealer["StatusReason"] . "</td>";
                                    echo "<td>" . $dealer["InvoiceStatus"] . "</td>";
                                    if(!empty($dealer["InvoiceCancelled"]))
                                        echo "<td>" . date("F d, Y g:i a", strtotime($dealer["InvoiceCancelled"])) . "</td>";
                                    else if(!empty($dealer["InvoiceFulfilled"]))
                                        echo "<td>" . date("F d, Y g:i a", strtotime($dealer["InvoiceFulfilled"])) . "</td>";
                                    else if(!empty($dealer["InvoiceSent"]))
                                        echo "<td>" . date("F d, Y g:i a", strtotime($dealer["InvoiceSent"])) . "</td>";
                                    else
                                        echo "<td>" . date("F d, Y g:i a", strtotime($dealer["InvoiceCreated"])) . "</td>";
                                    echo "</tr>\r\n";
                                }
                            } ?>
                        </table>
                    </div>
                    <hr />
                    With Selected: <select id="action" name="action" style="width: 165px;" disabled>
                        <option value="Approved" selected>Approve Requests</option>
                        <option value="Rejected">Reject Requests</option>
                        <option value="Pending">Send Requests Back</option>
                        <option value="Waitlist">Waitlist Requests</option>
                        <option value="NoChange">Update Reason Only</option>
                    </select><label for="reason" style=" margin-left: 15px;">Reason: <input type="text" id="reason" name="reason" style="width: 40%;" maxlength="100" placeholder="Optional" disabled></label>
                    <label for="clearReason"><input type="checkbox" id="clearReason" name="clearReason" disabled> Clear Reason</label><br />
                    <label for="message" class="fieldLabelShort">Message to Send to Dealer(s): </label><br />
                    <textarea id="message" name="message" maxlength="1000" rows="4" placeholder="(Optional) Enter Message To Send To Dealers Here" style="width: 98%;"></textarea><br />
                    <label for="noEmail"><input type="checkbox" id="noEmail" name="noEmail" disabled> Do Not Send Email(s) For This Change</label><br /><br />
                    <input type="submit" id="updateRequest" name="updateRequest" value="Apply Action to Requests" onclick="doUpdate(); return false;" disabled><br />
                    <br />
                    <span id="saveMessage">&nbsp;</span>
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