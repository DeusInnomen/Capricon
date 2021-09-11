<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("ArtShowLead"))
    header('Location: index.php');

$year = isset($_GET['year']) ? $_GET['year'] : (date("n") >= 3 ? date("Y") + 1: date("Y"));

$query = "SELECT Year FROM ConventionDetails ORDER BY Year DESC";
$result = $db->query($query);
$years = array();
while($row = $result->fetch_array())
    $years[] = $row["Year"];
$result->close();

$query = "SELECT COUNT(DISTINCT ap.ArtistAttendingID) AS Artists FROM ArtistPresence ap JOIN ArtSubmissions ar ON ap.ArtistAttendingID = ar.ArtistAttendingID " .
    "WHERE Year = $year HAVING COUNT(ar.ArtID) > 0";
$result = $db->query($query);
$row = $result->fetch_array();
$numArtists = $row["Artists"];
$result->close();

$artistsPrintshop = array();
$artistsAuction = array();
$artistsInAuction = 0;
$auctionPieces = 0;
$printShopPieces = 0;
$artistsInShop = 0;
$artistsInBoth = 0;
$query = "SELECT ar.ArtistAttendingID, IsPrintShop, COUNT(*) AS Pieces FROM ArtSubmissions ar JOIN ArtistPresence ap ON ap.ArtistAttendingID = ar.ArtistAttendingID " .
    "WHERE ap.Year = $year GROUP BY ar.ArtistAttendingID, IsPrintShop";
$result = $db->query($query);
while($row = $result->fetch_array()) {
    if($row["IsPrintShop"] == 0) {
        $artistsAuction[$row["ArtistAttendingID"]] = $row["Pieces"];
        $artistsInAuction++;
        $auctionPieces += $row["Pieces"];
    }
    else {
        $artistsPrintshop[$row["ArtistAttendingID"]] = $row["Pieces"];
        $printShopPieces += $row["Pieces"];
        if(array_key_exists($row["ArtistAttendingID"], $artistsAuction)) {
            $artistsInBoth++;
            $artistsInAuction--;
        }
        else
            $artistsInShop++;
    }
}
$result->close();

$query = "SELECT COUNT(*) AS Pieces FROM ArtSubmissions ar JOIN ArtistPresence ap ON ap.ArtistAttendingID = ar.ArtistAttendingID JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID " .
    "JOIN People p ON ad.PeopleID = p.PeopleID WHERE ap.Year = $year AND p.IsCharity = 1";
$result = $db->query($query);
$row = $result->fetch_array();
$charityPieces = $row["Pieces"];
$result->close();

$query = "SELECT SUM(Price) AS Gross, SUM(Total) AS Net FROM PurchaseHistory WHERE Year = $year AND ItemTypeName = 'Auction Sales'";
$result = $db->query($query);
$row = $result->fetch_array();
$auctionGrossSales = !empty($row["Gross"]) ? $row["Gross"] : 0;;
$auctionNetSales = !empty($row["Net"]) ? $row["Net"] : 0;;
$result->close();

$query = "SELECT SUM(Price) AS Gross, SUM(Total) AS Net FROM PurchaseHistory WHERE Year = $year AND ItemTypeName = 'PrintShop'";
$result = $db->query($query);
$row = $result->fetch_array();
$printShopGrossSales = !empty($row["Gross"]) ? $row["Gross"] : 0;;
$printShopNetSales = !empty($row["Net"]) ? $row["Net"] : 0;;
$result->close();

$query = "SELECT SUM(Tax) AS Total FROM PurchaseHistory WHERE Year = $year AND ItemTypeName IN ('Auction Sales', 'PrintShop')";
$result = $db->query($query);
$row = $result->fetch_array();
$taxesCollected = !empty($row["Total"]) ? $row["Total"] : 0;;
$result->close();

