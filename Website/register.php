<?php
	session_start();
	include_once('includes/functions.php');	
	if(isset($_SESSION["PeopleID"]))
		header('Location: index.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Create New Account</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
	<link rel="stylesheet" type="text/css" href="includes/jquery.validate.password.css" />
	<link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
	<script type="text/javascript" src="includes/jquery.validate.password.js"></script>
	<script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#password").keyup(function () {
				$(this).valid();
			});
			$("#passwordconfirm").keyup(function () {
				$(this).valid();
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
			$.validator.messages.required = "<span style=\"font-size: 0.9em; color: #FF0000;\">[REQUIRED]</span>";
			$("#checkEmailForm").submit(function () {
				$("#emailNotice").html("");
				var email = $("#emailToCheck").val();
				$.post("doCreation.php", { email: email, checkOnly: true }, function(result) {
					if(result.success)
					{
						$("#regFormArea").show();
						$("#emailNotice").html("This email address is not used by an account. Fill in the following " +
							"form to proceed with creating a new account.");
						$("#email").val(email);
					}
					else
					{
						$("#emailNotice").html("This email address is already in use! You may already have an account. " +
							"Try logging in <a href=\"login.php\">on the Login page</a>. If you don't know what your " +
							"password is, you can <a href=\"forgotpassword.php\">Reset Your Password</a> first.");
						$("#emailNotice").show();									
					}
					$("#regList").html(result);
				}, 'json');
				return false;
			});
			$("#regForm").validate({
				rules: {
					firstName: "required",
					lastName: "required",
					address1: "required",
					city: "required",
					zipcode: "required",
					adultVerify: "required",
					conductVerify: "required",
					email: {
						required: true,
						email: true
					},
					password: {
						required: true,
						minlength: 8
					},
					passwordconfirm: {
						required: true,
						minlength: 8,
						equalTo: "#password"
					}
				},
				messages: {
					password: {
						minlength: "<span style=\"font-size: 0.9em; color: #FF0000;\">Minimum 8 Chars</span>"
					},
					passwordconfirm:{
						minlength: "<span style=\"font-size: 0.9em; color: #FF0000;\">Minimum 8 Chars</span>",
						equalTo: "<span style=\"font-size: 0.9em; color: #FF0000;\">No Match</span>"
					}
					
				},
				submitHandler: function(form) {
					$("#regForm :input").prop("readonly", true);
					$("#codeError").html("&nbsp;");
					$("#registerErrorMessage").hide();
					$.post("doCreation.php", $(form).serialize(), function(result) {
						if(result.success)
						{
							$(".content").scrollTop(0);
							$("#checkEmail").fadeOut(500);
							$("#regForm").fadeOut(500);
							$("#message").html("Your account has been successfully created. Please see the email " + 
								"you were just sent for activation procedures. It may take up to an hour to arrive.");
							$("#message").show();
						}
						else
						{
							$("#registerErrorMessage").html(result.message);
							$("#registerErrorMessage").show();
						$("#regForm :input").prop("readonly", false);
						}
					}, 'json');
					return false;
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
			<h1>Register a New Account</h1>
			<div style="font-size: 1.05em; display: none;" id="message">&nbsp;</div>
			<div style="font-size: 1.05em;" id="checkEmail">
				<p>To create a new account, please enter your email address below first. We will check to see
				if that address is already in use (thus you may already have an account).</p>
				<p><span style="font-weight: bold;">Important! </span>We have been having significant problems with
				emails not being received by people with @yahoo.com email addresses. We strongly advise you use a
				different email provider if at all possible!</p>
				<form id="checkEmailForm" class="regForm" method="post" action="">
					<label for="emailToCheck" class="fieldLabelShort"><span class="requiredField">*</span>Email: </label><br />
					<input type="email" name="emailToCheck" id="emailToCheck" placeholder="Required" style="width: 70%;" />&nbsp;
					<input type="submit" value="Check Email" />
				</form><br>
				<div id="emailNotice">&nbsp;</div>
			</div>
			<div style="display: none;" id="regFormArea">
			<form id="regForm" class="regForm" method="post" action="">
				<p>Fields that have a "<span class="requiredField">*</span>" before them are required.</p>				
				<div class="headertitle">Personal Information</div>
				<label for="firstName" class="fieldLabelShort"><span class="requiredField">*</span>First Name: </label><br />
				<input type="text" name="firstName" id="firstName" placeholder="Required" style="width: 40%;" /><br />
				<label for="lastName" class="fieldLabelShort"><span class="requiredField">*</span>Last Name: </label><br />
				<input type="text" name="lastName" id="lastName" placeholder="Required" style="width: 40%;" /><br />
				<label for="badgeName" class="fieldLabelShort">Badge Name: </label><br />
				<input type="text" name="badgeName" id="badgeName" placeholder="Full Name Used if Blank" style="width: 40%;" /><br />
				<label><span class="requiredField">*</span>Address (Line 1): </label><br />
				<input type="text" name="address1" id="address1" style="width: 70%;" placeholder="Required" /><br />
				<label>Address (Line 2): </label><br />
				<input type="text" name="address2" id="address2" style="width: 70%;" /><br />
				<label for="city" class="fieldLabelShort"><span class="requiredField">*</span>City: </label><br />
				<input type="text" name="city" id="city" placeholder="Required" style="width: 40%;" /><br />
				<label for="state" class="fieldLabelShort"><span class="requiredField">*</span>State: </label><br />
				<select id="state" name="state" style="width: 40%;" >
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
				<label for="zip" class="fieldLabelShort"><span class="requiredField">*</span>Zip Code: </label><br />
				<input type="text" name="zip" id="zip" size="10" maxlength="10" placeholder="Required" style="width: 20%;" /><br />
				<label for="country" class="fieldLabelShort">Country:</label><br />
				<input type="text" name="country" id="country" value="USA" style="width: 40%;" /><br />
				<label>Phone Number (Main): </label><br />
				<input type="tel" name="phone1" id="phone1" style="width: 40%;" />
				<select id="phone1type" name="phone1type" >
					<option value="Home">Home</option>
					<option value="Mobile" selected>Mobile</option>
					<option value="Work">Work</option>
					<option value="Other">Other</option>
				</select><br />
				<label>Phone Number (Alternate): </label><br />
				<input type="tel" name="phone2" id="phone2" style="width: 40%;" />
				<select id="phone2type" name="phone2type">
					<option value="Home" selected>Home</option>
					<option value="Mobile">Mobile</option>
					<option value="Work">Work</option>
					<option value="Other">Other</option>
				</select><br /><br />
				<label><span class="requiredField">*</span>I certify that I am at least 13 years of age. <input type="checkbox" name="adultVerify" id="adultVerify" /></label><br />
				<label><span class="requiredField">*</span>I have read and agree to abide by the 
					<a href="http://capricon.org/mobile/Phandemonium_Code_of_Conduct.pdf" target="_new">Phandemonium Code of Conduct</a>. <input type="checkbox" name="conductVerify" id="conductVerify" /></label><br /><br />
				<label>How did you originally hear about Capricon?</label><br />
				<input type="text" name="heardFrom" id="heardFrom" style="width: 90%;" /><br />
				<hr />
				<div class="headertitle">Getting Involved</div>
				<p>Capricon offers many ways to get involved with the convention beyond simply attending. If you would like to let 
				any of the following departments know about your interest in participating, please select any of the following checkboxes 
				to let us know. (You can do this later from the "Manage Convention Interests" page, too, where there are other
				interests you can declare.)</p>
				<label><input type="checkbox" name="intGophers" id="intGophers" />
				<b>Volunteering as a Gopher/Helper</b></label> -- <a href="#" onClick="showInfo('volunteers');">More Information</a><br />
				<label><input type="checkbox" name="intProgram" id="intProgram" />
				<b>Programming Participant</b></label> -- <a href="#" onClick="showInfo('programming');">More Information</a><br />
				<label><input type="checkbox" name="intDealer" id="intDealer" />
				<b>Being a Dealer</b></label> -- <a href="#" onClick="showInfo('dealers');">More Information</a><br />
				<label><input type="checkbox" name="intArtShow" id="intArtShow" />
				<b>Exhibiting in the Art Show</b></label> -- <a href="#" onClick="showInfo('artshow');">More Information</a><br />
				<label><input type="checkbox" name="intAds" id="intAds" />
				<b>Advertising</b></label> -- <a href="#" onClick="showInfo('ads');">More Information</a><br />
				<label><input type="checkbox" name="intParties" id="intParties" />
				<b>Throwing a Party</b></label> -- <a href="#" onClick="showInfo('parties');">More Information</a>				
				<hr />
				<div class="headertitle">Password</div>
				<div style="display: block; float: left; width: 50%">
					<p style="font-size: 0.85em; color: #BB0000;">Important: Your email is your account login!</p>
					<label for="password" class="fieldLabelShort"><span class="requiredField">*</span>Password: </label><br />
					<input type="password" name="password" id="password" placeholder="Required" style="width: 70%;" /><br />
					<label for="passwordconfirm" class="fieldLabelShort"><span class="requiredField">*</span>Retype: </label><br />
					<input type="password" name="passwordconfirm" id="passwordconfirm" placeholder="Required" style="width: 70%;" /><br />
					Strength:<div class="password-meter" style="height: 50px;">
						<div class="password-meter-message" style="clear: both;"></div>
						<div class="password-meter-bg"><div class="password-meter-bar">&nbsp;</div></div>
					</div>
				</div>
				<div style="display: block; float: left; width: 50%">
					<p>Passwords must be at least 8 characters long, and at least two of: uppercase, lowercase, numbers and symbols.</p>
				</div>
				<div style="clear: both;">
					<p style="margin-top: 20px;">Verify that you have filled everything out above correctly, then press the following button to continue the
					registration process.</p>
					<input type="hidden" name="email" id="email" value="">
					<input type="submit" value="Submit Registration" />
				</div>
				<br /><br />
				<div id="registerErrorMessage">&nbsp;</div>
			</form>
			</div>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>	