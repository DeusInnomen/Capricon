<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("Treasurer"))
    header('Location: /index.php');

$year = isset($_GET['year']) ? $_GET['year'] : (date("n") >= 3 ? date("Y") + 1: date("Y"));

$query = "SELECT StartDate FROM ConventionDetails WHERE Year = $year";
$result = $db->query($query);
$row = $result->fetch_array();
$day1 = date($row["StartDate"]);
$result->close();

$day2=strftime("%Y-%m-%d", strtotime("$day1 +1 day"));
$day3=strftime("%Y-%m-%d", strtotime("$day2 +1 day"));
$day4=strftime("%Y-%m-%d", strtotime("$day3 +1 day"));


//get list of available years
$query = "SELECT Year FROM ConventionDetails ORDER BY Year DESC";
$result = $db->query($query);
$years = array();
while($row = $result->fetch_array())
    $years[] = $row["Year"];
$result->close();


//get sales info for days prior to day1
$query=<<<EOD
SELECT
	DISTINCT ph.RecordID,
	ph.PurchaserID,
	ph.PurchaserOneTimeID,
	CASE WHEN ph.PurchaserID IS NULL THEN o.FirstName ELSE p.FirstName END AS FirstName,
	CASE WHEN ph.PurchaserID IS NULL THEN o.LastName ELSE p.LastName END AS LastName,
	CASE WHEN ph.PurchaserID IS NULL THEN pbo.BadgeName ELSE pbp.BadgeName END AS BadgeName,
	CASE WHEN ph.PurchaserID IS NULL THEN pbo.BadgeNumber ELSE pbp.BadgeNumber END AS BadgeNumber,
	ph.ItemTypeName,
	ph.Details,
	ph.Price AS PaidPrice,
	ph.Purchased,
	ph.PaymentSource,
	ph.PaymentReference,
	CASE WHEN ph.PaymentSource = 'Check' THEN SUBSTRING(ph.PaymentReference, 16) ELSE NULL END AS CheckNumber,
    ph.AmountRefunded
FROM
		PurchaseHistory ph
	LEFT OUTER JOIN People p ON ph.PurchaserID = p.PeopleID
	LEFT OUTER JOIN OneTimeRegistrations o ON ph.PurchaserOneTimeID = o.OneTimeID
	LEFT OUTER JOIN PurchasedBadges pbp ON ph.PeopleID = pbp.PeopleID AND ph.Year = pbp.Year
	LEFT OUTER JOIN PurchasedBadges pbo ON ph.OneTimeID = pbo.OneTimeID AND ph.Year = pbo.Year
WHERE
	ph.ItemTypeName IN ('Print Shop', 'Auction Sales', 'Hanging Fees', 'PrintShop')
	AND (ph.AmountRefunded IS null OR ph.AmountRefunded = '')
	AND ph.Year = $year
	AND date(ph.Purchased) < '$day1'
ORDER BY
	ph.ItemTypeName, ph.PaymentSource, ph.Purchased
EOD;

$result = $db->query($query);
$day0sales = array();
while($row = $result->fetch_array())
    $day0sales[] = $row;
$result->close();

//get sales summary info for days prior to day1
$query=<<<EOD
SELECT
	SUM(ph.Price) AS PaidPrice,
	ph.ItemTypeName,
	ph.PaymentSource
FROM
		PurchaseHistory ph
WHERE
	ph.ItemTypeName IN ('Print Shop', 'Auction Sales', 'Hanging Fees', 'PrintShop')
	AND (ph.AmountRefunded IS null OR ph.AmountRefunded = '')
	AND ph.Year = $year
	AND date(ph.Purchased) < '$day1'
GROUP BY
	ph.ItemTypeName, ph.PaymentSource
EOD;

$result = $db->query($query);
$day0salessummary = array();
while($row = $result->fetch_array())
    $day0salessummary[] = $row;
$result->close();



