<?php
	session_start();
	include_once('includes/functions.php');
	if(!isset($_SESSION["PeopleID"]))
		header('Location: /login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	elseif(!DoesUserBelongHere("Treasurer"))
		header('Location: /index.php');
	
	$thisYear = date("n") >= 3 ? date("Y") + 1: date("Y");
	$codes = array();
	$result = $db->query("SELECT CodeID, Code, Discount, Expiration, UsesLeft, COUNT(pb.PromoCodeID) AS Uses FROM PromoCodes pc LEFT OUTER JOIN PurchasedBadges pb ON pc.CodeID = pb.PromoCodeID WHERE pc.Year = $thisYear AND (Expiration IS NULL OR DATE_ADD(Expiration, INTERVAL 3 MONTH) > NOW()) GROUP BY CodeID, Code, Discount, Expiration, UsesLeft ORDER BY Expiration DESC, Code ASC");
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_array())
			$codes[] = $row;
		$result->close();				
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Promotional Codes</title>
	<link rel="stylesheet" type="text/css" href="includes/style.css" />
	<link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.10.3/themes/redmond/jquery-ui.css" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript" src="includes/jquery-ui-1.10.3/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#expireDate").datepicker({ minDate: +1, changeMonth: true, changeYear: true });
			$("#oldExpireDate").datepicker({ minDate: -1, changeMonth: true, changeYear: true });
			$("#codeList table tr").click(function(e) {
				if(e.target.type !== "checkbox")
					$(":checkbox", this).trigger("click");
			});
			$("#codeList table tr :input:checkbox").click(function () {
				if($("#codeList table tr :input:checkbox:checked").length > 0)
				{
					$("#codeList :input:not(.codeNumber)").removeAttr("disabled");
					if($("#oldNeverExpire").is(':checked'))
						$("#oldExpireDate").attr("disabled", "disabled");
					else
						$("#oldExpireDate").removeAttr("disabled");
					if($("#oldUnlimitedUses").is(':checked'))
						$("#oldMaxUses").attr("disabled", "disabled");
					else
						$("#oldMaxUses").removeAttr("disabled");
				}
				else
					$("#codeList :input:not(.codeNumber)").attr("disabled", "disabled");
			});
			$("#neverExpire").click(function () {
				if($("#neverExpire").is(':checked'))
					$("#expireDate").attr("disabled", "disabled");
				else
					$("#expireDate").removeAttr("disabled");
			});
			$("#unlimitedUses").click(function () {
				if($("#unlimitedUses").is(':checked'))
					$("#maxUses").attr("disabled", "disabled");
				else
					$("#maxUses").removeAttr("disabled");
			});
			$("#oldNeverExpire").click(function () {
				if($("#oldNeverExpire").is(':checked'))
					$("#oldExpireDate").attr("disabled", "disabled");
				else
					$("#oldExpireDate").removeAttr("disabled");
			});
			$("#oldUnlimitedUses").click(function () {
				if($("#oldUnlimitedUses").is(':checked'))
					$("#oldMaxUses").attr("disabled", "disabled");
				else
					$("#oldMaxUses").removeAttr("disabled");
			});
			$("#newCodeForm").submit(function () {	
				$("#codeCreateNotice").html("");
				var code = $("#codeName").val();
				var amount = $("#codeValue").val();
				var expire = "";
				if(!$("#neverExpire").is(':checked'))
					expire = $("#expireDate").val();
				var uses = "-1";
				if(!$("#unlimitedUses").is(':checked'))
					uses = $("#maxUses").val();

				if($.trim(code).length == 0)
				{
					$("#codeCreateNotice").html("<span class=\"requiredField\">You must give the Promotional Code a name.</span>");
					return false;
				}
				if($.trim(amount).length == 0)
				{
					$("#codeCreateNotice").html("<span class=\"requiredField\">You must give the Promotional Code a value.</span>");
					return false;
				}
				if(!$("#neverExpire").is(':checked') && $.trim(expire).length == 0)
				{
					$("#codeCreateNotice").html("<span class=\"requiredField\">You must choose an expiration date, or click \"No Expiration\".</span>");
					return false;
				}
				if(!$("#unlimitedUses").is(':checked') && $.trim(uses).length == 0)
				{
					$("#codeCreateNotice").html("<span class=\"requiredField\">You must choose a maximum number of uses, or click \"Unlimited Uses\".</span>");
					return false;
				}
					
				$.post("doPromoCodeAction.php", { action: "Create", code: code, amount: amount, expire: expire, uses: uses }, function(result) {
					if(result.success)
						location.reload();
					else
						$("#codeCreateNotice").html(result.message);
				}, 'json');
				return false;
			});
		});
		
		function setExpire()
		{
			var expire = "";
			if(!$("#oldNeverExpire").is(':checked'))
				expire = $("#oldExpireDate").val();
			if(!$("#oldNeverExpire").is(':checked') && $.trim(expire).length == 0)
			{
				$("#codeActionNotice").html("<span class=\"requiredField\">You must choose an expiration date, or click \"No Expiration\".</span>");
				return false;
			}
			var ids = "";
			$("#codeActionForm table tr :input:checkbox:checked").each(function() {
				ids += "|" + $(this).val();
			});
			ids = ids.substring(1);
			$.post("doPromoCodeAction.php", { action: "DoExpire", ids: ids, expire: expire }, function(result) {
				if(result.success)
					location.reload();
				else
					$("#codeActionNotice").html(result.message);
			}, 'json');
		}
		
		function setUses()
		{
			var uses = "-1";
			if(!$("#oldUnlimitedUses").is(':checked'))
				uses = $("#oldMaxUses").val();
			if(!$("#oldUnlimitedUses").is(':checked') && $.trim(uses).length == 0)
			{
				$("#codeActionNotice").html("<span class=\"requiredField\">You must choose a maximum number of uses, or click \"Unlimited Uses\".</span>");
				return false;
			}
			
			var ids = "";
			$("#codeActionForm table tr :input:checkbox:checked").each(function() {
				ids += "|" + $(this).val();
			});
			ids = ids.substring(1);
			$.post("doPromoCodeAction.php", { action: "SetUses", ids: ids, uses: uses }, function(result) {
				if(result.success)
					location.reload();
				else
					$("#codeActionNotice").html(result.message);
			}, 'json');
		}

		function setValue()
		{
			var value = $("#oldValue").val();
			if(!$("#oldValue").is(':checked') && $.trim(value).length == 0)
			{
				$("#codeActionNotice").html("<span class=\"requiredField\">You must choose a value for the promo code.</span>");
				return false;
			}
			
			var ids = "";
			$("#codeActionForm table tr :input:checkbox:checked").each(function() {
				ids += "|" + $(this).val();
			});
			ids = ids.substring(1);
			$.post("doPromoCodeAction.php", { action: "SetValue", ids: ids, value: value }, function(result) {
				if(result.success)
					location.reload();
				else
					$("#codeActionNotice").html(result.message);
			}, 'json');
		}
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxmedium">
			<h1>Promotional Codes</h1>
			<div id="codeList">
