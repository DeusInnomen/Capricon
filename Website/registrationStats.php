<?php
	session_start();
	include_once('includes/functions.php');
	include_once('includes/inc.graph.php');
	
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("RegStaff") && !DoesUserBelongHere("Marketing"))
		header('Location: /index.php');

	$year = isset($_GET['year']) ? $_GET['year'] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
	$capriconYear = $year - 1980;
	
	$sql = "SELECT DISTINCT(Year) AS Year FROM PurchasedBadges ORDER BY Year DESC";
	$result = $db->query($sql);
	$years = array();
	while($row = $result->fetch_array())
		$years[] = $row["Year"];
	$result->close();
	
	$sql = "SELECT COUNT(*) AS Badges FROM PurchasedBadges WHERE Year = $year AND Status = 'Paid'";
	$result = $db->query($sql);
	$row = $result->fetch_array();
	$allBadges = $row["Badges"];
	$result->close();
	
	$sql = "SELECT Description, COUNT(pb.BadgeTypeID) AS Badges FROM PurchasedBadges pb JOIN BadgeTypes bt ON pb.BadgeTypeID = bt.BadgeTypeID WHERE pb.Year = $year AND pb.Status = 'Paid' GROUP BY pb.BadgeTypeID";
	$badgesByType = array();
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_array())
			$badgesByType[] = $row;
		$result->close();
	}
	
	$sql = "SELECT bt.Description as Description, pb.AmountPaid as AmountPaid, COUNT(pb.AmountPaid) AS Badges, SUM(AmountPaid) AS Revenue FROM PurchasedBadges pb JOIN BadgeTypes bt ON pb.BadgeTypeID = bt.BadgeTypeID WHERE pb.Year = $year AND pb.Status = 'Paid' GROUP BY bt.Description, pb.AmountPaid";
	$badgesByTypeByAmt = array();
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_array())
			$badgesByTypeByAmt[] = $row;
		$result->close();
	}
	
	$sql = "SELECT CAST(Created AS DATE) AS Day, COUNT(*) AS Badges FROM PurchasedBadges WHERE Year = $year AND Status = 'Paid' GROUP BY CAST(Created AS DATE) ORDER BY Created";
	$badgesByDay = array();
	$badgesByDayMap = array();
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_array()) {
			$badgesByDay[] = $row;
			$badgesByDayMap[$row['Day']] = $row['Badges'];
		}
		$result->close();
	}
	
	$sql = "SELECT CAST(Created AS DATE) AS Day, Description, COUNT(pb.BadgeTypeID) AS Badges FROM PurchasedBadges pb JOIN BadgeTypes bt ON pb.BadgeTypeID = bt.BadgeTypeID WHERE Year = $year AND pb.Status = 'Paid' GROUP BY CAST(Created AS DATE), pb.BadgeTypeID ORDER BY Created, pb.BadgeTypeID";
	$badgesByDayByType = array();
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_array())
			$badgesByDayByType[] = $row;
		$result->close();
	}
	
	$sql = "SELECT CAST(Created AS DATE) AS Day, Description, COUNT(pb.BadgeTypeID) AS Badges, PaymentSource, SUM(AmountPaid) AS Revenue FROM PurchasedBadges pb JOIN BadgeTypes bt ON pb.BadgeTypeID = bt.BadgeTypeID WHERE Year = $year AND pb.Status = 'Paid' GROUP BY CAST(Created AS DATE), pb.BadgeTypeID, PaymentSource ORDER BY Created, pb.BadgeTypeID, PaymentSource";
	$badgesByDayByTypeByPayment = array();
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_array())
			$badgesByDayByTypeByPayment[] = $row;
		$result->close();
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Registration Statistics</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
	<script type="text/javascript">
		$(function() {
			$("#tabs").tabs();
			$("#yearSelect").submit(function () {
				window.location = "registrationStats.php?year=" + $("#year").val();
				return false;
			});

		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Registration Statistics</h1>
			<p>Total Registrations for Capricon <?php echo $capriconYear; ?>: <b><?php echo $allBadges; ?></b></p>
			<p><a href="registrationStatsGraph.php">View Registrations as a Graph</a></p>
			<br />
			<h3>Detailed Breakdowns</h3>
			<div id="tabs">
				<ul>
					<li><a href="#tabs-1">By Type</a></li>
					<li><a href="#tabs-2">By Day</a></li>
					<li><a href="#tabs-3">By Day And Type</a></li>
					<?php if(DoesUserBelongHere("Treasurer")) echo '<li><a href="#tabs-4">By Day, Type, And Payment</a></li>'; ?>
					<?php if(DoesUserBelongHere("Treasurer")) echo '<li><a href="#tabs-5">By Type And Payment</a></li>'; ?>
				</ul>
				<div id="tabs-1" class="standardTable">
				<?php
					echo "<table>\r\n";
					echo "<tr><th>Badge Type</th><th>Badges Sold</th></tr>\r\n";
					foreach($badgesByType as $record)
						echo "<td>" . $record["Description"] . "</td><td>" . $record["Badges"] . "</td></tr>\r\n";
					echo "</table>\r\n";
				?>
				</div>
				<div id="tabs-2" class="standardTable">
				<?php
					$cnt = 0;
					echo "<table>\r\n";
					echo "<tr><th>Date</th><th>Badges Sold</th><th>Total</th></tr>\r\n";
					foreach($badgesByDay as $record) {
						$cnt = $cnt + $record["Badges"];
						echo "<td>" . $record["Day"] . "</td><td>" . $record["Badges"] . "</td><td>" . $cnt . "</td></tr>\r\n";
					}
					echo "</table>\r\n";
				?>
				</div>
				<div id="tabs-3" class="standardTable">
				<?php
					echo "<table>\r\n";
					echo "<tr><th>Date</th><th>Badge Type</th><th>Badges Sold</th></tr>\r\n";
					foreach($badgesByDayByType as $record)
						echo "<td>" . $record["Day"] . "</td><td>" . $record["Description"] . "</td><td>" . $record["Badges"] . "</td></tr>\r\n";
					echo "</table>\r\n";
				?>
				</div>
				<?php if(DoesUserBelongHere("Treasurer"))
				{
					echo '<div id="tabs-4" class="standardTable">' . "\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Date</th><th>Badge Type</th><th>Badges Sold</th><th>Payment Type</th><th>Revenue</th></tr>\r\n";
					$revenue = 0;
					foreach($badgesByDayByTypeByPayment as $record)
					{
						echo "<td>" . $record["Day"] . "</td><td>" . $record["Description"] . "</td><td>" . $record["Badges"] . "</td><td>" . $record["PaymentSource"] . "</td><td>" . $record["Revenue"] . "</td></tr>\r\n";
						$revenue += $record["Revenue"];
					}
					echo "</table>\r\n";
					echo "<p>Total Revenue: <b>" . sprintf("$%01.2f", $revenue) . "</b></p>\r\n";
					echo '</div>' . "\r\n";
				} ?>
				<?php if(DoesUserBelongHere("Treasurer"))
				{
					echo '<div id="tabs-5" class="standardTable">' . "\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Badge Type</th><th>Payment Amount</th><th>Badges Sold</th><th>Revenue</th></tr>\r\n";
					$revenue = 0;
					foreach($badgesByTypeByAmt as $record)
					{
						echo "<td>" . $record["Description"] . "</td><td>" . $record["AmountPaid"] . "</td><td>" . $record["Badges"] . "</td><td>" . $record["Revenue"] . "</td></tr>\r\n";
						$revenue += $record["Revenue"];
					}
					echo "</table>\r\n";
					echo "<p>Total Revenue: <b>" . sprintf("$%01.2f", $revenue) . "</b></p>\r\n";
					echo '</div>' . "\r\n";
				} ?>
			</div>
			<br>
			<form id="yearSelect" method="POST" action="">
				View a different year's statistics: <select id="year" name="year" style="width: 8%"> 
				<?php
					foreach($years as $yearOption)
					{
						echo "<option value=\"$yearOption\"" . ($yearOption == $year ? " selected" : "") . ">$yearOption</option>";
					}
				?>
				</select>
				<input type="submit" id="changeYear" name="changeYear" value="Change Year">
			</form><br>
			<div class="clearfix"></div>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>