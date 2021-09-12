<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	else
	{
		$id = $_SESSION["PeopleID"];
		$orders = array();
		$sql = "SELECT ph.Year, ph.PeopleID, CONCAT(p.FirstName, ' ', p.LastName) AS Name, CASE WHEN pb.Department IS NOT NULL THEN CONCAT (bt.Description, ': ', pb.Department) ELSE bt.Description END AS Description, CONCAT ('Badge Name: ', pb.BadgeName) AS Details, ph.Total, ph.Purchased FROM PurchaseHistory ph INNER JOIN PurchasedBadges pb ON ph.Year = pb.Year AND ph.PeopleID = pb.PeopleID INNER JOIN BadgeTypes bt ON bt.BadgeTypeID = ph.ItemTypeID INNER JOIN People p ON p.PeopleID = ph.PeopleID WHERE (ph.PeopleID = $id OR ph.PurchaserID = $id) AND ph.ItemTypeName = 'Badge' ORDER BY ph.Purchased DESC";		
		$result = $db->query($sql);
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
				$orders[] = $row;
			$result->close();
		}
		
		$others = array();
		$badgeNames = array();
		$result = $db->query("SELECT PeopleID, CONCAT(FirstName, ' ', LastName) AS Name, BadgeName FROM People WHERE ParentID = " .
			$_SESSION["PeopleID"]);
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
			{
				$others[] = $row;
				$badgeNames[$row["PeopleID"]] = $row["BadgeName"];
			}
			$result->close();
		}
		
		$badges = array();
		$result = $db->query("SELECT ab.AvailableBadgeID, bt.Description, bt.CategoryID, ab.Year, ab.Price, ab.AvailableTo, ab.BadgeTypeID FROM AvailableBadges ab 
			INNER JOIN BadgeTypes bt ON bt.BadgeTypeID = ab.BadgeTypeID WHERE ab.AvailableFrom <= CURDATE() AND ab.AvailableTo >= CURDATE() AND ab.AvailableOnline = 1
			ORDER BY ab.BadgeTypeID ASC, ab.Price ASC");
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
	<title>Capricon Registration System -- Items</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
	<link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
	<!--[if (gte IE 6)&(lte IE 8)]>
	<script type="text/javascript" src="includes/selectivizr-min.js"></script>
	<![endif]-->
	<script type="text/javascript">
		$(document).ready(function() {
			$("#badgeName").keypress(function(e) {
				if(e.keyCode == 13)
				{
					$(this).parent().parent().parent().find('.ui-dialog-buttonset button:first').click();
					return false;
				}
			});		
			$("#getBadgeNameForm").dialog({
				autoOpen: false,
				height: 270,
				width: 325,
				modal: true,
				buttons: {
					OK: function() {
						$(this).dialog("close");
						addItemToCart();
					},
					Cancel: function() {
						$("#orderBadgeForm :input").removeProp("readonly");
						$(this).dialog("close");
					}
				}
			});
			$("#orderBadgeForm :radio").click(function () {
				$("#addToCart").removeProp("disabled");
				if($("#orderBadgeForm input[name=badgeType]:checked").attr("typeID") == "2")
					$("#addToCartNotice").html('<span style="color: #FF0000; font-weight: bold;">NOTE: Because Kids-in-Tow badges are for ages 12 and ' + 
					'under only, they must be accompanied by a parent at all times, and ID will be checked at Registration.</span>');
				else
					$("#addToCartNotice").html("<br />");
			});
			$("#orderBadgeForm").submit(function () {
				$("#orderBadgeForm :input").prop("readonly", true);
				if($("#orderBadgeForm input[name=badgeType]:checked").attr("categoryID") == "1")
				{
					var who = $("input[name=recipient]:checked").val();
					if(who == "self")
						$("#badgeNameForm input#badgeName").val("<?php echo addslashes($_SESSION["BadgeName"]); ?>");
					else
						$("#badgeNameForm input#badgeName").val($("#otherName :selected").attr("badge"));
					$("#getBadgeNameForm").dialog("open");
				}
				else
				{
					$("#badgeNameForm input#badgeName").val("");
					addItemToCart();
				}
				return false;
			});
		});
		
		function addItemToCart() {
			$("#addToCartNotice").html("<br />");
			var who = $("input[name=recipient]:checked").val();
			if(who == "self")
				var recipient = "<?php echo $_SESSION["PeopleID"]; ?>";
			else
				var recipient = $("#otherName :selected").val();
			var badgeNumber = $("#orderBadgeForm input[name=badgeType]:checked").val();
			var badgeName = $("#badgeNameForm input#badgeName").val();
			if($.trim(badgeName) == "")
			{
				var who = $("#orderBadgeForm input[name=recipient]:checked").val();
				if(who == "self")
					badgeName = "<?php echo addslashes($_SESSION["BadgeName"]); ?>";
				else
					badgeName = $("#otherName :selected").attr("badge");
			}
			$.post("doCartAction.php", { entry: "AddItem", recipient: recipient, type: badgeNumber, badgeName: badgeName }, function(result) {
				if(result.success)
					location.reload();
				else
				{
					$("#orderBadgeForm :input").removeProp("readonly");
					$("#addToCartNotice").html(result.message);
				}
			}, 'json');
		}
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div id="getBadgeNameForm" title="Badge Name">
		<form id="badgeNameForm" method="post">
			<p>Please enter the name to put on this badge:</p>
			<input type="text" name="badgeName" id="badgeName" placeholder="If Blank, the account default is Used." style="width: 70%;" /><br />			
		</form>
	</div>
	<div class="content">
		<div class="centerboxwide">
			<h1>Convention Items</h1>
			<div class="headertitle">Order Convention Items</div>
			<p style="font-size: 0.9em;">To purchase items, such as convention badges, click an available option below to select it, then press Add to Cart.
			Your shopping cart will appear to the upper left just below the header.<br /> 
			<?php if(empty($others))
				echo 'Right now, you can only purchase items for yourself. To buy items for family members or other people, visit the ' . 
                    '<a href="manageRelatedPeople.php">Manage Related People</a> page to add people to your account.';
			else
				echo 'The Someone Else list is managed from the <a href="manageRelatedPeople.php">Manage Related People</a> page, which ' . 
                    'allows you to add family members to your account without needing a separate account for them. Note that the ' .
                    'Kids-in-Tow badge is for ages 12 and under only.';
			?></p>
			<?php if(!empty($badges)) { ?>
			<div id="recipients" <?php if(empty($others)) echo 'style="display: none;" '; ?>>
			<span style="font-size: 1em;">First, choose who this item is being bought for: <label for="self"><input type="radio" name="recipient" id="self" value="self" checked/>Myself</label>
			<label for="other" class="masterTooltip" title="This lists people who are registered under your account.">
			<input type="radio" name="recipient" id="other" value="other" <?php if(empty($others)) echo "disabled"; ?>/>Someone Else:</label></span>
			<select id="otherName" name="otherName" style="width:200px" <?php if(empty($others)) echo "disabled"; ?>>
				<?php
					if(!empty($others))
					{
						foreach($others as $other)
							echo "<option value=\"" . $other["PeopleID"] . "\" badge=\"" . $badgeNames[$other["PeopleID"]] . 
								"\" >" . $other["Name"] . "</option>\r\n";
					}
					else
						echo "<option>No Available Options</option>\r\n";
				?>
			</select><br /></div>
			<form id="orderBadgeForm" method="post">
			<div class="badgeList clearfix">
				<span style="font-size: 1em;">Next, click the item below that you wish to add to your cart, then press "Add to Cart":</span><br />
				<?php
				foreach($badges as $badge)
				{
					if(empty($others) && $badge["BadgeTypeID"] == 2) continue;
					echo "<label for=\"badge" . $badge["AvailableBadgeID"] . "\"><input type=\"radio\" name=\"badgeType\" id=\"badge" .
						$badge["AvailableBadgeID"] . "\" value=\"" . $badge["AvailableBadgeID"] . "\" typeID=\"" . $badge["BadgeTypeID"] .
						"\" categoryID=\"" . $badge["CategoryID"] . "\" /><span>" . $badge["Year"] . " " . $badge["Description"] . "<br />" . sprintf("$%01.2f", $badge["Price"]) . " until " . 
						date("F jS, Y", strtotime($badge["AvailableTo"])) .	"</span></label>\r\n"; 
				} ?>
				<div style="clear: both; float: left; margin-top: 10px;"><input type="submit" id="addToCart" value="Add to Cart" disabled /></div>
			</div>
			<?php }	else echo "<h3>There are no badges available for purchase at this time.</h3>\r\n"; ?>
			<div id="addToCartNotice"><br /></div>
			<div class="headertitle">Previously Purchased Items</div>
			<div id="orderHistory">
				<?php
					if(!empty($orders))
					{
						echo "<table>\r\n";
						echo "<tr><th>Year</th><th>Item Type</th><th>Details</th><th>For</th><th>Price</th><th>Purchased</th></tr>\r\n";
						foreach($orders as $data)
						{
							if($data["PeopleID"] == $_SESSION["PeopleID"])
							{
								$who = "Myself";
								$class = "self";
							}
							else
							{
								$who = $data["Name"];
								$class = "other";
							}
							$created = date("F d, Y", strtotime($data["Purchased"]));
							echo "<tr class='$class'><td>" . $data["Year"] . "</td><td>" . $data["Description"] . "</td><td>" . 
								$data["Details"] . "</td><td>$who</td><td>" . sprintf("$%01.2f", $data["Total"]) . 
								"</td><td>$created</td></tr>\r\n";
						}
						echo "</table>\r\n";
					}
					else
						echo '<p class="noneFound">You have not purchased any items.</p>' . "\r\n"; ?>
			</div>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>