<?php
				if(!empty($codes))
				{ ?>
				<form id="codeActionForm" method="post">
				<table>
				<tr><th>Select</th><th>Code</th><th>Discount</th><th>Expiration</th><th>Uses Left</th><th>Total Uses</th></tr>
<?php
				foreach($codes as $code)
				{	
					$discount = sprintf("$%01.2f", $code["Discount"]);
					$expire = isset($code["Expiration"]) ? date("F d, Y", strtotime($code["Expiration"])) : "No Expiration";
					$uses = $code["UsesLeft"] > -1 ? $code["UsesLeft"] : "Unlimited";
					$class = "";
					if($uses != "Unlimited" && $uses == 0) $class = "class='expired'";
					if($expire != "No Expiration")
					{
						$expiration = new DateTime($code["Expiration"]);
						if($expiration < new DateTime(date("F d, Y")))
						{
							$class = "class='expired'";
							$expire = "<s>" . $expire . "</s>";
						}
					}					
					echo "<tr $class><td style=\"text-align: center;\"><input type=\"checkbox\" value=\"" . $code["CodeID"] . 
								"\" class=\"codeNumber\" /></td><td>" . $code["Code"] . "</td><td>$discount</td><td>$expire</td><td>$uses</td><td>" . $code["Uses"] . "</td></tr>\r\n";
				} ?>				
				</table>
				<p>With Selected:<br />
					<input type="submit" onclick="setExpire(); return false;" value="Set Expiration:" disabled>
					<input type="text" id="oldExpireDate" name="oldExpireDate" style="width: 75px;" disabled />
					<input type="checkbox" id="oldNeverExpire" name="oldNeverExpire" checked disabled />No Expiration</label><br />
					<input type="submit" onclick="setUses(); return false;" value="Set Uses Left:" disabled>							
					<input type="number" id="oldMaxUses" name="oldMaxUses" style="width: 50px;" min="1" value="5" disabled />
					<label><input type="checkbox" id="oldUnlimitedUses" name="oldUnlimitedUses" checked disabled />Unlimited Uses</label><br />
					<input type="submit" onclick="setValue(); return false;" value="Set Discount:" disabled>							
					<label>$<input type="number" id="oldValue" name="oldValue" style="width: 50px;" min="1" value="5" disabled /></label><br />
				</p>
				</form>
				<div class="noticeSection" id="codeActionNotice"><?php echo $message; ?></div>
<?php } else { ?>
				</table>
				<p style="font-size: 0.9em;">There are no promotional codes that are active or recently expired at this time.</p>
<? } ?>
				<p style="font-size: 0.9em;">Promotional Codes that have been expired for longer than 3 months will no longer be viewable.</p>
			</div>
			<div id="createCode">
				<div class="headertitle">Create New Promotional Code</div>
				<p style="font-size: 0.9em;">To create a new promotional code, please choose a code name, set the discount amount, decide if and when it expires,
				and how many uses it has if it will not be unlimited. Note that codes created are only valid for the current convention
				year, even if you choose no expiration date and no limit on uses.</p>
				<form id="newCodeForm" method="post">
				<label>Promotional Code:<br /><input type="text" id="codeName" name="codeName" style="width: 40%;"/></label><br />
				<label>Discount:<br />$<input type="number" id="codeValue" name="codeValue" style="width: 50px;" min="1" value="5" /></label><br />
				
				<label for="expireDate">Expiration Date: </label><br />
				<input type="text" id="expireDate" name="expireDate" style="width: 75px;" disabled />
				<label><input type="checkbox" id="neverExpire" name="neverExpire" checked />No Expiration</label><br />
				
				<label for="maxUses">Uses: </label><br />
				<input type="number" id="maxUses" name="maxUses" style="width: 50px;" min="1" value="5" disabled />
				<label><input type="checkbox" id="unlimitedUses" name="unlimitedUses" checked />Unlimited Uses</label><br /><br />
				
				<div class="noticeSection" id="codeCreateNotice"><?php echo $message; ?></div>
				<br /><input type="submit" value="Create Promotional Code">
				</form>
				<br /><br />
			</div>
			<div class="goback">
				<a href="/index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>