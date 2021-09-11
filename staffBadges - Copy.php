<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("RegLead"))
		header('Location: /index.php');
		
	$year = isset($_GET["year"]) ? $_GET["year"] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
	$order = isset($_GET["OrderByDepartment"]) ? "Department, LastName" : "BadgeNumber";
    $sql  = "SELECT pb.BadgeID, pb.BadgeNumber, CASE WHEN pb.PeopleID IS NULL THEN CONCAT(ot.FirstName, ' ', ot.LastName) ELSE CONCAT(p.FirstName, ' ', p.LastName) END AS Name, pb.BadgeName, CASE WHEN pb.PeopleID IS NULL THEN ot.FirstName ELSE p.FirstName END AS FirstName, CASE WHEN pb.PeopleID IS NULL THEN ot.LastName ELSE p.LastName END AS LastName, pb.Department, CASE WHEN pb.BadgeTypeID = 1 THEN 'Comp Badge' ELSE bt.Description END AS BadgeType FROM PurchasedBadges pb JOIN BadgeTypes bt ON pb.BadgeTypeID = bt.BadgeTypeID LEFT OUTER JOIN People p ON p.PeopleID = pb.PeopleID LEFT OUTER JOIN OneTimeRegistrations ot ON ot.OneTimeID = pb.OneTimeID WHERE pb.Year = $year AND pb.Department IS NOT NULL AND (pb.BadgeTypeID IN (3, 4, 5) OR (pb.BadgeTypeID = 1 AND BadgeNumber < 150)) AND pb.Status = 'Paid' ORDER BY $order";
	
	$badges = array();
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_array())
			$badges[] = $row;
		$result->close();
	}
	
	$sql = "SELECT BadgeTypeID FROM PurchasedBadges WHERE Year = $year AND BadgeNumber < 150 ORDER BY BadgeNumber DESC LIMIT 1";
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		$row = $result->fetch_array();
		$lastBadgeTypeID = $row["BadgeTypeID"];
		$result->close();
	}
	else
		$lastBadgeTypeID = 4;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Staff Badges</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
	<link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<style>
		#regList {
			clear: both;
			width: 100%;
			max-height: 415px;
			overflow: auto;
		}
		#currentBadges {
			clear: both;
			width: 100%;
			max-height: 415px;
			overflow: auto;
		}
	</style>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
	<script type="text/javascript">
		function resetFields() {
			$("#searchLastName").val("");
			$("#searchBadgeName").val("");
			$("#regList").html("");
		}
		
		$(document).ready(function() {
			$(document).keypress(function(e){
				if (e.which == 13){
					$("#doSearch").click();
					return false;
				}
			});
			$("#search_form").submit(function () {
				$("#regList").html("");
				$("#actionMessage").html("");
				var lastname = $("#searchLastName").val();
				var badgename = $("#searchBadgeName").val();
				var sort = $("#sort").val();
				if($("#sortDesc").is(':checked')) sort += " DESC";
				$.post("getCompBadges.php", { lastname: lastname, badgename: badgename, sort: sort, isStaff: 1 }, function(result) {
					$("#regList").html(result);
				});
				return false;
			});
			$("#actionForm").submit(function() {
				$("#actionMessage").html("");
				var department = $("#department").val();
				var peopleID = $("input[name=id]:checked").val();
				var firstname = $("#firstName").val();
				var lastname = $("#lastName").val();
				var badgename = $("#badgeName").val();	
				if(peopleID == null && firstname == "")
				{
					$("#actionMessage").html("<p style=\"font-weight: bold;\">You must either pick a person from the results list or fill in at least the First Name to save the badge.</p>");
					return false;
				}
				var badgeTypeID = $("input[name=badgeTypeID]:checked").val();
				var order = "<?php echo isset($_GET["OrderByDepartment"]) ? "Department" : "BadgeNumber"; ?>";
				$.post("doIssueStaffBadge.php", { department: department, peopleID: peopleID, firstname: firstname, lastname: lastname, badgename: badgename, badgeType: badgeTypeID, resultsOrder: order }, function(result) {
					if(result.success)
					{
						$("#searchLastName").val("");
						$("#searchBadgeName").val("");
						$("#regList").html("");
						$("#actionMessage").html('<span style="font-weight: bold;">Staff Badge successfully created.</span>');
						$("#currentBadgeData tbody").remove();
						$.each(result.badgeData, function(i, item) {
							$('<tr>').append(
								$('<td>').text(item.Name),
								$('<td>').text(item.BadgeName),
								$('<td>').text(item.BadgeNumber),
								$('<td>').text(item.Department),
								$('<td>').text(item.BadgeType)
							).appendTo('#currentBadgeData');
						});
					}
					else
					{
						$("#actionMessage").html(result.message);
					}
				}, 'json');
				return false;
			});
			$("#moreInfo").dialog({
				autoOpen: false,
				height: 500,
				width: 600,
				modal: true,
				buttons: {
					Ok: function() { $( this ).dialog( "close" ); }
				}
			});
		});

		$(document).on("keyup", "#firstName,#lastName,#badgeName", function () {
			$(".people").prop("checked", false);
		});
		
		$(document).on("click", ".people", function() {
			$("#firstName").val("");
			$("#lastName").val("");
			$("#badgeName").val("");
		});
		
		$(document).on("keyup", "#department", function () {
			if($("#department").val() == "")
				$("#createBadge").attr("disabled", true);
			else
				$("#createBadge").removeAttr("disabled");
		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div id="moreInfo" title="More Info on Staff Badges" style="display: none;">
		<p>All Concom, Guest of Honor, and Staff badges are issued through this page. Complimentary badges that are issued to attendees are not issued through this page; visit the "Issue Complimentary Badges" page for this purpose. (Exception: Badges for guests of GOHs are Comp Badges.) There are a few rules of tradition that come with issuing these badges. In particular, the order these should be issued and numbered are:</p>
		<ul>
		<li>The Con Chair is always badge #1.</li>
		<li>The Board President is always badge #2.</li>
		<li>The rest of the Board Members and Board Staff follow the Board President.</li>
		<li>The Guests of Honor and their guests follow the Board Members.</li>
		<li>The Concom (department heads and seconds) follows the Guests of Honor.</li>
		<li>The Staff (and anyone added late) follows the Concom.</li>
		</ul>
		<p>If there are any questions about this process at any time, or you need help with something, do not hesitate
		to reach out to the IT Director or Treasurer for assistance.</p>
	</div>
	<div class="content">
		<div class="centerboxwide">
			<h1>Staff Badges</h1>
			<div class="headertitle">Tools</div>
			<p><a href="#" onClick="$('#moreInfo').dialog('open');">More Information</a>&nbsp;&nbsp;--&nbsp;&nbsp;<a href="staffBadges.php<?php echo isset($_GET["OrderByDepartment"]) ? "" : "?OrderByDepartment=1"; ?>">Show By <?php echo isset($_GET["OrderByDepartment"]) ? "Badge Number" : "Department"; ?></a>&nbsp;&nbsp;--&nbsp;&nbsp;<a href="getStaffBadgeCSV.php">Download Full List</a>&nbsp;&nbsp;--&nbsp;&nbsp;<a href="getStaffBadgeCSV.php?engravedOnly=1">Download Engraving List</a></p>
			<div class="headertitle">Issue Staff Badges</div>
			<p>To issue a badge, select the type of badge then search for the Recipient below.</p>
			<form id="badgeTypeForm" method="post">
				<span style="font-weight: bold;">Badge Type: </span>
				<label for="badgeConcom" class="fieldLabelShort"><input type="radio" id="badgeConcom" name="badgeTypeID" value="3"<?php echo ($lastBadgeTypeID == 3 ? " checked" : ""); ?>>Concom and Board</label>
				<label for="badgeGOH" class="fieldLabelShort"><input type="radio" id="badgeGOH" name="badgeTypeID" value="5"<?php echo ($lastBadgeTypeID == 5 ? " checked" : ""); ?>>Guest of Honor</label>
				<label for="badgeStaff" class="fieldLabelShort"><input type="radio" id="badgeStaff" name="badgeTypeID" value="4"<?php echo ($lastBadgeTypeID == 4 ? " checked" : ""); ?>>Staff</label>
				<label for="badgeComp" class="fieldLabelShort"><input type="radio" id="badgeComp" name="badgeTypeID" value="1"<?php echo ($lastBadgeTypeID == 1 ? " checked" : ""); ?>>Comp Badge</label><br><br>
			</form>
			<form id="search_form" method="post">
				<div style="width: 50%; float: left;">
					<label>Sort By: <select id="sort" name="sort" style="width: 27%;">
						<option value="LastName">Last Name</option>
						<option value="FirstName">First Name</option>
						<option value="BadgeName">Badge Name</option>
					</select></label><label><input type="checkbox" id="sortDesc" name="sortDesc" />Descending</label><br />
					<input type="Submit" value="Clear" onclick="resetFields(); return false;" /><input type="Submit" id="doSearch" name="doSearch" value="Search" />
				</div>
				<div style="width: 50%; float: left;">
					<label for="searchLastName">Last Name: <input type="text" id="searchLastName" /></label><br />
					<label for="searchBadgeName">Badge Name: <input type="text" id="searchBadgeName" /></label><br />
					<br />
				</div>
			</form>
			<div id="actionMessage"></div>
			<form id="actionForm" method="post">
			<div id="regList"></div>
			</form>
			<div class="headertitle">Current Staff Badges</div>
			<div id="currentBadges" class="standardTable">
			<?php
				if(!empty($badges))
				{
					echo "<table id=\"currentBadgeData\">\r\n";
					echo "<thead><tr><th>Name</th><th>Badge Name</th><th>Badge #</th><th>Department</th><th>Badge Type</th></tr></thead>\r\n";
					echo "<tbody>\r\n";
					foreach($badges as $badge)
						echo "<tr><td>" . $badge["Name"] . "</td><td>" . $badge["BadgeName"] . "</td><td>" . $badge["BadgeNumber"] . "</td><td>" . $badge["Department"] . "</td><td>" . $badge["BadgeType"] . "</td></tr>\r\n";
					echo "</tbody>\r\n";
					echo "</table>\r\n";
				}
				else
					echo "<p class=\"requiredField\">No staff badges have been issued for this convention year yet.</p>\r\n"; ?>
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