//get sales info for day 1
$query=<<<EOD
SELECT
	DISTINCT ph.RecordID,
	ph.PurchaserID,
	ph.PurchaserOneTimeID,
	CASE WHEN ph.PurchaserID IS NULL THEN o.FirstName ELSE p.FirstName END AS FirstName,
	CASE WHEN ph.PurchaserID IS NULL THEN o.LastName ELSE p.LastName END AS LastName,
	CASE WHEN ph.PurchaserID IS NULL THEN pbo.BadgeName ELSE pbp.BadgeName END AS BadgeName,
	CASE WHEN ph.PurchaserID IS NULL THEN pbo.BadgeNumber ELSE pbp.BadgeNumber END AS BadgeNumber,
	ph.ItemTypeName,
	ph.Details,
	ph.Price AS PaidPrice,
	ph.Purchased,
	ph.PaymentSource,
	ph.PaymentReference,
	CASE WHEN ph.PaymentSource = 'Check' THEN SUBSTRING(ph.PaymentReference, 16) ELSE NULL END AS CheckNumber,
    ph.AmountRefunded
FROM
		PurchaseHistory ph
	LEFT OUTER JOIN People p ON ph.PurchaserID = p.PeopleID
	LEFT OUTER JOIN OneTimeRegistrations o ON ph.PurchaserOneTimeID = o.OneTimeID
	LEFT OUTER JOIN PurchasedBadges pbp ON ph.PeopleID = pbp.PeopleID AND ph.Year = pbp.Year
	LEFT OUTER JOIN PurchasedBadges pbo ON ph.OneTimeID = pbo.OneTimeID AND ph.Year = pbo.Year
WHERE
	ph.ItemTypeName IN ('Print Shop', 'Auction Sales', 'Hanging Fees', 'PrintShop')
	AND (ph.AmountRefunded IS null OR ph.AmountRefunded = '')
	AND ph.Year = $year
	AND date(ph.Purchased) = '$day1'
ORDER BY
	ph.ItemTypeName, ph.PaymentSource, ph.Purchased
EOD;

$result = $db->query($query);
$day1sales = array();
while($row = $result->fetch_array())
    $day1sales[] = $row;
$result->close();

//get sales summary info for day 1
$query=<<<EOD
SELECT
	SUM(ph.Price) AS PaidPrice,
	ph.ItemTypeName,
	ph.PaymentSource
FROM
		PurchaseHistory ph
WHERE
	ph.ItemTypeName IN ('Print Shop', 'Auction Sales', 'Hanging Fees', 'PrintShop')
	AND (ph.AmountRefunded IS null OR ph.AmountRefunded = '')
	AND ph.Year = $year
	AND date(ph.Purchased) = '$day1'
GROUP BY
	ph.ItemTypeName, ph.PaymentSource
EOD;

$result = $db->query($query);
$day1salessummary = array();
while($row = $result->fetch_array())
    $day1salessummary[] = $row;
$result->close();



//get sales info for day 2
$query=<<<EOD
SELECT
	DISTINCT ph.RecordID,
	ph.PurchaserID,
	ph.PurchaserOneTimeID,
	CASE WHEN ph.PurchaserID IS NULL THEN o.FirstName ELSE p.FirstName END AS FirstName,
	CASE WHEN ph.PurchaserID IS NULL THEN o.LastName ELSE p.LastName END AS LastName,
	CASE WHEN ph.PurchaserID IS NULL THEN pbo.BadgeName ELSE pbp.BadgeName END AS BadgeName,
	CASE WHEN ph.PurchaserID IS NULL THEN pbo.BadgeNumber ELSE pbp.BadgeNumber END AS BadgeNumber,
	ph.ItemTypeName,
	ph.Details,
	ph.Price AS PaidPrice,
	ph.Purchased,
	ph.PaymentSource,
	ph.PaymentReference,
	CASE WHEN ph.PaymentSource = 'Check' THEN SUBSTRING(ph.PaymentReference, 16) ELSE NULL END AS CheckNumber,
    ph.AmountRefunded
FROM
		PurchaseHistory ph
	LEFT OUTER JOIN People p ON ph.PurchaserID = p.PeopleID
	LEFT OUTER JOIN OneTimeRegistrations o ON ph.PurchaserOneTimeID = o.OneTimeID
	LEFT OUTER JOIN PurchasedBadges pbp ON ph.PeopleID = pbp.PeopleID AND ph.Year = pbp.Year
	LEFT OUTER JOIN PurchasedBadges pbo ON ph.OneTimeID = pbo.OneTimeID AND ph.Year = pbo.Year
