<?php
	session_start();
	include_once('includes/functions.php');
	$showPieces = array();
	$printShopPieces = array();

	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("Artist"))
		header('Location: index.php');
	else
	{
		$year = date("n") >= 3 ? date("Y") + 1: date("Y");
		$capriconYear = $year - 1980;
		if(!empty($_GET["attendID"])) {
            if(!DoesUserBelongHere("ArtShowLead"))
                header('Location: index.php');
            $attendID = $_GET["attendID"];
		    $result = $db->query("SELECT ap.ArtistAttendingID, ap.Status, ap.StatusReason, ad.IsEAP, ad.DisplayName, p.IsCharity " .
    			"FROM ArtistPresence ap INNER JOIN ArtistDetails ad ON ad.ArtistID = ap.ArtistID INNER JOIN People p ON ad.peopleID = p.PeopleID WHERE ap.ArtistAttendingID = $attendID");
        }
        else {
            $attendID = 0;
		    $result = $db->query("SELECT ap.ArtistAttendingID, ap.Status, ap.StatusReason, ad.IsEAP, ad.DisplayName, p.IsCharity, ap.FeesWaived " .
    			"FROM ArtistPresence ap INNER JOIN ArtistDetails ad ON ad.ArtistID = ap.ArtistID INNER JOIN People p ON ad.peopleID = p.PeopleID WHERE ap.Year = $year AND ad.PeopleID = " . $_SESSION["PeopleID"]);
        }
			
		if($result->num_rows > 0)
		{
			$request = $result->fetch_array();
			$result->close();
			$id = $request["ArtistAttendingID"];
			$isEAP = ($request["IsEAP"] == 1);
            $isCharity = ($request["IsCharity"] == 1);
            $feesWaived = ($request["FeesWaived"] == 1 || $isEAP || $isCharity);
            if(DoesUserBelongHere("ArtShowLead"))
                $pieceLimit = 9999999;
            else
                $pieceLimit = 50;
			if($attendID != 0)
                $displayName = $request["DisplayName"];

			if($request["Status"] != "Approved")
			{
				$needsApproval = "Your application to show art is presently " . $request["Status"] . ". ";
				if($request["StatusReason"] !== null)
					$needsApproval .= "The reason given is '" . $request["StatusReason"] . "'. ";
				$needsApproval .= "Please contact <a href=\"mailto:artshow@capricon.org\">artshow@capricon.org</a> if you have any questions.";
			}
			else
			{
				$needsApproval = "";
				$feesToPay = false;
				
				$result = $db->query("SELECT ArtID, ShowNumber, Title, Notes, IsOriginal, OriginalMedia, PrintNumber, PrintMaxNumber, " . 
					"MinimumBid, QuickSalePrice, FeesPaid FROM ArtSubmissions WHERE ArtistAttendingID = $id " . 
					"AND IsPrintShop = 0");
				if($result->num_rows > 0)
				{
					while($row = $result->fetch_array())
					{
						$showPieces[] = $row;
						if($row["FeesPaid"] == 0) $feesToPay = true;
					}
					$result->close();
				}
				
				$perms = UserPermissions();
				if($feesWaived || in_array("artistgoh", $perms))
					$feesToPay = false;
				
				$result = $db->query("SELECT ArtID, ShowNumber, Title, Notes, OriginalMedia, QuantitySent, QuickSalePrice " . 
					"FROM ArtSubmissions WHERE ArtistAttendingID = $id AND IsPrintShop = 1");
				if($result->num_rows > 0)
				{
					while($row = $result->fetch_array())
						$printShopPieces[] = $row;
					$result->close();
				}
			}
		}
		else
		{		
			$needsApproval = "You must fill out your <a href=\"artistInformation.php\">Artist Information and Exhibition Request</a> " .
				"before you may enter art to be shown at the next Capricon Art Show. " . 
				"Please contact <a href=\"mailto:artshow@capricon.org\">artshow@capricon.org</a> if you have any questions.";
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Enter Art for Showing</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
	<link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
		<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(".standardTable tr").click(function(e) {
				if(e.target.type !== "radio")
					$(":radio", this).trigger("click");
			});
			$("#artShowForm input[type=radio]").change(function () {
			    $("#artShowEdit").removeProp("disabled");
			    $("#artShowDelete").removeProp("disabled");
			});
			$("#printShopForm input[type=radio]").change(function () {
			    $("#printShopEdit").removeProp("disabled");
			    $("#printShopDelete").removeProp("disabled");
			});
            <?php if(count($showPieces) < $pieceLimit) { ?>
			$("#artShowItemForm").dialog({
				autoOpen: false,
				height: 570,
				width: 500,
				modal: true,
				buttons: {
					"Add": function() {
						$("#showAddNew").prop("disabled", true);
						$("#noticeArtShow").html("&nbsp;");
						$.post("doArtistFunctions.php", $("#newArtShowItem").serialize(), function(result) {
							if(result.success)
								location.reload();
							else
							{
								$("#showAddNew").removeProp("disabled");
								$("#noticeArtShow").html(result.message);
							}
						}, 'json');
						$(this).dialog("close");
					},
					Cancel: function() {
						$(this).dialog("close");
					}
				}
			});
			$("#printShopItemForm").dialog({
				autoOpen: false,
				height: 490,
				width: 500,
				modal: true,
				buttons: {
					"Add": function() {
						$("#shopAddNew").prop("disabled", true);
						$("#noticePrintShop").html("&nbsp;");
						$.post("doArtistFunctions.php", $("#newPrintShopItem").serialize(), function(result) {
							if(result.success)
								location.reload();
							else
							{
								$("#shopAddNew").removeProp("disabled");
								$("#noticePrintShop").html(result.message);
							}
						}, 'json');
						$(this).dialog("close");
					},
					Cancel: function() {
						$(this).dialog("close");
					}
				}
			});
		});

		function addArtShowPiece() {
			$("#artShowItemForm").dialog("open");
		}

		function addPrintShopPiece() {
		    $("#printShopItemForm").dialog("open");
		}

        <?php } ?>

        function editArtShowPiece() {
		    var id = $("#artShowForm input[type=radio]:checked").val();
            var url = 'editArtItem.php?artID=' + id<?php echo ($attendID > 0 ? " + \"&attendID=$attendID\"" : ""); ?>;
		    window.location.href = url;
		}

		function deleteArtShowPiece() {
			var id = $("#artShowForm input[type=radio]:checked").val();
			$.post("doArtistFunctions.php", { task: "DeleteArtShowItem", id: id }, function(result) {
				if(result.success)
					location.reload();
				else
					$("#noticeArtShow").html(result.message);
			}, 'json');
		}

        function editPrintShopPiece() {
            var id = $("#printShopForm input[type=radio]:checked").val();
            var url = '/editArtItem.php?artID=' + id<?php echo ($attendID > 0 ? " + \"&attendID=$attendID\"" : ""); ?>;
		    window.location.href = url;
        }

		function deletePrintShopPiece() {
			var id = $("#printShopForm input[type=radio]:checked").val();
			$.post("doArtistFunctions.php", { task: "DeletePrintShopItem", id: id }, function(result) {
				if(result.success)
					location.reload();
				else
					$("#noticePrintShop").html(result.message);
			}, 'json');
		}

		function payHangingFees() {
			$.post("doArtistFunctions.php", { task: "AddFees" }, function(result) {
				location.reload();
			}, 'json');
		}

		function printInventoryReport() {
			window.location = "artistInventory.php";
		}

	</script>
