<?php
session_start();
include_once('includes/functions.php');
DoCleanup();
if(!isset($_SESSION["PeopleID"]))
    header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("RegLead"))
    header('Location: /index.php');
elseif(!isset($_GET["id"]))
    header('Location: /viewRegistrations.php');
else
{
    $message = isset($_POST["message"]) ? $_POST["message"] : "";
    $id = $_GET["id"];
    
    $result = $db->query("SELECT pb.PeopleID, pb.OneTimeID, pb.BadgeID, pb.BadgeNumber, CASE WHEN pb.PeopleID IS NULL THEN CONCAT(ot.FirstName, ' ', ot.LastName) ELSE CONCAT(p.FirstName, ' ', p.LastName) END AS Name, pb.BadgeName, pb.Created AS Purchased, CASE WHEN pb.PeopleID IS NULL THEN ot.LastName ELSE p.LastName END AS LastName, CASE WHEN pb.PeopleID IS NULL THEN ot.FirstName ELSE p.FirstName END AS FirstName, pb.Department, pb.PaymentSource, pb.PaymentReference, pc.Code, pb.BadgeTypeID, pb.AmountPaid, pb.Status FROM PurchasedBadges pb LEFT OUTER JOIN People p ON p.PeopleID = pb.PeopleID LEFT OUTER JOIN OneTimeRegistrations ot ON ot.OneTimeID = pb.OneTimeID LEFT OUTER JOIN PromoCodes pc ON pc.CodeID = pb.PromoCodeID WHERE pb.BadgeID = $id UNION SELECT pb.PeopleID, pb.OneTimeID, pb.BadgeID, pb.BadgeNumber, CASE WHEN pb.PeopleID IS NULL THEN CONCAT(ot.FirstName, ' ', ot.LastName) ELSE CONCAT(p.FirstName, ' ', p.LastName) END AS Name, pb.BadgeName, pb.Created AS Purchased, CASE WHEN pb.PeopleID IS NULL THEN ot.LastName ELSE p.LastName END AS LastName, CASE WHEN pb.PeopleID IS NULL THEN ot.FirstName ELSE p.FirstName END AS FirstName, pb.Department, pb.PaymentSource, pb.PaymentReference, pc.Code, pb.BadgeTypeID, pb.AmountPaid, pb.Status FROM PurchasedBadges pb LEFT OUTER JOIN People p ON p.PeopleID = pb.PeopleID LEFT OUTER JOIN OneTimeRegistrations ot ON ot.OneTimeID = pb.OneTimeID LEFT OUTER JOIN PromoCodes pc ON pc.CodeID = pb.PromoCodeID JOIN PurchasedBadges pb2 ON pb2.PaymentReference = pb.PaymentReference WHERE pb2.BadgeID = $id and pb2.PaymentSource IN ('Stripe', 'PayPal', 'Cash', 'Check')");
    
    if($result->num_rows > 0)
    {
        $badge = $result->fetch_array();
        $others = array();
        while($row = $result->fetch_array())
            $others[] = $row;
        $result->close();				
    }
    else
    {
        $badge = null;
        $message = "Could not find badge ID #$id!";			
    }
    
    if($badge["PaymentSource"] == "PayPal")
    {
        $date1 = new DateTime($badge["Purchased"]);
        $date2 = new DateTime();
        $diff = $date2->diff($date1)->days;	
        if($diff > 60)
        {
            $disableRefund = true;
            $disableMessage = "PayPal purchases older than 60 days cannot be refunded automatically. Contact the Treasurer for assistance.";
        }
        else
            $disableRefund = false;
    }
    elseif($badge["PaymentSource"] == "Stripe")
        $disableRefund = false;
    else
    {
        $disableRefund = true;
        $disableMessage = "Only badges paid for via Stripe or PayPal can be automatically refunded.";
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Edit Badge</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript">	
		$(document).ready(function() {
			$(document).keypress(function(e){
				if (e.which == 13){
					$("#doSearch").click();
					return false;
				}
			});
			$("#search_form").submit(function () {
				$("#regList").html("");
				var lastname = $("#searchLastName").val();
				var badgename = $("#searchBadgeName").val();
				var sort = $("#sort").val();
				if($("#sortDesc").is(':checked')) sort += " DESC";
				$.post("getCompBadges.php", { lastname: lastname, badgename: badgename, sort: sort, isTransfer: 1 }, function(result) {
					$("#regList").html(result);
				});
				return false;
			});
			$("#actionForm").submit(function() {
				$("#transferResult").html("");
				var peopleID = $("input[name=id]:checked").val();
				var firstname = $("#firstName").val();
				var lastname = $("#lastName").val();
				var badgename = $("#badgeName").val();
				if(peopleID == null && firstname == "")
				{
					$("#transferResult").html("<p style=\"font-weight: bold;\">You must either pick a person from the results list or fill in at least the First Name to transfer the badge.</p>");
					return false;
				}
				$.post("doBadgeAction.php", { action: "TransferBadge", id: <?php echo $id; ?>, peopleID: peopleID, firstname: firstname, lastname: lastname, badgename: badgename }, function(result) {
					$("#transferResult").html(result);
					setTimeout(function( ) { window.location = "<?php echo $_SERVER["REQUEST_URI"]; ?>"; }, 150);
				});
				return false;
			});
		});
		
		function checkCode() {
			$("#badgeActionNotice").html("");
			$("#transferResult").html("");
			var value = $("#promoCode").val();
			if($.trim(value).length == 0)
			{
				$("#badgeActionNotice").html("<span class=\"requiredField\">You must enter a promo code to apply to the badge.</span>");
				return false;
			}
			
			$.post("doBadgeAction.php", { action: "CheckPromoCode", id: <?php echo $id; ?>, value: value }, function(result) {
				$("#actionResult").html(result);
			});
		}
		
		function renameBadge() {
			$("#badgeActionNotice").html("");
			$("#transferResult").html("");
			var value = $("#badgeName").val();
			if($.trim(value).length == 0)
			{
				$("#badgeActionNotice").html("<span class=\"requiredField\">You must enter a value to change the badge name to.</span>");
				return false;
			}
			
			$.post("doBadgeAction.php", { action: "SetBadgeName", id: <?php echo $id; ?>, value: value }, function(result) {
				$("#actionResult").html(result);
			});
		}
		
		function changeDepartment() {
			$("#badgeActionNotice").html("");
			$("#transferResult").html("");
			var value = $("#department").val();
			if($.trim(value).length == 0)
			{
				$("#badgeActionNotice").html("<span class=\"requiredField\">You must enter a value to change the department to.</span>");
				return false;
			}
			
			$.post("doBadgeAction.php", { action: "SetDepartment", id: <?php echo $id; ?>, value: value }, function(result) {
				$("#actionResult").html(result);
			});
		}
		
		function changeType() {
			$("#badgeActionNotice").html("");
			$("#transferResult").html("");
			var value = $("input[name=badgeType]:checked").val();
			$.post("doBadgeAction.php", { action: "SetType", id: <?php echo $id; ?>, value: value }, function(result) {
				$("#actionResult").html(result);
			});
		}
		
		function deleteBadge() {
			$("#badgeActionNotice").html("");
			$("#transferResult").html("");
			if(!confirm("Are you sure you wish to delete this badge? This action CANNOT be undone!"))
				return false;
			
			$.post("doBadgeAction.php", { action: "DeleteBadge", id: <?php echo $id; ?> }, function(result) {
				$("#actionResult").html(result);
			});
		}
		<?php if(DoesUserBelongHere("SuperAdmin")) { ?>
		
		function deleteBadgeSuperAdmin() {
			$("#badgeActionNotice").html("");
			$("#transferResult").html("");
			if(!confirm("Are you sure you wish to purge this badge from the database? This action CANNOT be undone!"))
				return false;
			
			$.post("doBadgeAction.php", { action: "SuperAdminDeleteBadge", id: <?php echo $id; ?> }, function(result) {
				$("#actionResult").html(result);
			});
		}
		<?php } ?>
		
		function refundBadge() {
			$("#badgeActionNotice").html("");
			$("#transferResult").html("");
			if(!confirm("Are you sure you wish to refund this badge? This action CANNOT be undone!"))
				return false;
			
			$.post("doBadgeAction.php", { action: "RefundBadge", id: <?php echo $id; ?> }, function(result) {
				$("#actionResult").html(result);
			});
		}		
			
		function rolloverBadge() {
			$("#badgeActionNotice").html("");
			$("#transferResult").html("");
			if(!confirm("Are you sure you wish to roll this badge over to next year? This action CANNOT be undone!"))
				return false;
			
			$.post("doBadgeAction.php", { action: "RolloverBadge", id: <?php echo $id; ?> }, function(result) {
				$("#actionResult").html(result);
			});
		}

		function resetFields() {
			$("#searchLastName").val("");
			$("#searchBadgeName").val("");
			$("#regList").html("");
		}
		
		$(document).on("keyup", "#firstName,#lastName,#badgeName", function () {
			$(".people").prop("checked", false);
		});
		
		$(document).on("click", ".people", function() {
			$("#firstName").val("");
			$("#lastName").val("");
			$("#badgeName").val("");
		});
		
		function reloadWithMessage(message) {
			var form = $('<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">' + 
						 '<input type="hidden" name="message" value="' + message + '" /></form>');
			$('body').append(form);
			$(form).submit();
		}		
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Edit Badge Details</h1>
<?php
if(isset($badge))
{ ?>
			<div class="standardTable">
				<form id="badgeEditForm" method="post">
				<table>
				<tr><th>Name</th><th>Badge Name</th><th>Badge #</th><th>Purchased</th><th>Paid With</th><th>Reference #</th><th>Promo Code</th><?php if(DoesUserBelongHere("Treasurer")) echo "<th>AmountPaid</th>"; ?><th>Status</th></tr>
				<?php
    $link1 = !empty($badge["PeopleID"]) ? "<a href=\"manageAccountAdmin.php?id=" . $badge["PeopleID"] . "\">" : "";
    $link2 = !empty($badge["PeopleID"]) ? "</a>" : "";
    echo "<tr><td>$link1" . $badge["Name"] . "$link2</td><td>" . $badge["BadgeName"] . "</td><td>" . $badge["BadgeNumber"] . "</td><td>" . date("m/d/Y", strtotime($badge["Purchased"])) . "</td><td>" . $badge["PaymentSource"] . "</td><td>" . $badge["PaymentReference"] . "</td><td>" . $badge["Code"] . "</td>" . (DoesUserBelongHere("Treasurer") ? "<td>" . $badge["AmountPaid"] . "</td>" : "") . "<td>" . $badge["Status"] . "</td></tr>\r\n"; ?>
				</table>
				<?php
    if(!empty($others))
    {
        echo "<div class=\"headertitle\">Other Badges in This Purchase</div>\r\n";
        echo "<table>\r\n";
        echo "<tr><th>Name</th><th>Badge Name</th><th>Badge #</th><th>Promo Code</th>" . (DoesUserBelongHere("Treasurer") ? "<th>AmountPaid</th>" : "") . "<th>Status</th></tr>\r\n";
        foreach($others as $other)
        {
            $link = "<a href=\"editBadge.php?id=" . $other["BadgeID"] . "\">";
            if(empty($other["BadgeName"])) $link .= "[Blank Badge]";							
            echo "<tr><td>" . $other["Name"] . "</td><td>$link" . $other["BadgeName"] . "</a></td><td>" . $other["BadgeNumber"] . "</td><td>" . $other["Code"] . "</td>" . (DoesUserBelongHere("Treasurer") ? "<td>" . $other["AmountPaid"] . "</td>" : "") . "<td>" . $other["Status"] . "</td></tr>\r\n";
        }
        echo "</table>\r\n";
    } ?>
				<p>Available Actions:</p>
				<input type="submit" onclick="checkCode(); return false;" value="Apply Promo Code" <?php echo $badge["Code"] == "" ? "" : "disabled"; ?>>
				<input type="text" id="promoCode" name="promoCode" style="width: 125px;" <?php echo $badge["Code"] == "" ? "" : "disabled"; ?>/><br><br>
				<input type="submit" onclick="renameBadge(); return false;" value="Change Badge Name" <?php echo ($badge["Status"] != "Paid" ? "disabled" : ""); ?>>
				<input type="text" id="badgeName" name="badgeName" style="width: 125px;" value="<?php echo isset($badge) ? str_replace('"', '&quot;', $badge["BadgeName"]) : ""; ?>"/><br><br>
				<?php if(!empty($badge["Department"]))
                      {
                          echo "<input type=\"submit\" onclick=\"changeDepartment(); return false;\" value=\"Change Department\"> ";
                          echo "<input type=\"text\" id=\"department\" name=\"department\" style=\"width: 350px;\" value=\"" . $badge["Department"] . "\"/><br><br>\r\n";
                          echo "<input type=\"submit\" onclick=\"changeType(); return false;\" value=\"Change Badge Type\"> \r\n";
                          echo "<label for=\"badgeConcom\" class=\"fieldLabelShort\"><input type=\"radio\" id=\"badgeConcom\" name=\"badgeType\" value=\"3\"" . ($badge["BadgeTypeID"] == 3 ? " checked" : "") . ">Concom and Board</label>\r\n";
                          echo "<label for=\"badgeGOH\" class=\"fieldLabelShort\"><input type=\"radio\" id=\"badgeGOH\" name=\"badgeType\" value=\"5\"" . ($badge["BadgeTypeID"] == 5 ? " checked" : "") . ">Guest of Honor</label>\r\n";
                          echo "<label for=\"badgeStaff\" class=\"fieldLabelShort\"><input type=\"radio\" id=\"badgeStaff\" name=\"badgeType\" value=\"4\"" . ($badge["BadgeTypeID"] == 4 ? " checked" : "") . ">Staff</label>\r\n";
                          echo "<label for=\"badgeComp\" class=\"fieldLabelShort\"><input type=\"radio\" id=\"badgeComp\" name=\"badgeType\" value=\"1\"" . ($badge["BadgeTypeID"] == 1 ? " checked" : "") . ">Comp Badge</label><br><br>\r\n";
                      } ?>
				<input type="submit" onclick="deleteBadge(); return false;" value="Delete Badge" <?php echo (($badge["Status"] != "Paid" || $badge["AmountPaid"] == 0) ? "" : "disabled"); ?>>
				<?php if(DoesUserBelongHere("Treasurer")) {
                          echo '<input type="submit" onclick="deleteBadgeSuperAdmin(); return false;" value="Purge Badge From Database"><br><br>' . "\r\n";
                      }
                      if(DoesUserBelongHere("Treasurer"))
                      {
                          echo 'Refunds should only be done in extreme cases.<br>';
                          if($disableRefund)
                              echo '<input type="submit" onclick="return false;" value="Refund Badge" class="masterTooltip" title="' . $disableMessage . '" readonly><br><br>';
                          else
                              echo '<input type="submit" onclick="refundBadge(); return false;" value="Refund Badge">' . ($badge["PaymentSource"] == "PayPal" ? " Note: This badge can only be refunded in the next " . (60 - $diff) . " day" . ((60 - $diff) == 1 ? "" : "s") . " via PayPal." : "") . '<br><br>';
                      } ?>
				<?php echo ($badge["PaymentSource"] == "PayPal" ? '<p style="font-weight: bold;">CONTACT CHRIS (IT) BEFORE REFUNDING PAYPAL! Untested code!</p>' : ""); ?>
				<?php /*  <input type="submit" onclick="rolloverBadge(); return false;" value="Rollover Badge to <?php echo date("n") >= 3 ? date("Y") + 2: date("Y") + 1; ?>" <?php echo (($badge["Status"] == "Rolled Over" || $badge["AmountPaid"] == 0) ? "disabled" : ""); ?>> */ ?>
				<p>The rollover option has been removed per the Board. Phandemonium does not do rollovers.</p>
				<br>
				</form>
				<div id="actionResult"></div>
				<div class="headertitle">Transfer Badge</div>
				<p>To transfer this badge, search for the recipient below and select them, or enter a name to issue a badge to 
				someone without an account.</p>
				<form id="search_form" method="post">
					<div style="width: 50%; float: left;">
						<label>Sort By: <select id="sort" name="sort" style="width: 27%;">
							<option value="LastName">Last Name</option>
							<option value="FirstName">First Name</option>
							<option value="BadgeName">Badge Name</option>
						</select></label><label><input type="checkbox" id="sortDesc" name="sortDesc" />Descending</label><br />
						<input type="Submit" value="Clear" onclick="resetFields(); return false;" /><input type="Submit" id="doSearch" name="doSearch" value="Search" />
					</div>
					<div style="width: 50%; float: left;">
						<label for="searchLastName">Last Name: <input type="text" id="searchLastName" /></label><br />
						<label for="searchBadgeName">Badge Name: <input type="text" id="searchBadgeName" /></label><br />
						<br />
					</div>
				</form>
				<form id="actionForm" method="post">
				<div id="regList"></div>
				<div id="transferResult"></div>
				</form>
			</div>
<?php } ?>
			<div class="noticeSection" id="badgeActionNotice"><?php echo $message; ?></div>
			<div class="clearfix"></div>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>