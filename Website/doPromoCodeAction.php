<?php
	session_start();
	include_once('includes/functions.php');
	
	DoCleanup();	
	if(isset($_POST["action"]))
	{
		if($_POST["action"] == "Create")
		{
			$code = $db->real_escape_string($_POST["code"]);
			$amount = $db->real_escape_string($_POST["amount"]);
			$expire = $_POST["expire"];
			if(strlen($expire) > 0)
			{
				$expire = str_replace("-", "/", $expire);
				if(($modexpire = strtotime($expire)) === false)
				{
					echo '{ "success": false, "message": "Expiration date must be in MM/DD/YY format." }';
					exit;
				}
				$expiration = "'" . date('Y-m-d', $modexpire) . "'";
			}
			else
				$expiration = "NULL";
			$uses = $db->real_escape_string($_POST["uses"]);
			$thisYear = date("n") >= 3 ? date("Y") + 1: date("Y");

			$result = $db->query("SELECT CodeID FROM PromoCodes WHERE Year = $thisYear AND LOWER(Code) = '" . strtolower($code) . "'");
			if($result->num_rows > 0)
			{
				$result->close();
				echo '{ "success": false, "message": "The provided promotional code name has already been used this year." }';
				exit;
			}
			
			$db->query("INSERT INTO PromoCodes (Year, Code, Discount, Expiration, UsesLeft) VALUES ($thisYear, '$code', $amount, $expiration, $uses)");			
			echo '{ "success": true, "message": "The promotional codes were successfully created." }';
		}
		elseif($_POST["action"] == "DoExpire")
		{
			$codeIDs = explode("|", $db->real_escape_string($_POST["ids"]));
			$expire = $_POST["expire"];
			if(strlen($expire) > 0)
			{
				$expire = str_replace("-", "/", $expire);
				if(($modexpire = strtotime($expire)) === false)
				{
					echo '{ "success": false, "message": "Expiration date must be in MM/DD/YY format." }';
					exit;
				}
				$expiration = "'" . date('Y-m-d', $modexpire) . "'";
			}
			else
				$expiration = "NULL";
				
			foreach($codeIDs as $codeID)
				$db->query("UPDATE PromoCodes SET Expiration = $expiration WHERE CodeID = $codeID");
			
			echo '{ "success": true, "message": "The promotional codes were successfully updated." }';
		}
		elseif($_POST["action"] == "SetUses")
		{
			if(!isset($_POST["uses"]))
			{
				echo '{ "success": false, "message": "An unknown error has occurred." }';
				return;
			}
			
			$codeIDs = explode("|", $db->real_escape_string($_POST["ids"]));
			$uses = $db->real_escape_string($_POST["uses"]);
			
			foreach($codeIDs as $codeID)
				$db->query("UPDATE PromoCodes SET UsesLeft = $uses WHERE CodeID = $codeID");
			
			echo '{ "success": true, "message": "The promotional codes were successfully updated." }';
		}
		elseif($_POST["action"] == "SetValue")
		{
			if(!isset($_POST["value"]))
			{
				echo '{ "success": false, "message": "An unknown error has occurred." }';
				return;
			}
			
			$codeIDs = explode("|", $db->real_escape_string($_POST["ids"]));
			$value = $db->real_escape_string($_POST["value"]);
			
			foreach($codeIDs as $codeID)
				$db->query("UPDATE PromoCodes SET Discount = $value WHERE CodeID = $codeID");
			
			echo '{ "success": true, "message": "The promotional codes were successfully updated." }';
		}
		else
			echo '{ "success": false, "message": "An unknown error has occurred." }';
	}
	else
		echo '{ "success": false, "message": "An unknown error has occurred." }';
?>