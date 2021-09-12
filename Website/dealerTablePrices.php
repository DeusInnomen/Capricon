<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("DealerLead"))
    header('Location: index.php');
else
{
    $result = $db->query("SELECT Quantity, Price FROM DealerTablePrices ORDER BY Quantity ASC");
    while($row = $result->fetch_array())
        $prices[] = $row;
    $result->close();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Dealer Table Pricing</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
    <link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
    <link rel="icon" href="includes/favicon.png" />
    <link rel="shortcut icon" href="includes/favicon.ico" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
    <script type="text/javascript" src="includes/global.js"></script>
    <script type="text/javascript">
		$(document).ready(function() {
			$("#priceList tr").click(function(e) {
				if(e.target.type !== "radio")
					$(":radio", this).trigger("click");
			});
			$("#priceList :radio").click(function () {
				$("#setPrice").removeProp("disabled");
				$("#newPrice").removeProp("disabled");
			});			
        });

        function setPriceForTier()
		{
			var price = $("#newPrice").val();
			if($.trim(price).length == 0)
			{
				$("#saveMessage").html("<span class=\"requiredField\">You must set the price for the selected quantity.</span>");
				return false;
			}

            var quantity = $("#priceList input[name=quantity]:checked").val();
            $("#saveMessage").html("");
			$.post("doDealerFunctions.php", { task: "SetTablePrice", quantity: quantity, price: price }, function(result) {
				if(result.success)
					location.reload();
                else {
   					$("#saveMessage").addClass("errorMessage");
   					$("#saveMessage").html(result.message);
                }
			}, 'json');
        }

        function addRow()
		{
			$.post("doDealerFunctions.php", { task: "AddNextPriceTier" }, function(result) {
				if(result.success)
					location.reload();
                else {
   					$("#saveMessage").addClass("errorMessage");
   					$("#saveMessage").html(result.message);
                }
			}, 'json');
        }

        function delRow()
		{
			$.post("doDealerFunctions.php", { task: "RemoveLastPriceTier" }, function(result) {
				if(result.success)
					location.reload();
                else {
   					$("#saveMessage").addClass("errorMessage");
   					$("#saveMessage").html(result.message);
                }
			}, 'json');
		}
    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxmedium">
            <div style="margin-bottom: 30px;">
                <h1>Dealer Table Pricing</h1>
                <p>
                    Use this screen to set the prices for the requested quantities of tables. Changes do not affect dealers who have already paid or have been invoiced.
                </p>
			    <div id="priceList" class="standardTable">
				    <form id="priceActionForm" method="post">
				    <table>
				    <tr><th>Select</th><th>Quantity</th><th>Price</th></tr>
                        <?php
				    foreach($prices as $data)
				    {
                        $quantity = $data["Quantity"];
					    $qtyPrice = sprintf("$%01.2f", $data["Price"]);
					    echo "<tr><td style=\"text-align: center;\"><input type=\"radio\" id=\"quantity\" name=\"quantity\" value=\"$quantity\" class=\"codeNumber\" /></td><td>$quantity</td><td>$qtyPrice</td></tr>\r\n";
				    } ?>				
				    </table>
				    <p>With Selected:<br />
					    <input type="submit" id="setPrice" name="setPrice" onclick="setPriceForTier(); return false;" value="Set Price:" disabled>
					    <input type="text" id="newPrice" name="newPrice" style="width: 75px;" disabled /><br />
				    </p>
				    </form>
                    <input type="submit" onclick="addRow(); return false;" value="Add Next Quantity Level" />&nbsp;&nbsp;
                    <input type="submit" onclick="delRow(); return false;" value="Remove Highest Quantity Level" />
                    <br />
                    <br />
                    <span id="saveMessage">&nbsp;</span>
                    <br />
                </div>
            </div>
            <div class="goback">
                <a href="index.php">Return to the Main Menu</a>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>