$query = "SELECT SUM(ShippingPrepaid) AS Total FROM ArtistPresence WHERE Year = $year";
$result = $db->query($query);
$row = $result->fetch_array();
$shippingPrepaid = !empty($row["Total"]) ? $row["Total"] : 0;;
$result->close();

$query = "SELECT SUM(ShippingCost - IFNULL(ShippingPrepaid, 0)) AS Total FROM ArtistPresence WHERE Year = $year";
$result = $db->query($query);
$row = $result->fetch_array();
$shippingCosts = !empty($row["Total"]) ? $row["Total"] : 0;;
$result->close();

$query = "SELECT Count(Total) * 5 AS Total FROM PurchaseHistory WHERE Year = $year AND ItemTypeName = 'HangingFees' AND Details LIKE '%Mail-In Fee%'";
$result = $db->query($query);
$row = $result->fetch_array();
$mailingFees = !empty($row["Total"]) ? $row["Total"] : 0;
$result->close();

$query = "SELECT SUM(Total) AS Total FROM PurchaseHistory WHERE Year = $year AND ItemTypeName = 'HangingFees'";
$result = $db->query($query);
$row = $result->fetch_array();
$hangingFees = (!empty($row["Total"]) ? $row["Total"] : 0) - $mailingFees;
$result->close();

$query = "SELECT SUM(FinalSalePrice) AS Total FROM ArtSubmissions ar JOIN ArtistPresence ap ON ap.ArtistAttendingID = ar.ArtistAttendingID JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID " .
    "JOIN People p ON ad.PeopleID = p.PeopleID WHERE ap.Year = $year";
$result = $db->query($query);
$row = $result->fetch_array();
$expectedSales = !empty($row["Total"]) ? $row["Total"] : 0;;
$result->close();


$query = "SELECT SUM(FinalSalePrice) AS Total FROM ArtSubmissions ar JOIN ArtistPresence ap ON ap.ArtistAttendingID = ar.ArtistAttendingID JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID " .
    "JOIN People p ON ad.PeopleID = p.PeopleID WHERE ap.Year = $year AND p.IsCharity = 1";
$result = $db->query($query);
$row = $result->fetch_array();
$charitySales = !empty($row["Total"]) ? $row["Total"] : 0;;
$result->close();

$query = "SELECT SUM(FinalSalePrice * 0.1) AS Total FROM ArtSubmissions ar JOIN ArtistPresence ap ON ap.ArtistAttendingID = ar.ArtistAttendingID JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID " .
    "JOIN People p ON ad.PeopleID = p.PeopleID WHERE ap.Year = $year AND p.IsCharity = 0";
