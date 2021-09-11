<?php
session_start();
include_once('includes/functions.php');

if(!isset($_POST["badgeId"])) {
    header('Location: registrationCheckin.php');
    exit();
}

$year = isset($_GET['year']) ? $_GET['year'] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
$badge = $_POST["badgeId"];
$sql = "SELECT IFNULL(PurchaserID, OneTimePurchaserID) AS PurchaserID FROM PurchasedBadges WHERE BadgeId = $badge";
$result = $db->query($sql);
$row = $result->fetch_array();
$purchaser = $row["PurchaserID"];
$result->close();

$sql  = "SELECT pb.BadgeNumber, CASE WHEN pb.PeopleID IS NULL THEN CONCAT(ot.FirstName, ' ', ot.LastName) ELSE CONCAT(p.FirstName, ' ', p.LastName) END AS Name, " .
"pb.BadgeName, CASE WHEN p.City IS NULL THEN CONCAT(ot.City, ', ', ot.State) ELSE CONCAT(p.City, ', ', p.State) END AS Location, IFNULL(p.Email, '') AS Email " . 
"FROM PurchasedBadges pb LEFT OUTER JOIN People p ON p.PeopleID = pb.PeopleID LEFT OUTER JOIN OneTimeRegistrations ot ON ot.OneTimeID = pb.OneTimeID WHERE pb.BadgeId = $badge " .
"OR (pb.BadgeTypeID = 2 AND (pb.PurchaserID = $purchaser or pb.OneTimePurchaserID = $purchaser) AND pb.Year = $year) " . 
"ORDER BY FIELD(pb.BadgeTypeID, 1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 2) ASC";
$result = $db->query($sql);

if($result->num_rows == 0) {
    header('Location: registrationCheckin.php');
    exit();
}

$details = $result->fetch_array();
$kits = array();
while($row = $result->fetch_array())
    $kits[] = $row;
$result->close();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Main Menu</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>?202002141451" />
    <link rel="icon" href="includes/favicon.png" />
    <link rel="shortcut icon" href="includes/favicon.ico" />
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="includes/global.js"></script>
    <script type="text/javascript" src="includes/jSignature/jSignature.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#signature").jSignature({
                'background-color': 'transparent',
                'decor-color': 'transparent',
            });
        });

        function saveSignature() {
            var data = $("#signature").jSignature("getData", "svgbase64");            

            $.post("doCheckinFunctions.php", { action: "SaveSignature", img: data[1], badge: <?php echo $badge; ?> }, function(result) {
                $("#sign").attr("disabled", "disabled");
                if(result.success) {
                    $("#information").fadeOut(500, function() {
                        $("#completed").fadeIn(500).css('display', 'inline-block');
                    });
                }
                else {
                    $("#msg").html(result.message);
                    $("#sign").remoteAttr("disabled");
                }
			}, 'json');
        }
    </script>
</head>
<body>
    <?php include('includes/header_checkin.php'); ?>
    <div class="content">
        <h2>Capricon Sign-In</h2>
        <div id="information" class="checkinForm" style="display: inline-block; width: 800px;">
            <p>You are picking up badge <span style="font-weight: bold;">#<?php echo $details["BadgeNumber"]; ?></span> for 
                <span style="font-weight: bold;"><?php echo $details["Name"]; ?></span> with the name "<span style="font-weight: bold;"><?php echo $details["BadgeName"]; ?>"</span>.</p>
<?php
if(sizeof($kits) > 0) {
    ?>
            <p>In addition, you are picking up the following Kid-in-Tow badges:</p>
            <ul style="list-style-type: none;">
    <?php
    foreach($kits as $kit) {
        echo "                  <li><span style=\"font-weight: bold;\">" . $kit["Name"] . "</span> with the Badge Name '<span style=\"font-weight: bold;\">" . $kit["BadgeName"] . "</span>'</li>\n";
    }
    echo "              </ul>";
}
?>
            <p>By signing the below box and picking up your badge, you agree that you have read and will abide by the Phandemonium Code of Conduct. A copy of 
                the Code of Conduct is available for you to read now, it is also in the Program Guide.</p>
            <h3>Sign for your Badge Here</h3>
            <div class="signatureBox">
                <div id="signature"></div>
            </div>
            <form method="POST">
                <input type="submit" id="sign" name="sign" value="Accept Signature" onclick="saveSignature(); return false;" />
            </form>
            <p id="msg"></p>
        </div>
        <div id="completed" class="checkinForm" style="display: none; width: 50%;">
            <br/>
            <p>Thank you for registering to attend Capricon! We hope you have a great time. Be sure to pick up a copy of the Program Guide and the Pocket Program before you go!
                If you have any questions, seek out the Info Desk and they will be glad to assist you.</p>
            <br/><br/><br/>
            <h3><a href="registrationCheckin.php">Begin Checking In a New Attendee</a></h3>
        </div>
    </div>
    <?php include('includes/footer_checkin.php'); ?>
</body>
</html>
