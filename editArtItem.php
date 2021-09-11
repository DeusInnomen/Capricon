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
        }
        else
            $attendID = 0;
        $id = $_GET["artID"];

		$result = $db->query("SELECT ArtID, IsPrintShop, ShowNumber, Title, Notes, IsOriginal, OriginalMedia, PrintNumber, PrintMaxNumber, " .
			"MinimumBid, QuantitySent, QuickSalePrice, FeesPaid FROM ArtSubmissions WHERE ArtID = $id");

		if($result->num_rows == 0)
		    header('Location: artistSubmissions.php' . ($attendID != 0 ? "?attendID=$attendID" : ""));

        $art = $result->fetch_array();
        $result->close();

        $isPrintShop = ($art["IsPrintShop"] == 1);
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Edit Art Show Entry</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
    <link rel="icon" href="includes/favicon.png" />
    <link rel="shortcut icon" href="includes/favicon.ico" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="includes/global.js"></script>
    <script type="text/javascript">
		function saveArtChanges() {
		    $.post("doArtistFunctions.php", $("#editArtItem").serialize(), function (result) {
		        if (result.success) {
		            var url = 'artistSubmissions.php<?php echo ($attendID != 0 ? "?attendID=$attendID" : ""); ?>';
		            window.location.href = url;
		        }
		        else
		            $("#noticeSave").html(result.message);
		    }, 'json');
		}
        function cancelArtChanges() {
            var url = 'artistSubmissions.php<?php echo ($attendID != 0 ? "?attendID=$attendID" : ""); ?>';
            window.location.href = url;
        }
    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxnarrow">
            <h1>Edit <?php echo ($isPrintShop ? "Print Shop" : "Art Show"); ?> Item</h1>
            <form id="editArtItem" method="post">
                <input type="hidden" name="artID" value="<?php echo $id; ?>" />
            <?php if($isPrintShop) { ?>
                <input type="hidden" name="task" value="EditPrintShopItem" />
                <label for="printItemTitle" class="fieldLabelShort">Title: </label>
                <br />
                <input type="text" name="printItemTitle" id="printItemTitle" placeholder="Required" style="width: 90%;" value="<? echo $art["Title"]; ?>" />
                <br />
                <label for="printItemOriginalMedia" class="fieldLabelShort">Original Media: </label>
                <br />
                <input type="text" name="printItemOriginalMedia" id="printItemOriginalMedia" placeholder="Required" style="width: 90%;" value="<? echo $art["OriginalMedia"]; ?>" />
                <br />
                <label for="printItemQuantity" class="fieldLabelShort">Quantity Sending:</label>
                <input type="number" min="1" value="<? echo $art["QuantitySent"]; ?>" name="printItemQuantity" id="printItemQuantity" style="width: 20%;" />
                <br />
                <label for="printItemSalePrice" class="fieldLabelShort">Sale Price: </label>
                <br />
                $
                <input type="text" name="printItemSalePrice" id="printItemSalePrice" placeholder="Required" style="width: 60%;" value="<? echo $art["QuickSalePrice"]; ?>" />
                <br>
                <label for="printItemNotes" class="fieldLabelShort">Additional Notes:</label>
                </br>
                <textarea id="printItemNotes" name="printItemNotes" placeholder="Optional" maxlength="500" rows="4" style="width: 90%;"><? echo $art["Notes"]; ?></textarea>
                <br />
            <?php } else { ?>
                <input type="hidden" name="task" value="EditArtShowItem" />
                <label for="showItemTitle" class="fieldLabelShort">Title: </label>
                <br />
                <input type="text" name="showItemTitle" id="showItemTitle" placeholder="Required" style="width: 90%;" value="<? echo $art["Title"]; ?>" />
                <br />
                <label for="showItemIsOriginal">
                    <input type="checkbox" name="showItemIsOriginal" id="showItemIsOriginal" <?php echo ($art["IsOriginal"] == 1 ? "checked" : ""); ?> />
                    This is an Original Work (not a print).
                </label>
                <br />
                <label for="showItemOriginalMedia" class="fieldLabelShort">Original Media: </label>
                <br />
                <input type="text" name="showItemOriginalMedia" id="showItemOriginalMedia" placeholder="Required" style="width: 90%;" value="<? echo $art["OriginalMedia"]; ?>" />
                <br />
                <label for="showItemPrintNumber" class="fieldLabelShort">Print #</label>
                <input type="text" name="showItemPrintNumber" id="showItemPrintNumber" style="width: 20%;" value="<? echo $art["PrintNumber"]; ?>" />
                <span style="font-style: italic;">of </span>
                <input type="text" name="showItemMaxPrintNumber" id="showItemMaxPrintNumber" placeholder="Optional" style="width: 20%;" value="<? echo $art["PrintMaxNumber"]; ?>" />
                <br />
                <label for="showItemMinimumBid" class="fieldLabelShort">Minimum Bid: </label>
                <br />
                $
                <input type="text" name="showItemMinimumBid" id="showItemMinimumBid" placeholder="Leave Blank if Not For Sale" style="width: 60%;" value="<? echo $art["MinimumBid"]; ?>" />
                <br>
                <label for="showItemNotes" class="fieldLabelShort">Additional Notes:</label>
                <br />
                <textarea id="showItemNotes" name="showItemNotes" placeholder="Optional" maxlength="500" rows="4" style="width: 90%;"><? echo $art["Notes"]; ?></textarea>
                <br />
            <?php } ?>
                <input type="submit" id="saveChanges" onclick="saveArtChanges(); return false;" value="Save Changes" />
                <input type="submit" id="cancelChanges" onclick="cancelArtChanges(); return false;" value="Cancel Changes" />
            </form>
            <span id="noticeSave" style="font-size: 1.05em; font-weight: bold;">&nbsp;</span>
            <br /><br />
            <div class="goback">
                <a href="index.php">Return to the Main Menu</a>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>
