<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("RegStaff"))
		header('Location: /index.php');
		
	$year = date("n") >= 3 ? date("Y") + 1: date("Y");
    $sql  = "SELECT ab.AvailableBadgeID, bt.Description, ab.Price, ab.AvailableFrom, ab.AvailableTo, ab.AvailableOnline FROM AvailableBadges ab JOIN BadgeTypes bt ON ab.BadgeTypeID = bt.BadgeTypeID WHERE ab.Year = $year ORDER BY ab.AvailableFrom ASC";
	
	$badges = array();
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_array())
			$badges[] = $row;
		$result->close();
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Available Badge Types and Rates</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#oldAvailableFrom").datepicker({ minDate: new Date(<?php echo ($year - 1); ?>, 1, 1), maxDate: new Date(<?php echo $year; ?>, 2, 0), changeMonth: true, changeYear: true });
			$("#oldAvailableTo").datepicker({ minDate: new Date(<?php echo ($year - 1); ?>, 1, 1), maxDate: new Date(<?php echo $year; ?>, 2, 0), changeMonth: true, changeYear: true });
			$("#badgeList table tr").click(function(e) {
				if(e.target.type !== "radio")
					$(":radio", this).trigger("click");
			});
			$("#badgeList table tr :input:radio").click(function () {
				$("#badgeList :input:not(.badgeNumber)").removeAttr("disabled");
			});

		});

		function setAvailableFrom()
		{
			$("#badgeActionNotice").html("");
			var from = $("#oldAvailableFrom").val();
			if($.trim(from).length == 0)
			{
				$("#badgeActionNotice").html("<span class=\"requiredField\">You must choose a value date for the price to start.</span>");
				return false;
			}
			var id = $("#badgeList table tr :input:radio:checked").val();
			$.post("doBadgeAction.php", { action: "SetBadgeAvailableFrom", id: id, from: from }, function(result) {
				$("#badgeActionNotice").html(result);
			});
		}

		function setAvailableTo()
		{
			$("#badgeActionNotice").html("");
			var to = $("#oldAvailableTo").val();
			if($.trim(to).length == 0)
			{
				$("#badgeActionNotice").html("<span class=\"requiredField\">You must choose a value date for the price to expire.</span>");
				return false;
			}
			var id = $("#badgeList table tr :input:radio:checked").val();
			$.post("doBadgeAction.php", { action: "SetBadgeAvailableTo", id: id, to: to }, function(result) {
				$("#badgeActionNotice").html(result);
			});
		}

		function setPrice()
		{
			$("#badgeActionNotice").html("");
			var price = $("#oldPrice").val();
			if($.trim(price).length == 0)
			{
				$("#badgeActionNotice").html("<span class=\"requiredField\">You must set a price for the badge.</span>");
				return false;
			}			
			var id = $("#badgeList table tr :input:radio:checked").val();
			$.post("doBadgeAction.php", { action: "SetBadgePrice", id: id, price: price }, function(result) {
				$("#badgeActionNotice").html(result);
			});
		}
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Available Badges for Current Con Year</h1>
			<p>The following is a list of badges defined for the current convention year. <span style="font-weight: bold;">'Active'</span>badges are presently available for purchase through this website. <span style="font-weight: bold;">'Expires Soon'</span> badges will be unavailable within one week. <span style="font-weight: bold;">'Onsite Only'</span> badges are only available for purchase via the Registration software used at the convention. <?php echo DoesUserBelongHere("RegLead") ? "You may edit the badges below, but please do not alter any values without contacting IT and the Treasurer first. " : ""; ?>Please contact <a href="mailto:it@phandemonium.org">it@phandemonium.org</a> if additional badge options are needed.</p>
			<div id="badgeList" class="standardTable">
			<form id="badgeEditor" method="post">
			<?php
				if(!empty($badges))
				{
					echo "<table id=\"badgeData\">\r\n";
					echo "<thead><tr>" . (DoesUserBelongHere("RegLead") ? "<th>Select</th>" : "") ."<th>Type</th><th>Price</th><th>Available From</th><th>Available Through</th><th>Status</th></tr></thead>\r\n";
					echo "<tbody>\r\n";
					foreach($badges as $badge) {
						$style = "";
						if($badge["AvailableOnline"] == 0)
							$status = "Onsite Only";
						elseif(strtotime($badge["AvailableFrom"]) >= time())
							$status = "Not Yet Active";
						elseif(new DateTime($badge["AvailableTo"] . ' 23:59') < new DateTime()) {
							$status = "Expired";
							$style = " style='background-color: #FF6666;'";
						}
						elseif(strtotime($badge["AvailableTo"]) - 604800 < time()) { // 1 week warning
							$status = "Expires Soon";
							$style = " style='background-color: #66E0A3;'";
						}
						else {
							$status = "Active";
							$style = " style='background-color: #66E0A3;'";
						}
						echo "<tr$style>" . (DoesUserBelongHere("RegLead") ? "<td style=\"text-align: center;\"><input type=\"radio\" name=\"badges\" " . 
							"value=\"" . $badge["AvailableBadgeID"] . "\" class=\"badgeNumber\" /></td>" : "") . "<td>" . $badge["Description"] . 
							"</td><td>" . sprintf("$%01.2f", $badge["Price"]) . "</td><td>" . date("F jS, Y", strtotime($badge["AvailableFrom"])) . 
							"</td><td>" . date("F jS, Y", strtotime($badge["AvailableTo"])) . "</td><td>$status</td></tr>\r\n";
					}
					echo "</tbody>\r\n";
					echo "</table>\r\n";
				}
				else
					echo "<p class=\"requiredField\">There are no currently defined badges!</p>\r\n";
				if(DoesUserBelongHere("RegLead")) { ?>
			<p>With Selected:<br />
				<input type="submit" onclick="setAvailableFrom(); return false;" value="Set Available From:" disabled>
				<input type="text" id="oldAvailableFrom" name="oldAvailableFrom" style="width: 75px;" disabled /><br />
				<input type="submit" onclick="setAvailableTo(); return false;" value="Set Available Through:" disabled>
				<input type="text" id="oldAvailableTo" name="oldAvailableTo" style="width: 75px;" disabled /><br />
				<input type="submit" onclick="setPrice(); return false;" value="Set Price:" disabled>							
				<label>$<input type="text" id="oldPrice" name="oldPrice" style="width: 50px;" disabled /></label><br />
			</p>
			<?php } ?>
			</div>
			</form>
			<div class="noticeSection" id="badgeActionNotice"><?php echo $message; ?></div>
			<div class="clearfix"></div>
			<div class="goback">
				<a href="/index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>