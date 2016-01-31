<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("Catan"))
		header('Location: /index.php');

	$year = date("n") >= 3 ? date("Y") + 1: date("Y");
	$capriconYear = $year - 1980;
	$sql = "SELECT p.FirstName, p.LastName, pb.BadgeName, bt.Description FROM PurchaseHistory ph JOIN People p ON " . 
		"ph.PeopleID = p.PeopleID JOIN PurchasedBadges pb ON ph.PeopleID = pb.PeopleID JOIN BadgeTypes bt ON " . 
		"ph.ItemTypeID = bt.BadgeTypeID AND ItemTypeName = 'Catan' WHERE ph.Year = $year ORDER BY bt.Description, p.LastName";
	$players = array();
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_array())
			$players[] = $row;
		$result->close();
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Catan Tournament Registrations</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript">
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Catan Tournament Registrations</h1>
			<div class="standardTable">
			<?php
				if(!empty($players))
				{
					echo "<p>The following is a list of players currently registered for the Catan Tournament at the Capricon $capriconYear:</p>\r\n";
					echo "<table>\r\n";
					echo "<tr><th>First Name</th><th>Last Name</th><th>Badge Name</th><th>Registered For</th></tr>\r\n";
					foreach($players as $player)
					{
						echo "<td>" . $player["FirstName"] . "</td><td>" . $player["LastName"] . "</td><td>" . $player["BadgeName"] . 
							"</td><td>" . $player["Description"] . "</td></tr>\r\n";
					}
					echo "</table>\r\n";
				}
				else
					echo '<p class="noneFound">There are no players registered for the tournament at Capricon $capriconYear presently.</p>' . "\r\n"; ?>
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