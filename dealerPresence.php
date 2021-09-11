<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("Dealer"))
		header('Location: /index.php');
	else
	{
		$year = date("n") >= 3 ? date("Y") + 1: date("Y");
		$capriconYear = $year - 1980;
		
		$sql = "SELECT dic.Price FROM DealerItemCosts dic JOIN DealerItems di ON di.DealerItemID = dic.DealerItemID WHERE di.Type = 'Electricity' AND dic.Year = $year LIMIT 1";
		$result = $db->query($sql);
		$electricityCost = array();
		while($row = $result->fetch_array())
			$electricityCost = $row["Price"];
		$result->close();
		
		
		$result = $db->query("SELECT DealerID FROM DealerDetails WHERE PeopleID = " . $_SESSION["PeopleID"]);
		if($result->num_rows == 0)
			header('Location: /dealerDetails.php');
		else
		{
			$result = $db->query("SELECT dp.DealerPresenceID, dp.DealerNumber, dp.NeedsElectricity, dp.DealerItemCostID AS DealerItemCostID, dp.Notes AS Notes, dp.Status, dp.StatusReason FROM DealerPresence dp INNER JOIN DealerDetails dd ON dd.DealerID = dp.DealerID INNER JOIN DealerItemCosts dic ON dp.DealerItemCostID = dic.DealerItemCostID WHERE dp.Year = $year AND dd.PeopleID = " . $_SESSION["PeopleID"]);
			
			if($result->num_rows > 0)
			{
				$request = $result->fetch_array();
			}
			else
			{
				$request = array();
				$request["DealerPresenceID"] = "";
				$request["DealerNumber"] = "";
				$request["NeedsElectricity"] = false;
				$request["DealerItemCostID"] = "";
				$request["Notes"] = "";
				$request["Status"] = "";
				$request["StatusReason"] = "";
			}
		}
		$result->close();
		
		//check to see if this person has a badge
		$result = $db->query("SELECT par.PeopleID as parentID, pb.PeopleID, pb.BadgeID, pb.BadgeNumber, p.FirstName AS FirstName, p.LastName AS LastName, " .
			"pb.BadgeName, pb.BadgeTypeID, pb.Status, bt.Description AS BadgeDescription " .
			"FROM PurchasedBadges pb " .
			"INNER JOIN BadgeTypes bt ON bt.BadgeTypeID = pb.BadgeTypeID " .
			"LEFT OUTER JOIN People p ON p.PeopleID = pb.PeopleID " .
			"LEFT OUTER JOIN People par ON par.PeopleID = pb.PurchaserID " .
			"WHERE pb.Year = " . $year . " and pb.peopleID = " . $_SESSION["PeopleID"] . " " .
			"ORDER BY par.PeopleID, pb.PeopleID");
		if($result->num_rows > 0)
		{
			$badgeinfo = $result->fetch_array();
		}
		else
		{
			$badgeinfo = array();
			$badgeinfo["parentID"] = "";
			$badgeinfo["PeopleID"] = "";
			$badgeinfo["BadgeID"] = "";
			$badgeinfo["BadgeNumber"] = "";
			$badgeinfo["FirstName"] = "";
			$badgeinfo["LastName"] = "";
			$badgeinfo["BadgeName"] = "";
			$badgeinfo["BadgeTypeID"] = "";
			$badgeinfo["Status"] = "";
			$badgeinfo["BadgeDescription"] = "";
		}
		$result->close();
		
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Dealer Information - Page 2</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(":submit").click(function () {
				//if(this.name == "updateInfo") window.location = "/dealerDetails.php";
			});
			$("#requestForm").submit(function () {
				$("#requestForm :input").prop("readonly", true);
				$("#notes").prop("readonly", true);
				$("#accountSaveMessage").removeClass("goodMessage");
				$("#accountSaveMessage").removeClass("errorMessage");
				$("#accountSaveMessage").html("&nbsp;");
				$.post("doDealerFunctions.php", $(this).serialize(), function(result) {
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
			<h1>Dealer Information - Page 2 <br /> Space Application</h1>
			<div style="margin-bottom: 30px;">
				<form id="requestForm" class="accountForm" method="post" action="">
					<input type="hidden" id="task" name="task" value="SubmitRequest">
					<div class="headertitle">Application Status</div>
		<?php if($request["Status"] != "") { ?>
					<p>Your Application for space in the Dealers Room is: <span style="font-size: 1.2em; font-weight: bold;"><?php echo $request["Status"]; ?></span>.<br />
					<?php if(isset($request["StatusReason"])) echo 'Status note: <span style="font-weight: bold;">' . $request["StatusReason"] . "</span><br />"; ?>
					<?php if($request["Status"] == "Approved") echo '<br />You are approved. You may now pay for your space. You may also buy a dealer membership.<br /><br />'; ?>
					You may update your application below, if needed. This will cause your application to be re-reviewed.
					</p>
		<?php } else { ?>
					<p>You have no existing Application for space in the Dealers Room yet. Please complete the application below.</p>
		<?php } ?>

					<div class="headertitle">Membership Status</div>
		<?php if($badgeinfo["PeopleID"] == "") { ?>
					<p>You do not have a membership to the convention yet.</p>
		<?php } else { ?>
					<p>You have a membership to the convention. <?php echo $badgeinfo["BadgeDescription"]; ?></p>
		<?php } ?>
					<div class="headertitle">Dealer Room Application</div>

					<label for="needsElectricity"><input type="checkbox" name="needsElectricity" id="needsElectricity" <?php echo ($request["NeedsElectricity"] ? "checked" : ""); ?> />I will require electricity. Cost is: $<?php echo $electricityCost; ?></label>
					<br />

					<p>Number of 1.5' by 6' Tables Needed: </p>
					<select id="dealerItemCostID" name="dealerItemCostID" size=4>
						<?php 
						$result = $db->query("SELECT dic.DealerItemCostID AS DICID, di.Name, dic.Price FROM DealerItems di JOIN DealerItemCosts dic ON di.DealerItemID = dic.DealerItemID WHERE di.Type = 'Table' AND dic.Year = $year");
						while ($row = $result->fetch_array()){
							echo "<option value=" . $row['DICID'] . ($request["DealerItemCostID"]==$row['DICID'] ? " selected" : "") . ">" . $row['Name'] . " - $" . $row['Price'] . "</option>";
						}
						?>
					</select>
					</br />

					<div class="headertitle">Additional Notes</div>
					<textarea id="notes" name="notes" maxlength="500" rows="4" style="width: 98%;" ><?php echo $request["Notes"]; ?></textarea><br /><br />
					<!-- <label style="font-weight: bold;"><input type="checkbox" name="policiesRead" id="policiesRead" <?php //echo (($request["Status"] != "Pending" && $request["Status"] != "") ? "checked" : ""); ?> /> I acknowledge that I have read the <a href="http://capricon.org/docs/Capricon-Dealer-Policies.pdf" target="_new">Dealer's Room Policies</a>. </label><br /><br /> -->
					<input type="submit" name="saveRequest" value="Save Application" /> 
					<!-- <input type="submit" name="updateInfo" value="Update Dealer Information"> -->
					<br />
					<span id="accountSaveMessage">&nbsp;</span><br />
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