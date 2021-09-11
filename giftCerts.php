<?php
	session_start();
	include_once('includes/functions.php');
	DoCleanup();
	if(!isset($_SESSION["PeopleID"]))
		header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
	else
	{
		$certs = array();
		$result = $db->query("SELECT IFNULL(PurchaserID, 0) AS PurchaserID, CertificateID, CertificateCode, " . 
			"Recipient, Purchased, Redeemed, OriginalValue, CurrentValue, Badges, CanTransfer " . 
			"FROM GiftCertificates WHERE (PurchaserID = " . $_SESSION["PeopleID"] . 
			" OR Recipient = '" . $_SESSION["Email"] . "') ORDER BY Purchased DESC");
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
				$certs[] = $row;
			$result->close();				
		}
	}
	$message = isset($_POST["message"]) ? $_POST["message"] : "";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Capricon Registration System -- Gift Certificates</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
	<link rel="icon" href="includes/favicon.png" />
	<link rel="shortcut icon" href="includes/favicon.ico" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
	<script type="text/javascript" src="includes/global.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#certList tr").click(function(e) {
				if(e.target.type !== "checkbox")
					$(":checkbox", this).trigger("click");
			});
			$("#certActionForm :input:checkbox").click(function () {
				if($("#certActionForm :input:checkbox:checked").length > 0)
					$("#certActionForm :input[type!='checkbox']").removeAttr("disabled");
				else
					$("#certActionForm :input[type!='checkbox']").attr("disabled", "disabled");
			});
			$("#createNewCert").submit(function () {
				$("#createNewCert :input").prop("readonly", true);
				var amount = $("#amount").val();
				$.post("doCartAction.php", { entry: "AddCert", amount: amount }, function(result) {
					if(result.success)
						location.reload();
					else
					{
						$("#createNewCert :input").removeProp("readonly");
						$("#cartActionNotice").html(result.message);
					}
				}, 'json');
				return false;
			});
		});
		
		function printCert() {
			var values = "";
			$("#certActionForm :input:checkbox:checked").each(function() {
				values += "|" + $(this).val();
			});
			if(values.length > 0) values = values.substring(1);
			var form = $('<form action="doCertAction.php" method="post">' + 
						 '<input type="hidden" name="action" value="Print" />' + 
						 '<input type="hidden" name="values" value="' + values + '" />' + 
						 '</form>');
			$('body').append(form);
			$(form).submit();
		}
		
		function assignCert() {
			var values = "";
			$("#certActionForm :input:checkbox:checked").each(function() {
				values += "|" + $(this).val();
			});
			if(values.length > 0) values = values.substring(1);
			var recipient = $("#assignEmail").val();
			$.post("doCertAction.php", { action: "Assign", values: values, target: recipient }, function(result) {
				if(result.success)
					reloadWithMessage(result.message);
				else
					$("#certActionNotice").html(result.message);
			}, 'json');
		}
		
		function reloadWithMessage(message) {
			var form = $('<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">' + 
						 '<input type="hidden" name="message" value="' + message + '" /></form>');
			$('body').append(form);
			$(form).submit();
		}		
	</script>
</head>
<body>
	<?php include('includes/header.php'); ?>
	<div class="content">
		<div class="centerboxmedium">
			<h1>Gift Certificates</h1>
			<div class="headertitle">Bought By or Given To You</div>
			<div id="certList">
<?php
					if(!empty($certs)) { ?>
						<p>Gift certificates are displayed for 3 months after they have been fully redeemed, 
						and then they are removed from the system.</p>
						<form id="certActionForm" method="post">
						<table>
						<tr><th>Select</th><th>Code</th><th>Purchased</th><th>Redeemed</th><th>For</th><th>Value</th></tr>
<?php
						foreach($certs as $cert)
						{
							if(isset($cert["Recipient"]))
							{
								if($cert["PurchaserID"] == $_SESSION["PeopleID"])
								{
									$who = $cert["Recipient"];
									$class = "other";
								}
								else
								{
									$who = "You";
									$class = "self";
								}
							}
							else
							{
								$who = "Unassigned";
								$class = "self";
							}
							$created = date("F d, Y", strtotime($cert["Purchased"]));
							$redeemed = isset($cert["Redeemed"]) ? date("F d, Y", strtotime($cert["Redeemed"])) : "";
							if($cert["OriginalValue"] > 0)
								if($cert["CurrentValue"] > 0)
									$value = sprintf("$%01.2f", $cert["CurrentValue"]);
								else
									$value = "Redeemed";
							else
								if($cert["Badges"] > 0)
									$value = $cert["Badges"] . " Badge" . ($cert["Badges"] == 1 ? "" : "s");
								else
									$value = "Redeemed";
							if(isset($cert["CanTransfer"]) && $cert["CanTransfer"] == "0")
								$select = "<td style=\"text-align: center;\" class=\"masterTooltip\" title=\"This certificate has been marked as " . 
									"non-transferable and cannot be changed.\">---</td>";
							elseif($cert["PurchaserID"] == $_SESSION["PeopleID"])
							{
								if($value == "Redeemed")
									$select = "<td style=\"text-align: center;\" class=\"masterTooltip\" title=\"This certificate has been redeemed " . 
										"and cannot be changed.\">---</td>";
								else
									$select = "<td style=\"text-align: center;\"><input type=\"checkbox\" value=\"" . $cert["CertificateID"] . 
										"\" /></td>";
							}
							else
								$select = "<td style=\"text-align: center;\" class=\"masterTooltip\" title=\"This certificate was given to you by " . 
									"someone else, only they can modify it.\">---</td>";

							echo "<tr class='$class'>$select<td>" . $cert["CertificateCode"] . "</td><td>$created</td><td>$redeemed</td><td>$who</td><td>$value</td></tr>\r\n";
						} ?>
						</table>
						<p>With Selected: 
							<input type="submit" onclick="printCert(); return false;" value="Print Certificate(s)" disabled>
							<input type="submit" onclick="assignCert(); return false;" value="Assign To Email:" disabled>							
							<input id="assignEmail" type="text" style="width: 225px;" maxlength="30" disabled><br />
							<span style="display: block; text-align: right; font-size: 0.9em;">(Leave blank to remove any assignments.)</span>
						</p>
						</form>
						<div class="noticeSection" id="certActionNotice"><?php echo $message; ?></div>
<?php 
					}
					else
						echo '<p class="noneFound">You have not purchased or received any gift certificates.</p>' . "\r\n"; ?>
			</div>
			<div class="headertitle">Purchase New Certificate</div>
			<p>Gift certificates have an assigned cash value and do not expire. You will receive a
			code that can be given to anyone, or you may assign the gift certficiate to a single person
			after purchase.</p>
			<form id="createNewCert" method="post">
				<label>Amount: $<input type="number" id="amount" name="amount" style="width: 50px;" maxlength="6" value="50" /></label>
				<input type="submit" value="Add to Cart" />
			</form>
			<div class="noticeSection" id="cartActionNotice">&nbsp;</div>
			<div class="goback">
				<a href="index.php">Return to the Main Menu</a>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
</body>
</html>