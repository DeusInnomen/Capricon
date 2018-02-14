<?php
	include_once('includes/functions.php');

	$result = $db->query("SELECT ab.Price, ab.AvailableTo FROM AvailableBadges ab INNER JOIN BadgeTypes bt ON bt.BadgeTypeID = " .
		"ab.BadgeTypeID WHERE ab.AvailableFrom <= CURDATE() AND ab.AvailableTo >= CURDATE() AND ab.BadgeTypeID = 1");
	if($result->num_rows == 0)
	{
		if(empty($_GET["year"]) || empty($_GET["price"]) || empty($_GET["until"]))
		{
			header("Location: noBadgesAvailable.php");
			exit();
		}
		$price = sprintf("$%01.2f", $_GET["price"]);
		$until = date("F jS, Y", strtotime($_GET["until"]));
		$thisYear = $_GET["year"];
	}
	else
	{
		$row = $result->fetch_array();
		$result->close();
		$price = sprintf("$%01.2f", $row["Price"]);
		$until = date("F jS, Y", strtotime($row["AvailableTo"]));
		$thisYear = date("n") >= 3 ? date("Y") + 1: date("Y");
	}
	$capriconYear = $thisYear - 1980;

	$pdf = new FPDF('P', 'mm', 'Letter');
	$pdf->AddPage();
	$pdf->SetFont('Arial', '', 32);
    $pdf->Image('includes/capricious_clear.png', 10, 5);
    $pdf->Image('includes/capricious_clear.png', 165, 5);
	$pdf->Cell(0, 8, 'Capricon ' . $capriconYear, 0, 1, 'C');
	$pdf->SetFont('Arial', '', 24);
	$pdf->Cell(0, 12, 'Registration Form', 0, 1, 'C');
	$pdf->SetFont('Arial', '', 14);
	$pdf->Cell(0, 5, 'https://registration.capricon.org', 0, 1, 'C');
	$pdf->Ln(5);
	$pdf->SetFont('Arial', '', 12);
	$pdf->Cell(0, 6, 'To register for Capricon ' . $capriconYear . ', please fill in the following information:', 0, 1);
	$pdf->Ln(3);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(0, 10, 'Name: _________________________________  Badge Name: _______________________________________________', 0, 1);
	$pdf->Cell(0, 10, 'Name: _________________________________  Badge Name: ____________________________________ [ ] Kid-in-Tow', 0, 1);
	$pdf->Cell(0, 10, 'Name: _________________________________  Badge Name: ____________________________________ [ ] Kid-in-Tow', 0, 1);
	$pdf->Cell(0, 10, 'Name: _________________________________  Badge Name: ____________________________________ [ ] Kid-in-Tow', 0, 1);
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->Cell(0, 10, 'Full weekend badges cost ' . $price . ' each, and this price is valid through ' . $until . '.', 0, 1);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(0, 6, 'Kid-in-Tow badges are for children aged 12 and under, are free of charge, and must have a parent with them at all times.', 0, 1);
	$pdf->Ln(3);
	$pdf->Cell(0, 10, 'Address: ______________________________________________________________________________________', 0, 1);
	$pdf->Cell(0, 10, 'City: ______________________________________________  State: _______________  Zip: __________________', 0, 1);
	$pdf->Cell(0, 10, 'Phone # and Type (Home, Mobile, etc.): ____________________________________________________________', 0, 1);
	$pdf->Cell(0, 10, 'Email: ________________________________________________________________________________________', 0, 1);
	$pdf->SetFont('Arial', '', 12);
	$pdf->Cell(0, 6, 'Your email address will be used to create an account on Capricon\'s Registration website if you do not', 0, 1);
	$pdf->Cell(0, 6, 'already have one. You will receive an email when your registration has been entered into the system.', 0, 1);
	$pdf->Cell(0, 6, 'You can choose to opt in to emails via your account, but we will only automatically send you emails', 0, 1);
	$pdf->Cell(0, 6, 'regarding registrations. If you have any questions, send an email to it@phandemonium.org.', 0, 1);
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->Cell(0, 9, 'If for any reason you do not want us to create an account for you, please mark this box: [ ]', 0, 1);
	$pdf->SetFont('Arial', '', 12);
	$pdf->Cell(0, 10, 'How Did You Hear About Us? _________________________________________________________', 0, 1);
	$pdf->Cell(0, 10, 'If you have a promo code or gift certificate, write the code here: _______________________________', 0, 1);
	$pdf->Cell(0, 6, 'If sending a Check or Money Order, make them to "CAPRICON". DO NOT MAIL CASH!', 0, 1);
	$pdf->Cell(0, 6, 'Paying with a Credit Card? Fill out the following information:', 0, 1);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(0, 8, 'Card Type (Check One):   ___ Visa   ___ Mastercard   ___ American Express   ___ Discover', 0, 1);
	$pdf->Cell(0, 10, 'Card # ___________________________________ Expiration (MM/YYYY): ____________ CVV/Security Code: ________', 0, 1);
	$pdf->Ln(3);
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->Cell(0, 6, 'Please mail this form with your payment to:', 0, 1, 'C');
	$pdf->SetFont('Arial', '', 12);
	$pdf->Cell(0, 6, 'Capricon ' . $capriconYear, 0, 1, 'C');
	$pdf->Cell(0, 6, '126 E. Wing Street, #244', 0, 1, 'C');
	$pdf->Cell(0, 6, 'Arlington Heights, IL 60004', 0, 1, 'C');
	$pdf->Output();
?>