<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("ArtShowLead"))
    header('Location: /main.php');
else
{
    $year = date("n") >= 3 ? date("Y") + 1: date("Y");

    $result = $db->query("SELECT ad.ArtistID, DisplayName, CASE WHEN ap.ArtistAttendingID IS NOT NULL THEN ap.ArtistAttendingID ELSE 0 END AS HasPresence FROM ArtistDetails ad JOIN People p ON ad.PeopleID = p.PeopleID LEFT OUTER JOIN ArtistPresence ap ON ap.ArtistID = ad.ArtistID AND ap.Year = $year WHERE p.IsCharity = 1");
    $charities = array();
    while($row = $result->fetch_array())
        $charities[] = $row;
    $result->close();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Charities</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(".standardTable tr").click(function(e) {
				if(e.target.type !== "radio")
					$(":radio", this).trigger("click");
			});
			$("#charitiesForm input[type=radio]").change(function() {
				$("#addToShow").removeProp("disabled");
			});
		});
		function addCharityToShow() {
		    var id = $("#charitiesForm input[type=radio]:checked").val();
		    $.post("doArtistFunctions.php", { task: "AddCharityToShow", id: id }, function (result) {
		        if (result.success)
		            location.reload();
		        else
		            $("#noticeAddToShow").html(result.message);
		    }, 'json');
		}
		function addNewCharity() {
		    var name = $("#charityName").val();
		    $.post("doArtistFunctions.php", { task: "AddNewCharity", name: name }, function (result) {
		        if (result.success)
		            location.reload();
		        else
		            $("#noticeAddCharity").html(result.message);
		    }, 'json');
		}
    </script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxmedium">
		<h1>Charities Available to Capricon</h1>
			<div style="margin-bottom: 30px;">
                <div class="headertitle">Currently Listed Charities</div>
                <div class="standardTable">
                    <form id="charitiesForm" method="post">
                        <table>
                            <tr>
                                <th>Select</th>
                                <th>Charity Name</th>
                                <th>Has Art Show Presence</th>
                            </tr>
                            <?php
                            foreach($charities as $charity)
                            {
                                echo "<tr>";
                                echo "<td style=\"text-align: center;\">"; 
                                echo $charity["HasPresence"] != 0 ? "x" : "<input id=\"artistID\" name=\"artistID\" type=\"radio\" value=\"" . $charity["ArtistID"] . "\">";
                                echo "</td><td>" . $charity["DisplayName"] . "</td><td>";
                                echo $charity["HasPresence"] != 0 ? "Yes: <a href=\"artistSubmissions.php?attendID=" . $charity["HasPresence"] . "\">Manage Inventory</a>" : "No";
                                echo "</td></tr>\r\n";
                            } ?>
                        </table>
                        <br />
                        With Selected:
                        <input type="submit" id="addToShow" onclick="addCharityToShow(); return false;" value="Add Selected Charity to Art Show" disabled />
                        <span id="noticeAddToShow" style="font-size: 1.05em; font-weight: bold;">&nbsp;</span>
                    </form>
                    <div class="headertitle">Add New Charity</div>
                    <form id="addCharityForm" method="post">
                        <p>To add a new charity to the system, fill in the name of the charity below and press Submit.</p>
                        <label for="charityName" class="fieldLabelShort">Charity Name: </label><br />
                        <input type="text" name="charityName" id="charityName" maxlength="50" /><br />
                        <input type="submit" id="addCharity" onclick="addNewCharity(); return false;" value="Add New Charity" />
                        <span id="noticeAddCharity" style="font-size: 1.05em; font-weight: bold;">&nbsp;</span>
                    </form>
                    <div class="goback">
                        <a href="/index.php">Return to the Main Menu</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php include('includes/footer.php'); ?>
</body>
</html>