<?php
	session_start();
	include_once('includes/functions.php');
	
	DoCleanup();
	$return = isset($_GET["return"]) ? urldecode($_GET["return"]) : (isset($_POST["return"]) ? urldecode($_POST["return"]) : "index.php");
	
	if(isset($_POST["action"]))
	{
		if($_POST["action"] == "Print")
		{
			$certIDs = explode("|", $db->real_escape_string($_POST["values"]));		
			$pdf = produceGiftCertificatePDF($certIDs);
			$pdf->Output();
		}
		elseif($_POST["action"] == "Assign")
		{
			if(!isset($_POST["values"]))
			{
				echo '{ "success": false, "message": "An unknown error has occurred." }';
				return;
			}
			$certIDs = explode("|", $db->real_escape_string($_POST["values"]));
			$email = isset($_POST["target"]) ? $db->real_escape_string($_POST["target"]) : "";
			$recipient = strlen($email) > 0 ? "'$email'" : "NULL";
			
			foreach($certIDs as $certID)
				$db->query("UPDATE GiftCertificates SET Recipient = $recipient WHERE CertificateID = $certID");
			
			echo '{ "success": true, "message": "The certificates were successfully updated." }';
		}
		elseif($_POST["action"] == "GenerateCertificate")
		{
			if(!DoesUserBelongHere("SuperAdmin"))
			{
				echo '{ "success": false, "message": "Access denied." }';
				return;
			}
			
			$amount = $db->real_escape_string($_POST["amount"]);
			$badges = $db->real_escape_string($_POST["badges"]);
			$recipient = !empty($_POST["recipient"]) ? "'" . $db->real_escape_string($_POST["recipient"]) . "'" : "NULL";			
			
			$certCode = strtoupper(bin2hex(openssl_random_pseudo_bytes(7)));
			$db->query("INSERT INTO GiftCertificates (CertificateCode, PurchaserID, Recipient, Purchased, OriginalValue, CurrentValue, Badges) " .
				"VALUES ('$certCode', NULL, $recipient, CURDATE(), $amount, $amount, $badges)");
			echo '{ "success": true, "message": "Certificate ' . $certCode . ' was successfully created." }';
		}
	}	
	else
		echo '{ "success": false, "message": "An unknown error has occurred." }';
?>