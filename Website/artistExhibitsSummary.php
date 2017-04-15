<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("ArtShowStaff"))
		header('Location: /index.php');
		
	$year = date("n") >= 3 ? date("Y") + 1: date("Y");
	$result = $db->query("SELECT ap.ArtistAttendingID, p.Email, ad.DisplayName, ad.LegalName, ad.IsPro, ad.ArtType, ap.IsAttending, ap.AgentName, ap.AgentContact, ap.ShippingPref, ap.ShippingAddress, ap.NeedsElectricity, ap.NumTables, ap.NumGrid, ap.Notes, ap.Status, ap.StatusReason FROM ArtistPresence ap INNER JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID INNER JOIN People p ON ad.PeopleID = p.PeopleID WHERE ap.Year = $year AND ad.IsEAP = 1");
	
	$requestsEAP = array();
	while($row = $result->fetch_array())
		$requestsEAP[] = $row;
	$result->close();
	
	$result = $db->query("SELECT ap.ArtistAttendingID, p.Email, ad.DisplayName, ad.LegalName, ad.IsPro, ad.ArtType, ap.IsAttending, ap.AgentName, ap.AgentContact, ap.ShippingPref, ap.ShippingAddress, ap.NeedsElectricity, ap.NumTables, ap.NumGrid, ap.Notes, ap.Status, ap.StatusReason FROM ArtistPresence ap INNER JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID INNER JOIN People p ON ad.PeopleID = p.PeopleID WHERE ap.Year = $year AND ad.IsEAP = 0 AND ap.ShippingPref IS NOT NULL");
	
	$requestsMailIn = array();
	while($row = $result->fetch_array())
		$requestsMailIn[] = $row;
	$result->close();
	
	$result = $db->query("SELECT ap.ArtistAttendingID, p.Email, ad.DisplayName, ad.LegalName, ad.IsPro, ad.ArtType, ap.IsAttending, ap.AgentName, ap.AgentContact, ap.ShippingPref, ap.ShippingAddress, ap.NeedsElectricity, ap.NumTables, ap.NumGrid, ap.Notes, ap.Status, ap.StatusReason FROM ArtistPresence ap INNER JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID INNER JOIN People p ON ad.PeopleID = p.PeopleID WHERE ap.Year = $year AND ad.IsEAP = 0 AND ap.IsAttending = 1");
	
	$requestsAttend = array();
	while($row = $result->fetch_array())
		$requestsAttend[] = $row;
	$result->close();
	
	$result = $db->query("SELECT ap.ArtistAttendingID, p.Email, ad.DisplayName, ad.LegalName, ad.IsPro, ad.ArtType, ap.IsAttending, ap.AgentName, ap.AgentContact, ap.ShippingPref, ap.ShippingAddress, ap.NeedsElectricity, ap.NumTables, ap.NumGrid, ap.Notes, ap.Status, ap.StatusReason FROM ArtistPresence ap INNER JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID INNER JOIN People p ON ad.PeopleID = p.PeopleID WHERE ap.Year = $year AND ad.IsEAP = 0 AND ap.AgentName IS NOT NULL");
	
	$requestsAgent = array();
	while($row = $result->fetch_array())
		$requestsAgent[] = $row;
	$result->close();
	
	$result = $db->query("SELECT ars.ArtistAttendingID, COUNT(ars.ArtID) AS Pieces FROM ArtSubmissions ars JOIN ArtistPresence ap ON ars.ArtistAttendingID = ap.ArtistAttendingID WHERE ap.Year = $year GROUP BY ap.ArtistAttendingID");
	$pieces = array();
	while($row = $result->fetch_array())
		$pieces[$row["ArtistAttendingID"]] = $row["Pieces"];
	$result->close();
	
	$result = $db->query("SELECT ap.ArtistAttendingID, p.Email, ad.DisplayName, ad.LegalName, ad.IsPro, ad.ArtType, ap.IsAttending, ap.AgentName, ap.AgentContact, ap.ShippingPref, ap.ShippingAddress, ap.NeedsElectricity, ap.NumTables, ap.NumGrid, ap.Notes, ap.Status, ap.StatusReason FROM ArtistPresence ap INNER JOIN ArtistDetails ad ON ap.ArtistID = ad.ArtistID INNER JOIN People p ON ad.PeopleID = p.PeopleID WHERE ap.Year = $year AND ap.HasPrintShop = 1");
	
	$printShop = array();
	while($row = $result->fetch_array())
		$printShop[] = $row;
	$result->close();
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Artist Exhibits Summary</title>
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
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Artist Exhibits Summary</h1>
			<div id="tabs">
				<ul>
					<li><a href="#tabs-1">Emerging Artist Program</a></li>
					<li><a href="#tabs-2">Mail-In</a></li>
					<li><a href="#tabs-3">Attending</a></li>
					<li><a href="#tabs-4">Agent</a></li>
					<li><a href="#tabs-5">Print Shop</a></li>
				</ul>
				<div id="tabs-1" class="standardTable">
				<?php
					echo "<table>\r\n";
					echo "<tr><th>Display Name</th><th>Email</th><th>Handling Method</th><th>Power?</th><th># Tables</th><th># Grid</th><th>Pieces Submitted</th></tr>\r\n";
					foreach($requestsEAP as $request)
					{
						echo "<tr class=\"masterTooltip\" title=\"Legal Name: " . $request["LegalName"] . "<br>Is Professional? " . 
							($request["IsPro"] == 1 ? "Yes" : "No") . "<br>Art Type: " . $request["ArtType"];
						if($request["AgentName"] !== null)
							echo "<br>Agent Contact: " . $request["AgentContact"];
						elseif($request["ShippingPref"] !== null)
							echo "<br>Shipping Address: " . $request["ShippingAddress"];
						if(!empty($request["StatusReason"]))
							echo "<br>Status Reason: " . $request["StatusReason"];
						echo "<br>Notes: " . $request["Notes"] . "\" >";
						echo "<td>" . $request["DisplayName"] . "</td><td>" . $request["Email"] . "</td><td>";
						if($request["IsAttending"] == 1)
							echo "Attending In Person";
						elseif($request["AgentName"] !== null)
							echo "Agent: " . $request["AgentName"];
						else
							echo "Shipping via " . $request["ShippingPref"];
						echo "</td><td>" . ($request["NeedsElectricity"] == 1 ? "Yes" : "No") . "</td><td>" . $request["NumTables"] . "</td><td>" . $request["NumGrid"] . "</td><td>" . (isset($pieces[$request["ArtistAttendingID"]]) ? "<b>" . $pieces[$request["ArtistAttendingID"]] . "</b>" : "0") . "</td></tr>\r\n";
					}
					echo "</table>\r\n";
				?>
				</div>
				<div id="tabs-2" class="standardTable">
				<?php
					echo "<table>\r\n";
					echo "<tr><th>Display Name</th><th>Email</th><th>Shipping Preference</th><th>Power?</th><th># Tables</th><th># Grid</th><th>Pieces Submitted</th></tr>\r\n";
					foreach($requestsMailIn as $request)
					{
						echo "<tr class=\"masterTooltip\" title=\"Legal Name: " . $request["LegalName"] . "<br>Is Professional? " . 
							($request["IsPro"] == 1 ? "Yes" : "No") . "<br>Art Type: " . $request["ArtType"];
						echo "<br>Shipping Address: " . $request["ShippingAddress"];
						if(!empty($request["StatusReason"]))
							echo "<br>Status Reason: " . $request["StatusReason"];
						echo "<br>Notes: " . $request["Notes"] . "\" >";
						echo "<td>" . $request["DisplayName"] . "</td><td>" . $request["Email"] . "</td><td>Shipping via " . $request["ShippingPref"];
						echo "</td><td>" . ($request["NeedsElectricity"] == 1 ? "Yes" : "No") . "</td><td>" . $request["NumTables"] . "</td><td>" . $request["NumGrid"] . "</td><td>" . (isset($pieces[$request["ArtistAttendingID"]]) ? "<b>" . $pieces[$request["ArtistAttendingID"]] . "</b>" : "0") . "</td></tr>\r\n";
					}
					echo "</table>\r\n";
				?>
				</div>
				<div id="tabs-3" class="standardTable">
				<?php
					echo "<table>\r\n";
					echo "<tr><th>Display Name</th><th>Email</th><th>Power?</th><th># Tables</th><th># Grid</th><th>Pieces Submitted</th></tr>\r\n";
					foreach($requestsAttend as $request)
					{
						echo "<tr class=\"masterTooltip\" title=\"Legal Name: " . $request["LegalName"] . "<br>Is Professional? " . 
							($request["IsPro"] == 1 ? "Yes" : "No") . "<br>Art Type: " . $request["ArtType"];
						if(!empty($request["StatusReason"]))
							echo "<br>Status Reason: " . $request["StatusReason"];
						echo "<br>Notes: " . $request["Notes"] . "\" >";
						echo "<td>" . $request["DisplayName"] . "</td><td>" . $request["Email"] . "</td><td>" . ($request["NeedsElectricity"] == 1 ? "Yes" : "No") . "</td><td>" . $request["NumTables"] . "</td><td>" . $request["NumGrid"] . "</td><td>" . (isset($pieces[$request["ArtistAttendingID"]]) ? "<b>" . $pieces[$request["ArtistAttendingID"]] . "</b>" : "0") . "</td></tr>\r\n";
					}
					echo "</table>\r\n";
				?>
				</div>
				<div id="tabs-4" class="standardTable">
				<?php
					echo "<table>\r\n";
					echo "<tr><th>Display Name</th><th>Email</th><th>Agent Name</th><th>Power?</th><th># Tables</th><th># Grid</th><th>Pieces Submitted</th></tr>\r\n";
					foreach($requestsAgent as $request)
					{
						echo "<tr class=\"masterTooltip\" title=\"Legal Name: " . $request["LegalName"] . "<br>Is Professional? " . 
							($request["IsPro"] == 1 ? "Yes" : "No") . "<br>Art Type: " . $request["ArtType"];
						echo "<br>Agent Contact: " . $request["AgentContact"];
						if(!empty($request["StatusReason"]))
							echo "<br>Status Reason: " . $request["StatusReason"];
						echo "<br>Notes: " . $request["Notes"] . "\" >";
						echo "<td>" . $request["DisplayName"] . "</td><td>" . $request["Email"] . "</td><td>" . $request["AgentName"] . "</td><td>" . ($request["NeedsElectricity"] == 1 ? "Yes" : "No") . "</td><td>" . $request["NumTables"] . "</td><td>" . $request["NumGrid"] . "</td><td>" . (isset($pieces[$request["ArtistAttendingID"]]) ? "<b>" . $pieces[$request["ArtistAttendingID"]] . "</b>" : "0") . "</td></tr>\r\n";
					}
					echo "</table>\r\n";
				?>
				</div>
				<div id="tabs-5" class="standardTable">
				<?php
					echo "<table>\r\n";
					echo "<tr><th>Display Name</th><th>Email</th><th>Handling Method</th><th>Power?</th><th># Tables</th><th># Grid</th><th>Pieces Submitted</th></tr>\r\n";
					foreach($printShop as $request)
					{
						echo "<tr class=\"masterTooltip\" title=\"Legal Name: " . $request["LegalName"] . "<br>Is Professional? " . 
							($request["IsPro"] == 1 ? "Yes" : "No") . "<br>Art Type: " . $request["ArtType"];
						if($request["AgentName"] !== null)
							echo "<br>Agent Contact: " . $request["AgentContact"];
						elseif($request["ShippingPref"] !== null)
							echo "<br>Shipping Address: " . $request["ShippingAddress"];
						if(!empty($request["StatusReason"]))
							echo "<br>Status Reason: " . $request["StatusReason"];
						echo "<br>Notes: " . $request["Notes"] . "\" >";
						echo "<td>" . $request["DisplayName"] . "</td><td>" . $request["Email"] . "</td><td>";
						if($request["IsAttending"] == 1)
							echo "Attending In Person";
						elseif($request["AgentName"] !== null)
							echo "Agent: " . $request["AgentName"];
						else
							echo "Shipping via " . $request["ShippingPref"];
						echo "</td><td>" . ($request["NeedsElectricity"] == 1 ? "Yes" : "No") . "</td><td>" . $request["NumTables"] . "</td><td>" . $request["NumGrid"] . "</td><td>" . (isset($pieces[$request["ArtistAttendingID"]]) ? "<b>" . $pieces[$request["ArtistAttendingID"]] . "</b>" : "0") . "</td></tr>\r\n";
					}
					echo "</table>\r\n";
				?>
				</div>
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