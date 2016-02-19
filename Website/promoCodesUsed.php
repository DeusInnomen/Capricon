<?php
    session_start();
    include_once('includes/functions.php');
    if(!isset($_SESSION["PeopleID"]))
        header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
    elseif(!DoesUserBelongHere("RegLead"))
        header('Location: /index.php');

    $year = isset($_GET['year']) ? $_GET['year'] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
    $badges = array();
    $sql = "SELECT pb.BadgeNumber, pb.BadgeName, CASE WHEN pb.PeopleID IS NULL THEN ot.FirstName ELSE p.FirstName END AS FirstName, CASE WHEN pb.PeopleID IS NULL THEN ot.LastName ELSE p.LastName END AS LastName, pc.Code, pc.Discount, pb.Created AS Purchased FROM PurchasedBadges pb JOIN PromoCodes pc ON pb.PromoCodeID = pc.CodeID LEFT OUTER JOIN People p ON p.PeopleID = pb.PeopleID LEFT OUTER JOIN OneTimeRegistrations ot ON ot.OneTimeID = pb.OneTimeID WHERE pb.Year = $year ORDER BY Code ASC, LastName ASC";
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
	<title>Capricon Registration System -- Promotional Codes</title>
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
	<script type="text/javascript">
        $(document).ready(function () {
        });
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Promotional Codes Used</h1>
			<div class="standardTable">
<?php
if(!empty($badges))
{ ?>
				<table>
				<tr><th>Badge Number</th><th>First Name</th><th>Last Name</th><th>Badge Name</th><th>Promo Code</th><th>Discount</th><th>Purchased</th></tr>
            <?php
                foreach($badges as $badge)
                {	
                    $purchased = date("F d, Y", strtotime($badge["Purchased"]));
                    $discount = sprintf("$%01.2f", $badge["Discount"]);
                    echo "<tr><td>" . $badge["BadgeNumber"] . "</td><td>" . $badge["FirstName"] . "</td><td>" . $badge["LastName"] . "</td><td>" . $badge["BadgeName"] . "</td><td>" . $badge["Code"] . "</td><td>$discount</td><td>$purchased</td></tr>\r\n";
                } ?>				
				</table>
<?php } else { ?>
				<p style="font-size: 0.9em;">There are no promotional codes that have been used at this time.</p>
<? } ?>
			</div>
			<div class="goback">
				<a href="/index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>