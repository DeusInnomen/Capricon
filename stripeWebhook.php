<?php
	include_once('includes/functions.php');
	require_once("Stripe/Stripe.php");

	Stripe::setApiKey($stripeKey);

	// Retrieve the request's body and parse it as JSON
	$body = @file_get_contents('php://input');
	$event_json = json_decode($body);
	
	$event_id = $event_json->id;
	$event = Stripe_Event::retrieve($event_id);
	$type = $event->type;
	
	if($type == "charge.dispute.created")
	{
		
	}
	elseif($type == "charge.dispute.updated")
	{
		
	}
	elseif($type == "charge.dispute.closed")
	{
		
	}
	elseif($type == "transfer.created")
	{
		
	}
	elseif($type == "transfer.updated")
	{
		
	}
	elseif($type == "transfer.paid")
	{
		
	}
	elseif($type == "transfer.failed")
	{
		
	}

	// Other examples at https://gist.github.com/boucher/1708172
	// Reference: https://stripe.com/docs/webhooks
	// Events: https://stripe.com/docs/api#event_types

?>