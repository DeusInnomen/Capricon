<?php
    session_start();
    include_once('includes/functions.php');
    if(!isset($_SESSION["PeopleID"]))
        header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
    elseif(!DoesUserBelongHere("DealerStaff"))
        header('Location: index.php');

    $result = $db->query("SELECT DealerID, CompanyName, LegalName, City, State, Country, Description FROM Dealer WHERE PeopleID IS NOT NULL ORDER BY CompanyName ASC");
    $dealers = array();
    while($row = $result->fetch_array())
        $dealers[] = $row;
    $result->close();

    $result = $db->query("SELECT DealerID, CompanyName, LegalName, City, State, Country, Description FROM Dealer WHERE PeopleID IS NULL ORDER BY CompanyName ASC");
    $managedDealers = array();
    while($row = $result->fetch_array())
        $managedDealers[] = $row;
    $result->close();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Manage Dealers</title>
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
   			$("#dealerForm input[type=radio]").change(function() {
				$("#edit").removeProp("disabled");
			});
		});

        function editDealer() {
            var id = $("#dealerForm input[type=radio]:checked").val();
            var url = 'editDealer.php?id=' + id;
            window.location.href = url;
            return false;
        }
    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxwide">
            <h1>Manage Dealers</h1>
            <div class="standardTable">
                <form id="dealerForm" method="post">
                    <h3>Registered Dealers</h3>
                    <table>
                        <tr><th>Select</th><th>Company Name</th><th>Legal Name</th><th>Location</th><th>Description</th></tr>
                        <?php
                        foreach($dealers as $dealer)
                        {
                            echo "<tr><td style=\"text-align: center;\"><input type=\"radio\" name=\"dealerID\" value=\"" . $dealer["DealerID"] . "\" /></td>";
                            echo "<td>" . $dealer["CompanyName"] . "</td><td>" . $dealer["LegalName"] . "</td><td>";
                            if($dealer["Country"] != "USA" && $dealer["Country"] != "United States" && $dealer["Country"] != "US")
                                echo $dealer["City"] . ", " . $dealer["Country"];
                            else
                                echo $dealer["City"] . ", " . $dealer["State"];
                            echo "</td><td>" . $dealer["Description"] . "</td></tr>\r\n";
                        } ?>
                    </table>
                    <br />
                    <h3>Capricon-Managed Dealers</h3>
                    <a href="/editDealer.php">Create a New Capricon-Managed Dealer</a><br /><br />
                    <table>
                        <tr><th>Select</th><th>Company Name</th><th>Legal Name</th><th>Location</th><th>Description</th></tr>
                        <?php
                        foreach($managedDealers as $dealer)
                        {
                            echo "<tr><td style=\"text-align: center;\"><input type=\"radio\" name=\"dealerID\" class=\"dealerID\" value=\"" . $dealer["DealerID"] . "\" /></td>";
                            echo "<td>" . $dealer["CompanyName"] . "</td><td>" . $dealer["LegalName"] . "</td><td>";
                            if($dealer["Country"] != "USA" && $dealer["Country"] != "United States" && $dealer["Country"] != "US")
                                echo $dealer["City"] . ", " . $dealer["Country"];
                            else
                                echo $dealer["City"] . ", " . $dealer["State"];
                            echo "</td><td>" . $dealer["Description"] . "</td></tr>\r\n";
                        } ?>
                    </table>
                    <br />
                    With Selected: <input type="submit" id="edit" onclick="editDealer(); return false;" value="Edit Selected Dealer" disabled /><br /><br />
                </form>
            </div>
            <div class="clearfix goback">
                <a href="index.php">Return to the Main Menu</a>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>