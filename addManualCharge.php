<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("Treasurer"))
    header('Location: /index.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Add Manual Charges</title>
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

    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div id="resultsDialog" title="">
        <p></p>
    </div>
    <div class="content">
        <div class="centerboxwide">
            <h1>Add Manual Charges</h1>
            <p>
                Use this form to enter charges manually into the database. If there are any questions, please contact it@phandemonium.org.
                This system does not produce any emails to the recipients, unless the charge is sent as an invoice.
            </p>
            <form id="badgeInfo" method="post">
                <div id="transactionDetails" style="display: none;">
                    <div class="headertitle">Transaction Details</div>

                </div>
            </form>
            <div id="enterPayment" style="display: none;">
                <div class="headertitle">Payment Type to Record</div>
                <div id="paymentMethods">
                    <span>Payment Method: </span>
                    <label>
                        <input type="radio" name="paymentMethod" value="credit" onclick="$('#paymentCard').show(); $('#paymentMail').hide(); $('#paymentCash').hide(); $('#paymentInvoice').hide(); $('#regSubmit').show();" />Credit Card
                    </label>
                    <label>
                        <input type="radio" name="paymentMethod" value="check" onclick="$('#paymentCard').hide(); $('#paymentMail').show(); $('#paymentCash').hide(); $('#paymentInvoice').hide(); $('#regSubmit').show();" />Check
                    </label>
                    <label>
                        <input type="radio" name="paymentMethod" value="cash" onclick="$('#paymentCard').hide(); $('#paymentMail').hide(); $('#paymentCash').show(); $('#paymentInvoice').hide(); $('#regSubmit').show();" />Cash
                    </label>
                    <label>
                        <input type="radio" name="paymentMethod" value="invoice" onclick="$('#paymentCard').hide(); $('#paymentMail').hide(); $('#paymentCash').hide(); $('#paymentInvoice').show(); $('#regSubmit').show();" />Invoice
                    </label>
                    <br />
                </div>
                <div id="paymentCard" style="display: none;">
                    <form id="creditInfo" method="post">
                        <input type="hidden" name="method" value="credit" />
                        <a href="https://stripe.com">
                            <img src="includes/stripe.png" />
                        </a>
                        <div class="form-row">
                            <label>
                                <span>Card Number</span>
                                <input type="text" id="cc-number" size="20" data-stripe="number" />
                            </label>
                        </div>
                        <div class="form-row">
                            <label>
                                <span>CVC</span>
                                <input type="text" id="cc-cvc" size="4" data-stripe="cvc" />
                            </label>
                        </div>
                        <div class="form-row">
                            <label>
                                <span>Expiration (MM/YYYY)</span>
                                <input type="text" id="cc-expm" size="2" data-stripe="exp-month" />
                            </label>
                            <span>/ </span>
                            <input type="text" id="cc-expy" size="4" data-stripe="exp-year" />
                            <div style="float: right;">
                                <img src="includes/card_logos.gif" class="masterTooltip" title="We accept Visa, Mastercard, American Express, Discover, JCB and Diner's Club." />
                            </div>
                        </div>
                        <span class="payment-errors"></span>
                    </form>
                    <p>
                        This method will charge the specified credit card immediately. This assumes that either the card is physically present or the number, expiration,
                        and CVC values have been provided to you for the purpose of collecting this payment. If the recipient wishes to pay by credit card but they are
                        not physically present, use the Invoice option instead; this will send an invoice to the recipient to arrange for payment themselves.
                    </p>
                </div>
                <div id="paymentMail" style="display: none;">
                    <form id="checkInfo" method="post">
                        <input type="hidden" name="method" value="check" />
                        <label>
                            Check Number:
                            <input type="text" id="checkNumber" name="checkNumber" style="width: 70px" />
                        </label>
                        <br />
                    </form>
                    <p>
                        Please be sure that the amount listed on the check matches the above entered amount, as well as the check number.
                        The transaction will be marked as paid immediately.
                    </p>
                </div>
                <div id="paymentCash" style="display: none;">
                    <form id="cashInfo" method="post">
                        <input type="hidden" name="method" value="cash" />
                    </form>
                    <p>Please verify that the amount listed above has been collected.</p>
                </div>
                <div id="paymentInvoice" style="display: none;">
                    <form id="invoiceInfo" method="post">
                        <input type="hidden" name="method" value="invoice" />
                    </form>
                    <p>
                        The charge will be sent to the recipient via email, which can be paid via this website or a mailed check. Use this if you still need to collect
                    payment but the recipient is not physically present. The Treasurer will be notified when the payment is received, and optionally an email will be
                    sent to another address if you wish someone else to be notified.
                    </p>
                </div>
                <div id="regSubmit" style="display: none;">
                    <p>
                        <b>Once you are absolutely certain that everything has been entered correctly,</b>press the following button to submit the
					    transaction to the system.
                    </p>
                    <input type="submit" id="submitTransaction" name="submitTransaction" onclick="submitTransaction(); return false;" value="Submit Transaction" />
                    <br />
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