<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("Treasurer"))
    header('Location: /index.php');

$year = isset($_GET['year']) ? $_GET['year'] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
$capriconYear = $year - 1980;

$sql = "SELECT Year FROM ConventionDetails ORDER BY Year DESC";
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

$sql = "SELECT COUNT(*) AS Badges FROM PurchasedBadges WHERE Year = $year AND Status != 'Paid'";
$result = $db->query($sql);
$row = $result->fetch_array();
$skippedBadges = $row["Badges"];
$result->close();

$sql = "SELECT COUNT(*) AS Badges FROM PurchaseHistory WHERE Year = $year AND ItemTypeName = 'Badge'";
$result = $db->query($sql);
$row = $result->fetch_array();
$allBadgesPH = $row["Badges"];
$result->close();

$sql = "SELECT count(RecordID) AS Count, sum(Price) AS Revenue, ItemTypeName FROM PurchaseHistory WHERE Year = $year GROUP BY ItemTypeName";
$allItemsSold = array();
$result = $db->query($sql);
if($result->num_rows > 0)
{
    while($row = $result->fetch_array())
        $allItemsSold[] = $row;
    $result->close();
}

$sql = "SELECT count(RecordID) AS Count, sum(Price) AS Revenue, ItemTypeName, Details FROM PurchaseHistory WHERE Year = $year AND ItemTypeName = 'Miscellaneous Charge' GROUP BY ItemTypeName, Details";
$miscItemsSold = array();
$result = $db->query($sql);
if($result->num_rows > 0)
{
    while($row = $result->fetch_array())
        $miscItemsSold[] = $row;
    $result->close();
}

$sql="SELECT count(RecordID) AS Count, sum(Price) AS Revenue, ItemTypeName, PaymentSource FROM PurchaseHistory WHERE Year = $year GROUP BY ItemTypeName, PaymentSource ORDER BY ItemTypeName, PaymentSource";
$allItemsSoldbySource = array();
$result = $db->query($sql);
if($result->num_rows > 0)
{
    while($row = $result->fetch_array())
        $allItemsSoldbySource[] = $row;
    $result->close();
}

$sql="SELECT count(RecordID) AS Count, sum(Price) AS Revenue, ItemTypeName, PaymentSource FROM PurchaseHistory WHERE Year = $year GROUP BY ItemTypeName, PaymentSource ORDER BY ItemTypeName, PaymentSource";
$allItemsSoldbySource = array();
$result = $db->query($sql);
if($result->num_rows > 0)
{
    while($row = $result->fetch_array())
        $allItemsSoldbySource[] = $row;
    $result->close();
}

$sql = "SELECT count(RecordID) AS Count, sum(Price) AS Revenue, ItemTypeName, Details, PaymentSource FROM PurchaseHistory WHERE Year = $year AND ItemTypeName = 'Miscellaneous Charge' GROUP BY ItemTypeName, Details, PaymentSource ORDER BY ItemTypeName, Details, PaymentSource";
$miscItemsSoldbySource = array();
$result = $db->query($sql);
if($result->num_rows > 0)
{
    while($row = $result->fetch_array())
        $miscItemsSoldbySource[] = $row;
    $result->close();
}

$sql = "SELECT CAST(Purchased AS DATE) AS Day, ItemTypeName, COUNT(RecordID) AS Count, PaymentSource, SUM(Price) AS Revenue FROM PurchaseHistory WHERE Year = $year GROUP BY CAST(Purchased AS DATE), ItemTypeName, PaymentSource ORDER BY CAST(Purchased AS DATE), ItemTypeName, PaymentSource";
$itemsByDayByTypeByPayment = array();
$result = $db->query($sql);
if($result->num_rows > 0)
{
    while($row = $result->fetch_array())
        $itemsByDayByTypeByPayment[] = $row;
    $result->close();
}

$sql = "SELECT CAST(Purchased AS DATE) AS Day, ItemTypeName, COUNT(RecordID) AS Count, PaymentSource, SUM(Price) AS Revenue, Details FROM PurchaseHistory WHERE Year = $year AND ItemTypeName = 'Miscellaneous Charge' GROUP BY CAST(Purchased AS DATE), ItemTypeName, Details, PaymentSource ORDER BY CAST(Purchased AS DATE), ItemTypeName, Details, PaymentSource";
$miscItemsByDayByTypeByPayment = array();
$result = $db->query($sql);
if($result->num_rows > 0)
{
    while($row = $result->fetch_array())
        $miscItemsByDayByTypeByPayment[] = $row;
    $result->close();
}

