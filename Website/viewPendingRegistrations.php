<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("RegLead"))
		header('Location: /index.php');
	else
	{
		$badges = array();
		$sql = "SELECT pb.BadgeID, ph.Year, ph.PeopleID, CONCAT(p.FirstName, ' ', p.LastName) AS Name, CASE WHEN pb.Department IS NOT NULL THEN CONCAT (bt.Description, ': ', pb.Department) ELSE bt.Description END AS Description, " . 
            "CONCAT ('Badge Name: ', pb.BadgeName) AS Details, ph.Total, ph.Purchased FROM PurchaseHistory ph INNER JOIN PurchasedBadges pb ON ph.Year = pb.Year AND ph.PeopleID = pb.PeopleID INNER JOIN BadgeTypes bt ON " . 
            "bt.BadgeTypeID = ph.ItemTypeID INNER JOIN People p ON p.PeopleID = ph.PeopleID WHERE ph.ItemTypeName = 'Badge' AND pb.Status = 'Pending' ORDER BY p.LastName DESC";		
		$result = $db->query($sql);
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
				$badges[] = $row;
			$result->close();
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Pending Registrations</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<!--[if (gte IE 6)&(lte IE 8)]>
	<script type="text/javascript" src="includes/selectivizr-min.js"></script>
	<![endif]-->
	<script type="text/javascript">
		$(document).ready(function() {
			$("#updateBadges tr").click(function(e) {
				if(e.target.type !== "radio")
					$(":radio", this).trigger("click");
			});
			$("#updateBadges :radio").click(function () {
				$("#approve").removeProp("disabled");
				$("#checkNum").removeProp("disabled");
			});			
			$("#updateBadges").submit(function () {
				var checkNum = $("#updateBadges input#checkNum").val();
				if(checkNum == "")
					return false;
				var badgeID = $("#updateBadges input[name=badgeID]:checked").val();
				$.post("doBadgeAction.php", { action: "ApproveBadge", id: badgeID, checkNumber: checkNum }, function(result) {
					location.reload();
				});
				return false;
			});
		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Pending Registrations</h1>
			<p style="font-size: 0.9em;">The following is a list of registrations that are in the Pending status, due to awaiting a check being 
			mailed in to Phandemonium. These should only be approved once the check has been received and the check number can be verified.</p>
			<div id="updateBadges" class="standardTable">
				<?php
					if(!empty($badges))
					{
						echo "<form id=\"updateBadgeForm\" method=\"post\">\r\n";
						echo "<table>\r\n";
						echo "<tr><th>Select</th><th>Year</th><th>Name</th><th>Description</th><th>Details</th><th>Price</th><th>Purchased</th></tr>\r\n";
						foreach($badges as $data)
						{
							$created = date("F d, Y", strtotime($data["Purchased"]));
							echo "<tr><td style=\"text-align: center;\"><input type=\"radio\" id=\"badgeID\" name=\"badgeID\" value=\"" . $data["BadgeID"] . "\" /></td><td>" . $data["Year"] . "</td><td>" . $data["Name"] . 
                                "</td><td>" . $data["Description"] . "</td><td>" . $data["Details"] . "</td><td>" . sprintf("$%01.2f", $data["Total"]) . "</td><td>$created</td></tr>\r\n";
						}
						echo "</table><br>\r\n";
						echo "<input type=\"submit\" id=\"approve\" name=\"approve\" value=\"Approve With Check #\" disabled>\r\n";
						echo "<input type=\"text\" id=\"checkNum\" name=\"checkNum\" style=\"width: 75px;\" disabled />\r\n";
						echo "</form>\r\n";
					}
					else
						echo '<p class="noneFound">There are no pending registrations at this time.</p>' . "\r\n"; ?>
			</div>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>