WHERE
	ph.ItemTypeName IN ('Print Shop', 'Auction Sales', 'Hanging Fees', 'PrintShop')
	AND (ph.AmountRefunded IS null OR ph.AmountRefunded = '')
	AND ph.Year = $year
	AND date(ph.Purchased) = '$day2'
ORDER BY
	ph.ItemTypeName, ph.PaymentSource, ph.Purchased
EOD;

$result = $db->query($query);
$day2sales = array();
while($row = $result->fetch_array())
    $day2sales[] = $row;
$result->close();

//get sales summary info for day 2
$query=<<<EOD
SELECT
	SUM(ph.Price) AS PaidPrice,
	ph.ItemTypeName,
	ph.PaymentSource
FROM
		PurchaseHistory ph
WHERE
	ph.ItemTypeName IN ('Print Shop', 'Auction Sales', 'Hanging Fees', 'PrintShop')
	AND (ph.AmountRefunded IS null OR ph.AmountRefunded = '')
	AND ph.Year = $year
	AND date(ph.Purchased) = '$day2'
GROUP BY
	ph.ItemTypeName, ph.PaymentSource
EOD;

$result = $db->query($query);
$day2salessummary = array();
while($row = $result->fetch_array())
    $day2salessummary[] = $row;
$result->close();



//get sales info for day 3
$query=<<<EOD
SELECT
	DISTINCT ph.RecordID,
	ph.PurchaserID,
	ph.PurchaserOneTimeID,
	CASE WHEN ph.PurchaserID IS NULL THEN o.FirstName ELSE p.FirstName END AS FirstName,
	CASE WHEN ph.PurchaserID IS NULL THEN o.LastName ELSE p.LastName END AS LastName,
	CASE WHEN ph.PurchaserID IS NULL THEN pbo.BadgeName ELSE pbp.BadgeName END AS BadgeName,
	CASE WHEN ph.PurchaserID IS NULL THEN pbo.BadgeNumber ELSE pbp.BadgeNumber END AS BadgeNumber,
	ph.ItemTypeName,
	ph.Details,
	ph.Price AS PaidPrice,
	ph.Purchased,
	ph.PaymentSource,
	ph.PaymentReference,
	CASE WHEN ph.PaymentSource = 'Check' THEN SUBSTRING(ph.PaymentReference, 16) ELSE NULL END AS CheckNumber,
    ph.AmountRefunded
FROM
		PurchaseHistory ph
	LEFT OUTER JOIN People p ON ph.PurchaserID = p.PeopleID
	LEFT OUTER JOIN OneTimeRegistrations o ON ph.PurchaserOneTimeID = o.OneTimeID
	LEFT OUTER JOIN PurchasedBadges pbp ON ph.PeopleID = pbp.PeopleID AND ph.Year = pbp.Year
	LEFT OUTER JOIN PurchasedBadges pbo ON ph.OneTimeID = pbo.OneTimeID AND ph.Year = pbo.Year
WHERE
	ph.ItemTypeName IN ('Print Shop', 'Auction Sales', 'Hanging Fees', 'PrintShop')
	AND (ph.AmountRefunded IS null OR ph.AmountRefunded = '')
	AND ph.Year = $year
	AND date(ph.Purchased) = '$day3'
ORDER BY
	ph.ItemTypeName, ph.PaymentSource, ph.Purchased
EOD;

$result = $db->query($query);
$day3sales = array();
while($row = $result->fetch_array())
    $day3sales[] = $row;
$result->close();

//get sales summary info for day 3
$query=<<<EOD
SELECT
	SUM(ph.Price) AS PaidPrice,
	ph.ItemTypeName,
	ph.PaymentSource
FROM
		PurchaseHistory ph
WHERE
	ph.ItemTypeName IN ('Print Shop', 'Auction Sales', 'Hanging Fees', 'PrintShop')
	AND (ph.AmountRefunded IS null OR ph.AmountRefunded = '')
	AND ph.Year = $year
	AND date(ph.Purchased) = '$day3'
