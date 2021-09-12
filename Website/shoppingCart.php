<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	else
		$orders = getShoppingCart();
	
	if(isset($_SESSION["PayPalError"]))
	{
		$processed = false;
		$success = false;
		$message = $_SESSION["PayPalError"];
		unset($_SESSION["PayPalError"]);
	}
	else
	{
		$processed = isset($_POST["processed"]);
		$success = (isset($_POST["success"]) && $_POST["success"] == "1") ? true : false;
		$message = isset($_POST["message"]) ? $_POST["message"] : "";
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Shopping Cart</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>">
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
	<script type="text/javascript" src="includes/jquery.payment.js"></script>
	<script type="text/javascript">
		Stripe.setPublishableKey('<?php echo $stripePublicKey; ?>');
		var recipient = "<?php echo $_SESSION["PeopleID"]; ?>";
		
		var stripeResponseHandler = function(status, response) {
			var $form = $("#cc-payment-form");
			if(response.error)
			{
				$form.find(".payment-errors").text(response.error.message);
				$form.find("button").removeAttr("disabled");
			}
			else
			{
				var token = response.id;
				$form.append($("<input type=\"hidden\" name=\"stripeToken\" />").val(token));
				$form.get(0).submit();
			}
		};

		$(document).ready(function() {
			$("#paymentMethods :radio").click(function () {
				var method = $("#paymentMethods input[name=paymentMethod]:checked").val();
				if(method == "card")
				{
					$("#paymentCard").show();
					$("#paymentPaypal").hide();
					$("#paymentMail").hide();
				}
				else if(method == "paypal")
				{
					$("#paymentCard").hide();
					$("#paymentPaypal").show();
					$("#paymentMail").hide();
				}
				else
				{
					$("#paymentCard").hide();
					$("#paymentPaypal").hide();
					$("#paymentMail").show();
				}
			});
			$("#cartList tr").click(function(e) {
				if(e.target.type !== "checkbox")
					$(":checkbox", this).trigger("click");
			});
			$("#cartEditForm :input:checkbox").click(function () {
				if($("#cartEditForm :input:checkbox:checked").length > 0)
					$("#removeSelected").removeAttr("disabled");
				else
					$("#removeSelected").attr("disabled", "disabled");
			});
			$("#cc-payment-form").submit(function(form) {
				$("#couponErrors").html(" ");
				$(this).find("button").attr("disabled", "disabled");
				Stripe.createToken($(this), stripeResponseHandler);
				return false;
			});
		});
		
		function removeSelectedRows() {
			var values = "";
			$("#cartEditForm :input:checkbox:checked").each(function() {
				values += ", " + $(this).val();
			});
			if(values.length > 0) values = values.substring(2);
			$.post("doCartAction.php", { entry: "CartRemoveItem", recipient: recipient, type: values }, function(result) {
				if(result.success)
					reloadWithMessage(result.message);
			}, 'json');
		}
		
		function removeAllRows() {
			$.post("doCartAction.php", { entry: "CartRemoveItem", recipient: recipient, type: "" }, function(result) {
				if(result.success)
					reloadWithMessage(result.message);
			}, 'json');
		}
		
		function applyCode() {
			var code = $("#code").val();
			var values = "";
			$("#cartEditForm :input:checkbox:checked").each(function() {
				values += ", " + $(this).val();
			});
			if(values.length > 0) values = "|" + values.substring(2);
			$.post("doCartAction.php", { entry: "CartAddCode", recipient: recipient, type: code + values }, function(result) {
				if(result.success)
					reloadWithMessage(result.message);
				else
					$("#couponErrors").html(result.message);
			}, 'json');
		}
		
		function removeCode() {
			var values = "";
			$("#cartEditForm :input:checkbox:checked").each(function() {
				values += ", " + $(this).val();
			});
			if(values.length > 0) values = values.substring(2);
			$.post("doCartAction.php", { entry: "CartRemoveCode", recipient: recipient, type: values }, function(result) {
				if(result.success)
					reloadWithMessage(result.message);
				else
					$("#couponErrors").html(result.message);
			}, 'json');
		}
		
		function reloadWithMessage(message) {
			var form = $('<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">' + 
						 '<input type="hidden" name="message" value="' + message + '" /></form>');
			$('body').append(form);
			$(form).submit();
		}
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxmedium">
<?php if(!$processed || ($processed && !$success))
			{ ?>
			<h1>Your Shopping Cart</h1>
			<div id="cartList">
<?php
				if(!empty($orders))
				{ ?>
				<form id="cartEditForm" method="post">
				<table>
					<tr><th>Select</th><th>Recipient</th><th>Item</th><th>Details</th><th>Price</th><th>Discount</th></tr>
<?php
					$index = 0;
					$total = 0.0;
					foreach($orders as $order)
					{
						echo "<tr><td style=\"text-align: center;\"><input type=\"checkbox\" value=\"" . $order["CartID"] .
							"\" /></td><td>" . $order["Name"] . "</td><td>" . $order["Item"] . "</td><td>" . 
							$order["Details"] . "</td><td>";
						$price = $order["Price"];
						$codes = "";
						if(isset($order["PromoCode"])) 
						{
							$price -= $order["Discount"];
							if($price < 0) $price = 0;
							$codes .= "<br />" . $order["PromoCode"];
						}
						if(isset($order["CertificateCode"])) 
						{
							if($order["Badges"] > 0)
								$price = 0;
							else
								$price -= $order["CurrentValue"];
							if($price < 0) $price = 0;
							$codes .= "<br />" . $order["CertificateCode"];
						}
						if(strlen($codes) > 0) $codes = substr($codes, 6);
						echo sprintf("$%01.2f", $price) . "</td><td>$codes</td></tr>\r\n";
						$total += $price;
						$index++;
					} ?>
				</table>
				<div style="float: right; margin-top: 10px; font-size: 1.15em;">Total: <?php echo sprintf("$%01.2f", $total); ?></div>
				<div style="padding-top: 10px;">
					<input type="submit" id="removeSelected" onclick="removeSelectedRows(); return false;" value="Remove Selected" disabled />
					<input type="submit" onclick="removeAllRows(); return false;" value="Remove All" />
				</div>
				</form>
<?php
				}
				else
					echo '<p class="noneFound">Your shopping cart is current empty.</p>' . "\r\n"; ?>
			</div>
<?php if(!empty($orders)) { ?>
			<div id="discounts">
				<form id="couponForm" method="post">
				<p style="font-size: 0.9em;">To apply a gift certificate or promotional code, enter the code in the box below. It will apply starting with the first entry in the cart. If you would like to have the code apply to a specific item, Select the item first before pressing the	"Apply Code" button. The price change will reflect immediately for promotional codes, and the total amount will have the value of the gift certificate deducted, but <span style="color: #0000FF; font-weight: bold;">they will not be considered used until your transaction is submitted and complete.</span></p>
				<input type="text" id="code" style="width: 30%;" />
				<input type="submit" onclick="applyCode(); return false;" value="Apply Code" />
				<input type="submit" onclick="removeCode(); return false;" value="Remove Codes" /><br />
				</form>
				<p id="couponErrors"><?php echo $message; ?></p>
			</div>
<?php if($total == 0.0) { ?>
			<div id="submitNoCharge">
				<form action="processCart.php" method="POST" id="no-payment">
				<p>There is no charge at this time. To commit this order, press the Submit Order button.</p>
				<button type="submit">Submit Order</button>
				</form>
			</div>
<?php
      } else { ?>
			<div class="payments clearfix">
				<h2>Payment Options</h2>
				<div id="paymentMethods">
					<label><input type="radio" name="paymentMethod" value="card" checked />Credit Card</label>
					<label><input type="radio" name="paymentMethod" value="paypal" />Paypal</label>
					<label><input type="radio" name="paymentMethod" value="mail" />Mail-In Payment</label>
				</div>
				<div id="paymentCard">
					<p>To pay with a credit card (or debit or gift card of the below types), please enter your card information below. Your payment information will be
					encrypted prior to being sent to our servers for processing.</p>
					<form action="processCart.php" method="POST" id="cc-payment-form">						
						<a href="https://stripe.com"><img src="includes/stripe.png"></a>
						<div class="form-row">
							<label>
								<span>Card Number</span>
								<input type="text" size="20" data-stripe="number"/>
							</label>
						</div>
						<div class="form-row">
							<label>
								<span>CVC</span>
								<input type="text" size="4" data-stripe="cvc"/>
							</label>
						</div>
						<div class="form-row">
							<label>
								<span>Expiration (MM/YYYY)</span>
								<input type="text" size="2" data-stripe="exp-month"/>
							</label>
							<span> / </span>
							<input type="text" size="4" data-stripe="exp-year"/>
							<div style="float: right;">
								<img src="includes/card_logos.gif" class="masterTooltip" title="We accept Visa, Mastercard, American Express, Discover, JCB and Diner's Club.">
							</div>
						</div>
                        <?php if(IsTestSite()) echo "<p style='font-weight: bold;'>NO ACTUAL CHARGES WILL BE MADE, THIS USES TEST MODE PAYMENTS!</p>"; ?>
						<button type="submit">Submit Payment</button><span class="payment-errors"></span>
					</form>
				</div>
				<div id="paymentPaypal">
					<p>To pay using PayPal, press the "Check out with PayPal" button below. You will be redirected to the PayPal website to finalize your purchase.</p>
                    <?php if(IsTestSite()) echo "<p style='font-weight: bold;'>NO ACTUAL CHARGES WILL BE MADE, THIS USES TEST MODE PAYMENTS!</p>"; ?>
					<form action="transferToPayPal.php" method="POST" id="paypal-payment">
						<input type="hidden" name="method" value="Paypal" />
						<button type="submit"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" style="float: none; margin: 0px 3px 0px 3px;"></button>
					</form>					
				</div>
				<div id="paymentMail">
					<p>If you would rather not make a payment over the Internet using a credit card or Paypal, and
					instead would prefer to mail a check or credit card details, this option will produce
					a PDF you can print out and mail in the payment details to Capricon. Any badges in
					your order will appear in the system right away so you know they've ready but pending the
					processing of your payment. (Note that if we do not receive your payment prior to the 
					convention, you will have to pay the "At Door" prices at that time.)<br /><br />You will receive an email when the
					Registration team processes your payment, which happens in batches throughout the year.</p>
					<form action="processCart.php" method="POST" id="mail-payment">
						<input type="hidden" name="method" value="Mail" />
						<input type="submit" value="Submit Payment" />
					</form>
				</div>
			</div>
<?php } } }
else
{ ?>
			<h1>Purchase Completed!</h1>
			<p><?php echo $message; ?></p>
<?php } ?>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>