<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("SuperAdmin"))
		header('Location: index.php');
	elseif(!isset($_POST["IDs"]))
		header('Location: index.php');
		
	$IDs = explode("|", $_POST["IDs"]);
	$IDList = str_replace("|", ", ", $_POST["IDs"]);
	$result = $db->query("SELECT PeopleID, CONCAT(FirstName, ' ', LastName) AS Name FROM People WHERE PeopleID IN ($IDList) ORDER BY LastName");
	$names = array();
	while($row = $result->fetch_array())
		$names[$row["PeopleID"]] = $row["Name"];
	$result->close();
	
	$result = $db->query("SELECT Permission, ShortName, Description, Module FROM PermissionDetails ORDER BY Module, Permission");
	$permissions = array();
	while($row = $result->fetch_array())
		if($row["Permission"] != "SuperAdmin" && $row["Permission"] != "ConChair") $permissions[] = $row;
	$result->close();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Bulk Set Permissions</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#updatePermsForm").submit(function () {
				$.post("doBulkPermissions.php", $(this).serialize(), function(result) {
					if(result.success)
						window.location = "manageAllAccounts.php";
					else
						$("#message").html(result.message);
				}, 'json');
				return false;
			});
		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxnarrow">
			<h1>Bulk Set Permissions</h1>
			<p>The following users will have these permissions set:</p>
			<div style="overflow: auto; max-height: 150px; margin-bottom: 10px;">
<?php
			foreach($IDs as $id)
				echo $names[$id] . " (" . $id . ")<br />\r\n"; ?>
			</div>
			<span style="padding-left: 3px;">+&nbsp;&nbsp;&nbsp;-</span>
			<form id="updatePermsForm" method="POST">
			<input type="hidden" name="action" value="DoBulk" />
			<input type="hidden" name="IDs" value="<?php echo $_POST["IDs"]; ?>" />
			<div id="permissions" style="font-size: 0.9em;">
<?php
				foreach($permissions as $permission)
				{
					$module = strlen($permission["Module"]) > 0 ? $permission["Module"] : "Global";
					echo "<label class=\"masterTooltip\" title=\"" . $permission["Description"] . "\">" . 
						"<input type=\"checkbox\" name=\"addPerm" . $permission["Permission"] . "\" id=\"addPerm" . $permission["Permission"] . "\" /> " .
						"<input type=\"checkbox\" name=\"delPerm" . $permission["Permission"] . "\" id=\"delPerm" . $permission["Permission"] . "\" />";
					echo "$module: " . $permission["ShortName"] . "</label><br />\r\n";
				} ?>
			</div>
			<br />
			<input type="submit" value="Update Permissions" />
			</form>
			<div id="message"></div>
			<div class="clearfix"></div>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>