GROUP BY
	ph.ItemTypeName, ph.PaymentSource
EOD;

$result = $db->query($query);
$day3salessummary = array();
while($row = $result->fetch_array())
    $day3salessummary[] = $row;
$result->close();



//get sales info for day 4
$query=<<<EOD
SELECT
	DISTINCT ph.RecordID,
	ph.PurchaserID,
	ph.PurchaserOneTimeID,
	CASE WHEN ph.PurchaserID IS NULL THEN o.FirstName ELSE p.FirstName END AS FirstName,
	CASE WHEN ph.PurchaserID IS NULL THEN o.LastName ELSE p.LastName END AS LastName,
	CASE WHEN ph.PurchaserID IS NULL THEN pbo.BadgeName ELSE pbp.BadgeName END AS BadgeName,
	CASE WHEN ph.PurchaserID IS NULL THEN pbo.BadgeNumber ELSE pbp.BadgeNumber END AS BadgeNumber,
	ph.ItemTypeName,
	ph.Details,
	ph.Price AS PaidPrice,
	ph.Purchased,
	ph.PaymentSource,
	ph.PaymentReference,
	CASE WHEN ph.PaymentSource = 'Check' THEN SUBSTRING(ph.PaymentReference, 16) ELSE NULL END AS CheckNumber,
    ph.AmountRefunded
FROM
		PurchaseHistory ph
	LEFT OUTER JOIN People p ON ph.PurchaserID = p.PeopleID
	LEFT OUTER JOIN OneTimeRegistrations o ON ph.PurchaserOneTimeID = o.OneTimeID
	LEFT OUTER JOIN PurchasedBadges pbp ON ph.PeopleID = pbp.PeopleID AND ph.Year = pbp.Year
	LEFT OUTER JOIN PurchasedBadges pbo ON ph.OneTimeID = pbo.OneTimeID AND ph.Year = pbo.Year
WHERE
	ph.ItemTypeName IN ('Print Shop', 'Auction Sales', 'Hanging Fees', 'PrintShop')
	AND (ph.AmountRefunded IS null OR ph.AmountRefunded = '')
	AND ph.Year = $year
	AND date(ph.Purchased) = '$day4'
ORDER BY
	ph.ItemTypeName, ph.PaymentSource, ph.Purchased
EOD;

$result = $db->query($query);
$day4sales = array();
while($row = $result->fetch_array())
    $day4sales[] = $row;
$result->close();

//get sales summary info for day 4
$query=<<<EOD
SELECT
	SUM(ph.Price) AS PaidPrice,
	ph.ItemTypeName,
	ph.PaymentSource
FROM
		PurchaseHistory ph
WHERE
	ph.ItemTypeName IN ('Print Shop', 'Auction Sales', 'Hanging Fees', 'PrintShop')
	AND (ph.AmountRefunded IS null OR ph.AmountRefunded = '')
	AND ph.Year = $year
	AND date(ph.Purchased) = '$day4'
GROUP BY
	ph.ItemTypeName, ph.PaymentSource
EOD;

$result = $db->query($query);
$day4salessummary = array();
while($row = $result->fetch_array())
    $day4salessummary[] = $row;
$result->close();



//get sales info for days after day4
$query=<<<EOD
SELECT
	DISTINCT ph.RecordID,
	ph.PurchaserID,
	ph.PurchaserOneTimeID,
	CASE WHEN ph.PurchaserID IS NULL THEN o.FirstName ELSE p.FirstName END AS FirstName,
	CASE WHEN ph.PurchaserID IS NULL THEN o.LastName ELSE p.LastName END AS LastName,
	CASE WHEN ph.PurchaserID IS NULL THEN pbo.BadgeName ELSE pbp.BadgeName END AS BadgeName,
	CASE WHEN ph.PurchaserID IS NULL THEN pbo.BadgeNumber ELSE pbp.BadgeNumber END AS BadgeNumber,
	ph.ItemTypeName,
	ph.Details,
	ph.Price AS PaidPrice,
	ph.Purchased,
	ph.PaymentSource,
	ph.PaymentReference,
	CASE WHEN ph.PaymentSource = 'Check' THEN SUBSTRING(ph.PaymentReference, 16) ELSE NULL END AS CheckNumber,
    ph.AmountRefunded
