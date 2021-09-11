<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	else
	{
		$buyer = "";
		$people = array();
		$result = $db->query("SELECT PeopleID, FirstName, LastName, Phone1, Phone1Type, Phone2, Phone2Type, BadgeName, " . 
			"Email, ParentID FROM People WHERE ParentID = " . $_SESSION["PeopleID"]);
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
			{
				$people[] = $row;
				if(strlen($row["Email"]) > 0) $buyer = $row["PeopleID"];
			}
			$result->close();
		}
		$result = $db->query("SELECT p1.PeopleID, p1.FirstName, p1.LastName, p1.Phone1, p1.Phone1Type, p1.Phone2, " . 
			"p1.Phone2Type, p1.BadgeName, p1.Email, p1.ParentID FROM People p1 INNER JOIN People p2 ON p1.PeopleID = " .
			"p2.ParentID WHERE p2.PeopleID = " . $_SESSION["PeopleID"]);
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
				$people[] = $row;

			$result->close();
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Manage Related People</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
	<link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="includes/jquery.validate.password.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
	<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#modifyRelatives tr").click(function(e) {
				if(e.target.type !== "radio")
					$(":radio", this).trigger("click");
			});
			$("#modifyRelatives input[type=radio]").change(function() {
				var acctType = $("#modifyRelatives input[type=radio]:checked").parent().parent().find("#AcctType").text();
				if(acctType == "Sub Account")
				{
					$("#edit").removeAttr("disabled");
					$("#promote").removeAttr("disabled");
					$("#remove").attr("disabled", "disabled");
				}
				else
				{
					$("#edit").attr("disabled", "disabled");
					$("#promote").attr("disabled", "disabled");
					$("#remove").removeAttr("disabled");
				}
			});
			$("#authorizeForm").validate({
				rules: {
					email: {
						required: true,
						email: true
					}
				},
				submitHandler: function(form) {					
					$("#authorizeForm :input").prop("readonly", true);
					$("#authorizeMessage").removeClass("errorMessage");
					$("#authorizeMessage").hide();
					$.post("doSubAccountAction.php", $(form).serialize(), function(result) {
						if(result.success)
							$("#authorizeMessage").addClass("goodMessage");
						else
							$("#authorizeMessage").addClass("errorMessage");
						$("#authorizeForm :input").prop("readonly", false);
						$("#authorizeMessage").show();
						$("#authorizeMessage").html(result.message);
					}, 'json');
					return false;
				}
			});
			$("#personDetailsForm").dialog({
				autoOpen: false,
				height: 440,
				width: 450,
				modal: true
			});
			$("#upgradePersonForm").dialog({
				autoOpen: false,
				height: 400,
				width: 330,
				modal: true,
				buttons: {
					"Confirm": function() {
						$("#upgradeEmail").removeClass("ui-state-error");
						$("#adultVerify").parent().removeClass("errorMessageBold");
						var valid = true;
						var id = $("#modifyRelatives input[type=radio]:checked").val();
						var email = $("#upgradeEmail").val();
						var regExp = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;
						if(!$('#adultVerify').is(':checked'))
						{
							$("#adultVerify").parent().addClass("errorMessageBold");
							valid = false;
						}					
						if(!regExp.test(email))
						{
							$("#upgradeEmail").addClass("ui-state-error");
							valid = false;
						}
						if(valid)
						{
							$("#upgradePerson :input").prop("readonly", true);
							$.post("doSubAccountAction.php", { action: "Promote", PeopleID: id, email: email }, function(result) {							
								if(result.success)
									$("#relativeEditMessage").addClass("goodMessage");
								else
									$("#relativeEditMessage").addClass("errorMessage");
								$("#upgradePerson :input").prop("readonly", true);
								$("#relativeEditMessage").show();
								$("#relativeEditMessage").html(result.message);
								$("#upgradePersonForm").dialog("close");
							}, "json");
						}
					},
					Cancel: function() {
						$(this).dialog("close");
					}
				}
			});
		});
		
		function addNewAccount() {
			$("#personDetails input[type='text']").val("");
			$("#phone1type option").filter(function() { return $(this).text() == "Mobile"; }).prop("selected", true);
			$("#phone2type option").filter(function() { return $(this).text() == "Home"; }).prop("selected", true);
			
			$("#personDetailsForm").dialog("option", "buttons", {
				"Add Person": function() {						
					$("#firstName").removeClass("ui-state-error");
					$("#lastName").removeClass("ui-state-error");
					var valid = true;
					if($("#firstName").val().length == 0)
					{
						$("#firstName").addClass("ui-state-error");
						valid = false;
					}
					if($("#lastName").val().length == 0)
					{
						$("#lastName").addClass("ui-state-error");
						valid = false;
					}
					if(valid)
					{
						$("#personDetails :input").prop("readonly", true);
						$("#relativeSaveMessage").removeClass("errorMessage");
						$("#relativeSaveMessage").hide();
						$.post("doSubAccountAction.php", $("#personDetails").serialize(), function(result) {
							if(result.success)
								location.reload();
							else
							{
								$("#relativeSaveMessage").show();
								$("#relativeSaveMessage").addClass("errorMessage");
								$("#relativeSaveMessage").html(result.message);
								$("#personDetails :input").prop("readonly", false);
							}
						}, 'json');
					}
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			});
			
			$("#personDetailsForm").dialog("option", "title", "Add New Person");
			$("#personDetailsForm input[name='action']").attr("value", "AddNew");
			$("#personDetailsForm").dialog("open");
		}
		
		function editAccount() {
			var row = $("#modifyRelatives input[type=radio]:checked").parent().parent();
			$("#personDetails input#firstName").val(row.find(".firstName").text());
			$("#personDetails input#lastName").val(row.find(".lastName").text());
			$("#personDetails input#phone1").val(row.find(".phone1").text());
			$("#personDetails input#phone2").val(row.find(".phone2").text());
			$("#personDetails input#badgeName").val(row.find(".badgeName").text());
			$("#phone1type option").filter(function() { return $(this).text() == row.find(".phone1type").text(); }).prop("selected", true);
			$("#phone2type option").filter(function() { return $(this).text() == row.find(".phone2type").text(); }).prop("selected", true);
			$("#personDetails input[name='PeopleID']").attr("value", row.find(".peopleID").val());
		
			$("#personDetailsForm").dialog("option", "buttons", {
				"Save Details": function() {
					$("#firstName").removeClass("ui-state-error");
					$("#lastName").removeClass("ui-state-error");
					var valid = true;
					if($("#firstName").val().length == 0)
					{
						$("#firstName").addClass("ui-state-error");
						valid = false;
					}
					if($("#lastName").val().length == 0)
					{
						$("#lastName").addClass("ui-state-error");
						valid = false;
					}
					if(valid)
					{
						$("#personDetails :input").prop("readonly", true);
						$("#relativeSaveMessage").removeClass("errorMessage");
						$("#relativeSaveMessage").hide();
						$.post("doSubAccountAction.php", $("#personDetails").serialize(), function(result) {
							if(result.success)
								location.reload();
							else
							{
								$("#relativeSaveMessage").show();
								$("#relativeSaveMessage").addClass("errorMessage");
								$("#relativeSaveMessage").html(result.message);
								$("#personDetails :input").prop("readonly", false);
							}
						}, 'json');
					}
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			});			
				
			$("#personDetailsForm").dialog("option", "title", "Edit Person Details");
			$("#personDetails input[name='action']").attr("value", "Update");
			$("#personDetailsForm").dialog("open");
		}
				
		function removeAccount() {
			if(confirm("You will no longer be able to make purchases for this person. Are you sure?"))
			{
				$("#modifyRelatives :input").prop("readonly", true);
				var id = $("#modifyRelatives input[type=radio]:checked").val();
				var email = "Test";
				$.post("doSubAccountAction.php", { action: "Remove", PeopleID: id }, function(result) {
					if(result.success)
						$("#relativeEditMessage").addClass("goodMessage");
					else
						$("#relativeEditMessage").addClass("errorMessage");
					$("#modifyRelatives :input").prop("readonly", false);
					$("#relativeEditMessage").show();
					$("#relativeEditMessage").html(result.message);
				}, "json");
			}
		}
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div id="upgradePersonForm" title="Upgrade to Full Account">
		<form id="upgradePerson" method="post">
			<p style="font-size: 0.95em;">To convert this person to a full account, you will need to enter their email address. This will send
			a confirmation to them. Once they have clicked the link within the email and created a password, their
			account will be activated.</p>
			<label>Email: <input type="email" name="upgradeEmail" id="upgradeEmail" style="width: 75%; " /></label>
			<label>I certify that this person is at least 13 years of age. <input type="checkbox" name="adultVerify" id="adultVerify" /></label>
		</form>
	</div>
	<div id="personDetailsForm" title="">
		<form id="personDetails" method="post">
			<input type="hidden" name="action" value="" />
			<input type="hidden" name="PeopleID" value="" />
			<label for="firstName" class="fieldLabelShort">First Name: </label><br />
			<input type="text" name="firstName" id="firstName" placeholder="Required" style="width: 70%;" /><br />
			<label for="lastName" class="fieldLabelShort">Last Name: </label><br />
			<input type="text" name="lastName" id="lastName" placeholder="Required" style="width: 70%;" /><br />
			<label>Phone Number (Main): </label><br />
			<input type="tel" name="phone1" id="phone1" style="width: 70%;" />
			<select id="phone1type" name="phone1type" >
				<option value="Home">Home</option>
				<option value="Mobile" selected>Mobile</option>
				<option value="Work">Work</option>
				<option value="Other">Other</option>
			</select><br />
			<label>Phone Number (Alternate): </label><br />
			<input type="tel" name="phone2" id="phone2" style="width: 70%;" />
			<select id="phone2type" name="phone2type">
				<option value="Home" selected>Home</option>
				<option value="Mobile">Mobile</option>
				<option value="Work">Work</option>
				<option value="Other">Other</option>
			</select><br />
			<label for="badgeName" class="fieldLabelShort">Badge Name: </label><br />
			<input type="text" name="badgeName" id="badgeName" placeholder="If Blank, the Full Name is Used." style="width: 70%;" /><br />
		</form>
		<div id="formMessage">&nbsp;</div>
	</div>
	<div class="content">
		<div class="centerboxmedium" style="width: 800px;">
			<h1>Manage Related People</h1>
			<div class="headertitle">Existing Related People</div>
<?php 
	if(!empty($people)) { ?>
			<div id="relatives">
				<form id="modifyRelatives" method="post">
					<table>
					<tr><th>Select</th><th>First Name</th><th>Last Name</th><th>Main Phone</th><th>Type</th><th>Alt Phone</th><th>Type</th><th>Badge Name</th><th>Relation</th></tr>
<?php
					foreach($people as $person)
					{
						$targetID = $person["ParentID"] == $_SESSION["PeopleID"] ? $person["PeopleID"] : $_SESSION["PeopleID"];
						echo "<tr><td style=\"text-align: center;\"><input type=\"radio\" name=\"peopleID\" class=\"peopleID\" value=\"$targetID\" /></td>" . 
							"<td class=\"firstName\">" . $person["FirstName"] . "</td><td class=\"lastName\">" . $person["LastName"] . "</td>"; 
						if(isset($person["Phone1"]))
							echo "<td class=\"phone1\">" . $person["Phone1"] . "</td><td class=\"phone1type\">" . $person["Phone1Type"] . "</td>";
						else
							echo "<td class=\"phone1\"></td><td class=\"phone1type\"></td>";
						if(isset($person["Phone2"]))
							echo "<td class=\"phone2\">" . $person["Phone2"] . "</td><td class=\"phone1type\">" . $person["Phone2Type"] . "</td>";
						else
							echo "<td class=\"phone2\"></td><td class=\"phone2type\"></td>";
						echo "<td class=\"badgeName\">" . $person["BadgeName"] . "</td><td id=\"AcctType\">"; 
						if(strlen($person["Email"]) > 0)
						{
							if($targetID != $_SESSION["PeopleID"])
								echo "May Purchase For Them";
							else
								echo "Can Purchase For You";
						}
						else
							echo "Sub Account";
						echo "</td></tr>\r\n";
					}
?>
					</table>
					With Selected:
					<input type="submit" id="edit" onclick="editAccount(); return false;" value="Edit Details" disabled>
					<input type="submit" id="promote" onclick="$('#upgradePersonForm').dialog('open'); return false;" value="Promote to Full Account" disabled>
					<input type="submit" id="remove" onclick="removeAccount(); return false;" value="Remove Authorization" disabled>
					<div id="relativeEditMessage">&nbsp;</div>
				</form>
			</div>
<?php } else {
			echo "<p style=\"margin-bottom: 10px;\">You currently do not have any accounts related to your account. You have \r\n"; 
			echo "not authorized anyone to purchase items for you. No one has authorized you to purchase items for them.</p>\r\n";
} ?>
			<div class="headertitle">Add New Person</div>
			<div id="relativeSaveMessage">&nbsp;</div>
			<p style="margin-bottom: 10px;">In order to purchase a badge for someone else, such as a child
			or spouse, you will need to create a subaccount. This person will have a subaccount that only you 
			can modify, and will not have a full account unless you release it to them, and as such they cannot
			log in and use the site features.</p>
			<input type="submit" id="addNew" onclick="addNewAccount(); return false;" style="margin-bottom: 10px;" value="Add New Person" />
<?php if($buyer == "") { ?>
			<div style="margin-bottom: 30px;">
				<div class="headertitle">Authorize Someone to Purchase For You</div>
				<p>Enter the email address of someone you wish to authorize to make purchases on your behalf. They
				must confirm this request within 72 hours or it will expire. Payments are their responsibility, but
				you become an option for badge purchasing, payment of fees, etc.</p>
				<form id="authorizeForm" method="post">
					<input type="hidden" name="action" value="Authorize" />
					<label>Email: <input type="email" name="requestEmail" id="requestEmail" style="width: 30%; " /></label>
					<input type="submit" value="Send Request" />
				</form>
				<div id="authorizeMessage">&nbsp;</div>
			</div>
<?php } ?>			
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a> - <a href="manageAccount.php">Manage Your Account</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>