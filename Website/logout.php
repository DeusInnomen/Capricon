<?php
	session_start();
	$_SESSION = array();
	session_regenerate_id(true);
	session_destroy();
	header('Location: index.php');
?>