FROM
		PurchaseHistory ph
	LEFT OUTER JOIN People p ON ph.PurchaserID = p.PeopleID
	LEFT OUTER JOIN OneTimeRegistrations o ON ph.PurchaserOneTimeID = o.OneTimeID
	LEFT OUTER JOIN PurchasedBadges pbp ON ph.PeopleID = pbp.PeopleID AND ph.Year = pbp.Year
	LEFT OUTER JOIN PurchasedBadges pbo ON ph.OneTimeID = pbo.OneTimeID AND ph.Year = pbo.Year
WHERE
	ph.ItemTypeName IN ('Print Shop', 'Auction Sales', 'Hanging Fees', 'PrintShop')
	AND (ph.AmountRefunded IS null OR ph.AmountRefunded = '')
	AND ph.Year = $year
	AND date(ph.Purchased) > '$day4'
ORDER BY
	ph.ItemTypeName, ph.PaymentSource, ph.Purchased
EOD;

$result = $db->query($query);
$day5sales = array();
while($row = $result->fetch_array())
    $day5sales[] = $row;
$result->close();

//get sales summary info for days after day4
$query=<<<EOD
SELECT
	SUM(ph.Price) AS PaidPrice,
	ph.ItemTypeName,
	ph.PaymentSource
FROM
		PurchaseHistory ph
WHERE
	ph.ItemTypeName IN ('Print Shop', 'Auction Sales', 'Hanging Fees', 'PrintShop')
	AND (ph.AmountRefunded IS null OR ph.AmountRefunded = '')
	AND ph.Year = $year
	AND date(ph.Purchased) > '$day4'
GROUP BY
	ph.ItemTypeName, ph.PaymentSource
EOD;

$result = $db->query($query);
$day5salessummary = array();
while($row = $result->fetch_array())
    $day5salessummary[] = $row;
$result->close();



