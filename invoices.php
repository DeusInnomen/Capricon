<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
else
{
    $year = date("n") >= 3 ? date("Y") + 1: date("Y");
    $result = $db->query("SELECT i.InvoiceID, i.InvoiceType, i.Status, ils.SubTotal, ils.Taxes, ils.TotalDue, i.Created, i.Sent, i.Fulfilled, i.Cancelled, il.LineNumber, il.Description, "
        . "il.Price, il.Tax, il.ReferenceID FROM Invoice i JOIN InvoiceLine il ON i.InvoiceID = il.InvoiceID JOIN (SELECT InvoiceID, SUM(Price) AS SubTotal, SUM(Tax) AS Taxes, "
        . "SUM(Price) + SUM(Tax) AS TotalDue FROM InvoiceLine GROUP BY InvoiceID) ils ON i.InvoiceID = ils.InvoiceID WHERE PeopleID = " . $_SESSION["PeopleID"]
        . " ORDER BY Created DESC, LineNumber ASC");
    $pastInvoices = array();
    $mailedInvoices = array();
    $dealerInvoices = array();
    $hasInvoices = false;
    while($row = $result->fetch_array()) {
        $hasInvoices = true;
        if($row["Status"] == "Paid" || $row["Status"] == "Cancelled") {
            if(!isset($pastInvoices[$row["InvoiceID"]]))
                $pastInvoices[$row["InvoiceID"]] = array();
            $pastInvoices[$row["InvoiceID"]][] = $row;
        }
        else if($row["Status"] == "Mailed") {
            if(!isset($mailedInvoices[$row["InvoiceID"]]))
                $mailedInvoices[$row["InvoiceID"]] = array();
            $mailedInvoices[$row["InvoiceID"]][] = $row;
        }
        else if($row["InvoiceType"] == "Dealer") {
            if(!isset($dealerInvoices[$row["InvoiceID"]]))
                $dealerInvoices[$row["InvoiceID"]] = array();
            $dealerInvoices[$row["InvoiceID"]][] = $row;
        }
    }
    $result->close();
}

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
    <title>Capricon Registration System -- Invoices</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
    <link rel="icon" href="includes/favicon.png" />
    <link rel="shortcut icon" href="includes/favicon.ico" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="includes/global.js"></script>
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script type="text/javascript" src="includes/jquery.payment.js"></script>
    <script type="text/javascript">
        Stripe.setPublishableKey('<?php echo $stripePublicKey; ?>');

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
           		var requests = "";
        		$("#invoicesForm :input[name=select]:checkbox:checked").each(function() {
        			requests += "," + $(this).attr("id");
        		});
                requests = requests.substring(1);
                $form.append($("<input type=\"hidden\" name=\"invoiceIDs\" />").val(requests));
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

			$(".standardTable tr").click(function(e) {
				if(e.target.type !== "checkbox")
					$(":checkbox", this).trigger("click");
			});
			$("#invoicesForm table :input:checkbox").click(function () {
                $("#invoicesForm table tr").each(function () {
                    if ($("td", this).length > 1) {
                        if ($(":checkbox", this).is(":checked"))
                            $(this).next("tr").show();
                        else
                            $(this).next("tr").hide();
                    }
                });
                getAmountDue();
            });
			$("#pastInvoices table :input:checkbox").click(function () {
                $("#pastInvoices table tr").each(function () {
                    if ($("td", this).length > 1) {
                        if ($(":checkbox", this).is(":checked"))
                            $(this).next("tr").show();
                        else
                            $(this).next("tr").hide();
                    }
                });
            });
			$("#mailedInvoices table :input:checkbox").click(function () {
                $("#mailedInvoices table tr").each(function () {
                    if ($("td", this).length > 1) {
                        if ($(":checkbox", this).is(":checked"))
                            $(this).next("tr").show();
                        else
                            $(this).next("tr").hide();
                    }
                });
				if($("#mailedInvoices table :input:checkbox:checked").length > 0)
					$("#reprintInvoice").removeAttr("disabled");
				else
					$("#reprintInvoice").attr("disabled", "disabled");
            });
        	$("#cc-payment-form").submit(function(form) {
        		var requests = "";
        		$("#invoicesForm :input[name=select]:checkbox:checked").each(function() {
        			requests += "," + $(this).attr("id");
        		});
        		if(requests.length == 0) {
                    $("#payment-errors").html("You must select at least one invoice before running your payment.");
                }
                else
                {
                	$(this).find("button").attr("disabled", "disabled");
        			Stripe.createToken($(this), stripeResponseHandler);
                }
				return false;
			});
        	$("#paypal-payment").submit(function(form) {
				var requests = "";
        		$("#invoicesForm :input[name=select]:checkbox:checked").each(function() {
        			requests += "," + $(this).attr("id");
        		});
        		if(requests.length > 0) {
                    requests = requests.substring(1);
                    $(this).append($("<input type=\"hidden\" name=\"invoiceIDs\" />").val(requests));
                    $(this).get(0).submit();
                 }
                 else {
                    $("#paypal-errors").html("You must select at least one invoice before beginning the Paypal process.");
                    return false;
                 }
			});
            $("#mail-payment").submit(function(form) {
				var requests = "";
        		$("#invoicesForm :input[name=select]:checkbox:checked").each(function() {
        			requests += "," + $(this).attr("id");
        		});
        		if(requests.length > 0) {
                    requests = requests.substring(1);
                    $(this).append($("<input type=\"hidden\" name=\"invoiceIDs\" />").val(requests));
                    $(this).get(0).submit();
                 }
                 else {
                    $("#mail-errors").html("You must select at least one invoice before beginning the payment process.");
                    return false;
                 }
			});
			$("#mailedInvoices").submit(function () {
        		var requests = "";
        		$("#mailedInvoices :input[name=select]:checkbox:checked").each(function() {
        			requests += "," + $(this).attr("id");
        		});
        		if(requests.length > 0) {
                    requests = requests.substring(1);
                    $(this).append($("<input type=\"hidden\" name=\"invoiceIDs\" />").val(requests));
                    $(this).get(0).submit();
                }
                else
                    return false;
			});
		});

		function reloadWithMessage(message) {
			var form = $('<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">' +
						 '<input type="hidden" name="message" value="' + message + '" /></form>');
			$('body').append(form);
			$(form).submit();
		}

        function getAmountDue() {
            var total = 0;
			$("#invoicesForm :input[name=select]:checkbox:checked").each(function() {
                var amount = $(this).attr("total");
				total += +amount;
			});
			$("#totalDue").html(total.toFixed(2));
            if(total > 0) {
                $("#selectNotice").hide();
                $("#paymentMethods").show();
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
            }
            else {
                $("#selectNotice").show();
                $("#paymentMethods").hide();
                $("#paymentCard").hide();
                $("#paymentMail").hide();
            }
        }
    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxwide">
            <?php if(!$processed || ($processed && !$success)) { ?>
            <div style="margin-bottom: 30px;">
                <h1>Your Invoices</h1>
                <?php if(!$hasInvoices) { ?>
                <p>
                    You do not presently have any invoices in our system. Nothing to see here!
                </p>
                <p>
                    Invoices are a special feature which are used when somebody at Capricon needs to reach out to you to bill you for something, such as fees related to being
                    a Dealer or paying for forgotten artwork. If you have an invoice in the future, you will receive an email about it, and it will appear on this page for payment.
                </p>
                <?php } else { 
                      if(!empty($message)) echo "<p class='errorMessage'>An error occurred: $message</p>\r\n"; ?>
                <?php if(sizeof($dealerInvoices) > 0) { ?>
                <h3>Dealers Invoices Currently Due</h3>
                <p>
                    Questions regarding the invoices in this section should be sent to
                    <a href="mailto:dealers@capricon.org?subject=Invoice%20Question">Capricon Dealers</a>.
                </p>
                <form id="invoicesForm" class="accountForm" method="post" action="">
                    <div class="standardTable">
                        <table>
                            <tr>
                                <th>Select</th>
                                <th>Invoice Sent</th>
                                <th>Sub Total</th>
                                <th>Taxes</th>
                                <th>Total Due</th>
                            </tr><?php
                            foreach($dealerInvoices as $dealerInvoice)
                            {
                                $invoice = reset($dealerInvoice);
                                $invoiceID = $invoice["InvoiceID"];
                                echo "<tr>";
                                echo "<td style=\"text-align: center;\"><input type=\"checkbox\" name=\"select\" id=\"$invoiceID\" total=\"" . $invoice["TotalDue"] . "\"/></td>";
                                echo "<td>" . (!empty($invoice["Sent"]) ? date("F d, Y g:i a", strtotime($invoice["Sent"])) : date("F d, Y g:i a", strtotime($invoice["Created"]))) . "</td>";
                                echo "<td>" . sprintf("$%01.2f", $invoice["SubTotal"]) . "</td>";
                                echo "<td>" . sprintf("$%01.2f", $invoice["Taxes"]) . "</td>";
                                echo "<td>" . sprintf("$%01.2f", $invoice["TotalDue"]) . "</td>";
                                echo "</tr>\r\n";
                                echo "<tr style=\"display: none; background: #FFFFFF;\"><td colspan=\"5\">";
                                foreach($dealerInvoice as $line) {
                                    echo $line["LineNumber"] . ". " . $line["Description"] . " @ " . sprintf("$%01.2f", $line["Price"]);
                                    if($line["Tax"] != 0)
                                        echo " (+ " . sprintf("$%01.2f", $line["Tax"]) . " Taxes)";
                                    if($line["LineNumber"] != sizeof($dealerInvoice)) echo "<br>";
                                    echo "\r\n";
                                }
                            } ?>
                        </table>
                    </div>
                    <p style="font-size: 1.2em;">
                        Amount Due:
                        <span style="font-weight: bold;">
                            $
                            <span id="totalDue">0.00</span>
                        </span>
                    </p>
                </form>
                <div class="payments clearfix" style="width: 100%;">
    				<h2>Payment Options</h2>
                    <p id="selectNotice">
                        Select one or more invoices to show the payment options.
                    </p>
				    <div id="paymentMethods" style="display: none;">
					    <label><input type="radio" name="paymentMethod" value="card" checked />Credit Card</label>
					    <label><input type="radio" name="paymentMethod" value="paypal" />Paypal</label>
                        <label><input type="radio" name="paymentMethod" value="mail" />Mail-In Payment</label>
				    </div>
				    <div id="paymentCard" style="display: none;">
					    <p>To pay with a credit card (or debit or gift card of the below types), please enter your card information below. Your payment information will be
					    encrypted prior to being sent to our servers for processing.</p>
					    <form action="processInvoices.php" method="POST" id="cc-payment-form">						
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
						    <button type="submit">Submit Payment</button><br /><span class="payment-errors"></span>
					    </form>
				    </div>
				    <div id="paymentPaypal" style="display: none;">
					    <p>To pay using PayPal, press the "Check out with PayPal" button below. You will be redirected to the PayPal website to finalize your purchase.</p>
					    <form action="transferInvoicesToPayPal.php" method="POST" id="paypal-payment">
						    <input type="hidden" name="method" value="Paypal" />
						    <button type="submit"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" style="float: none; margin: 0px 3px 0px 3px;"></button>
					    </form>					
                        <span class="paypal-errors"></span>
				    </div>
				    <div id="paymentMail">
					    <p>If you would rather not make a payment over the Internet using a credit card or Paypal, and instead would prefer to mail 
					    a check, this option will produce a PDF you can print out and mail in the payment details to Capricon. <br /><br />
                        You will receive an email when the Treasurer processes your payment, which happens in batches throughout the year.</p>
					    <form action="processInvoices.php" method="POST" id="mail-payment">
						    <input type="hidden" name="method" value="Mail" />
						    <input type="submit" value="Submit For Payment" />
					    </form>
				    </div>
			    </div>
                <?php } ?>
                <?php if(sizeof($mailedInvoices) > 0) {
                          if(sizeof($dealerInvoices > 0)) echo "<hr />\r\n"; ?>
                <form id="mailedInvoices" class="accountForm" method="post" action="showInvoice.php">
                    <h3>Invoices You Are Mailing To Capricon</h3>
                    <div class="standardTable">
                        <table>
                            <tr>
                                <th>Expand</th>
                                <th>Invoice Type</th>
                                <th>Invoice Sent</th>
                                <th>Status</th>
                                <th>As Of</th>
                                <th>Sub Total</th>
                                <th>Taxes</th>
                                <th>Total Due</th>
                            </tr><?php
                          foreach($mailedInvoices as $mailedInvoice)
                          {
                              $invoice = reset($mailedInvoice);
                              $invoiceID = $invoice["InvoiceID"];
                              echo "<tr>";
                              echo "<td style=\"text-align: center;\"><input type=\"checkbox\" name=\"select\" id=\"$invoiceID\" /></td>";
                              echo "<td>" . $invoice["InvoiceType"] . "</td>";
                              echo "<td>" . (!empty($invoice["Sent"]) ? date("F d, Y g:i a", strtotime($invoice["Sent"])) : date("F d, Y g:i a", strtotime($invoice["Created"]))) . "</td>";
                              echo "<td>" . $invoice["Status"] . "</td>";
                              if(!empty($invoice["Cancelled"]))
                                  echo "<td>" . date("F d, Y g:i a", strtotime($invoice["Cancelled"])) . "</td>";
                              else if(!empty($invoice["Fulfilled"]))
                                  echo "<td>" . date("F d, Y g:i a", strtotime($invoice["Fulfilled"])) . "</td>";
                              else if(!empty($invoice["Sent"]))
                                  echo "<td>" . date("F d, Y g:i a", strtotime($invoice["Sent"])) . "</td>";
                              else
                                  echo "<td>" . date("F d, Y g:i a", strtotime($invoice["Created"])) . "</td>";
                              echo "<td>" . sprintf("$%01.2f", $invoice["SubTotal"]) . "</td>";
                              echo "<td>" . sprintf("$%01.2f", $invoice["Taxes"]) . "</td>";
                              echo "<td>" . sprintf("$%01.2f", $invoice["TotalDue"]) . "</td>";
                              echo "</tr>\r\n";
                              echo "<tr style=\"display: none; background: #FFFFFF;\"><td colspan=\"8\">";
                              foreach($mailedInvoice as $line) {
                                  echo $line["LineNumber"] . ". " . $line["Description"] . " @ " . sprintf("$%01.2f", $line["Price"]);
                                  if($line["Tax"] != 0)
                                      echo " (+ " . sprintf("$%01.2f", $line["Tax"]) . ")";
                                  if($line["LineNumber"] != sizeof($mailedInvoice)) echo "<br>";
                                  echo "\r\n";
                              }
                          } ?>
                        </table>
                    </div>
                    <br />
                    <input type="submit" id="reprintInvoice" name="reprintInvoice" value="Reprint Invoice(s)" disabled />
                    <br />
                </form>
                <?php } ?>
                <?php if(sizeof($pastInvoices) > 0) { if(sizeof($dealerInvoices > 0) || sizeof($mailedInvoices > 0)) echo "<hr />\r\n"; ?>
                <form id="pastInvoices" class="accountForm" method="post" action="">
                    <h3>Past Invoices</h3>
                    <div class="standardTable">
                        <table>
                            <tr>
                                <th>Expand</th>
                                <th>Invoice Type</th>
                                <th>Invoice Sent</th>
                                <th>Status</th>
                                <th>As Of</th>
                                <th>Sub Total</th>
                                <th>Taxes</th>
                                <th>Total Due</th>
                            </tr><?php
                            foreach($pastInvoices as $pastInvoice)
                            {
                                $invoice = reset($pastInvoice);
                                $invoiceID = key($pastInvoice);
                                echo "<tr>";
                                echo "<td style=\"text-align: center;\"><input type=\"checkbox\"/></td>";
                                echo "<td>" . $invoice["InvoiceType"] . "</td>";
                                echo "<td>" . (!empty($invoice["Sent"]) ? date("F d, Y g:i a", strtotime($invoice["Sent"])) : date("F d, Y g:i a", strtotime($invoice["Created"]))) . "</td>";
                                echo "<td>" . $invoice["Status"] . "</td>";
                                if(!empty($invoice["Cancelled"]))
                                    echo "<td>" . date("F d, Y g:i a", strtotime($invoice["Cancelled"])) . "</td>";
                                else if(!empty($invoice["Fulfilled"]))
                                    echo "<td>" . date("F d, Y g:i a", strtotime($invoice["Fulfilled"])) . "</td>";
                                else if(!empty($invoice["Sent"]))
                                    echo "<td>" . date("F d, Y g:i a", strtotime($invoice["Sent"])) . "</td>";
                                else
                                    echo "<td>" . date("F d, Y g:i a", strtotime($invoice["Created"])) . "</td>";
                                echo "<td>" . sprintf("$%01.2f", $invoice["SubTotal"]) . "</td>";
                                echo "<td>" . sprintf("$%01.2f", $invoice["Taxes"]) . "</td>";
                                echo "<td>" . sprintf("$%01.2f", $invoice["TotalDue"]) . "</td>";
                                echo "</tr>\r\n";
                                echo "<tr style=\"display: none; background: #FFFFFF;\"><td colspan=\"8\">";
                                foreach($pastInvoice as $line) {
                                    echo $line["LineNumber"] . ". " . $line["Description"] . " @ " . sprintf("$%01.2f", $line["Price"]);
                                    if($line["Tax"] != 0)
                                        echo " (+ " . sprintf("$%01.2f", $line["Tax"]) . ")";
                                    if($line["LineNumber"] != sizeof($pastInvoice)) echo "<br>";
                                    echo "\r\n";
                                }
                            } ?>
                        </table>
                    </div>
                    <br />
                    <span id="accountSaveMessage">&nbsp;</span>
                    <br />
                </form>
                <?php } ?>
                <?php } ?>
            </div>
            <?php } else { ?>
            <h1>Purchase Completed!</h1>
            <p>
                <?php echo $message; ?>
            </p>
            <?php } ?>
            <div class="goback">
                <a href="index.php">Return to the Main Menu</a>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>