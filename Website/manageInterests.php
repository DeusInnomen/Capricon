<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	else
		$interests = UserInterests();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Manage Interests</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<style>
		#moreInfoForm {
			display: none;
		}
	</style>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#interestsForm').submit(function (e) {
				$("#interestsForm :input").prop("readonly", true);
				$("#saveMessage").html("&nbsp;");
				$.post("doSaveInterests.php", $('#interestsForm').serialize(), function(result) {
					if(result.success)
						$("#saveMessage").addClass("goodMessage");
					else
						$("#saveMessage").addClass("errorMessage");

					$("#saveMessage").html(result.message);
					$("#interestsForm :input").prop("readonly", false);
				}, 'json');
				e.preventDefault();
			});
			$("#moreInfoForm").dialog({
				autoOpen: false,
				height: 360,
				width: 400,
				modal: true,
				buttons: {
					Ok: function() { $( this ).dialog( "close" ); }
				}
			});
		});
		
		function showInfo(type)
		{
			var title = "";
			var message = "";
			switch(type)
			{
				case "volunteers":
					title = "Volunteers";
					message = "Volunteering is not just a great way to give back to the convention, it can also help " + 
						"you get your badge fee reimbursed! By selecting this Interest, you will be contacted prior " + 
						"to the convention by our Gopher Department to fill you in on what kind of help we will need.";
					break;
				case "programming":
					title = "Programming";
					message = "Our convention thrives on interesting and diverse programming content. If you have an " + 
						"interest in being involved with the convention by putting on a panel (or more than one!), let " + 
						"us know as soon as possible by checking this box.";
					break;
				case "dealers":
					title = "Dealers";
					message = "Do you have a small business and wish to bring your wares to Capricon to sell them? Part " + 
						"of the convention is a Dealer's Hall where exhibitors can set up and sell product to the convention " + 
						"attendees. Space is limited, so please be sure to let us know early if you wish to join us.";
					break;
				case "artshow":
					title = "Art Show";
					message = "Capricon's Art Show has an incredibly enticing array of artwork, ranging from prints to " + 
						"sculptures, hand-crafted items to mechanical wonders. We are always looking for more artists who " + 
						"would like to present their crafts.";
					break;
				case "ads":
					title = "Advertising";
					message = "The Capricon Program Book isn't just the place to find out what's going on at our convention, " + 
						"it's also an advertising opportunity for businesses. By checking this box, we will reach out to you " + 
						"and let you know what space is available and the pricing.";
					break;
				case "parties":
					title = "Parties";
					message = "When the sun goes down and dinner is over, it's time to kick back and have some fun. Every " + 
						"year at Capricon, we have numerous parties across a broad range of interests, from small themed parties " + 
						"to large and organized groups. We also have awards voted by the attendees for a number of categories, " + 
						"such as Best Overall Party. Interested in throwing a party? Just check this box and let us know.";
					break;
				default:
					title = "Error";
					message = "An unrecognizd message type was passed.";
					break;				
			}
			$("#moreInfoForm").dialog("option", "title", title);
			$("#moreInfoForm p").html(message);
			$("#moreInfoForm").dialog("open");
		}
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div id="moreInfoForm" title="">
		<p></p>
	</div>
	<div class="content">
		<div class="centerboxmedium">
			<h1>Your Convention Interests</h1>
			<form id="interestsForm" class="interestsForm" method="post" action="">
			<p>There are many ways you can become involved with Capricon. The following list of check boxes is used to express
			your interest in a particular area of the convention. This is how we know to contact you for these functions, so
			it's important that you tell us what you're willing to be involved with! Click the "More Information" link next to
			each Interest to get additional information on that area.</p>
			<label><input type="checkbox" name="intGophers" id="intGophers" <?php echo (!empty($interests) && in_array("Gophers", $interests)) ? "checked" : ""; ?>/>
			<b>Volunteering as a Gopher/Helper</b></label> -- <a href="#" onClick="showInfo('volunteers');">More Information</a><br />
			<label><input type="checkbox" name="intProgram" id="intProgram" <?php echo (!empty($interests) && in_array("Program", $interests)) ? "checked" : ""; ?>/>
			<b>Programming Participant</b></label> -- <a href="#" onClick="showInfo('programming');">More Information</a><br />
			<label><input type="checkbox" name="intDealer"  id="intDealer"  <?php echo (!empty($interests) && in_array("Dealer", $interests)) ? "checked" : ""; ?>/>
			<b>Being a Dealer</b></label> -- <a href="#" onClick="showInfo('dealers');">More Information</a><br />
			<label><input type="checkbox" name="intArtShow" id="intArtShow" <?php echo (!empty($interests) && in_array("ArtShow", $interests)) ? "checked" : ""; ?>/>
			<b>Exhibiting in the Art Show</b></label> -- <a href="#" onClick="showInfo('artshow');">More Information</a><br />
			<label><input type="checkbox" name="intAds"     id="intAds"     <?php echo (!empty($interests) && in_array("Ads", $interests)) ? "checked" : ""; ?>/>
			<b>Advertising</b></label> -- <a href="#" onClick="showInfo('ads');">More Information</a><br />
			<label><input type="checkbox" name="intParties" id="intParties" <?php echo (!empty($interests) && in_array("Parties", $interests)) ? "checked" : ""; ?>/>
			<b>Throwing a Party</b></label> -- <a href="#" onClick="showInfo('parties');">More Information</a>
			<hr>
			<p>The following Interests aren't for becoming involved in the convention, but what they help us do is
			determine how much interest these is in these particular categories. These typically require a fair bit
			of planning, money, or both in order to organize, so give us a hand by checking the boxes that match your
			interests. And thanks for your help!</p>
			<label><input type="checkbox" name="intAnime"   id="intAnime"   <?php echo (!empty($interests) && in_array("Anime", $interests)) ? "checked" : ""; ?>/>
			<b>Anime</b></label><br />
			<label><input type="checkbox" name="intFilms"   id="intFilms"   <?php echo (!empty($interests) && in_array("Films", $interests)) ? "checked" : ""; ?>/>
			<b>Films</b></label><br />
			<label><input type="checkbox" name="intGaming"  id="intGaming"  <?php echo (!empty($interests) && in_array("Gaming", $interests)) ? "checked" : ""; ?>/>
			<b>Gaming</b> (Board, Card, RPG, Miniature, etc.)</label><br />
			<label><input type="checkbox" name="intEvents"  id="intEvents"  <?php echo (!empty($interests) && in_array("Events", $interests)) ? "checked" : ""; ?>/>
			<b>Special Events</b> (This includes stuff outside of the convention!)</label><br />
			<br />
			<input type="submit" value="Submit Changes to Your Interests" />&nbsp;<span id="saveMessage">&nbsp;</span></form>			
			<br /><br />
			<div class="goback">
				<a href="/index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>