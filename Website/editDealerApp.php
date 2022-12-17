<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("DealerStaff"))
    header('Location: index.php');
else
{
    $presenceID = $_GET["id"];
    $year = isset($_GET['year']) ? $_GET['year'] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
    $result = $db->query("SELECT d.DealerID, DealerPresenceID, d.CompanyName, NumTables, ElectricalNeeded, AddedDetails, Status, StatusReason FROM DealerPresence dp JOIN Dealer d ON dp.DealerID = d.DealerID " .
        "WHERE DealerPresenceID = $presenceID");
    $request = $result->fetch_array();
    $result->close();
    $id = $request["DealerID"];

    $result = $db->query("SELECT Quantity, Price FROM DealerTablePrices ORDER BY Quantity ASC");
    while($row = $result->fetch_array())
        $prices[] = $row;
    $result->close();

    $result = $db->query("SELECT WaitListAfterTableNum, WaitListAfterDate, ApplicationCloseDate, ElectricFee, BadgeFee FROM DealerConfig WHERE Year = $year");
    if($result->num_rows > 0)
    {
        $config = $result->fetch_array();
        $result->close();
    }

    foreach($prices as $price) {
        if($price["Quantity"] == $request["NumTables"]) {
            $startTotal = $price["Price"];
            break;
        }
    }

    if($request["ElectricalNeeded"])
        $startTotal += $config["ElectricFee"];

    $badges = array();
    if($presenceID != "") {
        $result = $db->query("SELECT BadgeName, FirstName, LastName, Price, BadgeTypeID FROM DealerBadges WHERE DealerPresenceID = $presenceID");
        while($row = $result->fetch_array()) {
            $badges[] = $row;
            $startTotal += ($row["BadgeTypeID"] == 1 ? $config["BadgeFee"] : 0);
        }
        $result->close();
    }

    $result = $db->query("SELECT BadgeID FROM PurchasedBadges pb JOIN Dealer d ON pb.PeopleID = d.PeopleID WHERE Year = $year AND DealerID = $id");
    if($result->num_rows > 0) {
        $hasBadge = true;
        $result->close();
    }
    else {
        $hasBadge = false;
        $startTotal += $config["BadgeFee"];
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

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Edit Application for <?php echo $request["CompanyName"]; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
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
            var tables = $("#tables").val();
            var tableVals = tables.split(";");
       		var badgePrice = <?php echo $config["BadgeFee"]; ?>;
			var total = <?php echo $hasBadge ? "0" : $config["BadgeFee"]; ?> + parseFloat(tableVals[1]);
            if($("#electricity").prop("checked")) total += <?php echo $config["ElectricFee"]; ?>;
            if($("#addlKid1").prop("checked") || $("#addlKid2").prop("checked") || $("#addlKid3").prop("checked") || $("#addlKid4").prop("checked") || $("#addlKid5").prop("checked"))
                $("#kitNotice").html('<br/><span style="color: #FF0000; font-weight: bold;">NOTE: Because Kids-in-Tow badges are for ages 12 and ' +
					'under only, they must be accompanied by a parent at all times, and ID will be checked at Registration.</span>');
            else
                $("#kitNotice").html('');

			if($("#addlFName1").val() != "")
				total += $("#addlKid1").prop("checked") ? 0 : badgePrice;
			else
				return total;

			if($("#addlFName2").val() != "")
				total += $("#addlKid2").prop("checked") ? 0 : badgePrice;
			else
				return total;

			if($("#addlFName3").val() != "")
				total += $("#addlKid3").prop("checked") ? 0 : badgePrice;
			else
				return total;

			if($("#addlFName4").val() != "")
				total += $("#addlKid4").prop("checked") ? 0 : badgePrice;
			else
				return total;

			if($("#addlFName5").val() != "")
				total += $("#addlKid5").prop("checked") ? 0 : badgePrice;
			return total;
		}

        function toggleRegFormData()
		{
			var total = getTotalPrice();
			$("#totalFees").html(parseFloat(total).toFixed(2));

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
		}
    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxwide">
            <div style="margin-bottom: 30px;">
                <form id="applicationForm" class="accountForm" method="post" action="">
                    <h1>Edit Application for <?php echo $request["CompanyName"]; ?></h1>
                    <label for="tables" class="fieldLabelShort">Number of Tables Requested: </label>
                    <select id="tables" name="tables" style="width: 200px; margin-bottom: 10px;" onchange="toggleRegFormData();">
                        <?php
                              foreach($prices as $price) {
                                  echo "<option value='" . $price["Quantity"] . ";" . $price["Price"] . "'" . ($price["Quantity"] == $request["NumTables"] ? " selected" : "") . ">" . $price["Quantity"] . " Table" .
                                  ($price["Quantity"] == 1 ? "" : "s") . " @ " . sprintf("$%01.2f", $price["Price"]) . "</option>\r\n";
                              }
                        ?>
                    </select>
                    <br />
                    <label>
                        The booth requires electricity (<?php echo sprintf("$%01.2f", $config["ElectricFee"]); ?> Fee)
                        <input type="checkbox" name="electricity" id="electricity" onchange="toggleRegFormData();" <?php if($request["ElectricalNeeded"] == 1) echo "checked"; ?> />
                    </label>
                    <br />
                    <label for="addedDetails" class="fieldLabelShort">Additional Details or Requests: </label>
                    <br />
                    <textarea id="addedDetails" name="addedDetails" maxlength="500" rows="4" placeholder="(Optional) Additional Information To Be Given To The Dealers Team" style="width: 98%;">
                        <?php echo $request["AddedDetails"]; ?>
                    </textarea>
                    <br />
                    <?php if($hasBadge) { ?>
                    <p>
                        This dealer already has a badge for this convention year and dooes not need to buy a badge.
                    </p>
                    <?php } else { ?>
                    <p>
                        This dealer requires a badge, which costs <span style="font-weight: bold;"><?php echo sprintf("$%01.2f", $config["BadgeFee"]); ?></span>.
                    </p>
                    <?php } ?>
                    <p>
                        This application may add up to five badges, for a fee of
                        <span style="font-weight: bold;">
                            <?php echo sprintf("$%01.2f", $config["BadgeFee"]); ?>
                        </span> each. (There is no fee for a kid-in-tow badge for someone who is under the age of 13.)
                    </p>
                    Extra Badge #1
                    <br />
                    <label>
                        First Name:
                        <input type="text" name="addlFName1" id="addlFName1" style="width: 100px;" onkeyup="toggleRegFormData();" value="<?php echo $badges[0]["FirstName"]; ?>" />
                    </label>
                    <label>
                        Last Name:
                        <input type="text" name="addlLName1" id="addlLName1" style="width: 100px;" value="<?php echo $badges[0]["LastName"]; ?>" />
                    </label>
                    <label>
                        Badge Name:
                        <input type="text" name="addlBadge1" id="addlBadge1" style="width: 160px;" placeholder="Named Used If Blank" value="<?php echo $badges[0]["BadgeName"]; ?>" />
                    </label>
                    <label>
                        <input type="checkbox" name="addlKid1" id="addlKid1" onchange="toggleRegFormData();" <?php if($badges[0]["BadgeTypeID"] == 2) echo "checked"; ?> /> Kid-in-Tow
                    </label>
                    <br />
                    <div id="addl2" style="display: none;">
                        Extra Badge #2
                        <br />
                        <label>
                            First Name:
                            <input type="text" name="addlFName2" id="addlFName2" style="width: 100px;" onkeyup="toggleRegFormData();" value="<?php echo $badges[1]["FirstName"]; ?>" />
                        </label>
                        <label>
                            Last Name:
                            <input type="text" name="addlLName2" id="addlLName2" style="width: 100px;" value="<?php echo $badges[1]["LastName"]; ?>" />
                        </label>
                        <label>
                            Badge Name:
                            <input type="text" name="addlBadge2" id="addlBadge2" style="width: 160px;" placeholder="Named Used If Blank" value="<?php echo $badges[1]["BadgeName"]; ?>" />
                        </label>
                        <label>
                            <input type="checkbox" name="addlKid2" id="addlKid2" onchange="toggleRegFormData();" <?php if($badges[1]["BadgeTypeID"] == 2) echo "checked"; ?> /> Kid-in-Tow
                        </label>
                        <br />
                    </div>
                    <div id="addl3" style="display: none;">
                        Extra Badge #3
                        <br />
                        <label>
                            First Name:
                            <input type="text" name="addlFName3" id="addlFName3" style="width: 100px;" onkeyup="toggleRegFormData();" value="<?php echo $badges[2]["FirstName"]; ?>" />
                        </label>
                        <label>
                            Last Name:
                            <input type="text" name="addlLName3" id="addlLName3" style="width: 100px;" value="<?php echo $badges[2]["LastName"]; ?>" />
                        </label>
                        <label>
                            Badge Name:
                            <input type="text" name="addlBadge3" id="addlBadge3" style="width: 160px;" placeholder="Named Used If Blank" value="<?php echo $badges[2]["BadgeName"]; ?>" />
                        </label>
                        <label>
                            <input type="checkbox" name="addlKid3" id="addlKid3" onchange="toggleRegFormData();" <?php if($badges[2]["BadgeTypeID"] == 2) echo "checked"; ?> /> Kid-in-Tow
                        </label>
                        <br />
                    </div>
                    <div id="addl4" style="display: none;">
                        Extra Badge #4
                        <br />
                        <label>
                            First Name:
                            <input type="text" name="addlFName4" id="addlFName4" style="width: 100px;" onkeyup="toggleRegFormData();" value="<?php echo $badges[3]["FirstName"]; ?>" />
                        </label>
                        <label>
                            Last Name:
                            <input type="text" name="addlLName4" id="addlLName4" style="width: 100px;" value="<?php echo $badges[3]["LastName"]; ?>" />
                        </label>
                        <label>
                            Badge Name:
                            <input type="text" name="addlBadge4" id="addlBadge4" style="width: 160px;" placeholder="Named Used If Blank" value="<?php echo $badges[3]["BadgeName"]; ?>" />
                        </label>
                        <label>
                            <input type="checkbox" name="addlKid4" id="addlKid4" onchange="toggleRegFormData();" <?php if($badges[3]["BadgeTypeID"] == 2) echo "checked"; ?> /> Kid-in-Tow
                        </label>
                        <br />
                    </div>
                    <div id="addl5" style="display: none;">
                        Extra Badge #5
                        <br />
                        <label>
                            First Name:
                            <input type="text" name="addlFName5" id="addlFName5" style="width: 100px;" onkeyup="toggleRegFormData();" value="<?php echo $badges[4]["FirstName"]; ?>" />
                        </label>
                        <label>
                            Last Name:
                            <input type="text" name="addlLName5" id="addlLName5" style="width: 100px;" value="<?php echo $badges[4]["LastName"]; ?>" />
                        </label>
                        <label>
                            Badge Name:
                            <input type="text" name="addlBadge5" id="addlBadge5" style="width: 160px;" placeholder="Named Used If Blank" value="<?php echo $badges[4]["BadgeName"]; ?>" />
                        </label>
                        <label>
                            <input type="checkbox" name="addlKid5" id="addlKid5" onchange="toggleRegFormData();" <?php if($badges[4]["BadgeTypeID"] == 2) echo "checked"; ?> /> Kid-in-Tow
                        </label>
                        <br />
                    </div>
                    <span id="kitNotice"></span>
                    <hr />
                    <p>
                        Total Dealer Table and Badge(s) Registration Fee:
                        <span style="font-weight: bold; font-size: 1.1em">
                            $
                            <span id="totalFees">
                                <?php echo sprintf("%01.2f", $startTotal); ?>
                            </span>
                        </span>
                    </p>
                    <input type="hidden" name="dealerId" value="<?php echo $id; ?>" />
                    <input type="hidden" name="task" value="SaveDealerApplication" />
                    <input style="float: right;" type="submit" name="save" value="Save Changes" />
                    <br />
                    <span id="saveMessage">&nbsp;</span>
                    <br />
                </form>
            </div>
            <div class="goback">
                <a href="manageDealerApps.php">Return to the Application List</a>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>