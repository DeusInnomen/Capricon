<?php
	session_start();
	include_once('includes/functions.php');

	DoCleanup();

	if(!DoesUserBelongHere("Artist"))
		header('Location: index.php');

	$year = date("n") >= 3 ? date("Y") + 1: date("Y");
	$capriconYear = $year - 1980;

	$result = $db->query("SELECT ap.ArtistAttendingID FROM ArtistPresence ap INNER JOIN ArtistDetails ad ON ad.ArtistID = ap.ArtistID " .
		"WHERE ap.Year = $year AND ad.PeopleID = " . $_SESSION["PeopleID"]);
	if($result->num_rows > 0)
	{
		$row = $result->fetch_array();
		$id = $row["ArtistAttendingID"];
		$result->close();
	}
	if(isset($_GET["ID"])) $id = $_GET["ID"];

	$showPieces = array();
	$printShopPieces = array();

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

	$result = $db->query("SELECT ArtID, ShowNumber, Title, Notes, OriginalMedia, QuantitySent, QuickSalePrice " .
		"FROM ArtSubmissions WHERE ArtistAttendingID = $id AND IsPrintShop = 1");
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_array())
			$printShopPieces[] = $row;
		$result->close();
	}