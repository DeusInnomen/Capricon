<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("Program"))
		header('Location: /index.php');

	$year = date("n") >= 3 ? date("Y") + 1: date("Y");
	$capriconYear = $year - 1980;
	$sql = "SELECT p.FirstName, p.LastName, s.PreferredContact, CASE WHEN p.Phone1Type = 'Home' THEN p.Phone1 ELSE p.Phone2 END " . 
		"AS HomePhone, CASE WHEN p.Phone1Type = 'Mobile' THEN p.Phone1 ELSE p.Phone2 END AS MobilePhone, p.Email, " . 
		"CONCAT(p.Address1, ', ', p.Address2, ', ', p.City, ', ', p.State, ' ', p.ZipCode, ' ', p.Country) AS Address, " .
		"s.Created FROM ProgramSurvey s JOIN People p ON s.PeopleID = p.PeopleID WHERE s.Year = $year ORDER BY p.LastName";
	$responses = array();
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_array())
			$responses[] = $row;
		$result->close();
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Programming Survey Responses</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#startDate").datepicker({ changeMonth: true, changeYear: true });
			$("input:radio[name=dateRange]").click(function () {
				if($("input:radio[name=dateRange]:checked").val() == "AllHistory")
					$("#startDate").attr("disabled", "disabled");
				else
					$("#startDate").removeAttr("disabled");
			});
			$("#getResponseForm").submit(function () {
				var url = "getProgramSurveyCSV.php?downloadType=" + $("input:radio[name=downloadType]:checked").val();
				if($("#preferredOnly").is(":checked"))
					url += "&preferredOnly=true";
				if($("input:radio[name=dateRange]:checked").val() == "FromDate")
					url += "&startDate=" + $("#startDate").val();
				window.location = url;
				return false;
			});			
		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Programming Survey Responses</h1>
			<p>The following is a preview of the people who have responded to the Capricon <?php echo $capriconYear; ?> Programming
			Survey. To download and view the complete survey results, use the following options:</p>
			<form id="getResponseForm" class="getResponseForm" method="post" action="">
				<span>Information to Include in Download:</span><br>
				<label for="emailOnly" class="fieldLabelShort"><input type="radio" id="emailOnly" name="downloadType" value="EmailOnly">Email Addresses Only</label>&nbsp;&nbsp;
				<label for="emailAndNames" class="fieldLabelShort"><input type="radio" id="emailAndNames" name="downloadType" value="EmailAndNames">Email and Names</label>&nbsp;&nbsp;
				<label for="allResponses" class="fieldLabelShort"><input type="radio" id="allResponses" name="downloadType" value="AllResponses" checked >Full Survey Responses</label><br>
				<label for="preferredOnly" class="fieldLabelShort"><input type="checkbox" id="preferredOnly" name="preferredOnly" checked >Show Preferred Contact Only in Full Survey Responses</label><br>
				<label for="allHistory" class="fieldLabelShort"><input type="radio" id="allHistory" name="dateRange" value="AllHistory" checked >Get All Responses</label>&nbsp;&nbsp;
				<label for="fromDate" class="fieldLabelShort"><input type="radio" id="fromDate" name="dateRange" value="FromDate" >Get Responses Since:</label>&nbsp;
				<input type="text" id="startDate" name="startDate" style="width: 75px;" disabled /><br>
				<input type="submit" id="getResponses" name="getResponses" value="Download Responses As CSV File"><br>
			</form>
			<p>Total Responses: <b><?php echo count($responses); ?></b></p>
			<div class="standardTable">
			<table>
				<tr><th>First Name</th><th>Last Name</th><th>Contact By</th><th>Contact Info</th><th>Created</th></tr>
<?
				foreach($responses as $response)
				{
					echo "				<tr><td>" . $response["FirstName"] . "</td><td>" . $response["LastName"] . "</td><td>" .
						$response["PreferredContact"] . "</td><td>";
					if($response["PreferredContact"] == "Email")
						echo $response["Email"];
					elseif($response["PreferredConatct"] == "Home")
						echo $response["HomePhone"];
					elseif($response["PreferredConatct"] == "Mobile")
						echo $response["MobilePhone"];
					elseif($response["PreferredConatct"] == "Mail")
						echo $response["Address"];
					echo "</td><td>" . $response["Created"] . "</td></tr>\r\n";
				} ?>
			</table>
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