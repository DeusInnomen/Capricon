<?php
	session_start();
	include_once('includes/functions.php');

	DoCleanup();

	if(!DoesUserBelongHere("Artist"))
		header('Location: main.php');

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

	$pdf = new FPDF('P', 'mm', 'Letter');
	$pdf->AddPage();
	$pdf->SetFont('Arial', '', 32);
	$pdf->Cell(0, 8, 'Capricon ' . $capriconYear, 0, 1, 'C');
	$pdf->SetFont('Arial', '', 24);
	$pdf->Cell(0, 12, 'Art Show Inventory', 0, 1, 'C');
	$pdf->Ln(5);
	$pdf->SetFont('Arial', '', 12);
	$pdf->Cell(0, 6, 'The following is a summary of the artwork you are showing this year. If you have any questions, please', 0, 1);
	$pdf->Cell(0, 6, 'contact artshow@capricon.org for help!', 0, 1);
	$pdf->Ln(5);
	$pdf->SetFont('Arial', '', 20);
	$pdf->Cell(0, 12, 'Art Show Pieces', 0, 1, 'C');

	if(!empty($showPieces))
	{
		$pdf->SetFillColor(152, 191, 33);
		$pdf->SetTextColor(6, 27, 97);
		$pdf->SetLineWidth(.3);
		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(100, 7, 'Title', 1, 0, 'C', true);
		$pdf->Cell(45, 7, 'Original Media', 1, 0, 'C', true);
		$pdf->Cell(25, 7, 'Print', 1, 0, 'C', true);
		$pdf->Cell(25, 7, 'Bid', 1, 0, 'C', true);
		$pdf->Ln();
		$pdf->SetFillColor(244, 235, 255);
		$pdf->SetTextColor(0);
		$pdf->SetFont('Courier', '', 12);
		$fill = false;
		foreach($showPieces as $piece)
		{
			$title = $piece["Title"];
			$media = $piece["OriginalMedia"];
			$tHeight = ceil(strlen($title) / 38) * 6;
            if($tHeight == 0) $tHeight = 6;
			$mHeight = ceil(strlen($media) / 16) * 6;
            if($mHeight == 0) $mHeight = 6;
			$height = $tHeight > $mHeight ? $tHeight : $mHeight;

			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->MultiCell(100, $height / $tHeight * 6, $title, "LR", 'C', $fill);
			$x += 100;
			$pdf->SetXY($x, $y);
			$pdf->MultiCell(45, $height / $mHeight * 6, $media, "LR", 'C', $fill);
			$x += 45;
			$pdf->SetXY($x, $y);
			$num = $piece["PrintNumber"];
			if($piece["PrintMaxNumber"] !== null) $num .= " of " . $piece["PrintMaxNumber"];
			$pdf->Cell(25, $height, $num, "LR", 0, 'C', $fill);
			$pdf->Cell(25, $height, sprintf("$%01.2f", $piece["MinimumBid"]), "LR", 0, 'C', $fill);
			$pdf->Ln();
			if(!empty($piece["Notes"]))
			{
				$pdf->SetFont('Arial', '', 10);
				$pdf->MultiCell(195, 6, "Notes: " . $piece["Notes"], "LR", 'L', $fill);
				$pdf->SetFont('Courier', '', 12);
			}
			$fill = !$fill;
		}
		$pdf->Cell(195, 0, "", "T");
	}
	else
	{
		$pdf->Ln(5);
		$pdf->SetFont('Arial', '', 16);
		$pdf->Cell(0, 12, 'You have no pieces listed in this year\'s Capricon Art Show.', 1, 1, 'C');
	}

	$pdf->SetY(-22);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(0, 0, 'Page 1 of 2', 0, 0, 'L');
	$pdf->Cell(0, 0, 'Current as of ' . date("F j, Y, g:i a"), 0, 0, 'R');

	$pdf->AddPage();
	$pdf->SetFont('Arial', '', 32);
	$pdf->Cell(0, 8, 'Capricon ' . $capriconYear, 0, 1, 'C');
	$pdf->SetFont('Arial', '', 24);
	$pdf->Cell(0, 12, 'Art Show Inventory', 0, 1, 'C');
	$pdf->Ln(5);
	$pdf->SetFont('Arial', '', 12);
	$pdf->Cell(0, 6, 'The following is a summary of the artwork you are showing this year. If you have any questions, please', 0, 1);
	$pdf->Cell(0, 6, 'contact artshow@capricon.org for help!', 0, 1);
	$pdf->Ln(5);
	$pdf->SetFont('Arial', '', 20);
	$pdf->Cell(0, 12, 'Print Shop Pieces', 0, 1, 'C');

	if(!empty($printShopPieces))
	{
		$pdf->SetFillColor(152, 191, 33);
		$pdf->SetTextColor(6, 27, 97);
		$pdf->SetLineWidth(.3);
		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(100, 7, 'Title', 1, 0, 'C', true);
		$pdf->Cell(45, 7, 'Original Media', 1, 0, 'C', true);
		$pdf->Cell(25, 7, '# Sent', 1, 0, 'C', true);
		$pdf->Cell(25, 7, 'Price', 1, 0, 'C', true);
		$pdf->Ln();
		$pdf->SetFillColor(244, 235, 255);
		$pdf->SetTextColor(0);
		$pdf->SetFont('Courier', '', 12);
		$fill = false;
		foreach($printShopPieces as $piece)
		{
			$title = $piece["Title"];
			$media = $piece["OriginalMedia"];
			$tHeight = ceil(strlen($title) / 38) * 6;
            if($tHeight == 0) $tHeight = 6;
			$mHeight = ceil(strlen($media) / 16) * 6;
            if($mHeight == 0) $mHeight = 6;
			$height = $tHeight > $mHeight ? $tHeight : $mHeight;

			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->MultiCell(100, $height / $tHeight * 6, $title, "LR", 'C', $fill);
			$x += 100;
			$pdf->SetXY($x, $y);
			$pdf->MultiCell(45, $height / $mHeight * 6, $media, "LR", 'C', $fill);
			$x += 45;
			$pdf->SetXY($x, $y);
			$pdf->Cell(25, $height, $piece["QuantitySent"], "LR", 0, 'C', $fill);
			$pdf->Cell(25, $height, sprintf("$%01.2f", $piece["QuickSalePrice"]), "LR", 0, 'C', $fill);
			$pdf->Ln();
			if(!empty($piece["Notes"]))
			{
				$pdf->SetFont('Arial', '', 10);
				$pdf->MultiCell(195, 6, "Notes: " . $piece["Notes"], "LR", 'L', $fill);
				$pdf->SetFont('Courier', '', 12);
			}
			$fill = !$fill;
		}
		$pdf->Cell(195, 0, "", "T");
	}
	else
	{
		$pdf->Ln(5);
		$pdf->SetFont('Arial', '', 16);
		$pdf->Cell(0, 12, 'You have no pieces listed in this year\'s Capricon Print Shop.', 1, 1, 'C');
	}

	$pdf->SetY(-22);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(0, 0, 'Page 2 of 2', 0, 0, 'L');
	$pdf->Cell(0, 0, 'Current as of ' . date("F j, Y, g:i a"), 0, 0, 'R');
	$pdf->Output();
?>