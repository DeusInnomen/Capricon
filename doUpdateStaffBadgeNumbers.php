<?php
	session_start();
	include_once('includes/functions.php');
	if(!DoesUserBelongHere("RegLead"))
		header('Location: index.php');

    $year = date("n") >= 3 ? date("Y") + 1: date("Y");
    
    $sql = "SELECT BadgeID, BadgeTypeID FROM PurchasedBadges WHERE BadgeNumber > 1 AND BadgeNumber < 150 AND BadgeTypeID IN (1, 3, 4, 5, 12) AND Year = $year";
    $result = $db->query($sql);
    $gohs = array();
    $gohGuests = array();
    $concom = array();
    $staff = array();
    $special = array();
    
    while($row = $result->fetch_array()) {
        if($row["BadgeTypeID"] == 1)
            $gohGuests[] = $row["BadgeID"];
        if($row["BadgeTypeID"] == 3)
            $concom[] = $row["BadgeID"];
        if($row["BadgeTypeID"] == 4)
            $staff[] = $row["BadgeID"];
        if($row["BadgeTypeID"] == 5)
            $gohs[] = $row["BadgeID"];
        if($row["BadgeTypeID"] == 12)
            $special[] = $row["BadgeID"];
    }
    $result->close();

    $number = 2;
    foreach($gohs as $badgeid) {
        $sql = "UPDATE PurchasedBadges SET BadgeNumber = $number WHERE BadgeID = $badgeid";
        $db->query($sql);
        $number++;
    }
    foreach($gohGuests as $badgeid) {
        $sql = "UPDATE PurchasedBadges SET BadgeNumber = $number WHERE BadgeID = $badgeid";
        $db->query($sql);
        $number++;
    }
    foreach($special as $badgeid) {
        $sql = "UPDATE PurchasedBadges SET BadgeNumber = $number WHERE BadgeID = $badgeid";
        $db->query($sql);
        $number++;
    }
    foreach($concom as $badgeid) {
        $sql = "UPDATE PurchasedBadges SET BadgeNumber = $number WHERE BadgeID = $badgeid";
        $db->query($sql);
        $number++;
    }
    foreach($staff as $badgeid) {
        $sql = "UPDATE PurchasedBadges SET BadgeNumber = $number WHERE BadgeID = $badgeid";
        $db->query($sql);
        $number++;
    }

    header('Location: /staffBadges.php');
    exit();
?>