</head>
<body>	
	<?php include('includes/header.php'); ?>
    <?php if(count($showPieces) < $pieceLimit) { ?>
    <div id="artShowItemForm" title="Art Show Item Details">
        <form id="newArtShowItem" method="post">
			<input type="hidden" name="task" value="AddArtShowItem" />
            <?php if($attendID != 0) echo "<input type=\"hidden\" name=\"attendID\" value=\"$attendID\" />"; ?>
			<label for="showItemTitle" class="fieldLabelShort">Title: </label><br />
			<input type="text" name="showItemTitle" id="showItemTitle" placeholder="Required" style="width: 90%;" /><br />
			<label for="showItemIsOriginal"><input type="checkbox" name="showItemIsOriginal" id="showItemIsOriginal" checked>This is an Original Work (not a print).</label><br />
			<label for="showItemOriginalMedia" class="fieldLabelShort">Original Media: </label><br />
			<input type="text" name="showItemOriginalMedia" id="showItemOriginalMedia" placeholder="Required" style="width: 90%;" /><br />
			<label for="showItemPrintNumber" class="fieldLabelShort">Print #</label>
			<input type="text" name="showItemPrintNumber" id="showItemPrintNumber" style="width: 20%;" /> <span style="font-style: italic;">of </span>
			<input type="text" name="showItemMaxPrintNumber" id="showItemMaxPrintNumber" placeholder="Optional" style="width: 20%;" /><br />
			<label for="showItemMinimumBid" class="fieldLabelShort">Minimum Bid: </label><br />
			$<input type="text" name="showItemMinimumBid" id="showItemMinimumBid" placeholder="Leave Blank if Not For Sale" style="width: 60%;" /><br />
			<label for="showItemNotes" class="fieldLabelShort">Additional Notes:</label></br />
			<textarea id="showItemNotes" name="showItemNotes" placeholder="Optional" maxlength="500" rows="4" style="width: 90%;" ></textarea><br /><br />
		</form>
	</div>
	<div id="printShopItemForm" title="Print Shop Item Details">
		<form id="newPrintShopItem" method="post">
			<input type="hidden" name="task" value="AddPrintShopItem" />
            <?php if($attendID != 0) echo "<input type=\"hidden\" name=\"attendID\" value=\"$attendID\" />"; ?>
            <label for="printItemTitle" class="fieldLabelShort">Title: </label><br />
			<input type="text" name="printItemTitle" id="printItemTitle" placeholder="Required" style="width: 90%;" /><br />
			<label for="printItemOriginalMedia" class="fieldLabelShort">Original Media: </label><br />
			<input type="text" name="printItemOriginalMedia" id="printItemOriginalMedia" placeholder="Required" style="width: 90%;" /><br />
			<label for="printItemQuantity" class="fieldLabelShort">Quantity Sending:</label>
			<input type="number" min="1" value="1" name="printItemQuantity" id="printItemQuantity" style="width: 20%;" /><br />
			<label for="printItemSalePrice" class="fieldLabelShort">Sale Price: </label><br />
			$<input type="text" name="printItemSalePrice" id="printItemSalePrice" placeholder="Required" style="width: 60%;" /><br />
			<label for="printItemNotes" class="fieldLabelShort">Additional Notes:</label></br />
			<textarea id="printItemNotes" name="printItemNotes" placeholder="Optional" maxlength="500" rows="4" style="width: 90%;" ></textarea><br /><br />
		</form>
	</div>
    <?php } ?>
	<div class="content">
		<div class="centerboxwide">
			<h1>Art Being Shown at Capricon <?php echo $capriconYear; ?></h1>
            <?php if($attendID != 0)
                      echo "<h2>Managing Inventory for \"$displayName\"" . ($isCharity ? " [Charity]" : "") . "</h2>\r\n";
            ?>
        <?php if($needsApproval != "") { ?>
			<p style="font-size: 1.2em; margin-top: 100px;"><?php echo $needsApproval; ?></p>
		<?php } else { ?>
			<div class="headertitle">Art Show Pieces</div>
			<div class="standardTable">
				<form id="artShowForm" method="post">
				<table>
					<tr><th>Select</th><th>Title</th><th>Original Media</th><th>Print #</th><th>Minimum Bid</th><th>Fees Paid?</th></tr>
<?php
					foreach($showPieces as $piece)
					{
						echo "<tr class=\"masterTooltip\" title=\"Notes: " . htmlspecialchars($piece["Notes"]) . "<br>Original Piece? " . 
							($piece["IsOriginal"] == 1 ? "Yes" : "No") . "<br>Show Piece Number: " . $piece["ShowNumber"] . "\">";
						echo "<td style=\"text-align: center;\"><input type=\"radio\" id=\"artShowID\" name=\"artShowID\" value=\"" . $piece["ArtID"] .
							"\" /></td><td>" . $piece["Title"] . "</td><td>" . $piece["OriginalMedia"] . "</td><td>" .
							$piece["PrintNumber"];
						if($piece["PrintMaxNumber"] !== null)
							echo " of " . $piece["PrintMaxNumber"];
						echo "</td><td>";
						if($piece["MinimumBid"] !== null)
							echo sprintf("$%01.2f", $piece["MinimumBid"]);
						else
							echo "Not For Sale";
						echo "</td><td>" . ($isEAP ? "N/A" : ($piece["FeesPaid"] == 0 ? "No" : "Yes")) . "</td></tr>\r\n";
					} ?>
				</table>
				With Selected: <input type="submit" id="artShowEdit" onclick="editArtShowPiece(); return false;" value="Edit" disabled />&nbsp;
                <input type="submit" id="artShowDelete" onclick="deleteArtShowPiece(); return false;" value="Delete" disabled><br />
                <input type="submit" <?php if(count($showPieces) < $pieceLimit) { ?> onclick="addArtShowPiece(); return false;" 
                       <?php } else { ?> class="masterTooltip" title="You have met or exceeded the show piece limit of <?php echo $pieceLimit; ?> pieces. Please contact artshow@capricon.org." readonly <?php } ?>
                       value="Add New Art Show Piece"><br/>
				<span id="noticeArtShow" style="font-size: 1.05em; font-weight: bold;">&nbsp;</span>
				</form>
			</div>
			<?php if($feesToPay) { ?>
			<p>To pay any unpaid hanging fees, press the following button. Fees are based on the minimum bids: <b>$0.50</b> each for pieces
			with bids less than $100, <b>$1.00</b> each for pieces with bids of at least $100.<br />
			<input type="submit" id="payFees" onclick="payHangingFees(); return false;" value="Add Hanging Fees to Shopping Cart" <?php echo ($feesToPay ? "" : "disabled"); ?>></p>
			<?php } else { ?>
			<p><span style="font-weight: bold;">NOTE</span>: You are not required to pay hanging fees.</p>
			<?php } ?>
			<div class="headertitle">Print Shop Pieces</div>
			<div class="standardTable">
				<form id="printShopForm" method="post">
				<table>
					<tr><th>Select</th><th>Title</th><th>Original Media</th><th>Quantity Sent</th><th>Price</th></tr>
<?php
					foreach($printShopPieces as $piece)
					{
						echo "<tr class=\"masterTooltip\" title=\"Notes: " . htmlspecialchars($piece["Notes"]) . "<br>Show Piece Number: " . 
							$piece["ShowNumber"] . "\">";
						echo "<td style=\"text-align: center;\"><input type=\"radio\"  id=\"printShopID\" name=\"printShopID\" value=\"" . $piece["ArtID"] .
							"\" /></td><td>" . $piece["Title"] . "</td><td>" . $piece["OriginalMedia"] . "</td><td>" .
							$piece["QuantitySent"] . "</td><td>" . sprintf("$%01.2f", $piece["QuickSalePrice"]) . "</td></tr>\r\n";
					} ?>
				</table>
				With Selected: <input type="submit" id="printShopEdit" onclick="editPrintShopPiece(); return false;" value="Edit" disabled>&nbsp;
                <input type="submit" id="printShopDelete" onclick="deletePrintShopPiece(); return false;" value="Delete" disabled><br />
				<input type="submit" id="shopAddNew" onclick="addPrintShopPiece(); return false;" value="Add New Print Shop Piece"><br />
				<span id="noticePrintShop" style="font-size: 1.05em; font-weight: bold;">&nbsp;</span>
				</form>
			</div>
			<p style="font-style:italic;">Hover over any piece above to see additional information.</p>
			<input type="submit" id="printReport" onclick="printInventoryReport(); return false;" value="Produce Printable Inventory Report" style="margin-bottom: 30px;"><br />
		<?php } ?>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>