<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("DealerLead"))
    header('Location: index.php');
else
{
    $active = false;
    $expired = false;
    $year = date("n") >= 3 ? date("Y") + 1: date("Y");
    $result = $db->query("SELECT WaitListAfterTableNum, WaitListAfterDate, ApplicationCloseDate, ElectricFee, BadgeFee FROM DealerConfig WHERE Year = $year");
    if($result->num_rows > 0)
    {
        $active = true;
        $config = $result->fetch_array();
        $result->close();
    }
    else
    {
        $config = array();
        $config["WaitListAfterTableNum"] = 0;
        $config["WaitListAfterDate"] = null;
        $config["ApplicationCloseDate"] = date("Y-m-d", mktime(0, 0, 0, 1, 31, $year));
        $config["ElectricFee"] = 50.00;
        $config["BadgeFee"] = 45.00;
    }

    if(!empty($config["ApplicationCloseDate"])) {
        $expiration = new DateTime($config["ApplicationCloseDate"]);
        if($expiration <= new DateTime(date("F d, Y")))
            $expired = true;
    }
    $setNoValue = empty($config["WaitListAfterDate"]);

    $result = $db->query("SELECT IFNULL(SUM(NumTables), 0) AS Approved FROM DealerPresence WHERE Year = $year AND Status = 'Approved'");
    $counts = $result->fetch_array();
    $result->close();
    $approved = $counts["Approved"];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Dealer Application Configuration</title>
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
        	$("#waitlistAfterDate").datepicker({ minDate: 0, maxDate: new Date(<?php echo $year; ?>, 1, 31), changeMonth: true, dateFormat: "yy-mm-dd" });
        	$("#closeDate").datepicker({ minDate: 0, maxDate: new Date(<?php echo $year; ?>, 1, 31), changeMonth: true, dateFormat: "yy-mm-dd" });
        	$("#noWaitlist").click(function () {
				if($("#noWaitlist").is(':checked'))
					$("#waitlistAfterDate").attr("disabled", "disabled");
				else
					$("#waitlistAfterDate").removeAttr("disabled");
			});
        	$(":submit").click(function () {
                if(this.name == "closeApplications")
                   	$("#task").val("CloseConfig")
                else
                	$("#task").val("SaveConfig")
			});
			$("#configForm").submit(function () {
				if($("#noWaitlist").is(':checked'))
					$("#waitListAfterDate").val("");
				$("#configForm :input").prop("readonly", true);
				$("#saveMessage").removeClass("goodMessage");
				$("#saveMessage").removeClass("errorMessage");
				$("#saveMessage").html("&nbsp;");
				$.post("doDealerFunctions.php", $(this).serialize(), function(result) {
					$("#saveMessage").html(result.message);
					if(result.success)
						$("#saveMessage").addClass("goodMessage");
					else
						$("#saveMessage").addClass("errorMessage");
					$("#configForm :input").prop("readonly", false);
                	if($("#task").val() == "CloseConfig") {
                        $("#updateConfig").prop("disabled", true);
                        $("#closeApplications").prop("disabled", true);
                    }
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
                <form id="configForm" class="accountForm" method="post" action="">
                    <h1>Dealer Application Configuration</h1>
                    <?php if($active) { ?>
                    <p>
                        The Dealer's Room is
                        <span style="color: #479347; font-weight: bold;">currently</span> taking applications for the upcoming convention year. To make changes to the dealer room
                        configuration, make your changes below and then press "Update Configuration". Please note that fee changes will only affect new applications for the convention year,
                        existing or paid invoices will not be updated. (If you would like to close the system for any further applications, press "Close Applications".)
                    </p>
                    <?php } else { ?>
                    <p>
                        The Dealer's Room is
                        <span style="color: #FF0000; font-weight: bold;">currently not</span> taking applications
                        <?php if($expired) echo "<span style='font-weight: bold;'>because the Application Close Date has passed </span>"; ?>
                        for the upcoming convention year. To enable accepting applications, fill in the below configuration and then press "Turn On Applications".
                    </p>
                    <?php } ?>
                    <hr />
                    <p>
                        The following two fields determine when applications are automatically waitlisted. If either condition is met, new applications will be 
                        in the "Waitlist" status upon submission.<br />
                        <span style="font-size: 0.9em;"><span style="font-style: italic">Note:</span> There <?php echo $approved == 1 ? "is 1 table" : "are $approved tables"; ?> approved for the current convention year.</span>
                    </p>
                    <label for="waitlistAfterNumber" class="fieldLabelShort"># Approved Tables Until Auto-Waitlisting (0 to disable): </label>
                    <input type="text" name="waitlistAfterNumber" id="waitlistAfterNumber" maxlength="3" style="width: 30px;" value="<?php echo $config["WaitListAfterTableNum"]; ?>" /><br />
                    <label for="waitlistAfterDate">Date to Begin Auto-Waitlist Applications: </label>
                    <input type="text" id="waitlistAfterDate" name="waitlistAfterDate" style="width: 75px;" value="<?php echo $config["WaitListAfterDate"]; ?>"<?php if($setNoValue) echo " disabled"; ?> /><br />
				    <label><input type="checkbox" id="noWaitlist" name="noWaitlist"<?php if($setNoValue) echo " checked"; ?> />No Auto-Waitlist By Date</label><br />
                    <hr />
				    <label for="expireDate">Date to Stop Taking Any Applications: </label>
                    <input type="text" id="closeDate" name="closeDate" style="width: 75px;" value="<?php echo $config["ApplicationCloseDate"]; ?>" /><br />
                    <label for="electricFee" class="fieldLabelShort">Electricity Fee: </label>
				    <label>$<input type="text" id="electricFee" name="electricFee" style="width: 50px;" value="<?php echo $config["ElectricFee"]; ?>"/></label><br />
                    <label for="badgeFee" class="fieldLabelShort">Badge Fee: </label>
				    <label>$<input type="text" id="badgeFee" name="badgeFee" style="width: 50px;" value="<?php echo $config["BadgeFee"]; ?>"/></label><br />
                    <br />
                    <br />
                    <input type="hidden" id="task" name="task" value="" />
                    <?php if($active) { ?>
                    <input style="float: right;" type="submit" id="updateConfig" name="updateConfig" value="Update Configuration" /><br/><br/>
                    <input style="float: right;" type="submit" id="closeApplications" name="closeApplications" value="Close Applications" />
                    <?php } else { ?>
                    <input style="float: right;" type="submit" name="turnOn" value="Turn On Applications" />
                    <?php } ?>
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