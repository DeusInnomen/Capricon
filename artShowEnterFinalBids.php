<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("ArtShowStaff"))
    header('Location: index.php');
$message = "";
$pieceNum = isset($_POST["pieceNum"]) ? $_POST["pieceNum"] : "";
$badgeNum = isset($_POST["badgeNum"]) ? $_POST["badgeNum"] : "";
$amount = isset($_POST["amount"]) ? $_POST["amount"] : "";
$auction = isset($_POST["auction"]) ? "1" : "0";
if(!empty($pieceNum)) {
    $style = "goodMessage";
    $result = PostToURL("https://" . $_SERVER['HTTP_HOST'] . "/functions/artQuery.php",
        array('action' => 'EnterFinalBid', 'pieceNum' => $pieceNum, 'badgeNum' => $badgeNum, 'amount' => $amount, 'auction' => $auction));
    $values = json_decode($result, true);
    $message = $values["message"];
    if($values["success"]) {
        $pieceNum = "";
        $badgeNum = "";
        $amount = "";
        $auction = 0;
    }
    else
        $style = "errorMessage";
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Enter Final Bids</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
    <link rel="icon" href="includes/favicon.png" />
    <link rel="shortcut icon" href="includes/favicon.ico" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="includes/global.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#pieceNum").focus();
        }
    </script>
    <style>
        ::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
            color: #ded3d3;
            opacity: 1; /* Firefox */
        }

        :-ms-input-placeholder { /* Internet Explorer 10-11 */
            color: #ded3d3;
        }

        ::-ms-input-placeholder { /* Microsoft Edge */
            color: #ded3d3;
        }

        input[type=checkbox] {
            -ms-transform: scale(6); /* IE */
            -moz-transform: scale(6); /* FF */
            -webkit-transform: scale(6); /* Safari and Chrome */
            -o-transform: scale(6); /* Opera */
            margin-left: 40px;
            display: inline-block;
            margin-bottom: 0px;
            vertical-align: top;
            margin-top: 36px;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxwide">
            <h1 style="font-size: 84px;">Enter Final Bids</h1>
            <p class="<?php echo $style; ?>" style="font-size: 48px;"><?php echo $message; ?></p>
            <form id="bidForm" method="POST" action="artShowEnterFinalBids.php">
                <label for="displayName" class="fieldLabelShort" style="font-size: 72px;">Went To Auction:</label><input type="checkbox" name="auction" id="auction" value="auction" style="font-size: 72px;" <?php if($auction == 1) echo "checked "; ?>/><br />
                <label for="displayName" class="fieldLabelShort" style="font-size: 72px; font-size: 72px;">Piece #</label><br />
                <input type="number" name="pieceNum" id="pieceNum" style="width: 98%; font-size: 72px;" value="<?php echo $pieceNum; ?>" placeholder="Piece Number" /><br />
                <label for="displayName" class="fieldLabelShort" style="font-size: 72px;">Badge #</label><br />
                <input type="number" name="badgeNum" id="badgeNum" style="width: 98%; font-size: 72px;" value="<?php echo $badgeNum; ?>" placeholder="Badge Number" /><br />
                <label for="displayName" class="fieldLabelShort" style="font-size: 72px;">Final Bid</label><br />
                <span style="font-size: 72px;">$<input type="number" name="amount" id="amount" style="width: 95%; font-size: 72px;" value="<?php echo $amount; ?>" placeholder="50.00" /></span><br />
                <br /><br />
                <button style="font-size: 104px;">Submit Bids</button>
            </form>
            <br />
            <br />
            <div class="clearfix" style="margin-top: 40px;"></div>
            <div class="goback" style="margin-bottom: 30px;">
                <a href="index.php" style="font-size: 48px;">Return to the Main Menu</a>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>