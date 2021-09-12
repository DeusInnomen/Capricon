<?php
session_start();
include_once('includes/functions.php');
$action = $_POST["action"];

if(isset($action))
{
    switch($action)
    {
        case "Search":
            $year = date("n") >= 3 ? date("Y") + 1: date("Y");
            $sql  = "SELECT pb.BadgeID, pb.BadgeNumber, CASE WHEN pb.PeopleID IS NULL THEN CONCAT(ot.FirstName, ' ', ot.LastName) ELSE CONCAT(p.FirstName, ' ', p.LastName) END AS Name, " .
                "pb.BadgeName, CASE WHEN p.City IS NULL THEN CONCAT(ot.City, ', ', ot.State) ELSE CONCAT(p.City, ', ', p.State) END AS Location, CASE WHEN pb.PeopleID IS NULL THEN ot.LastName " . 
                "ELSE p.LastName END AS LastName, CASE WHEN pb.PeopleID IS NULL THEN ot.FirstName ELSE p.FirstName END AS FirstName, pb.Status, IFNULL(p.Email, '') AS Email, pb.PickedUp " . 
                "FROM PurchasedBadges pb LEFT OUTER JOIN People p ON p.PeopleID = pb.PeopleID LEFT OUTER JOIN OneTimeRegistrations ot ON ot.OneTimeID = pb.OneTimeID ";
            $where = "WHERE pb.Year = $year ";
            
            if(isset($_POST["lastname"]) && strlen($_POST["lastname"]) > 0)
                $where .= "AND (p.LastName LIKE '" . $db->real_escape_string($_POST["lastname"]) . "%' OR " .
                    "ot.LastName LIKE '%" . $db->real_escape_string($_POST["lastname"]) . "%') ";
        
            if(isset($_POST["badgename"]) && strlen($_POST["badgename"]) > 0)
                $where .= "AND pb.BadgeName LIKE '" . $db->real_escape_string($_POST["badgename"]) . "%' ";

            if(isset($_POST["email"]) && strlen($_POST["email"]) > 0)
                $where .= "AND p.Email LIKE '" . $db->real_escape_string($_POST["email"]) . "%' ";
        
            $sql .= $where . "ORDER BY Name";
            
            $result = $db->query($sql);
            if($result->num_rows > 0)
            {
                $currentRows = $result->num_rows;
                echo "<table>\r\n";
                echo "<tr><th>Name</th><th>Badge Name</th><th>Location</th><th>Email Address</th><th>Status</th></tr>\r\n";
                while($row = $result->fetch_array())
                {
                    $fade = $row["Status"] != "Paid" ? " style=\"background-color: #FF6666;\"" : $row["PickedUp"] == 1 ? " style=\"background-color: #AAAAAA;\"" : "";
                    $status = $row["Status"] != "Paid" ? "Unpaid" : $row["PickedUp"] == 1 ? "Picked Up" : "Available";
                    $badge = ($row["Status"] == "Paid" && $row["PickedUp"] == 0) ? " badge=\"" . $row["BadgeID"] . "\"" : "";
                    echo "<tr" . $fade . $badge . "><td>" . $row["Name"] . "</td><td>" . $row["BadgeName"] . "</td><td>" . $row["Location"] . "</td><td>" . $row["Email"] . "</td><td>" . $status . "</td></tr>\r\n";
                }
                $result->close();
                echo "</table>\r\n";
            }
            else
                echo "<p class=\"requiredField\">No badges appear to have been purchased that meet the above parameters.</p><p>$sql</p>\r\n";
        break;

        case "SaveSignature":
            $year = isset($_GET['year']) ? $_GET['year'] : (date("n") >= 3 ? date("Y") + 1: date("Y"));
            $img = $_POST["img"];
            $badge = $_POST["badge"];
            $badges = "($badge";
            $sql = "SELECT IFNULL(PurchaserID, OneTimePurchaserID) AS PurchaserID FROM PurchasedBadges WHERE BadgeId = $badge";
            $result = $db->query($sql);
            $row = $result->fetch_array();
            $purchaser = $row["PurchaserID"];
            $result->close();

            $sql = "SELECT pb.BadgeID FROM PurchasedBadges pb WHERE pb.BadgeTypeID = 2 AND (pb.PurchaserID = $purchaser or pb.OneTimePurchaserID = $purchaser) AND pb.Year = $year";
            $result = $db->query($sql);
            if($result->num_rows > 0) {
                while($row = $result->fetch_array())
                    $badges .= "," . $row["BadgeID"];
                $result->close();
            }
            $badges .= ")";

            $sql = "UPDATE PurchasedBadges SET PickedUp = 1, PickUpTime = NOW(), PickupSignature = '" . $db->real_escape_string($img) . "' WHERE BadgeID IN $badges";
            $db->query($sql);
            //$data = base64_decode($img);
            //file_put_contents("data/signatures/testing." . date("Ymd.His"). ".svg", $data);
            echo '{ "success": true, "message": "Signature Saved." }';
            
        break;
        
        default:
            echo '{ "success": false, "message": "Incorrect Action" }';
        break;
    }
}
else
    echo '{ "success": false, "message": "Incorrect Action" }';
?>