?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Art Show Sales Summary</title>
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
		});
    </script>
    <script type="text/javascript">
		$(function() {
			$("#tabs").tabs();
			$("#yearSelect").submit(function () {
				window.location = "artShowSalesSummary.php?year=" + $("#year").val();
				return false;
			});
		});
    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxwide">
            <h1>Art Show Sales Summary</h1>
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

            <div id="tabs">
                <ul>
                    <li>
                        <a href="#tabs-0">Before Con Sales</a>
                    </li>
                    <li>
                        <a href="#tabs-1">Thursday Sales</a>
                    </li>
                    <li>
                        <a href="#tabs-2">Friday Sales</a>
                    </li>
                    <li>
                        <a href="#tabs-3">Saturday Sales</a>
                    </li>
                    <li>
                        <a href="#tabs-4">Sunday Sales</a>
                    </li>
                    <li>
                        <a href="#tabs-5">After Con Sales</a>
                    </li>
                </ul>
                <div id="tabs-0" class="standardTable">
                    <?php
					echo "<p>Summary of sales</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Type</th><th>Payment Source</th><th>Total Price</th></tr>\r\n";
					foreach($day0salessummary as $request)
					{
						echo "<tr>";
						echo "<td>" . $request["ItemTypeName"] . "</td>";
						echo "<td>" . $request["PaymentSource"] . "</td>";
						echo "<td>" . $request["PaidPrice"] . "</td>";
						echo "</tr>\r\n";
					}
					echo "</table>\r\n";
					echo "<br>\r\n";
					echo "<br>\r\n";
					echo "<p>Detail of sales</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>First Name</th><th>Last Name</th><th>Type</th><th>Details</th><th>Paid Price</th><th>Purchase Time</th><th>Payment Source</th></tr>\r\n";
					foreach($day0sales as $request)
					{
						echo "<tr class=\"masterTooltip\" title=\"Badge Name: " . $request["BadgeName"];
						echo "<br>Badge Number " . $request["BadgeNumber"];
                        echo "\" >";
						echo "<td>" . $request["FirstName"] . "</td>";
						echo "<td>" . $request["LastName"] . "</td>";
						echo "<td>" . $request["ItemTypeName"] . "</td>";
						echo "<td>" . $request["Details"] . "</td>";
						echo "<td>" . $request["PaidPrice"] . "</td>";
						echo "<td>" . $request["Purchased"] . "</td>";
						echo "<td>" . $request["PaymentSource"] . "</td>";
						echo "</tr>\r\n";
					}
					echo "</table>\r\n";
                    ?>
                </div>
                <div id="tabs-1" class="standardTable">
                    <?php
					echo "<p>Summary of sales</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Type</th><th>Payment Source</th><th>Total Price</th></tr>\r\n";
					foreach($day1salessummary as $request)
					{
						echo "<tr>";
						echo "<td>" . $request["ItemTypeName"] . "</td>";
						echo "<td>" . $request["PaymentSource"] . "</td>";
						echo "<td>" . $request["PaidPrice"] . "</td>";
						echo "</tr>\r\n";
					}
					echo "</table>\r\n";
					echo "<br>\r\n";
					echo "<br>\r\n";
					echo "<p>Detail of sales</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>First Name</th><th>Last Name</th><th>Type</th><th>Details</th><th>Paid Price</th><th>Purchase Time</th><th>Payment Source</th></tr>\r\n";
					foreach($day1sales as $request)
					{
						echo "<tr class=\"masterTooltip\" title=\"Badge Name: " . $request["BadgeName"];
						echo "<br>Badge Number " . $request["BadgeNumber"];
                        echo "\" >";
						echo "<td>" . $request["FirstName"] . "</td>";
						echo "<td>" . $request["LastName"] . "</td>";
						echo "<td>" . $request["ItemTypeName"] . "</td>";
						echo "<td>" . $request["Details"] . "</td>";
						echo "<td>" . $request["PaidPrice"] . "</td>";
						echo "<td>" . $request["Purchased"] . "</td>";
						echo "<td>" . $request["PaymentSource"] . "</td>";
						echo "</tr>\r\n";
					}
					echo "</table>\r\n";
                    ?>
                </div>
                <div id="tabs-2" class="standardTable">
                    <?php
					echo "<p>Summary of sales</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Type</th><th>Payment Source</th><th>Total Price</th></tr>\r\n";
					foreach($day2salessummary as $request)
					{
						echo "<tr>";
						echo "<td>" . $request["ItemTypeName"] . "</td>";
						echo "<td>" . $request["PaymentSource"] . "</td>";
						echo "<td>" . $request["PaidPrice"] . "</td>";
						echo "</tr>\r\n";
					}
					echo "</table>\r\n";
					echo "<br>\r\n";
					echo "<br>\r\n";
					echo "<p>Detail of sales</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>First Name</th><th>Last Name</th><th>Type</th><th>Details</th><th>Paid Price</th><th>Purchase Time</th><th>Payment Source</th></tr>\r\n";
					foreach($day2sales as $request)
					{
						echo "<tr class=\"masterTooltip\" title=\"Badge Name: " . $request["BadgeName"] . "\" >";
						echo "<td>" . $request["FirstName"] . "</td>";
						echo "<td>" . $request["LastName"] . "</td>";
						echo "<td>" . $request["ItemTypeName"] . "</td>";
						echo "<td>" . $request["Details"] . "</td>";
						echo "<td>" . $request["PaidPrice"] . "</td>";
						echo "<td>" . $request["Purchased"] . "</td>";
						echo "<td>" . $request["PaymentSource"] . "</td>";
						echo "</tr>\r\n";
					}
					echo "</table>\r\n";
                    ?>
                </div>
                <div id="tabs-3" class="standardTable">
                    <?php
					echo "<p>Summary of sales</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Type</th><th>Payment Source</th><th>Total Price</th></tr>\r\n";
					foreach($day3salessummary as $request)
					{
						echo "<tr>";
						echo "<td>" . $request["ItemTypeName"] . "</td>";
						echo "<td>" . $request["PaymentSource"] . "</td>";
						echo "<td>" . $request["PaidPrice"] . "</td>";
						echo "</tr>\r\n";
					}
					echo "</table>\r\n";
					echo "<br>\r\n";
					echo "<br>\r\n";
					echo "<p>Detail of sales</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>First Name</th><th>Last Name</th><th>Type</th><th>Details</th><th>Paid Price</th><th>Purchase Time</th><th>Payment Source</th></tr>\r\n";
					foreach($day3sales as $request)
					{
						echo "<tr class=\"masterTooltip\" title=\"Badge Name: " . $request["BadgeName"] . "\" >";
						echo "<td>" . $request["FirstName"] . "</td>";
						echo "<td>" . $request["LastName"] . "</td>";
						echo "<td>" . $request["ItemTypeName"] . "</td>";
						echo "<td>" . $request["Details"] . "</td>";
						echo "<td>" . $request["PaidPrice"] . "</td>";
						echo "<td>" . $request["Purchased"] . "</td>";
						echo "<td>" . $request["PaymentSource"] . "</td>";
						echo "</tr>\r\n";
					}
					echo "</table>\r\n";
                    ?>
                </div>
                <div id="tabs-4" class="standardTable">
                    <?php
					echo "<p>Summary of sales</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Type</th><th>Payment Source</th><th>Total Price</th></tr>\r\n";
					foreach($day4salessummary as $request)
					{
						echo "<tr>";
						echo "<td>" . $request["ItemTypeName"] . "</td>";
						echo "<td>" . $request["PaymentSource"] . "</td>";
						echo "<td>" . $request["PaidPrice"] . "</td>";
						echo "</tr>\r\n";
					}
					echo "</table>\r\n";
					echo "<br>\r\n";
					echo "<br>\r\n";
					echo "<p>Detail of sales</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>First Name</th><th>Last Name</th><th>Type</th><th>Details</th><th>Paid Price</th><th>Purchase Time</th><th>Payment Source</th></tr>\r\n";
					foreach($day4sales as $request)
					{
						echo "<tr class=\"masterTooltip\" title=\"Badge Name: " . $request["BadgeName"] . "\" >";
						echo "<td>" . $request["FirstName"] . "</td>";
						echo "<td>" . $request["LastName"] . "</td>";
						echo "<td>" . $request["ItemTypeName"] . "</td>";
						echo "<td>" . $request["Details"] . "</td>";
						echo "<td>" . $request["PaidPrice"] . "</td>";
						echo "<td>" . $request["Purchased"] . "</td>";
						echo "<td>" . $request["PaymentSource"] . "</td>";
						echo "</tr>\r\n";
					}
					echo "</table>\r\n";
                    ?>
                </div>
                <div id="tabs-5" class="standardTable">
                    <?php
					echo "<p>Summary of sales</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>Type</th><th>Payment Source</th><th>Total Price</th></tr>\r\n";
					foreach($day5salessummary as $request)
					{
						echo "<tr>";
						echo "<td>" . $request["ItemTypeName"] . "</td>";
						echo "<td>" . $request["PaymentSource"] . "</td>";
						echo "<td>" . $request["PaidPrice"] . "</td>";
						echo "</tr>\r\n";
					}
					echo "</table>\r\n";
					echo "<br>\r\n";
					echo "<br>\r\n";
					echo "<p>Detail of sales</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>First Name</th><th>Last Name</th><th>Type</th><th>Details</th><th>Paid Price</th><th>Purchase Time</th><th>Payment Source</th></tr>\r\n";
					foreach($day5sales as $request)
					{
						echo "<tr class=\"masterTooltip\" title=\"Badge Name: " . $request["BadgeName"];
						echo "<br>Badge Number " . $request["BadgeNumber"];
							echo "\" >";
						echo "<td>" . $request["FirstName"] . "</td>";
						echo "<td>" . $request["LastName"] . "</td>";
						echo "<td>" . $request["ItemTypeName"] . "</td>";
						echo "<td>" . $request["Details"] . "</td>";
						echo "<td>" . $request["PaidPrice"] . "</td>";
						echo "<td>" . $request["Purchased"] . "</td>";
						echo "<td>" . $request["PaymentSource"] . "</td>";
						echo "</tr>\r\n";
					}
					echo "</table>\r\n";
                    ?>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="goback">
                <a href="/index.php">Return to the Main Menu</a>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>