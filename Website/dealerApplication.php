<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("Dealer"))
    header('Location: index.php');
else
{
    $result = $db->query("SELECT DealerID FROM Dealer WHERE PeopleID = " . $_SESSION["PeopleID"]);
    if($result->num_rows == 0) {
        header('Location: dealerDetails.php');
        //exit();
    }
    else
        $result->close();

    $active = true;
    $year = date("n") >= 3 ? date("Y") + 1: date("Y");
    $result = $db->query("SELECT WaitListAfterTableNum, WaitListAfterDate, ApplicationCloseDate, ElectricFee, BadgeFee FROM DealerConfig WHERE Year = $year");
    if($result->num_rows > 0)
    {
        $config = $result->fetch_array();
        $result->close();
        if(!empty($config["ApplicationCloseDate"])) {
            $expiration = new DateTime($config["ApplicationCloseDate"]);
            if($expiration <= new DateTime(date("F d, Y")))
                $active = false;
        }
    }
    else
        $active = false;

    $result = $db->query("SELECT DealerPresenceID, NumTables, ElectricalNeeded, AddedDetails, Status, StatusReason FROM DealerPresence dp JOIN Dealer d ON dp.DealerID = d.DealerID " .
        "WHERE Year = $year AND d.PeopleID = " . $_SESSION["PeopleID"]);
    if($result->num_rows > 0)
    {
        $filled = true;
        $request = $result->fetch_array();
        $result->close();
    }
    else
    {
        $filled = false;
        $request = array();
        $request["DealerPresenceID"] = "";
        $request["NumTables"] = 1;
        $request["ElectricalNeeded"] = 0;
        $request["AddedDetails"] = "";
        $request["Status"] = "Pending";
        $request["StatusReason"] = "";
    }
    $presenceID = $request["DealerPresenceID"];
    $canEdit = ($request["Status"] == "Pending" || $request["Status"] == "Waitlist");

    $result = $db->query("SELECT Quantity, Price FROM DealerTablePrices ORDER BY Quantity ASC");
    while($row = $result->fetch_array())
        $prices[] = $row;
    $result->close();

    $badges = array();
    if($presenceID != "") {
        $result = $db->query("SELECT BadgeName, FirstName, LastName, Price, BadgeTypeID FROM DealerBadges WHERE DealerPresenceID = $presenceID");
        while($row = $result->fetch_array())
            $badges[] = $row;
        $result->close();
    }

    $result = $db->query("SELECT InvoiceID, Status, Sent, Fulfilled FROM Invoice WHERE InvoiceType = 'Dealer' AND RelatedRecordID = $presenceID");
    if($result->num_rows > 0)
    {
        $invoiced = true;
        $invoice = $result->fetch_array();
        $result->close();

        $invoiceID = $invoice["InvoiceID"];
        $result = $db->query("SELECT Price, Tax FROM InvoiceLine WHERE InvoiceID = $invoiceID");
        $invoiceTotal = 0.0;
        while($row = $result->fetch_array())
            $invoiceTotal += $row["Price"] + $row["Tax"];
        $result->close();
    }
    else
    {
        $invoiced = false;
    }

    foreach($prices as $price) {
        if($price["Quantity"] == 1) {
            $startTotal = $price["Price"] + $config["BadgeFee"];
            break;
        }
    }

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Application for Dealer's Hall</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css" />
    <link rel="icon" href="includes/favicon.png" />
    <link rel="shortcut icon" href="includes/favicon.ico" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="includes/global.js"></script>
    <script type="text/javascript">
		$(document).ready(function() {
            toggleRegFormData();
			$("#applicationForm").submit(function () {
				$("#applicationForm :input").prop("readonly", true);
				$("#saveMessage").removeClass("goodMessage");
				$("#saveMessage").removeClass("errorMessage");
				$("#saveMessage").html("&nbsp;");
				$.post("doDealerFunctions.php", $(this).serialize(), function(result) {
					$("#saveMessage").html(result.message);
					if(result.success)
						$("#saveMessage").addClass("goodMessage");
					else
						$("#saveMessage").addClass("errorMessage");
					$("#applicationForm :input").prop("readonly", false);
				}, 'json');
				return false;
			});
        });

		function getTotalPrice()
		{
            <?php if($canEdit) { ?>
            var tables = $("#tables").val();
            var tableVals = tables.split(";");
       		var badgePrice = <?php echo $config["BadgeFee"]; ?>;
			var total = +badgePrice + +tableVals[1];
            if($("#electricity").prop("checked")) total += <?php echo $config["ElectricFee"]; ?>;
            if($("#addlKid1").prop("checked") || $("#addlKid2").prop("checked") || $("#addlKid3").prop("checked") || $("#addlKid4").prop("checked") || $("#addlKid5").prop("checked"))
                $("#kitNotice").html('<br/><span style="color: #FF0000; font-weight: bold;">NOTE: Because Kids-in-Tow badges are for ages 12 and ' +
					'under only, they must be accompanied by a parent at all times, and ID will be checked at Registration.</span>');
            else
                $("#kitNotice").html('');

			if($("#addlFName1").val() != "")
				total += $("#addlKid1").prop("checked") ? 0 : +badgePrice;
			else
				return total;

			if($("#addlFName2").val() != "")
				total += $("#addlKid2").prop("checked") ? 0 : +badgePrice;
			else
				return total;

			if($("#addlFName3").val() != "")
				total += $("#addlKid3").prop("checked") ? 0 : +badgePrice;
			else
				return total;

			if($("#addlFName4").val() != "")
				total += $("#addlKid4").prop("checked") ? 0 : +badgePrice;
			else
				return total;

			if($("#addlFName5").val() != "")
				total += $("#addlKid5").prop("checked") ? 0 : +badgePrice;
			return total;
            <?php } ?>
		}

        function toggleRegFormData()
		{
            <?php if($canEdit) { ?>
			var total = getTotalPrice();
			$("#totalFees").html(total.toFixed(2));

			if($("#addlFName1").val() != "")
				$("#addl2").show();
			else
			{
				$("#addl2").hide();
				$("#addl3").hide();
				$("#addl4").hide();
				$("#addl5").hide();
				return;
			}

			if($("#addlFName2").val() != "")
				$("#addl3").show();
			else
			{
				$("#addl3").hide();
				$("#addl4").hide();
				$("#addl5").hide();
				return;
			}

			if($("#addlFName3").val() != "")
				$("#addl4").show();
			else
			{
				$("#addl4").hide();
				$("#addl5").hide();
				return;
			}

			if($("#addlFName4").val() != "")
				$("#addl5").show();
			else
				$("#addl5").hide();
            <?php } ?>
		}
    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxwide">
            <div style="margin-bottom: 30px;">
                <form id="applicationForm" class="accountForm" method="post" action="">
                    <h1>Application for Capricon Dealer's Hall</h1>
                    <?php if(!$active) { ?>
                    <p>
                        Applications for this convention year are presently closed. If you have any questions, please reach out to 
                        <a href="mailto:dealers@capricon.org?subject=Dealers%20Application">Capricon Dealers</a> and they will be able to assist you.
                    </p>
                    <?php } ?>
                    <?php if($filled) { ?>
                    <?php if(!$canEdit || !$active) { ?>
                    <p>
                        You have an application presently filled out for the current convention year, as follows:
                    </p>
                    Number of Tables Requested: <span style="font-weight: bold;"><?php echo $request["NumTables"]; ?></span><br/>
                    Electrical Requested? <span style="font-weight: bold;"><?php echo $request["ElectricalNeeded"] == 1 ? "Yes" : "No"; ?></span><br/>
                    Number of Badges Requested: <span style="font-weight: bold;"><?php echo (sizeof($badges) + 1); ?></span><br/>
                    Additional Details or Requests:<br/>
                    <p><?php echo !empty($request["AddedDetails"]) ? $request["AddedDetails"] : "No additional details provided."; ?></p>
                    <?php } else { ?>
                    <p>
                        You have an application presently filled out for the current convention year, with the present status:
                    </p>
                    <?php } ?>
                    <span style="font-weight: bold;">Application Status: <?php echo $request["Status"]; ?></span><br/>
                    <?php if(!empty($request["StatusReason"])) echo "<span style='font-weight: bold;'>Status Explanation: " . $request["StatusReason"] . "</span><br/>"; ?><br />
                    <?php if($invoiced) { ?>
                    <span style="font-weight: bold;">Invoice Status: <?php echo $invoice["Status"]; ?></span><? if($invoice["Status"] == "Sent") echo "&nbsp;&nbsp;<a href='invoices.php'>Click Here To Pay Invoice Now</a>"; ?><br/>
                    <span style="font-weight: bold;">Invoice Amount: <?php echo sprintf("$%01.2f", $invoiceTotal); ?></span><br/>
                    <span style="font-weight: bold;">Invoice Sent: <?php echo !empty($invoice["Sent"]) ? date("F d, Y", strtotime($invoice["Sent"])) : "Not Yet"; ?></span><br/>
                    <span style="font-weight: bold;">Invoice Paid: <?php echo !empty($invoice["Fulfilled"]) ? date("F d, Y", strtotime($invoice["Fulfilled"])) : "Not Yet"; ?></span><br/>
                    <?php } ?>
                    <?php } if($active && $canEdit) { if($presenceID == "") { ?>
                    <p>
                        To request space in the upcoming Capricon's Dealer's Hall, please fill in the following details. Your cost will be calculated and 
                        shown before you submit your request. If you have any questions about the process, please reach out to
                        <a href="mailto:dealers@capricon.org?subject=Dealers%20Application">Capricon Dealers</a> and they will be able to assist you.
                    </p>
                    <?php } else { ?>
                    <p>
                        To modify your request for space in the upcoming Capricon's Dealer's Hall, please change the details below. Your cost will be calculated and
                        shown before you submit your request. If you have any questions about the process, please reach out to
                        <a href="mailto:dealers@capricon.org?subject=Dealers%20Application">Capricon Dealers</a> and they will be able to assist you.
                    </p>
                    <?php } ?>
                    <label for="tables" class="fieldLabelShort">Number of Tables Requested: </label>
                    <select id="tables" name="tables" style="width: 200px; margin-bottom: 10px;" onchange="toggleRegFormData();">
                        <?php
                        foreach($prices as $price) {
                            echo "<option value='" . $price["Quantity"] . ";" . $price["Price"] . "'" . ($price["Quantity"] == $request["NumTables"] ? " selected" : "") . ">" . $price["Quantity"] . " Table" .
                                ($price["Quantity"] == 1 ? "" : "s") . " @ " . sprintf("$%01.2f", $price["Price"]) . "</option>\r\n";
                        }
                        ?>
                    </select><br />
                    <label>My booth requires electricity (<?php echo sprintf("$%01.2f", $config["ElectricFee"]); ?> Fee) <input type="checkbox" name="electricity" id="electricity" onchange="toggleRegFormData();" <?php if($request["ElectricalNeeded"] == 1) echo "checked"; ?>/></label><br />
                    <label for="addedDetails" class="fieldLabelShort">Additional Details or Requests: </label><br />
                    <textarea id="addedDetails" name="addedDetails" maxlength="500" rows="4" placeholder="(Optional) Additional Information To Be Given To The Dealers Team" style="width: 98%;"><?php echo $request["AddedDetails"]; ?></textarea><br />
                    <span>Badge Fee for Your Badge: <span style="font-weight: bold;"><? echo sprintf("$%01.2f", $config["BadgeFee"]); ?></span> (All badges that are added below are at the same fee.)</span><br /><br />
                    Extra Badge #1<br /><label>First Name: <input type="text" name="addlFName1" id="addlFName1" style="width: 100px;" onkeyup="toggleRegFormData();" value="<?php echo $badges[0]["FirstName"]; ?>"></label> 
					<label>Last Name: <input type="text" name="addlLName1" id="addlLName1" style="width: 100px;" value="<?php echo $badges[0]["LastName"]; ?>"></label> 
					<label>Badge Name: <input type="text" name="addlBadge1" id="addlBadge1" style="width: 160px;" placeholder="Named Used If Blank" value="<?php echo $badges[0]["BadgeName"]; ?>"></label>
					<label><input type="checkbox" name="addlKid1" id="addlKid1" onchange="toggleRegFormData();" <?php if($badges[0]["BadgeTypeID"] == 2) echo "checked"; ?>> Kid-in-Tow</label><br />
					<div id="addl2" style="display: none;">Extra Badge #2<br /><label>First Name: <input type="text" name="addlFName2" id="addlFName2" style="width: 100px;" onkeyup="toggleRegFormData();" value="<?php echo $badges[1]["FirstName"]; ?>"></label> 
					<label>Last Name: <input type="text" name="addlLName2" id="addlLName2" style="width: 100px;" value="<?php echo $badges[1]["LastName"]; ?>"></label> 
					<label>Badge Name: <input type="text" name="addlBadge2" id="addlBadge2" style="width: 160px;" placeholder="Named Used If Blank" value="<?php echo $badges[1]["BadgeName"]; ?>"></label>
					<label><input type="checkbox" name="addlKid2" id="addlKid2" onchange="toggleRegFormData();" <?php if($badges[1]["BadgeTypeID"] == 2) echo "checked"; ?>> Kid-in-Tow</label><br /></div>
					<div id="addl3" style="display: none;">Extra Badge #3<br /><label>First Name: <input type="text" name="addlFName3" id="addlFName3" style="width: 100px;" onkeyup="toggleRegFormData();" value="<?php echo $badges[2]["FirstName"]; ?>"></label> 
					<label>Last Name: <input type="text" name="addlLName3" id="addlLName3" style="width: 100px;" value="<?php echo $badges[2]["LastName"]; ?>"></label> 
					<label>Badge Name: <input type="text" name="addlBadge3" id="addlBadge3" style="width: 160px;" placeholder="Named Used If Blank" value="<?php echo $badges[2]["BadgeName"]; ?>"></label>
					<label><input type="checkbox" name="addlKid3" id="addlKid3" onchange="toggleRegFormData();" <?php if($badges[2]["BadgeTypeID"] == 2) echo "checked"; ?>> Kid-in-Tow</label><br /></div>
					<div id="addl4" style="display: none;">Extra Badge #4<br /><label>First Name: <input type="text" name="addlFName4" id="addlFName4" style="width: 100px;" onkeyup="toggleRegFormData();" value="<?php echo $badges[3]["FirstName"]; ?>"></label> 
					<label>Last Name: <input type="text" name="addlLName4" id="addlLName4" style="width: 100px;" value="<?php echo $badges[3]["LastName"]; ?>"></label> 
					<label>Badge Name: <input type="text" name="addlBadge4" id="addlBadge4" style="width: 160px;" placeholder="Named Used If Blank" value="<?php echo $badges[3]["BadgeName"]; ?>"></label>
					<label><input type="checkbox" name="addlKid4" id="addlKid4" onchange="toggleRegFormData();" <?php if($badges[3]["BadgeTypeID"] == 2) echo "checked"; ?>> Kid-in-Tow</label><br /></div>
					<div id="addl5" style="display: none;">Extra Badge #5<br /><label>First Name: <input type="text" name="addlFName5" id="addlFName5" style="width: 100px;" onkeyup="toggleRegFormData();" value="<?php echo $badges[4]["FirstName"]; ?>"></label> 
					<label>Last Name: <input type="text" name="addlLName5" id="addlLName5" style="width: 100px;" value="<?php echo $badges[4]["LastName"]; ?>"></label> 
					<label>Badge Name: <input type="text" name="addlBadge5" id="addlBadge5" style="width: 160px;" placeholder="Named Used If Blank" value="<?php echo $badges[4]["BadgeName"]; ?>"></label>
					<label><input type="checkbox" name="addlKid5" id="addlKid5" onchange="toggleRegFormData();" <?php if($badges[4]["BadgeTypeID"] == 2) echo "checked"; ?>> Kid-in-Tow</label><br /></div>
                    <span id="kitNotice"></span>
                    <hr />
                    <p>
                        Total Dealer Registration Fee: <span style="font-weight: bold; font-size: 1.1em">$<span id="totalFees"><?php echo sprintf("%01.2f", $startTotal); ?></span></span><br/><br/>
                        You will receive an email (sent to both your account email as well as your business email, if provided) when your application status is updated, and an invoice (with a link to pay the invoice online) 
                        will be sent once the application has been approved. You may make changes to your application while it is in either the 'Pending' or the 'Waitlist' status.<br/><br/>Please note! Once the application 
                        is Approved or Rejected, you can no longer make changes to the application and must contact <a href="mailto:dealers@capricon.org?subject=Dealers%20Application">Capricon Dealers</a> with your request.
                    </p>
                    <input type="hidden" name="task" value="SaveDealerApplication" />
                    <input style="float: right;" type="submit" name="save" value="Submit Application" />
                    <br />
                    <span id="saveMessage">&nbsp;</span>
                    <br />
                    <?php } ?>
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