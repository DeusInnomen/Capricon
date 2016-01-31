<?php
	// Set Timezone
	date_default_timezone_set('America/Chicago');
	
	// Database Object
	$db = new mysqli("server", "user", "password", "database");
	$db->query("set character_set_client='utf8'"); 
	$db->query("set character_set_results='utf8'"); 
	$db->query("set collation_connection='utf8_general_ci'"); 
	
	// PayPal API Settings
	$paypalModeSetting = "live";
	$paypalUser = ($paypalModeSetting == "sandbox") ? "FILL IN" : "FILL IN";
	$paypalPassword = ($paypalModeSetting == "sandbox") ? "FILL IN" : "FILL IN";
	$paypalSignature = ($paypalModeSetting == "sandbox") ? "FILL IN" : "FILL IN";
	
	// Stripe Settings
	$stripeKey = "FILL IN";
  $stripePublicKey = "FILL IN";
	
	// Email Settings
	$smtpPass = "FILL IN";
?>
