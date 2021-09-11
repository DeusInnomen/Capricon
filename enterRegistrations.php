<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("RegLead") && !DoesUserBelongHere("Marketing"))
		header('Location: /index.php');
		
	$badgePrice = '70';
	$result = $db->query("SELECT Price FROM AvailableBadges WHERE AvailableFrom <= CURDATE() AND AvailableTo >= CURDATE() AND BadgeTypeID = 1");
	if($result->num_rows > 0)
	{
		$row = $result->fetch_array();
		$badgePrice = round($row["Price"]);
		$result->close();
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Enter Manual Registrations</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
	<link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<style>
		#resultsBlock {
			clear: both;
			width: 100%;
			max-height: 415px;
			overflow: auto;
		}
	</style>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
	<script type="text/javascript" src="includes/jquery.payment.js"></script>
	<script type="text/javascript">
        Stripe.setPublishableKey('<?php echo $stripePublicKey; ?>');

		$(document).ready(function() {
			$("#checkForCurrentForm").submit(function () {
				var searchTerm = $("#searchTerm").val();				
				$.post("manualRegUserSearch.php", { terms: searchTerm }, function(result) {
					$("#searchResults").html(result);
				});
				return false;
			});
			$("#resultsDialog").dialog({
				autoOpen: false,
				height: 350,
				width: 450,
				modal: true,
				buttons: {
					Ok: function() { $( this ).dialog( "close" ); }
				}
			});
		});

		
		function submitRegistration()
		{
			$("#submitRegInfo").attr("readonly", true);
			var method = $("#paymentMethods input[name=paymentMethod]:checked").val();
			
			if(method == "credit")
			{
				Stripe.createToken({
					number: $("#cc-number").val(),
					cvc: $("#cc-cvc").val(),
					exp_month: $("#cc-expm").val(),
					exp_year: $("#cc-expy").val()
				}, stripeResponseHandler);
			}
			else
				processRegistration();
		}
		
		var stripeResponseHandler = function(status, response) {
			if(response.error)
			{
				$("#submitRegInfo").removeAttr("readonly");
				$("#resultsDialog").dialog("option", "title", "Payment Failed");
				$("#resultsDialog p").html("The payment attempted failed for the following reason:<br />" + response.error.message);
				$("#resultsDialog").dialog("open");
			}
			else
			{
				var token = response.id;
				$("#creditInfo").append($("<input type=\"hidden\" name=\"stripeToken\" />").val(token));
				processRegistration();
			}
		};
		
		function processRegistration()
		{
			var user = $("#addressInfo").serialize();
			var badges = $("#badgeInfo").serialize();
			var method = $("#paymentMethods input[name=paymentMethod]:checked").val();
			var payment;
			if (method == "credit")
				payment = $("#creditInfo").serialize();
			else if(method == "check")
				payment = $("#checkInfo").serialize();
			else
				payment = $("#cashInfo").serialize();
			
			$.post("doManualRegistration.php", { action: "ProcessRegistration", user: user, badges: badges, payment: payment }, function(result) {
				$("#submitRegInfo").removeAttr("readonly");
				if(result.success)
				{
					$("#resultsDialog").dialog("option", "title", "Registration Complete");
					$("#resultsDialog").dialog("option", "buttons", { Ok: function() { location.reload(); }	});
				}
				else
					$("#resultsDialog").dialog("option", "title", "Registration Failed");
				$("#resultsDialog p").html(result.message);
				$("#resultsDialog").dialog("open");
			}, 'json');		
		}

		function togglePromoFields()
		{
			if($("#promoCode").val() != "")
			{
				$("#submitRegInfo").attr("readonly", true);
				$("#validateCode").removeAttr("readonly");
			}
			else
			{
				$("#submitRegInfo").removeAttr("readonly");
				$("#validateCode").attr("readonly", true);
			}
		}
		
		function validateCode()
		{
			$("#validateCode").attr("readonly", true);
			var code = $("#promoCode").val();
			$.post("doManualRegistration.php", { action: "ValidateCode", code: code }, function(result) {
				$("#submitRegInfo").removeAttr("readonly");
				$("#codeMessage").html(result.message);
				$("#codeMessage").show();
				$total = getTotalPrice();
				if(result.success)
				{
					$("#codeType").val(result.codeType);
					$("#codeID").val(result.codeID);
					$("#codeValue").val(code);
					$badgePrice = parseInt($("#badgePrice").val(), 10);
					if(result.isFreeBadge)
						$total -= ($badgePrice * result.value);
					else
						$total -= result.value;
					if($total < 0) $total = 0;
				}
				else
				{
					$("#codeType").val("");
					$("#codeID").val("");
					$("#codeValue").val("");
					$("#validateCode").removeAttr("readonly");
				}
				$("#finalTotal").html($total);
				$("#creditAmount").val($total);
				$("#checkAmount").val($total);
			}, 'json');
		}
		
		function getTotalPrice()
		{
			$badgePrice = parseInt($("#badgePrice").val(), 10);
			$total = $badgePrice;
			
			if($("#addlFName1").val() != "")
				$total += $("#addlKid1").prop("checked") ? 0 : $badgePrice;
			else
				return $total;
			
			if($("#addlFName2").val() != "")
				$total += $("#addlKid2").prop("checked") ? 0 : $badgePrice;
			else
				return $total;
				
			if($("#addlFName3").val() != "")
				$total += $("#addlKid3").prop("checked") ? 0 : $badgePrice;
			else
				return $total;
				
			if($("#addlFName4").val() != "")
				$total += $("#addlKid4").prop("checked") ? 0 : $badgePrice;
			else
				return $total;

			if($("#addlFName5").val() != "")
				$total += $("#addlKid5").prop("checked") ? 0 : $badgePrice;		
			return $total;
		}
		
		function toggleRegFormData()
		{
			$total = getTotalPrice();
			$("#regTotal").html($total);
			$("#finalTotal").html($total);
			$("#creditAmount").val($total);
			$("#checkAmount").val($total);

			if($("#addlFName1").val() != "")
				$("#addl2").show();
			else
			{
				$("#addl2").hide();
				$("#addl3").hide();
				$("#addl4").hide();
				$("#addl5").hide();
				return;
			}
			
			if($("#addlFName2").val() != "")
				$("#addl3").show();
			else
			{
				$("#addl3").hide();
				$("#addl4").hide();
				$("#addl5").hide();
				return;
			}
			
			if($("#addlFName3").val() != "")
				$("#addl4").show();
			else
			{
				$("#addl4").hide();
				$("#addl5").hide();
				return;
			}
			
			if($("#addlFName4").val() != "")
				$("#addl5").show();
			else
				$("#addl5").hide();
		}
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div id="resultsDialog" title="">
		<p></p>
	</div>
	<div class="content">
		<div class="centerboxwide">
			<h1>Enter Manual Registrations</h1>
			<p>Enter the email address of the member who is registering. If this has not been provided by the
				member, then enter the First and Last Name (or just the Last Name) instead. (You can enter a 
				partial name and it will search for names that start with your terms.) Leave blank and press 
				Search to enter a new member.</p>
			<form id="checkForCurrentForm" method="post">
				<b>User to Look Up:</b> <input type="text" name="searchTerm" id="searchTerm" style="width: 300px;" />
				<input type="submit" id="searchSubmit" name="searchSubmit" value="Search" />
				<input type="submit" id="pageReset" name="pageReset" value="Reset Page" onclick="location.reload();" />
			</form>
			<div id="searchResults"></div>
			<form id="badgeInfo" method="post">
			<div id="enterReg" style="display: none;">
				<div class="headertitle">Registration Info</div>
				<div id="regList">
					<label>Main Attendee's Badge Name: <input type="text" name="mainBadge" id="mainBadge" style="width: 125px;"></label>
					<label>Listed Badge Price: $<input type="number" name="badgePrice" id="badgePrice" style="width: 50px;" onchange="toggleRegFormData();" 
						step="1.0" value="<?php echo $badgePrice; ?>"></label><br>
    				<div class="headertitle">Additional Attendees</div>
					<label>Attendee #1 First Name: <input type="text" name="addlFName1" id="addlFName1" style="width: 100px;" onkeyup="toggleRegFormData();"></label> 
					<label>Last Name: <input type="text" name="addlLName1" id="addlLName1" style="width: 100px;"></label> 
					<label>Badge Name: <input type="text" name="addlBadge1" id="addlBadge1" style="width: 125px;"></label>
					<label><input type="checkbox" name="addlKid1" id="addlKid1" onchange="toggleRegFormData();"> Kid-in-Tow</label><br />
					<div id="addl2" style="display: none;"><label>Attendee #2 First Name: <input type="text" name="addlFName2" id="addlFName2" style="width: 100px;" onkeyup="toggleRegFormData();"></label> 
					<label>Last Name: <input type="text" name="addlLName2" id="addlLName2" style="width: 100px;"></label> 
					<label>Badge Name: <input type="text" name="addlBadge2" id="addlBadge2" style="width: 125px;"></label>
					<label><input type="checkbox" name="addlKid2" id="addlKid2" onchange="toggleRegFormData();"> Kid-in-Tow</label><br /></div>
					<div id="addl3" style="display: none;"><label>Attendee #3 First Name: <input type="text" name="addlFName3" id="addlFName3" style="width: 100px;" onkeyup="toggleRegFormData();"></label> 
					<label>Last Name: <input type="text" name="addlLName3" id="addlLName3" style="width: 100px;"></label> 
					<label>Badge Name: <input type="text" name="addlBadge3" id="addlBadge3" style="width: 125px;"></label>
					<label><input type="checkbox" name="addlKid3" id="addlKid3" onchange="toggleRegFormData();"> Kid-in-Tow</label><br /></div>
					<div id="addl4" style="display: none;"><label>Attendee #4 First Name: <input type="text" name="addlFName4" id="addlFName4" style="width: 100px;" onkeyup="toggleRegFormData();"></label> 
					<label>Last Name: <input type="text" name="addlLName4" id="addlLName4" style="width: 100px;"></label> 
					<label>Badge Name: <input type="text" name="addlBadge4" id="addlBadge4" style="width: 125px;"></label>
					<label><input type="checkbox" name="addlKid4" id="addlKid4" onchange="toggleRegFormData();"> Kid-in-Tow</label><br /></div>
					<div id="addl5" style="display: none;"><label>Attendee #5 First Name: <input type="text" name="addlFName5" id="addlFName5" style="width: 100px;" onkeyup="toggleRegFormData();"></label> 
					<label>Last Name: <input type="text" name="addlLName5" id="addlLName5" style="width: 100px;"></label> 
					<label>Badge Name: <input type="text" name="addlBadge5" id="addlBadge5" style="width: 125px;"></label>
					<label><input type="checkbox" name="addlKid5" id="addlKid5" onchange="toggleRegFormData();"> Kid-in-Tow</label><br /></div>
					<br />
					<p>Badge Registration Total: <b>$<label name="regTotal" id="regTotal"><?php echo $badgePrice; ?></label>.00</b></p>
					<input type="hidden" id="codeType" name="codeType" value="" /><input type="hidden" id="codeID" name="codeID" value="" /><input type="hidden" id="codeValue" name="codeValue" value="" />
					<input type="submit" id="badgesGood" name="badgesGood" onclick="$('#enterPayment').show(); $('#badgesGood').prop('readonly', 'true'); return false;" value="The Badge Information is Correct" />
				</div>
			</div>
			</form>
			<div id="enterPayment" style="display: none;">
				<div class="headertitle">Payment Info</div>
				<div id="paymentMethods">
					<span>Payment Method: </span>
					<label><input type="radio" name="paymentMethod" value="credit" onclick="$('#paymentCard').show(); $('#paymentMail').hide(); $('#paymentCash').hide(); $('#regSubmit').show();" />Credit Card</label>
					<label><input type="radio" name="paymentMethod" value="check" onclick="$('#paymentCard').hide(); $('#paymentMail').show(); $('#paymentCash').hide(); $('#regSubmit').show();" />Check</label>
					<label><input type="radio" name="paymentMethod" value="cash" onclick="$('#paymentCard').hide(); $('#paymentMail').hide(); $('#paymentCash').show(); $('#regSubmit').show();" />Cash</label><br/>
				</div>
				<div id="paymentCard" style="display: none;">
					<form id="creditInfo" method="post">
						<input type="hidden" name="method" value="credit" />
						<input type="hidden" id="creditAmount" name="amount" value="<?php echo $badgePrice; ?>" />
						<a href="https://stripe.com"><img src="includes/stripe.png"></a>
						<div class="form-row">
							<label>
								<span>Card Number</span>
								<input type="text" id="cc-number" size="20" data-stripe="number"/>
							</label>
						</div>
						<div class="form-row">
							<label>
								<span>CVC</span>
								<input type="text" id="cc-cvc" size="4" data-stripe="cvc"/>
							</label>
						</div>
						<div class="form-row">
							<label>
								<span>Expiration (MM/YYYY)</span>
								<input type="text" id="cc-expm" size="2" data-stripe="exp-month"/>
							</label>
							<span> / </span>
							<input type="text" id="cc-expy" size="4" data-stripe="exp-year"/>
							<div style="float: right;">
								<img src="includes/card_logos.gif" class="masterTooltip" title="We accept Visa, Mastercard, American Express, Discover, JCB and Diner's Club.">
							</div>
						</div>
						<span class="payment-errors"></span>
					</form>
				</div>
				<div id="paymentMail" style="display: none;">
					<form id="checkInfo" method="post">
						<input type="hidden" name="method" value="check" />
						<input type="hidden" id="checkAmount" name="amount" value="<?php echo $badgePrice; ?>" />
						<label>Check Number: <input type="text" id="checkNumber" name="checkNumber" style="width: 70px" /></label><br />
					</form>
					<p><b>Ensure the amount on the check matches the amount listed below in the "Final Registration Total" value.</b> If it does not
					match the amount on the check, you must contact the member immediately to correct the check amount! This person's registration information 
					will be marked as "Pending" in the system. They will be notified that it has been entered and will be changed to "Paid" once their
					check has been successfully cashed.</p>
				</div>
				<div id="paymentCash" style="display: none;">
					<form id="cashInfo" method="post">
						<input type="hidden" name="method" value="cash" />
					</form>
					<p>Please verify the amount listed below has been collected.</p>
				</div>
				<div id="regSubmit" style="display: none;">
					<label>Promo Code or Gift Certificate: <input type="text" name="promoCode" id="promoCode" onkeyup="togglePromoFields();" style="width: 250px;" /></label>
					<input type="submit" id="validateCode" name="validateCode" onclick="validateCode(); return false;" value="Validate Code" readonly /><br />
					<p id="codeMessage" style="display: none;" />
					<p>Final Registration Total: <b>$<label name="finalTotal" id="finalTotal"><?php echo $badgePrice; ?></label>.00</b></p>				
					<p><b>Once you are absolutely certain that everything has been entered correctly,</b> press the following button to submit the 
					registration(s) to the system. If the member has chosen to pay with a credit card, it will be charged immediately.</p>
					<input type="submit" id="submitRegInfo" name="submitRegInfo" onclick="submitRegistration(); return false;" value="Submit Registration" /><br />
				</div>
			</div>			
			<div class="clearfix"></div>
			<div class="goback">
				<a href="/index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>