$sql = "SELECT sum(`FinalSalePrice`) AS Revenue FROM `ArtSubmissions` WHERE `ArtistAttendingID`=130 AND `Category`='Sold'";
$charityAuction = array();
$result = $db->query($sql);
if($result->num_rows > 0)
{
    while($row = $result->fetch_array())
        $charityAuction[] = $row;
    $result->close();
}
$sql = "SELECT sum(`FinalSalePrice`) AS Revenue FROM `ArtSubmissions` WHERE `ArtistAttendingID`!=130 AND `Category`='Sold' AND `PrintNumber` is null";
$artShowTotal = array();
$result = $db->query($sql);
if($result->num_rows > 0)
{
    while($row = $result->fetch_array())
        $artShowTotal[] = $row;
    $result->close();
}

$sql = "SELECT sum(`FinalSalePrice`) AS Revenue FROM `ArtSubmissions` WHERE `ArtistAttendingID`!=130 AND `Category`='Sold' AND `PrintNumber` is not null";
$printShopTotal = array();
$result = $db->query($sql);
if($result->num_rows > 0)
{
    while($row = $result->fetch_array())
        $printShopTotal[] = $row;
    $result->close();
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Treasurer Statistics</title>
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
				window.location = "treasurerStats.php?year=" + $("#year").val();
				return false;
			});

		});
    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxwide">
            <h1>Treasurer Statistics</h1>
            <p>
                Total Paid Badges from Purchased Badges for Capricon <?php echo $capriconYear; ?>:
                <b>
                    <?php echo $allBadges; ?>
                </b>
            </p>
            <p>
                Total Non-Paid Badges from Purchased Badges for Capricon <?php echo $capriconYear; ?>:
                <b>
                    <?php echo $skippedBadges; ?>
                </b>
            </p>
            <p>
                Total Badges from Purchase History for Capricon <?php echo $capriconYear; ?>:
                <b>
                    <?php echo $allBadgesPH; ?>
                </b>
            </p>
            <br />
            <h3>Detailed Breakdowns</h3>
            <div id="tabs">
                <ul>
                    <li>
                        <a href="#tabs-1">Summarize Items</a>
                    </li>
                    <li>
                        <a href="#tabs-2">Summarize Items by type</a>
                    </li>
                    <li>
                        <a href="#tabs-3">Detail of Items</a>
                    </li>
                </ul>
                <div id="tabs-1" class="standardTable">
                    <?php
					echo "<p>Breakdown of all Items</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Count</th><th>Revenue</th><th>Item Name</th></tr>\r\n";
					$revenue = 0;
					foreach($allItemsSold as $record) {
						echo "<td>" . $record["Count"] . "</td><td>" . $record["Revenue"] . "</td><td>" . $record["ItemTypeName"] . "</td></tr>\r\n";
						$revenue += $record["Revenue"];
					}
					echo "</table>\r\n";
					echo "<p>Total Revenue: <b>" . sprintf("$%01.2f", $revenue) . "</b></p>\r\n";
					echo "<p>Breakdown of Miscellaneous Charge Items</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Count</th><th>Revenue</th><th>Misc Item Name</th></tr>\r\n";
					$revenue = 0;
					foreach($miscItemsSold as $record) {
						echo "<td>" . $record["Count"] . "</td><td>" . $record["Revenue"] . "</td><td>" . $record["Details"] . "</td></tr>\r\n";
						$revenue += $record["Revenue"];
					}
					echo "</table>\r\n";
					echo "<p>Total Revenue: <b>" . sprintf("$%01.2f", $revenue) . "</b></p>\r\n";
                    ?>
                </div>
                <div id="tabs-2" class="standardTable">
                    <?php
					echo "<p>Breakdown of all Items by Payment Type</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Count</th><th>Revenue</th><th>Item Name</th><th>Payment Type</th></tr>\r\n";
					$revenue = 0;
					foreach($allItemsSoldbySource as $record) {
						echo "<td>" . $record["Count"] . "</td><td>" . $record["Revenue"] . "</td><td>" . $record["ItemTypeName"] . "</td><td>" . $record["PaymentSource"] . "</td></tr>\r\n";
						$revenue += $record["Revenue"];
					}
					echo "</table>\r\n";
					echo "<p>Total Revenue: <b>" . sprintf("$%01.2f", $revenue) . "</b></p>\r\n";
					echo "<p>Breakdown of Miscellaneous Charge Items by Payment Type</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Count</th><th>Revenue</th><th>Misc Item Name</th><th>Payment Type</th></tr>\r\n";
					$revenue = 0;
					foreach($miscItemsSoldbySource as $record) {
						echo "<td>" . $record["Count"] . "</td><td>" . $record["Revenue"] . "</td><td>" . $record["Details"] . "</td><td>" . $record["PaymentSource"] . "</td></tr>\r\n";
						$revenue += $record["Revenue"];
					}
					echo "</table>\r\n";
					echo "<p>Total Revenue: <b>" . sprintf("$%01.2f", $revenue) . "</b></p>\r\n";
					$revenue = 0;
					echo "<p>Charity Auction Total</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Revenue</th></tr>\r\n";
					foreach($charityAuction as $record) {
						echo "<td>" . $record["Revenue"] . "</td></tr>\r\n";
						$revenue += $record["Revenue"];
					}
					echo "</table>\r\n";
					echo "<p>Art Show Total</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Revenue</th></tr>\r\n";
					foreach($artShowTotal as $record) {
						echo "<td>" . $record["Revenue"] . "</td></tr>\r\n";
						$revenue += $record["Revenue"];
					}
					echo "</table>\r\n";
					echo "<p>Print Shop Total</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Revenue</th></tr>\r\n";
					foreach($printShopTotal as $record) {
						echo "<td>" . $record["Revenue"] . "</td></tr>\r\n";
						$revenue += $record["Revenue"];
					}
					echo "</table>\r\n";
					echo "<p>Total Revenue: <b>" . sprintf("$%01.2f", $revenue) . "</b></p>\r\n";
                    ?>
                </div>
                <div id="tabs-3" class="standardTable">
                    <?php
					echo "<p>Breakdown of all Items by Payment Type</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Day</th><th>Item Name</th><th>Count</th><th>Revenue</th><th>Payment Type</th></tr>\r\n";
					$revenue = 0;
					foreach($itemsByDayByTypeByPayment as $record) {
						echo "<td>" . $record["Day"] . "</td><td>" . $record["ItemTypeName"] . "</td><td>" . $record["Count"] . "</td><td>" . $record["Revenue"] . "</td><td>" . $record["PaymentSource"] . "</td></tr>\r\n";
						$revenue += $record["Revenue"];
					}
					echo "</table>\r\n";
					echo "<p>Total Revenue: <b>" . sprintf("$%01.2f", $revenue) . "</b></p>\r\n";
					echo "<p>Breakdown of all Miscellaneous Items by Payment Type</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Day</th><th>Item Name</th><th>Count</th><th>Revenue</th><th>Payment Type</th></tr>\r\n";
					$revenue = 0;
					foreach($miscItemsByDayByTypeByPayment as $record) {
						echo "<td>" . $record["Day"] . "</td><td>" . $record["Details"] . "</td><td>" . $record["Count"] . "</td><td>" . $record["Revenue"] . "</td><td>" . $record["PaymentSource"] . "</td></tr>\r\n";
						$revenue += $record["Revenue"];
					}
					echo "</table>\r\n";
					echo "<p>Total Revenue: <b>" . sprintf("$%01.2f", $revenue) . "</b></p>\r\n";
                    ?>
                </div>
            </div>
            <br />
            <form id="yearSelect" method="POST" action="">
                View a different year's statistics:
                <select id="year" name="year" style="width: 8%">
                    <?php
					foreach($years as $yearOption)
					{
						echo "<option value=\"$yearOption\"" . ($yearOption == $year ? " selected" : "") . ">$yearOption</option>";
					}
                    ?>
                </select>
                <input type="submit" id="changeYear" name="changeYear" value="Change Year" />
            </form>
            <br />
            <div class="clearfix"></div>
            <div class="goback">
                <a href="/index.php">Return to the Main Menu</a>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>