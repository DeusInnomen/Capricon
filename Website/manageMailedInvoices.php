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
    <title>Capricon Registration System -- Pending Invoice Payments</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css" />
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
					$("#approve").removeAttr("disabled");
					$("#checkNum").removeAttr("disabled");
				}
				else
				{
					$("#approve").attr("disabled", "disabled");
					$("#checkNum").attr("disabled", "disabled");
				}
            });
			$("#updateInvoices").submit(function () {
				var checkNum = $("#updateInvoices input#checkNum").val();
                if (checkNum == "") {
                    $("#notice").html("No check number was provided.");
                    return false;
                }
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
		                else
		                    $("#notice").html(result.message);
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
        <div class="centerboxwide">
            <h1>Pending Invoice Payments</h1>
            <p style="font-size: 0.9em;">
                The following is a list of invoices that are in the Mailed status, due to awaiting a check being
			    mailed in to Phandemonium. These should only be approved once the check has been received and the check number can be verified.
            </p>
            <p style="font-size: 0.9em;">
                To mark invoices as paid, check the box next to the invoice numbers (as they appear on the mailed-in invoice) that are being paid at this time.
                It is possible for someone to pay for multiple invoices at the same time, so be sure all invoice numbers are marked. Then, type in the Check Number
                that was sent and click "Approve With Check #" to mark the invoices as paid and process them.
            </p>
            <div id="updateInvoices" class="standardTable">
                <?php
                if(!empty($invoicesShort))
                {
                    echo "<form id=\"updateInvoicesForm\" method=\"post\">\r\n";
                    echo "<table>\r\n";
                    echo "<tr><th>Select</th><th>Invoice #</th><th>Description</th><th>Total Due</th><th>Invoice Created</th></tr>\r\n";
                    foreach($invoicesShort as $invoice)
                    {
                        $relatedID = $invoice["RelatedRecordID"];
                        if($invoice["InvoiceType"] == "Dealer") {
                            if($dealers[$relatedID]["OnlyUseThisEmail"] == 1 || !empty($dealers[$relatedID]["ContactEmail"]))
                                $email = $dealers[$relatedID]["ContactEmail"];
                            else
                                $email = $dealers[$relatedID]["Email"];
                            $companyName = !empty($dealers[$relatedID]["LegalName"]) ? $dealers[$relatedID]["LegalName"] : $dealers[$relatedID]["CompanyName"];
                            echo "<tr class=\"masterTooltip\" title=\"Legal Name: $companyName<br>Contact Email: $email<br>\">";
                        }
                        else
                            echo "<tr>";
                        echo "<td style=\"text-align: center;\"><input type=\"checkbox\" name=\"select\" invoiceID=\"" . $invoice["InvoiceID"] . "\" /></td>";
                        echo "<td>" . $invoice["InvoiceID"] . "</td>";
                        echo "<td>";
                        if($invoice["InvoiceType"] == "Dealer")
                            echo "Dealer Invoice for " . $dealers[$relatedID]["FirstName"] . " " . $dealers[$relatedID]["LastName"] . " at " . $dealers[$relatedID]["CompanyName"];
                        echo "</td>";
                        echo "<td>" . sprintf("$%01.2f", $invoice["TotalDue"]) . "</td>";
                        echo "<td>" . date("F d, Y", strtotime($invoice["Created"])) . "</td>";
                        echo "</tr>\r\n";
                    }
                    echo "</table><br>\r\n";
                    echo "<input type=\"submit\" id=\"approve\" name=\"approve\" value=\"Approve With Check #\" disabled>\r\n";
                    echo "<input type=\"text\" id=\"checkNum\" name=\"checkNum\" style=\"width: 75px;\" disabled />\r\n";
                    echo "</form>\r\n";
                    echo "<span id=\"notice\" style=\"font-size: 1.05em; font-weight: bold;\">&nbsp;</span>\r\n";
                }
                else
                    echo '<p class="noneFound">There are no pending invoices at this time.</p>' . "\r\n"; ?>
            </div>
            <div class="goback">
                <a href="index.php">Return to the Main Menu</a>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>