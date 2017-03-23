<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("Treasurer"))
		header('Location: /index.php');
	elseif(!isset($_GET["id"]))
		header('Location: /manageAllAccounts.php');
	else
	{		
		$orders = array();
		$peopleID = $_GET["id"];
		$query = <<<EOD
SELECT 
ph.ItemTypeName AS Item, 
ph.PeopleID, 
CONCAT(p.FirstName, ' ', p.LastName) AS Name, 
CASE WHEN ph.ItemTypeName = 'Badge' THEN 
   CONCAT(ph.Year, ' ', bt.Description, ' ',
      CASE WHEN pb.Status = 'Paid' THEN '' ELSE pb.Status END,
      ' - Note:', ph.PaymentSource) 
   ELSE CONCAT(ph.Details, ' - Note:', ph.PaymentSource)
END AS Details, 
ph.Total, 
ph.Purchased 
FROM 
PurchaseHistory ph 
LEFT OUTER JOIN AvailableBadges ab ON ab.AvailableBadgeID = ph.ItemTypeID 
LEFT OUTER JOIN BadgeTypes bt ON bt.BadgeTypeID = ab.BadgeTypeID 
LEFT OUTER JOIN PurchasedBadges pb ON pb.RecordID = ph.RecordID
INNER JOIN People p ON p.PeopleID = ph.PeopleID 
WHERE (ph.PeopleID = $peopleID OR ph.PurchaserID = $peopleID) 
ORDER BY ph.Purchased DESC
EOD;
		//$result = $db->query("SELECT ph.ItemTypeName AS Item, ph.PeopleID, CONCAT(p.FirstName, ' ', p.LastName) AS Name, CASE WHEN ph.ItemTypeName = 'Badge' THEN CONCAT(ph.Year, ' ', bt.Description) ELSE ph.Details END AS Details, ph.Total, ph.Purchased FROM PurchaseHistory ph LEFT OUTER JOIN AvailableBadges ab ON ab.AvailableBadgeID = ph.ItemTypeID LEFT OUTER JOIN BadgeTypes bt ON bt.BadgeTypeID = ab.BadgeTypeID INNER JOIN People p ON p.PeopleID = ph.PeopleID WHERE (ph.PeopleID = " . $_SESSION["PeopleID"] . " OR ph.PurchaserID = " . $_SESSION["PeopleID"] . ") ORDER BY ph.Purchased DESC");
		$result = $db->query($query);
		while($row = $result->fetch_array())
			$orders[] = $row;
		$result->close();
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Purchase History</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxmedium">
			<h1>Purchase History</h1>
			<div id="orderHistory">
				<?php
					if(!empty($orders))
					{
						echo "<table>\r\n";
						echo "<tr><th>Item</th><th>Details</th><th>For</th><th>Price</th><th>Purchased</th></tr>\r\n";
						foreach($orders as $data)
						{
							if($data["PeopleID"] == $peopleID)
							{
								$who = $data["Name"];
								$class = "self";
							}
							else
							{
								$who = $data["Name"];
								$class = "other";
							}
							$purchased = date("F d, Y", strtotime($data["Purchased"]));
							echo "<tr class='$class'><td>" . $data["Item"] . "</td><td>" . $data["Details"] . "</td><td>$who</td><td>" .
								sprintf("$%01.2f", $data["Total"]) . "</td><td>$purchased</td></tr>\r\n";
						}
						echo "</table>\r\n";
					}
					else
						echo '<span class="noneFound">You have not purchased anything yet.</span>' . "\r\n"; ?>
				
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
