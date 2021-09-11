<?php
	session_start();
	include_once('includes/functions.php');
	include_once('includes/paypal.php');
	
	$orders = getShoppingCart();
	$total = 0;
	$num = -1;
	
	$padata =	'&METHOD=SetExpressCheckout' .
		  	'&RETURNURL=' . urlencode("https://registration.capricon.org/processCart.php") .
			'&CANCELURL=' . urlencode("https://registration.capricon.org/shoppingCart.php") .
			'&PAYMENTREQUEST_0_PAYMENTACTION=SALE';
	
	foreach($orders as $order)
	{
		$num++;
		$price = $order["Price"];
		if(isset($order["PromoCode"])) 
		{
			$price -= $order["Discount"];
			if($price < 0) $price = 0;
		}
		if(isset($order["CertificateCode"])) 
		{
			if($order["Badges"] > 0)
				$price = 0;
			else
				$price -= $order["CurrentValue"];
			if($price < 0) $price = 0;
		}
			
		$padata .=	'&L_PAYMENTREQUEST_0_NAME' . $num . '=' . urlencode($order["Item"]).
				'&L_PAYMENTREQUEST_0_DESC' . $num . '=' . urlencode($order["Details"]).
				'&L_PAYMENTREQUEST_0_AMT' . $num . '=' . urlencode($price).
				'&L_PAYMENTREQUEST_0_QTY' . $num . '=1';
		$total += $price;		
	}
	
	$padata .=	'&NOSHIPPING=1' . 			
			'&PAYMENTREQUEST_0_ITEMAMT=' . urlencode(sprintf("%01.2f", $total) ).
			'&PAYMENTREQUEST_0_AMT=' . urlencode(sprintf("%01.2f", $total)) .
			'&PAYMENTREQUEST_0_CURRENCYCODE=USD' .
			'&LOCALECODE=US' . 
			'&LOGOIMG=' . urlencode("https://registration.capricon.org/includes/capricious.png") .
			'&CARTBORDERCOLOR=0000FF' . 
			'&ALLOWNOTE=1';
			
	$paypal = new MyPayPal();
	$response = $paypal->PPHttpPost('SetExpressCheckout', $padata);
	if("SUCCESS" == strtoupper($response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($response["ACK"]))
	{
		//Redirect user to PayPal store with Token received.
		$paypalmode = ($paypalModeSetting == 'sandbox') ? '.sandbox' : '';
	 	$paypalurl ='https://www' . $paypalmode . '.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=' . $response["TOKEN"];
		header('Location: '. $paypalurl);
		 
	}else{
		$_SESSION["PayPalError"] = urldecode($response["L_ERRORCODE0"]) . ": " . urldecode($response["L_LONGMESSAGE0"]);
		header('Location: shoppingCart.php');
	}
				
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Shopping Cart</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>">
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxnarrow">
			<br><br><br><br>
			<p style="text-align: center;">You are being transferred to PayPal to finalize your purchase. Please wait...</p>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>