$result = $db->query($query);
$row = $result->fetch_array();
$commission = !empty($row["Total"]) ? $row["Total"] : 0;;
$result->close();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Art Show Overall Summary</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
    <link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
    <link rel="icon" href="includes/favicon.png" />
    <link rel="shortcut icon" href="includes/favicon.ico" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="includes/global.js"></script>
    <script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
    <script type="text/javascript">
        $(function () {
            $("#tabs").tabs();
        });
    </script>
    <script type="text/javascript">
        $(function () {
            $("#tabs").tabs();
            $("#yearSelect").submit(function () {
                window.location = "showArtshowSummary.php?year=" + $("#year").val();
                return false;
            });
        });
    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxwide">
            <h1>Art Show Overall Summary</h1>
            <br />
            <form id="yearSelect" method="POST" action="">
                View a different year's statistics:
                <select id="year" name="year" style="width: 8%">
                    <?php foreach($years as $yearOption) { echo "<option value=\"$yearOption\"" . ($yearOption == $year ? " selected" : "") . ">$yearOption</option>"; } ?>
                </select>
                <input type="submit" id="changeYear" name="changeYear" value="Change Year" />
            </form>
            <br />
            <div class="headertitle">Artist Presence</div>
            <div style="float: left; width: 50%; margin-bottom: 20px;">
                <label class="fieldLabelShort">Total Artists: </label><span style="font-weight: bold;"><?php echo $numArtists; ?></span><br />
                <label class="fieldLabelShort">Auction Artists: </label><span style="font-weight: bold;"><?php echo $artistsInAuction; ?></span><br />
                <label class="fieldLabelShort">Print Shop Artists: </label><span style="font-weight: bold;"><?php echo $artistsInShop; ?></span><br />
                <label class="fieldLabelShort">Artists in Both: </label><span style="font-weight: bold;"><?php echo $artistsInBoth; ?></span><br />
            </div>
            <div style="float: left; width: 50%;">
                <label class="fieldLabelShort"># Pieces in Show: </label><span style="font-weight: bold;"><?php echo $printShopPieces + $auctionPieces; ?></span><br />
                <label class="fieldLabelShort"># Pieces in Auction: </label><span style="font-weight: bold;"><?php echo $auctionPieces; ?></span><br />
                <label class="fieldLabelShort"># Pieces in Print Shop: </label><span style="font-weight: bold;"><?php echo $printShopPieces; ?></span><br />
                <label class="fieldLabelShort"># Charity Pieces: </label><span style="font-weight: bold;"><?php echo $charityPieces; ?></span><br />
            </div>
            <div class="headertitle">Sales Details</div>
            <div style="float: left; width: 50%;">
                <label class="fieldLabelShort">Total Gross Sales: </label><span style="font-weight: bold;"><?php echo sprintf("$%01.2f", $auctionGrossSales + $printShopGrossSales); ?></span><br />
                <label class="fieldLabelShort">Auction Gross Sales: </label><span style="font-weight: bold;"><?php echo sprintf("$%01.2f", $auctionGrossSales); ?></span><br />
                <label class="fieldLabelShort">Expected Auction Sales: </label><span style="font-weight: bold;"><?php echo sprintf("$%01.2f", $expectedSales); ?></span><br />
                <label class="fieldLabelShort">Print Shop Gross Sales: </label><span style="font-weight: bold;"><?php echo sprintf("$%01.2f", $printShopGrossSales); ?></span><br />
            </div>
            <div style="float: left; width: 50%;">
                <label class="fieldLabelShort">Total Net Sales: </label><span style="font-weight: bold;"><?php echo sprintf("$%01.2f", $auctionNetSales + $printShopNetSales); ?></span><br />
                <label class="fieldLabelShort">Auction Net Sales: </label><span style="font-weight: bold;"><?php echo sprintf("$%01.2f", $auctionNetSales); ?></span><br />
                <label class="fieldLabelShort">Print Shop Net Sales: </label><span style="font-weight: bold;"><?php echo sprintf("$%01.2f", $printShopNetSales); ?></span><br />
            </div>
            <p>&nbsp;</p>
            <div style="float: left; width: 50%">
                <label class="fieldLabelShort">Taxes Collected: </label><span style="font-weight: bold;"><?php echo sprintf("$%01.2f", $taxesCollected); ?></span><br />
                <label class="fieldLabelShort">Mail-In Fees Collected: </label><span style="font-weight: bold;"><?php echo sprintf("$%01.2f", $mailingFees); ?></span><br />
                <label class="fieldLabelShort">Hanging Fees Collected: </label><span style="font-weight: bold;"><?php echo sprintf("$%01.2f", $hangingFees); ?></span><br />
            </div>
            <div style="float: left; width: 50%;">
                <label class="fieldLabelShort">Commission Collected: </label><span style="font-weight: bold;"><?php echo sprintf("$%01.2f", $commission); ?></span><br />
                <label class="fieldLabelShort">Total Charity Sales: </label><span style="font-weight: bold;"><?php echo sprintf("$%01.2f", $charitySales); ?></span><br />
            </div>
            <div class="clearfix"></div>
            <div class="goback">
                <a href="index.php">Return to the Main Menu</a>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>