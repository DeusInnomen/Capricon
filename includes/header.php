<?php
	session_start();
	include_once('includes/functions.php');
	
	if(isset($_SESSION["PeopleID"]) && strtolower($_SERVER["PHP_SELF"]) != "shoppingcart.php"
		&& strtolower($_SERVER["PHP_SELF"]) != "transfertopaypal.php")
	{
		$result = $db->query("SELECT COUNT(CartID) AS Count, SUM(Price) AS Total FROM ShoppingCart Where PurchaserID = " . 
			$_SESSION["PeopleID"]);
		if($result->num_rows > 0)
		{
			$row = $result->fetch_array();
			$count = $row["Count"];
			$total = $row["Total"];
			$result->close();
			
			if($count > 0)
			{
				?>
<div class="miniCart">
		<p>Shopping Cart</p>
		<span><?php echo $count; ?> Entr<?php echo $count == 1 ? "y" : "ies"; ?> in Cart</span><br />
		<span>Subtotal: <?php echo sprintf("$%01.2f", $total); ?></span><br /><br />
		<a href="shoppingCart.php">Checkout</a>
		<hr>
		<a href="doCartAction.php?entry=clear&return=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">Clear Cart</a>
	</div>
<?php
			}
		}
	}
?>
	<div class="header">
		<div class="posLeft"><a class="linkedImage" href="index.php"><img src="includes/CapReg.png" /></a></div>
        <?php if(IsTestSite()) echo "<div style='position: absolute; right: 20%; top: 30px; font-size: 22px; font-weight: bold; clear: none;'><span>REGISTRATION TEST SITE</span></div>"; ?>
		<div class="posRight"><a class="linkedImage" href="index.php"><img src="includes/capricious.png" /></a></div>
	</div>