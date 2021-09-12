<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("RegLead") && !DoesUserBelongHere("Ops"))
		header('Location: index.php');
	elseif(!isset($_GET["id"]))
		header('Location: manageAllAccounts.php');
	else
	{
		$result = $db->query("SELECT FirstName, LastName, Address1, Address2, City, State, ZipCode, Country, " .
			"Phone1, Phone1Type, Phone2, Phone2Type, Email, BadgeName, HeardFrom FROM People WHERE PeopleID = " .
			$_GET["id"]);
		
		$info = $result->fetch_array();
		$result->close();
		if($info["Phone1Type"] == "") $info["Phone1Type"] = "Mobile";
		if($info["Phone2Type"] == "") $info["Phone2Type"] = "Home";
		
		$permsSet = UserPermissions($_GET['id'], true);
		
		$result = $db->query("SELECT Permission, ShortName, Description, Module FROM PermissionDetails ORDER BY Module, Permission");
		$permissions = array();
		while($row = $result->fetch_array())
			$permissions[] = $row;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Manage Account (Admin)</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
	<link rel="stylesheet" type="text/css" href="includes/jquery.validate.password.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
	<script type="text/javascript" src="includes/jquery.validate.password.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("select#state option").each(function() { this.selected = (this.value == "<?php echo $info["State"]; ?>"); });
			$("select#phone1type option").each(function() { this.selected = (this.text == "<?php echo $info["Phone1Type"]; ?>"); });
			$("select#phone2type option").each(function() { this.selected = (this.text == "<?php echo $info["Phone2Type"]; ?>"); });
			$("#password").keyup(function () {
				$(this).valid();
			});
			$("#passwordconfirm").keyup(function () {
				$(this).valid();
			});
			$.validator.messages.required = "";
			$("#accountForm").validate({
				rules: {
					passwordCurrent: "required",
					firstName: "required",
					lastName: "required",
					address1: "required",
					city: "required",
					zipcode: "required",
					email: {
						required: true,
						email: true
					},
					passwordconfirm: {
						equalTo: "#password"
					}
				},
				submitHandler: function(form) {
					$("#accountForm :input").prop("readonly", true);
					$("#accountSaveMessage").removeClass("good");
					$("#accountSaveMessage").removeClass("error");
					$("#accountSaveMessage").html("&nbsp;");
					$("#email").removeClass("error");
					$("#passwordCurrent").removeClass("error");
					$.post("doSaveAccount.php", $(form).serialize(), function(result) {
						$("#passwordCurrent").val("");
						if(result.authenticated)
						{
							if(result.success)
							{
								$("#accountSaveMessage").addClass("good");
								$("#accountSaveMessage").html(result.message);
								$("#accountForm :input").prop("readonly", false);
							}
							else if(result.emailInUse)
							{
								$("#email").addClass("error");
								$("#accountSaveMessage").addClass("error");
								$("#accountSaveMessage").html(result.message);
								$("#accountForm :input").prop("readonly", false);
							}
							else
							{
								$("#accountSaveMessage").addClass("error");
								$("#accountSaveMessage").html(result.message);
								$("#accountForm :input").prop("readonly", false);
							}
						}
						else
						{
							$("#passwordCurrent").addClass("error");
							$("#accountSaveMessage").addClass("error");
							$("#accountSaveMessage").html(result.message);
							$("#accountForm :input").prop("readonly", false);
						}
					}, 'json');
					return false;
				},
				errorPlacement: function(error, element) {
					error.appendTo(element.parent().next());
				}
			});
		});
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxwide">
			<form id="accountForm" class="accountForm" method="post" action="">	
			<input type="hidden" name="peopleID" value="<?php echo $_GET["id"]; ?>" />
			<input type="hidden" name="currentEmail" class="currentEmail" value="<?php echo $info["Email"]; ?>" />
			<h1>Manage Your Account</h1>		
			<div style="float: left; width: 50%; height: 500px;">
				<div class="headertitle">Personal Information</div>
				<label for="firstName" class="fieldLabelShort" >First Name: </label><br />
				<input type="text" name="firstName" id="firstName" placeholder="Required" style="width: 70%;" value="<?php echo $info["FirstName"]; ?>" /><br />
				<label for="lastName" class="fieldLabelShort" >Last Name: </label><br />
				<input type="text" name="lastName" id="lastName" placeholder="Required" style="width: 70%;" value="<?php echo $info["LastName"]; ?>" /><br />
				<label>Address (Line 1): <input type="text" name="address1" id="address1" style="width: 90%;" placeholder="Required" value="<?php echo $info["Address1"]; ?>" /></label><br />
				<label>Address (Line 2): <input type="text" name="address2" id="address2" style="width: 90%;" value="<?php echo $info["Address2"]; ?>" /></label><br />
				<label for="city" class="fieldLabelShort">City: </label><br />
				<input type="text" name="city" id="city" placeholder="Required" style="width: 70%;" value="<?php echo $info["City"]; ?>" /><br />
				<label for="state" class="fieldLabelShort">State: </label><br />
				<select id="state" name="state" style="width: 40%">
					<option value="AK">AK - Alaska</option><option value="AL">AL - Alabama</option>
					<option value="AR">AR - Arkansas</option><option value="AZ">AZ - Arizona</option>
					<option value="CA">CA - California</option><option value="CO">CO - Colorado</option>
					<option value="CT">CT - Connecticut</option><option value="DC">DC - District Of Columbia</option>
					<option value="DE">DE - Delaware</option><option value="FL">FL - Florida</option>
					<option value="GA">GA - Georgia</option><option value="HI">HI - Hawaii</option>
					<option value="IA">IA - Iowa</option><option value="ID">ID - Idaho</option>
					<option value="IL" selected>IL - Illinois</option><option value="IN">IN - Indiana</option>
					<option value="KS">KS - Kansas</option><option value="KY">KY - Kentucky</option>
					<option value="LA">LA - Louisiana</option><option value="MA">MA - Massachusetts</option>
					<option value="MD">MD - Maryland</option><option value="ME">ME - Maine</option>
					<option value="MI">MI - Michigan</option><option value="MN">MN - Minnesota</option>
					<option value="MO">MO - Missouri</option><option value="MS">MS - Mississippi</option>
					<option value="MT">MT - Montana</option><option value="NC">NC - North Carolina</option>
					<option value="ND">ND - North Dakota</option><option value="NE">NE - Nebraska</option>
					<option value="NH">NH - New Hampshire</option><option value="NJ">NJ - New Jersey</option>
					<option value="NM">NM - New Mexico</option><option value="NV">NV - Nevada</option>
					<option value="NY">NY - New York</option><option value="OH">OH - Ohio</option>
					<option value="OK">OK - Oklahoma</option><option value="OR">OR - Oregon</option>
					<option value="PA">PA - Pennsylvania</option><option value="RI">RI - Rhode Island</option>
					<option value="SC">SC - South Carolina</option><option value="SD">SD - South Dakota</option>
					<option value="TN">TN - Tennessee</option><option value="TX">TX - Texas</option>
					<option value="UT">UT - Utah</option><option value="VA">VA - Virginia</option>
					<option value="VT">VT - Vermont</option><option value="WA">WA - Washington</option>
					<option value="WI">WI - Wisconsin</option><option value="WV">WV - West Virgina</option>
					<option value="WY">WY - Wyoming</option><option value="">Other/Unused</option>
				</select><br />
				<label for="zip" class="fieldLabelShort">Zip Code: </label><br />
				<input type="text" name="zip" id="zip" size="10" maxlength="10" placeholder="Required" value="<?php echo $info["ZipCode"]; ?>" /><br />
				<label for="country" class="fieldLabelShort">Country: </label><br />
				<input type="text" name="country" id="country" style="width: 40%;" value="<?php echo $info["Country"]; ?>"  /><br />
				<label for="phone1" class="fieldLabelWide" >Phone Number (Main): </label><br />
				<input type="tel" name="phone1" id="phone1" style="width: 50%;" value="<?php echo $info["Phone1"]; ?>" />
				<select id="phone1type" name="phone1type">
					<option value="Home">Home</option>
					<option value="Mobile" selected>Mobile</option>
					<option value="Work">Work</option>
					<option value="Other">Other</option>
				</select></label><br />
				<label for="phone2" class="fieldLabelWide" >Phone Number (Alternate): </label><br />
				<input type="tel" name="phone2" id="phone2" style="width: 50%;" value="<?php echo $info["Phone2"]; ?>" />
				<select id="phone2type" name="phone2type">
					<option value="Home" selected>Home</option>
					<option value="Mobile">Mobile</option>
					<option value="Work">Work</option>
					<option value="Other">Other</option>
				</select></label><br />
				<label>How Did You Hear About Capricon?<input type="text" name="heardFrom" id="heardFrom" style="width: 90%; margin-bottom: 40px;" value="<?php echo $info["HeardFrom"]; ?>" /></label><br />
				<?php
				if(DoesUserBelongHere("SuperAdmin"))
					echo '<a href="adminSwitchAccount.php?id=' . $_GET["id"] . '">!! Log In To This Account !!</a><br />' ?>
			</div>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
			<div style="float: left; width: 50%; margin-bottom: 60px">
				<div class="headertitle">Your Badge Name</div>
				<span style="font-size: 0.9em;">This will be the name shown on your convention badge. Please make
				your changes before purchasing any badges.</span><br />
				<input type="text" name="badgeName" id="badgeName" placeholder="Required" style="width: 70%;" value="<?php echo $info["BadgeName"]; ?>" /><br />
				<div class="headertitle">Email Address</div>
				<label>Email Address: <input type="text" name="email" id="email" size="30" value="<?php echo $info["Email"]; ?>" /></label>
				<label class="masterTooltip" title="If checked, the user's email will be changed with NO notice to them! Be sure they know this is happening."><br />
				<input type="checkbox" name="forceEmail" id="forceEmail" />Don't Verify</label><br />
				<span>Note: Changes to this email address will be confirmed with the user via their 
				<span style="font-style: italic;">current</span> email before they take effect.</span>
				<div class="headertitle">Permissions<?php if(!DoesUserBelongHere("SuperAdmin")) echo " (Read Only)"; ?></div>
				<div id="permissions">
					<?php
						if(DoesUserBelongHere("RegLead")) echo '<input type="hidden" name="setPerms" value="setPerms" />' . "\r\n";
						foreach($permissions as $permission)
						{
							$module = strlen($permission["Module"]) > 0 ? $permission["Module"] : "Global";
							echo "<label class=\"masterTooltip\" title=\"" . $permission["Description"] . "\"><input type=\"checkbox\" name=\"perm" . $permission["Permission"] . "\" id=\"perm" .
								$permission["Permission"] . "\"";
							if(in_array(strtolower($permission["Permission"]), $permsSet)) echo " checked";
							if(!DoesUserBelongHere("RegLead")) echo " disabled";
							echo " />$module: " . $permission["ShortName"] . "</label><br />\r\n";
						} ?>
				</div>
			</div>
			<div class="accountFooter">
				<span id="accountSaveMessage">&nbsp;</span><br />
				<input type="submit" value="Save Changes" />
			</div>
			</form>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>