<?php
	session_start();
	include_once('includes/functions.php');
	include_once('includes/inc.graph.php');
	
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("RegStaff"))
		header('Location: /index.php');

	$year = isset($_GET['year']) ? $_GET['year'] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
	$lastYear = strval(intval($year) - 1);
	$capriconYear = $year - 1980;
	
	$sql = "SELECT DISTINCT(Year) AS Year FROM PurchasedBadges ORDER BY Year DESC";
	$result = $db->query($sql);
	$years = array();
	while($row = $result->fetch_array())
		$years[] = $row["Year"];
	$result->close();
	
	$sql = "SELECT COUNT(*) AS Badges FROM PurchasedBadges WHERE Year = $year AND Status = 'Paid'";
	$result = $db->query($sql);
	$row = $result->fetch_array();
	$allBadges = $row["Badges"];
	$result->close();
	
	
	$sql = "SELECT CAST(Created AS DATE) AS Day, COUNT(*) AS Badges FROM PurchasedBadges WHERE Year = $year AND Status = 'Paid' GROUP BY CAST(Created AS DATE) ORDER BY Created";
	$badgesByDay = array();
	$result = $db->query($sql);
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_array())
			$badgesByDayMap[$row['Day']] = $row['Badges'];
		$result->close();
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Registration Stats Graph</title>
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
			$("#yearSelect").submit(function () {
				window.location = "registrationStatsGraph.php?year=" + $("#year").val();
				return false;
			});

		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
			<h1>Registration Statistics</h1>
			<p>Total Registrations for Capricon <?php echo $capriconYear; ?>: <b><?php echo $allBadges; ?></b></p>
			<p>
			<?php
				$weekly = array();
				$running = array();
				$headers = array();
				$skipFirst = true;
				
				$start = new DateTime(date('Y-m-d', strtotime('second sunday of february ' . $lastYear)));
				$current = new DateTime(date('Y-m-d', strtotime('second sunday of february ' . $lastYear)));
				$finish = new DateTime(date('Y-m-d', strtotime('second monday of february ' . $year)));
				$week = $current->format('m/d');
				$overallTotal = 0;
				$weekTotal = 0;
				$breaker = 0;
				while($current < $finish || $breaker++ < 400) {
					if($skipFirst)
						$skipFirst = false;
					else {
						if($current->format('D') == 'Sun') {
							if($weekTotal > 0) {
								$headers[] = $week;					
								$weekly[] = $weekTotal;
								$running[] = $overallTotal;
							}
							
							$week = $current->format('m/d');
							$weekTotal = 0;
						}
					}
					$weekTotal += isset($badgesByDayMap[$current->format('Y-m-d')]) ? 
						$badgesByDayMap[$current->format('Y-m-d')] : 0;
					$overallTotal += isset($badgesByDayMap[$current->format('Y-m-d')]) ?
						$badgesByDayMap[$current->format('Y-m-d')] : 0;
					$current->add(new DateInterval('P1D'));
				}
				if($weekTotal > 0) {
					$headers[] = $week;					
					$weekly[] = $weekTotal;
					$running[] = $overallTotal;
				}
				$data = array('Overall' => $running, 'This Week' => $weekly);
				
				$graph = graph_lines(array(
					'graph.height' => 300,
					'cell.width' => 40,
					'cell.marginx' => 4,
					'cell.marginy' => 12,
					'bar.indicator' => true,
					'graph.background' => 'ccc',
					'graph' => $data,
					'graph.colors' => array ('00f', '0a0'),
					'header.even.background' => 'eee',
					'header.odd.background' => 'aaa',
					'header' => $headers,
					'graph.legend.width' => 55,
					'graph.legend.count' => 8,
					'graph.legend.line.color' => 'aaa',
					'table' => true,
					'table.even.background' => 'eee',
					'table.odd.background' => 'aaa'
				));
				echo "<p>From " . $start->format('Y-m-d') . " to " . $finish->format('Y-m-d') . " (Header shows \"Week Of\".)</p>";
				echo $graph;
			?>
			</p>
			<br>
			<form id="yearSelect" method="POST" action="">
				View a different year's statistics: <select id="year" name="year" style="width: 8%"> 
				<?php
					foreach($years as $yearOption)
					{
						echo "<option value=\"$yearOption\"" . ($yearOption == $year ? " selected" : "") . ">$yearOption</option>";
					}
				?>
				</select>
				<input type="submit" id="changeYear" name="changeYear" value="Change Year">
			</form><br>
			<div class="clearfix"></div>
			<div class="goback">
				<a href="/index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>