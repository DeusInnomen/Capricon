<div class="footer">
	<div class="userInfo">
	<?php
		if(isset($_SESSION["PeopleID"]))
		{
			if($_SESSION["FullName"] !== $_SESSION["BadgeName"])
				echo "\t" . 'You are logged in as: <span class="userName">' . $_SESSION["FullName"] . ' (' . $_SESSION["BadgeName"] . ")</span><br />\r\n";
			else
				echo "\t" . 'You are logged in as: <span class="userName">' . $_SESSION["FullName"] . "</span><br />\r\n";
			echo "\t\tAccess Level: " . GetUserDescription($_SESSION["PeopleID"]) . "<br />\r\n";
			echo "\t\t" . '<a href="/logout.php">Log Out</a>';
		}
		else
		{
			echo "\tYou are not presently logged in.<br />\r\n\r\n";			
			echo "\t\t" . '<a href="/login.php">Log In</a>';
		}
		echo ' -- <a href="/index.php">Main Menu</a> -- <a href="mailto:it@phandemonium.org?Subject=Capricon%20Registration%20Site%20Question">Email Support</a>' . "\r\n";
	?>
	</div>
	<div class="footerNotes">
		<span>Copyright &copy; <?php echo date("Y"); ?> Phandemonium, Inc.</span><br />
		<span style="font-weight: bold;"><a href="privacy.php">Click to view the Privacy Policy.</a></span><br />
	</div>
</div>
