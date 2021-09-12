<?php
session_start();
include_once('includes/functions.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Main Menu</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
    <link rel="icon" href="includes/favicon.png" />
    <link rel="shortcut icon" href="includes/favicon.ico" />
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="includes/global.js"></script>
    <script type="text/javascript">
   		function resetFields() {
            $("#email").val("");
			$("#lastname").val("");
			$("#badgename").val("");
		}
        $(document).ready(function() {
            $(document).keypress(function(e){
                if (e.which == 13){
                    $("#doSearch").click();
                    return false;
                }
            });
            $("#checkinList").delegate("table tbody tr ", "click", (function(e) {
                var id = $(this).attr("badge");
                if(id != null) {
                    $("#badgeId").val(id);
                    $("#checkin").submit();
                }
			}));
            $("#search_form").submit(function () {
                document.activeElement.blur();
                $("#regList").html("");
                var email = $("#email").val();
                var lastname = $("#lastname").val();
                var badgename = $("#badgename").val();
                $.post("doCheckinFunctions.php", { action: "Search", email: email, lastname: lastname, badgename: badgename }, function(result) {
                    $("#checkinList").html(result);
                });
                return false;
            });
        });
    </script>
</head>
<body>
    <?php include('includes/header_checkin.php'); ?>
    <div class="content">
        <h2>Capricon Sign-In</h2>
        <h3>Look Up Registrations</h3>
        <form id="search_form" class="checkinForm" method="post" autocomplete="off">
            <label for="email" class="fieldLabelShort">Email Address: <input type="text" id="email" /></label><br />
            <label for="lastname" class="fieldLabelShort">Last Name: <input type="text" id="lastname" /></label><br />
            <label for="badgename" class="fieldLabelShort">Badge Name: <input type="text" id="badgename" /></label><br />
            <br />
            <input type="Submit" value="Clear" onclick="resetFields(); return false;" />&nbsp;&nbsp;&nbsp;<input type="Submit" id="doSearch" name="doSearch" value="Search" />
        </form>
        <br/><br/>
        <div id="checkinList"></div>
        <form id="checkin" method="post" action="registrationSignature.php">
            <input type="hidden" id="badgeId" name="badgeId" value="" />
        </form>
    </div>
    <?php include('includes/footer_checkin.php'); ?>
</body>
</html>
