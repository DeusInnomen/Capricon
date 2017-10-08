<?php
	session_start();
	if(!isset($_GET['id']))
		header('Location: index.php');
	include_once('includes/functions.php');
	$id = $db->real_escape_string($_GET['id']);

	DoCleanup();

	$result = $db->query("SELECT PendingID, Entered FROM PendingAccounts WHERE PendingID = '$id'");
	if($result->num_rows > 0)
	{
        $row = $result->fetch_array();
        $result->close();
        if($row["Entered"] == 0) {
		    $expire = new DateTime();
		    $expire->add(new DateInterval('PT2H'));
		    $db->query("UPDATE PendingAccounts SET Entered = 1, Expires = '" . DateToMySQL($expire) . "' WHERE PendingID = '$id'");

            $db->query("INSERT INTO People (FirstName, LastName, Address1, Address2, City, State, ZipCode, Country, " .
			    "Phone1, Phone1Type, Phone2, Phone2Type, HeardFrom, Email, Password, Registered, BadgeName) SELECT FirstName, LastName, " .
			    "Address1, Address2, City, State, ZipCode, Country, Phone1, Phone1Type, Phone2, Phone2Type, HeardFrom, Email, Password, " .
			    "NOW(), BadgeName FROM PendingAccounts WHERE PendingID = '$id'");
		    $peopleID = $db->insert_id;
		    $result = $db->query("SELECT Interests FROM PendingAccounts WHERE PendingID = '$id'");
		    $row = $result->fetch_array();
		    $result->close();
		    if($row["Interests"] != "")
		    {
			    $interests = explode("|", $row["Interests"]);
			    foreach($interests as $interest)
			    {
				    $db->query("INSERT INTO PeopleInterests (PeopleID, Interest) VALUES ($peopleID, '$interest')");
				    if($interest == "ArtShow")
					    $db->query("INSERT INTO Permissions (PeopleID, Permission) VALUES ($peopleID, 'Artist')");
				    if($interest == "Dealer")
					    $db->query("INSERT INTO Permissions (PeopleID, Permission) VALUES ($peopleID, 'Dealer')");
			    }
		    }
        }
		$message = 'Your account has been successfully activated. Please feel free to <a href="/login.php">Log In now</a>. ' .
			'<br /><br />Thanks for joining Capricon!';
	}
	else
	{
		$message = 'Sorry, an invalid ID was provided.<br /><br />If it has been more than 72 hours since you registered, ' .
			'your account was removed for failure to activate in time.<br /><br />If you were expecting an activation email ' .
			'and never received one, please try the <a href="resetpassword.php">Reset Password</a> page. It will send out a ' .
            'fresh activation email.<br /><br />If you continue to have problems, please contact ' .
			'<a href="mailto:it@phandemonium.org?Subject=Capricon Account Activation Problems">Phandemonium IT</a> ' .
			'for further assistance.';
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Account Activation</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxnarrow">
			<p><?php echo $message; ?></p>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>