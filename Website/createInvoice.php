<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("Treasurer"))
    header('Location: index.php');
else
{
    $result = $db->query("SELECT i.InvoiceID, i.InvoiceType, i.PeopleID, i.RelatedRecordID, i.Status, ils.SubTotal, ils.Taxes, ils.TotalDue, i.Created, i.Sent, i.Fulfilled, i.Cancelled, il.LineNumber, "
        . "il.Description, il.Price, il.Tax, il.ReferenceID FROM Invoice i JOIN InvoiceLine il ON i.InvoiceID = il.InvoiceID JOIN (SELECT InvoiceID, SUM(Price) AS SubTotal, SUM(Tax) AS Taxes, "
        . "SUM(Price) + SUM(Tax) AS TotalDue FROM InvoiceLine GROUP BY InvoiceID) ils ON i.InvoiceID = ils.InvoiceID WHERE i.Status = 'Mailed' ORDER BY Created DESC, LineNumber ASC");
    $invoices = array(); // Full invoices with lines
    $invoicesShort = array(); // Just the first line, for Paypal processing
    $dealerPresenceIDs = "";
    if($result->num_rows > 0) {
        while($row = $result->fetch_array()) {
            if(!isset($invoices[$row["InvoiceID"]])) {
                $invoices[$row["InvoiceID"]] = array();
                $invoicesShort[] = $row;
                if($row["InvoiceType"] == "Dealer")
                    $dealerPresenceIDs .= "," . $row["RelatedRecordID"];
            }
            $invoices[$row["InvoiceID"]][] = $row;
        }
        $result->close();
    }

    $dealers = array();
    if(strlen($dealerPresenceIDs) > 0) {
        $sql = "SELECT DealerPresenceID, FirstName, LastName, CompanyName, LegalName, Email, ContactEmail, OnlyUseThisEmail FROM DealerPresence dp JOIN Dealer d ON dp.DealerID = d.DealerID JOIN People p ON p.PeopleID = d.PeopleID " 
            . "WHERE DealerPresenceID IN (" . substr($dealerPresenceIDs, 1) . ")";
        $result = $db->query($sql);
        while($row = $result->fetch_array())
            $dealers[$row["DealerPresenceID"]] = $row;
        $result->close();
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Create Invoice</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
    <link rel="icon" href="includes/favicon.png" />
    <link rel="shortcut icon" href="includes/favicon.ico" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="includes/global.js"></script>
    <!--[if (gte IE 6)&(lte IE 8)]>
	<script type="text/javascript" src="includes/selectivizr-min.js"></script>
	<![endif]-->
    <script type="text/javascript">
		$(document).ready(function() {
			$("#updateInvoices tr").click(function(e) {
				if(e.target.type !== "checkbox")
					$(":checkbox", this).trigger("click");
			});
			$("#updateInvoices table :input:checkbox").click(function () {
				if($("#updateInvoices table :input:checkbox:checked").length > 0)
				{
                	$("#approveCheck").removeAttr("disabled");
				    $("#checkNum").removeAttr("disabled");
        			$("#approveCash").removeAttr("disabled");
                    $("#clearMailed").removeAttr("disabled");
				}
				else
				{
        			$("#approveCheck").attr("disabled", "disabled");
					$("#checkNum").attr("disabled", "disabled");
                	$("#approveCash").attr("disabled", "disabled");
                    $("#clearMailed").attr("disabled", "disabled");
				}
            });
            var buttonPressed;            
            $("#approveCash").click(function () { buttonPressed = $(this).attr("name"); });
            $("#approveCheck").click(function () { buttonPressed = $(this).attr("name"); });
            $("#clearMailed").click(function () { buttonPressed = $(this).attr("name"); });
			$("#updateInvoices").submit(function () {
                if (buttonPressed == "clearMailed") {
                    $.post("doInvoiceFunctions.php", { task: "ClearMailedFlag", ids: invoices }, function (result) {
                        $("#updateInvoicesForm :input").removeProp("readonly");
                        if (result.success)
                            location.reload();
                        else {
                            $("#notice").html(result.message);
                            $("#updateInvoicesForm :input").removeProp("readonly");
                        }
                    }, 'json');
                }
				var checkNum = $("#updateInvoices input#checkNum").val();
                if (buttonPressed == "approveCheck" && checkNum == "") {
                    $("#notice").html("No check number was provided.");
                    return false;
                }
                if (buttonPressed == "approveCash")
                    checkNum = "Cash";
        		var invoices = "";
			    $("#updateInvoices :input[name=select]:checkbox:checked").each(function() {
				    invoices += "," + $(this).attr("invoiceID");
			    });
                if (invoices.length == 0) {
                    $("#notice").html("No invoices were selected.");
       				return false;
                }
                else {
                    $("#notice").html("");
			        $("#updateInvoicesForm :input").prop("readonly", true);
                    invoices = invoices.substring(1);
                    $.post("doInvoiceFunctions.php", { task: "MarkInvoicesAsPaid", ids: invoices, checkNumber: checkNum }, function (result) {
                        $("#updateInvoicesForm :input").removeProp("readonly");
                        if (result.success)
                            location.reload();
                        else {
                            $("#notice").html(result.message);
                            $("#updateInvoicesForm :input").removeProp("readonly");
                        }
                    }, 'json');
                }
				return false;
    		});
		});
    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxmedium">
            <h1>Create Invoice</h1>
            <p style="font-size: 0.9em;">
                To create an invoice, fill in the details below.
            </p>
            <label for="recipientName" class="fieldLabelShort"><span class="requiredField">*</span>Recipient Name: </label><br />
            <input type="text" name="recipientName" id="recipientName" placeholder="Required" style="width: 98%;" /><br />
            <label for="recipientEmail" class="fieldLabelShort"><span class="requiredField">*</span>Recipient Email Address: </label><br />
            <input type="text" name="recipientEmail" id="displayName" placeholder="Required" style="width: 98%;" /><br />
            <label for="notes" class="fieldLabelShort">Notes to Include on Invoice: </label><br />
            <textarea id="notes" name="notes" maxlength="500" rows="4" placeholder="This text will be sent verbatim to the recipient." style="width: 98%;"></textarea>
            <br/><br/>
            <label for="line1Name" class="fieldLabelShort">Line 1: </label>
            <input type="text" name="line1Name" id="line1Name" placeholder="Required" style="width: 40%;" />&nbsp;&nbsp;
            <label for="line1Amount" class="fieldLabelShort">Amount: $</label>
            <input type="text" name="line1Amount" id="line1Amount" placeholder="Required" style="width: 10%;" />
            <label for="line1Taxable" class="line1Taxable">Taxable? </label><input type="checkbox" name="line1Taxable" id="line1Taxable" /><br/>

            <label for="line2Name" class="fieldLabelShort">Line 2: </label>
            <input type="text" name="line2Name" id="line2Name" style="width: 40%;" />&nbsp;&nbsp;
            <label for="line2Amount" class="fieldLabelShort">Amount: $</label>
            <input type="text" name="line2Amount" id="line2Amount" style="width: 10%;" />
            <label for="line2Taxable" class="line2Taxable">Taxable? </label><input type="checkbox" name="line2Taxable" id="line2Taxable" /><br/>

            <label for="line3Name" class="fieldLabelShort">Line 3: </label>
            <input type="text" name="line3Name" id="line3Name" style="width: 40%;" />&nbsp;&nbsp;
            <label for="line3Amount" class="fieldLabelShort">Amount: $</label>
            <input type="text" name="line3Amount" id="line3Amount" style="width: 10%;" />
            <label for="line3Taxable" class="line3Taxable">Taxable? </label><input type="checkbox" name="line3Taxable" id="line3Taxable" /><br/>

            <label for="line4Name" class="fieldLabelShort">Line 4: </label>
            <input type="text" name="line4Name" id="line4Name" style="width: 40%;" />&nbsp;&nbsp;
            <label for="line4Amount" class="fieldLabelShort">Amount: $</label>
            <input type="text" name="line4Amount" id="line4Amount" style="width: 10%;" />
            <label for="line4Taxable" class="line4Taxable">Taxable? </label><input type="checkbox" name="line4Taxable" id="line4Taxable" /><br/>

            <label for="line5Name" class="fieldLabelShort">Line 5: </label>
            <input type="text" name="line5Name" id="line5Name" style="width: 40%;" />&nbsp;&nbsp;
            <label for="line5Amount" class="fieldLabelShort">Amount: $</label>
            <input type="text" name="line5Amount" id="line5Amount" style="width: 10%;" />
            <label for="line5Taxable" class="line5Taxable">Taxable? </label><input type="checkbox" name="line5Taxable" id="line5Taxable" /><br/>

            <label for="line6Name" class="fieldLabelShort">Line 6: </label>
            <input type="text" name="line6Name" id="line6Name" style="width: 40%;" />&nbsp;&nbsp;
            <label for="line6Amount" class="fieldLabelShort">Amount: $</label>
            <input type="text" name="line6Amount" id="line6Amount" style="width: 10%;" />
            <label for="line6Taxable" class="line6Taxable">Taxable? </label><input type="checkbox" name="line6Taxable" id="line6Taxable" /><br/>
            <br/>
            <label for="taxRate" class="fieldLabelShort">Tax Rate (when applicable): </label>
            <input type="text" name="taxRate" id="taxRate" style="width: 40px;" value="<?php echo $taxRate; ?>" />%

            <div class="goback">
                <a href="index.php">Return to the Main Menu</a>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>