<?php
    session_start();
    include_once('includes/functions.php');

    if(!isset($_SESSION['PeopleID']))
	{
		echo '{ "success": false, "message": "No user is logged in." }';
		return;
	}
    if(!DoesUserBelongHere("Dealer") && !DoesUserBelongHere("DealerStaff"))
	{
		echo '{ "success": false, "message": "An unknown error has occurred." }';
		return;
	}
	$task = $_POST["task"];
	$year = date("n") >= 3 ? date("Y") + 1: date("Y");

	if($task == "SaveDealerDetails")
	{
        $result = $db->query("SELECT DealerID FROM Dealer WHERE PeopleID = " . $_SESSION["PeopleID"]);
        if($result->num_rows > 0)
        {
            $row = $result->fetch_array();
            $dealerID = $row["DealerID"];
            $result->close();
        }
        else {
            $dealerID = "";
        }

        $companyName = $db->real_escape_string($_POST["companyName"]);
        $legalName = !empty($_POST["legalName"]) ? "'" . $db->real_escape_string($_POST["legalName"]) . "'" : "null";
        $url = !empty($_POST["url"]) ? "'" . $db->real_escape_string($_POST["url"]) . "'" : "null";
        $email = !empty($_POST["contactEmail"]) ? "'" . $db->real_escape_string($_POST["contactEmail"]) . "'" : "null";
        $onlyContact = !empty($_POST["onlyThisEmail"]) ? 1 : 0;
        if($onlyContact == 1 && $email == "null") {
            echo '{ "success": false, "message": "You must provide an email address on this page if you indicate we should only contact you at the given email address." }';
            return;
        }
        $desc = !empty($_POST["description"]) ? "'" . $db->real_escape_string($_POST["description"]) . "'" : "null";
        $add1 = $db->real_escape_string($_POST["address1"]);
        $add2 = !empty($_POST["address2"]) ? "'" . $db->real_escape_string($_POST["address2"]) . "'" : "null";
        $add3 = !empty($_POST["address3"]) ? "'" . $db->real_escape_string($_POST["address3"]) . "'" : "null";
        $city = $db->real_escape_string($_POST["city"]);
        $state = !empty($_POST["state"]) ? "'" . $db->real_escape_string($_POST["state"]) . "'" : "null";
        $zip = $db->real_escape_string($_POST["zip"]);
        $country = !empty($_POST["country"]) ? "'" . $db->real_escape_string($_POST["country"]) . "'" : "null";
        $phone = !empty($_POST["phone"]) ? "'" . $db->real_escape_string($_POST["phone"]) . "'" : "null";
        $phonetype = !empty($_POST["phonetype"]) ? "'" . $db->real_escape_string($_POST["phonetype"]) . "'" : "null";
        $taxNumber = !empty($_POST["taxNumber"]) ? "'" . $db->real_escape_string($_POST["taxNumber"]) . "'" : "null";

        if($dealerID == "")
        {
            $sql = "INSERT INTO Dealer (PeopleID, CompanyName, LegalName, URL, ContactEmail, OnlyUseThisEmail, Description, Address1, Address2, Address3, City, State, ZipCode, Country, Phone, PhoneType, TaxNumber, Created) " .
                "VALUES (" . $_SESSION["PeopleID"] .
                ", '$companyName', $legalName, $url, $email, $onlyContact, $desc, '$add1', $add2, $add3, '$city', $state, '$zip', $country, $phone, $phonetype, $taxNumber, NOW())";
        }
        else
        {
            $sql = "UPDATE Dealer SET " .
                "CompanyName = '$companyName', " .
                "LegalName = $legalName, " .
                "URL = $url, " .
                "ContactEmail = $email, " .
                "OnlyUseThisEmail = $onlyContact, " .
                "Description = $desc, " .
                "Address1 = '$add1', " .
                "Address2 = $add2, " .
                "Address3 = $add3, " .
                "City = '$city', " .
                "State = $state, " .
                "ZipCode = '$zip', " .
                "Country = $country, " .
                "Phone = $phone, " .
                "PhoneType = $phonetype, " .
                "TaxNumber = $taxNumber WHERE DealerID = $dealerID";
        }
        $db->query($sql);
        echo '{ "success": true, "message": "Your details have been updated successfully." }';
    }
    else if($task == "SaveConfig") {
        $result = $db->query("SELECT DealerConfigID FROM DealerConfig WHERE Year = $year");
        if($result->num_rows > 0)
        {
            $row = $result->fetch_array();
            $dealerConfigID = $row["DealerConfigID"];
            $result->close();
        }
        else
            $dealerConfigID = "";

        $waitlistTables = !empty($_POST["waitlistAfterNumber"]) ? $db->real_escape_string($_POST["waitlistAfterNumber"]) : "0";
        $waitlistDate = !empty($_POST["waitlistAfterDate"]) ? "'" . $db->real_escape_string($_POST["waitlistAfterDate"]) . "'" : "null";
        $closeDate = !empty($_POST["closeDate"]) ? "'" . $db->real_escape_string($_POST["closeDate"]) . "'" : "null";
        $electric = !empty($_POST["electricFee"]) ? $db->real_escape_string($_POST["electricFee"]) : "50.00";
        $badge = !empty($_POST["badgeFee"]) ? $db->real_escape_string($_POST["badgeFee"]) : "45.00";

        if($dealerConfigID == "") {
            $sql = "INSERT INTO DealerConfig (Year, WaitListAfterTableNum, WaitListAfterDate, ApplicationCloseDate, ElectricFee, BadgeFee) " .
                "VALUES ($year, $waitlistTables, $waitlistDate, $closeDate, $electric, $badge)";
        }
        else {
            $sql = "UPDATE DealerConfig SET " .
                "WaitListAfterTableNum = $waitlistTables, " .
                "WaitListAfterDate = $waitlistDate, " .
                "ApplicationCloseDate = $closeDate, " .
                "ElectricFee = $electric, " .
                "BadgeFee = $badge WHERE DealerConfigID = $dealerConfigID";
        }
        $db->query($sql);
        echo '{ "success": true, "message": "Dealer configuration has been updated successfully." }';
    }
    else if($task == "CloseConfig") {
        $db->query("DELETE FROM DealerConfig WHERE Year = $year");
        echo '{ "success": true, "message": "Applications have been closed off successfully." }';
    }
    else if($task == "SetTablePrice") {
        $quantity = $db->real_escape_string($_POST["quantity"]);
        $price = $db->real_escape_string($_POST["price"]);

        $result = $db->query("SELECT TablePriceID FROM DealerTablePrices WHERE Quantity = $quantity");
        if($result->num_rows > 0)
        {
            $row = $result->fetch_array();
            $tablePriceID = $row["TablePriceID"];
            $result->close();
        }
        else
            $tablePriceID = "";

        if($tablePriceID == "")
            $sql = "INSERT INTO DealerTablePrices (Quantity, Price) VALUES ($quantity, $price)";
        else
            $sql = "UPDATE DealerTablePrices SET Price = $price WHERE TablePriceID = $tablePriceID";
        $db->query($sql);
        echo '{ "success": true, "message": "Table pricing has been updated successfully." }';
    }
    else if($task == "AddNextPriceTier") {
        $db->query("INSERT INTO DealerTablePrices (Quantity, Price) SELECT IFNULL(MAX(Quantity) + 1, 1) AS Quantity, IFNULL(MAX(Price), 50.00) AS Price FROM DealerTablePrices");
        echo '{ "success": true, "message": "New table pricing has been added successfully." }';
    }
    else if($task == "RemoveLastPriceTier") {
        $result = $db->query("SELECT MAX(Quantity) AS Quantity FROM DealerTablePrices");
        $row = $result->fetch_array();
        $quantity = $row["Quantity"];
        $result->close();

        if($quantity > 1) {
            $db->query("DELETE FROM DealerTablePrices WHERE Quantity = $quantity");
            echo '{ "success": true, "message": "Highest table pricing row has been removed successfully." }';
        }
        else
            echo '{ "success": false, "message": "You cannot remove the last table pricing row." }';
    }
    else if($task == "SaveDealerApplication") {
        $result = $db->query("SELECT DealerID FROM Dealer WHERE PeopleID = " . $_SESSION["PeopleID"]);
        if($result->num_rows > 0)
        {
            $row = $result->fetch_array();
            $dealerID = $row["DealerID"];
            $result->close();
        }
        else {
            echo '{ "success": false, "message": "An unknown error has occurred, please contact it@capricon.org. [Code 3]" }';
            return;
        }

        $result = $db->query("SELECT DealerPresenceID, Status, StatusReason FROM DealerPresence WHERE DealerID = $dealerID");
        if($result->num_rows > 0)
        {
            $row = $result->fetch_array();
            $dealerPresenceID = $row["DealerPresenceID"];
            $status = $row["Status"];
            $statusReason = $row["StatusReason"];
            $result->close();
        }
        else {
            $dealerPresenceID = "";
            $status = "Pending";
            $statusReason = null;
        }

        if($status == "Approved" || $status == "Rejected") {
            echo '{ "success": false, "message": "This application has already been finalized and cannot be modified further." }';
            return;
        }

        try {
            $tables = $_POST["tables"];
            $tableValues = explode(";", $_POST["tables"]);
            if(sizeof($tableValues) != 2) {
                echo '{ "success": false, "message": "An unknown error has occurred, please contact it@capricon.org. [Code 1]" }';
                return;
            }
            $tableCount = $tableValues[0];
            $electric = isset($_POST["electricity"]) ? 1 : 0;
            $addedDetails = $db->real_escape_string($_POST["addedDetails"]);

            $result = $db->query("SELECT WaitListAfterTableNum, WaitListAfterDate, ApplicationCloseDate, ElectricFee, BadgeFee FROM DealerConfig WHERE Year = $year");
            $config = $result->fetch_array();
            $result->close();
            if($dealerPresenceID == "") {
                if(!empty($config["ApplicationCloseDate"])) {
                    $expiration = new DateTime($config["ApplicationCloseDate"]);
                    if($expiration <= new DateTime(date("F d, Y")))
                        $status = "Waitlist";
                }
                if($config["WaitListAfterTableNum"] > 0) {
                    $result = $db->query("SELECT IFNULL(SUM(NumTables), 0) AS Approved FROM DealerPresence WHERE Year = $year and Status = 'Approved'");
                    $row = $result->fetch_array();
                    $result->close();
                    $numApproved = $row["Approved"];
                    if($numApproved >= $config["WaitListAfterTableNum"])
                        $status = "Waitlist";
                }

                $sql = "INSERT INTO DealerPresence (DealerID, Year, NumTables, ElectricalNeeded, AddedDetails, Status, StatusReason, Created) VALUES ($dealerID, $year, $tableCount, $electric, "
                    . "'$addedDetails', '$status', null, NOW())";
                $db->query($sql);
                $dealerPresenceID = $db->insert_id;
                $verb = "submitted";

                $result = $db->query("SELECT FirstName, LastName, Email, CompanyName, Description, ContactEmail, OnlyUseThisEmail FROM DealerPresence dp " .
                    "JOIN Dealer d on d.DealerID = dp.DealerID JOIN People p ON d.PeopleID = p.PeopleID WHERE DealerPresenceID = $dealerPresenceID");
                $dealer = $result->fetch_array();
                $result->close();

                $numBadges = (!empty($_POST["addlFName1"]) ? 1 : 0) + (!empty($_POST["addlFName2"]) ? 1 : 0) + (!empty($_POST["addlFName3"]) ? 1 : 0) + (!empty($_POST["addlFName4"]) ? 1 : 0)
                    + (!empty($_POST["addlFName5"]) ? 1 : 0);

                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->Port = 587;
                $mail->Host = "mail.capricon.org";
                $mail->Username = "outgoing@capricon.org";
                $mail->Password = $smtpPass;
                $mail->From = "registration@capricon.org";
                $mail->FromName = "Capricon Registration System";
                $mail->AddAddress("dealers@capricon.org", "Capricon Dealers");
                $mail->WordWrap = 135;
                $mail->Subject = "New Dealer Application Received (" . $dealer["CompanyName"] . ")";
                $msg = "Hello! This email is to let you know that a request to be a Dealer at the next Capricon has been received:\r\n\r\n";
                $msg .= "Company: " . $dealer["CompanyName"] . "\r\n";
                $msg .= "Contact: " . $dealer["FirstName"] . " " . $dealer["LastName"] . "\r\n";
                $msg .= "Email: ";
                if($dealer["OnlyUseThisEmail"] == 1)
                    $msg .= $dealer["ContactEmail"] . "\r\n";
                else if(empty($dealer["ContactEmail"]))
                    $msg .= $dealer["Email"] . "\r\n";
                else
                    $msg .= $dealer["ContactEmail"] . " or " . $dealer["Email"] . "\r\n";
                $msg .= "Description: " . $dealer["Description"] . "\r\n";
                $msg .= "Tables Requested: $tableCount\r\n";
                $msg .= "Electricity Needed? " . ($electric == 1 ? "Yes" : "No") . "\r\n";
                $msg .= "# Extra Badges: $numBadges\r\n";
                if(!empty($addedDetails) && trim($addedDetails) != "")
                    $msg .= "Additional Details: $addedDetails\r\n";
                $msg .= "You may view or update this application by going to https://registration.capricon.org/manageDealers.php. \r\n\r\nSincerely,\r\nThe Capricon Registration System\r\n";
                $mail->Body = $msg;
                $mail->Send();

                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->Port = 587;
                $mail->Host = "mail.capricon.org";
                $mail->Username = "outgoing@capricon.org";
                $mail->Password = $smtpPass;
                $mail->From = "dealers@capricon.org";
                $mail->FromName = "Capricon Dealers";
                if($dealer["OnlyUseThisEmail"] == 0)
                    $mail->AddAddress($dealer["Email"], $dealer["FirstName"] . " " . $dealer["LastName"]);
                if(!empty($dealer["ContactEmail"]))
                    $mail->AddAddress($dealer["ContactEmail"], $dealer["CompanyName"]);
                $mail->WordWrap = 135;
                $mail->Subject = "Capricon Dealer's Hall Application Received";
                $msg = "Hello! This email is to let you know that the Dealers Team has received a request to show in the Capricon Dealer's Hall. You provided the following information:\r\n\r\n";
                $msg .= "Tables Requested: $tableCount\r\nElectricity Needed? " . ($electric == 1 ? "Yes" : "No") . "\r\n";
                $msg .= "# Extra Badges: $numBadges\r\n";
                if(!empty($addedDetails) && trim($addedDetails) != "")
                    $msg .= "Additional Details: $addedDetails\r\n";
                $msg .= "\r\nYou will be updated when the application status has changed, or if the Dealers team has any questions. You do not owe any money at this time. Once the application is "
                    . "approved, you will receive an invoice email with instructions on how to pay any fees for this application. If you have any questions, please contact the team at "
                    . "dealers@capricon.org!\r\n\r\nSincerely,\r\nThe Capricon Dealers Team\r\n";
                $mail->Body = $msg;
                $mail->Send();

            }
            else {
                $sql = "UPDATE DealerPresence SET "
                    . "NumTables = $tableCount, "
                    . "ElectricalNeeded = $electric, "
                    . "AddedDetails = '$addedDetails' WHERE DealerPresenceID = $dealerPresenceID";
                $db->query($sql);
                $verb = "updated";

                $numBadges = (!empty($_POST["addlFName1"]) ? 1 : 0) + (!empty($_POST["addlFName2"]) ? 1 : 0) + (!empty($_POST["addlFName3"]) ? 1 : 0) + (!empty($_POST["addlFName4"]) ? 1 : 0)
                    + (!empty($_POST["addlFName5"]) ? 1 : 0);

                $result = $db->query("SELECT FirstName, LastName, Email, CompanyName, Description, ContactEmail, OnlyUseThisEmail FROM DealerPresence dp " .
                    "JOIN Dealer d on d.DealerID = dp.DealerID JOIN People p ON d.PeopleID = p.PeopleID WHERE DealerPresenceID = $dealerPresenceID");
                $dealer = $result->fetch_array();
                $result->close();

                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->Port = 587;
                $mail->Host = "mail.capricon.org";
                $mail->Username = "outgoing@capricon.org";
                $mail->Password = $smtpPass;
                $mail->From = "registration@capricon.org";
                $mail->FromName = "Capricon Registration System";
                $mail->AddAddress("dealers@capricon.org", "Capricon Dealers");
                $mail->WordWrap = 135;
                $mail->Subject = "Updated Dealer Application Received (" . $dealer["CompanyName"] . ")";
                $msg = "Hello! This email is to let you know that a request to be a Dealer at the next Capricon has been updated:\r\n\r\n";
                $msg .= "Company: " . $dealer["CompanyName"] . "\r\n";
                $msg .= "Contact: " . $dealer["FirstName"] . " " . $dealer["LastName"] . "\r\n";
                $msg .= "Email: ";
                if($dealer["OnlyUseThisEmail"] == 1)
                    $msg .= $dealer["ContactEmail"] . "\r\n";
                else if(empty($dealer["ContactEmail"]))
                    $msg .= $dealer["Email"] . "\r\n";
                else
                    $msg .= $dealer["ContactEmail"] . " or " . $dealer["Email"] . "\r\n";
                $msg .= "Description: " . $dealer["Description"] . "\r\n";
                $msg .= "Tables Requested: $tableCount\r\n";
                $msg .= "Electricity Needed? " . ($electric == 1 ? "Yes" : "No") . "\r\n";
                $msg .= "# Extra Badges: $numBadges\r\n";
                if(!empty($addedDetails) && trim($addedDetails) != "")
                    $msg .= "Additional Details: $addedDetails\r\n";
                $msg .= "You may view or update this application by going to https://registration.capricon.org/manageDealers.php. \r\n\r\nSincerely,\r\nThe Capricon Registration System\r\n";
                $mail->Body = $msg;
                $mail->Send();
            }

            $db->query("DELETE FROM DealerBadges WHERE DealerPresenceID = $dealerPresenceID");

            if(!empty($_POST["addlFName1"])) {
                $first = $db->real_escape_string(trim($_POST["addlFName1"]));
                $last = $db->real_escape_string(trim($_POST["addlLName1"]));
                $badgeName = empty($_POST["addlBadge1"]) ? $first . " " . $last : $db->real_escape_string($_POST["addlBadge1"]);
                $badgeType = isset($_POST["addlKid1"]) ? "2" : "1";
                $price = isset($_POST["addlKid1"]) ? 0 : $config["BadgeFee"];
                $sql = "INSERT INTO DealerBadges (DealerPresenceID, BadgeName, FirstName, LastName, Price, BadgeTypeID) VALUES ($dealerPresenceID, '$badgeName', '$first', '$last', $price, $badgeType)";
                $db->query($sql);
            }
            if(!empty($_POST["addlFName2"])) {
                $first = $db->real_escape_string(trim($_POST["addlFName2"]));
                $last = $db->real_escape_string(trim($_POST["addlLName2"]));
                $badgeName = empty($_POST["addlBadge2"]) ? $first . " " . $last : $db->real_escape_string($_POST["addlBadge2"]);
                $badgeType = isset($_POST["addlKid2"]) ? "2" : "1";
                $price = isset($_POST["addlKid2"]) ? 0 : $config["BadgeFee"];
                $sql = "INSERT INTO DealerBadges (DealerPresenceID, BadgeName, FirstName, LastName, Price, BadgeTypeID) VALUES ($dealerPresenceID, '$badgeName', '$first', '$last', $price, $badgeType)";
                $db->query($sql);
            }
            if(!empty($_POST["addlFName3"])) {
                $first = $db->real_escape_string(trim($_POST["addlFName3"]));
                $last = $db->real_escape_string(trim($_POST["addlLName3"]));
                $badgeName = empty($_POST["addlBadge3"]) ? $first . " " . $last : $db->real_escape_string($_POST["addlBadge3"]);
                $badgeType = isset($_POST["addlKid3"]) ? "2" : "1";
                $price = isset($_POST["addlKid3"]) ? 0 : $config["BadgeFee"];
                $sql = "INSERT INTO DealerBadges (DealerPresenceID, BadgeName, FirstName, LastName, Price, BadgeTypeID) VALUES ($dealerPresenceID, '$badgeName', '$first', '$last', $price, $badgeType)";
                $db->query($sql);
            }
            if(!empty($_POST["addlFName4"])) {
                $first = $db->real_escape_string(trim($_POST["addlFName4"]));
                $last = $db->real_escape_string(trim($_POST["addlLName4"]));
                $badgeName = empty($_POST["addlBadge4"]) ? $first . " " . $last : $db->real_escape_string($_POST["addlBadge4"]);
                $badgeType = isset($_POST["addlKid4"]) ? "2" : "1";
                $price = isset($_POST["addlKid4"]) ? 0 : $config["BadgeFee"];
                $sql = "INSERT INTO DealerBadges (DealerPresenceID, BadgeName, FirstName, LastName, Price, BadgeTypeID) VALUES ($dealerPresenceID, '$badgeName', '$first', '$last', $price, $badgeType)";
                $db->query($sql);
            }
            if(!empty($_POST["addlFName5"])) {
                $first = $db->real_escape_string(trim($_POST["addlFName5"]));
                $last = $db->real_escape_string(trim($_POST["addlLName5"]));
                $badgeName = empty($_POST["addlBadge5"]) ? $first . " " . $last : $db->real_escape_string($_POST["addlBadge5"]);
                $badgeType = isset($_POST["addlKid5"]) ? "2" : "1";
                $price = isset($_POST["addlKid5"]) ? 0 : $config["BadgeFee"];
                $sql = "INSERT INTO DealerBadges (DealerPresenceID, BadgeName, FirstName, LastName, Price, BadgeTypeID) VALUES ($dealerPresenceID, '$badgeName', '$first', '$last', $price, $badgeType)";
                $db->query($sql);
            }

            echo '{ "success": true, "message": "The application has been successfully ' . $verb . '." }';
        }
        catch (Exception $e)
        {
            echo '{ "success": false, "message": "An unknown error has occurred, please contact it@capricon.org. [Code 2]" }';
            error_log("Unknown error occurred handling Dealer Application Save/Edit: [Line " . $e->getLine() . "] " . $e->getMessage());
        }
    }
    else if($task == "UpdateDealerApplications") {
        $requests = $db->real_escape_string($_POST["requests"]);
        $status = $db->real_escape_string($_POST["status"]);
        $message = $db->real_escape_string($_POST["message"]);
        $message = str_replace("\\n", "\r\n", $message);
        $reason = "'" . $db->real_escape_string(trim($_POST["reason"])) . "'";
        $clearReason = (isset($_POST["clearReason"]) && $_POST["clearReason"] == "true");
        if($reason == "''" && !$clearReason)
            $reason = "null";
        $noEmail = (isset($_POST["noEmail"]) && $_POST["noEmail"] == "true");
        $presenceIDs = explode(", ", $requests);

        $result = $db->query("SELECT ElectricFee, BadgeFee FROM DealerConfig WHERE Year = $year");
        $config = $result->fetch_array();
        $result->close();
        $electricFee = $config["ElectricFee"];
        $badgeFee = $config["BadgeFee"];

        if($status == "NoChange") {
            if($reason != "null")
                $db->query("UPDATE DealerPresence SET StatusReason = $reason WHERE DealerPresenceID IN ($requests)");
            if(!empty($message) && !$noEmail) {
                foreach($presenceIDs as $presenceID) {
                    $result = $db->query("SELECT FirstName, LastName, Email, CompanyName, ContactEmail, OnlyUseThisEmail FROM DealerPresence dp " .
                        "JOIN Dealer d on d.DealerID = dp.DealerID JOIN People p ON d.PeopleID = p.PeopleID WHERE DealerPresenceID = $presenceID");
                    if($result->num_rows > 0)
                    {
                        $dealer = $result->fetch_array();
                        $result->close();
                    }
                    else {
                        echo '{ "success": false, "message": "An unknown application ID was passed." }';
                        return;
                    }

                    $mail = new PHPMailer;
                    $mail->isSMTP();
                    $mail->SMTPAuth = true;
                    $mail->Port = 587;
                    $mail->Host = "mail.capricon.org";
                    $mail->Username = "outgoing@capricon.org";
                    $mail->Password = $smtpPass;
                    $mail->From = "dealers@capricon.org";
                    $mail->FromName = "Capricon Dealers";
                    if($dealer["OnlyUseThisEmail"] == 0)
                        $mail->AddAddress($dealer["Email"], $dealer["FirstName"] . " " . $dealer["LastName"]);
                    if(!empty($dealer["ContactEmail"]))
                        $mail->AddAddress($dealer["ContactEmail"], $dealer["CompanyName"]);
                    $mail->WordWrap = 135;
                    $mail->Subject = "A Message From the Capricon Dealers Team";
                    $msg = "Hello! This email is to let you know that the Dealers Team has sent the following information to you:\r\n\r\n$message\r\n\r\n";
                    $msg .= "If you have any questions, please contact the team at dealers@capricon.org!\r\n\r\nSincerely,\r\nThe Capricon Dealers Team\r\n";
                    $mail->Body = $msg;
                    $mail->Send();
                }
            }
        }
        else if($reason != "null")
            $db->query("UPDATE DealerPresence SET Status = '$status', StatusReason = $reason WHERE DealerPresenceID IN ($requests)");
        else
            $db->query("UPDATE DealerPresence SET Status = '$status' WHERE DealerPresenceID IN ($requests)");

        if($status == "Approved") {
            foreach($presenceIDs as $presenceID) {
                $result = $db->query("SELECT NumTables, ElectricalNeeded, Status, StatusReason, FirstName, LastName, Email, CompanyName, ContactEmail, OnlyUseThisEmail, p.PeopleID FROM DealerPresence dp " .
                    "JOIN Dealer d on d.DealerID = dp.DealerID JOIN People p ON d.PeopleID = p.PeopleID WHERE DealerPresenceID = $presenceID");
                if($result->num_rows > 0)
                {
                    $dealer = $result->fetch_array();
                    $result->close();
                }
                else {
                    echo '{ "success": false, "message": "An unknown application ID was passed." }';
                    return;
                }
                $currentReason = $dealer["StatusReason"];
                $tables = $dealer["NumTables"];
                $electric = $dealer["ElectricalNeeded"] == 1;

                $result = $db->query("SELECT Price FROM DealerTablePrices WHERE Quantity = $tables");
                $row = $result->fetch_array();
                $result->close();
                $tableFee = $row["Price"];

                $result = $db->query("SELECT BadgeID FROM PurchasedBadges WHERE Year = $year AND PeopleID = " . $_SESSION["PeopleID"]);
                if($result->num_rows > 0) {
                    $hasBadge = true;
                    $result->close();
                }
                else
                    $hasBadge = false;


                $badges = array();
                if($presenceID != "") {
                    $result = $db->query("SELECT BadgeName, FirstName, LastName, Price, BadgeTypeID FROM DealerBadges WHERE DealerPresenceID = $presenceID");
                    while($row = $result->fetch_array())
                        $badges[] = $row;
                    $result->close();
                }

                $result = $db->query("SELECT InvoiceID, Status FROM Invoice WHERE InvoiceType = 'Dealer' AND RelatedRecordID = $presenceID AND Status != 'Cancelled' ORDER BY Created DESC LIMIT 1");
                if($result->num_rows == 0) {
                    $peopleID = $dealer["PeopleID"];
                    $db->query("INSERT INTO Invoice (PeopleID, InvoiceType, RelatedRecordID, Status, Created) VALUES ($peopleID, 'Dealer', $presenceID, 'Created', NOW())");
                    $invoiceID = $db->insert_id;
                    $invoiceStatus = "Created";

                    $lineNum = 1;
                    $db->query("INSERT INTO InvoiceLine (InvoiceID, LineNumber, Description, Price, Tax) VALUES ($invoiceID, " . $lineNum++ . ", 'Fee for $tables Table" . ($table == 1 ? "" : "s") . "', $tableFee, 0)");
                    if($electric) {
                        $db->query("INSERT INTO InvoiceLine (InvoiceID, LineNumber, Description, Price, Tax) VALUES ($invoiceID, " . $lineNum++ . ", 'Fee for Electricity', $electricFee, 0)");
                    }
                    if(!$hasBadge)
                        $db->query("INSERT INTO InvoiceLine (InvoiceID, LineNumber, Description, Price, Tax) VALUES ($invoiceID, " . $lineNum++ . ", 'Dealer\\'s Badge Fee', $badgeFee, 0)");
                    foreach($badges as $badge)
                        $db->query("INSERT INTO InvoiceLine (InvoiceID, LineNumber, Description, Price, Tax) VALUES ($invoiceID, " . $lineNum++ . ", 'Badge Fee for \"" . $db->real_escape_string($badge["BadgeName"]) . "\"', " . $badge["Price"] . ", 0)");
                }
                else {
                    $row = $result->fetch_array();
                    $invoiceID = $row["InvoiceID"];
                    $invoiceStatus = $row["Status"];
                    $result->close();
                }

                if($invoiceStatus == "Created" && !$noEmail) {
                    $invoiceMessage = "";
                    $result = $db->query("SELECT LineNumber, Description, Price, Tax FROM InvoiceLine WHERE InvoiceID = $invoiceID ORDER BY LineNumber ASC");
                    $invoicePrice = 0.0;
                    $invoiceTax = 0.0;
                    while($row = $result->fetch_array()) {
                        $invoicePrice += $row["Price"];
                        $invoiceTax += $row["Tax"];
                        $invoiceMessage .= $row["LineNumber"] . ": " . $row["Description"] . " @ " . sprintf("$%01.2f", $row["Price"]);
                        if($row["Tax"] != 0)
                            $invoiceMessage .= " (+ " + sprintf("$%01.2f", $row["Tax"]) + " Tax)";
                        $invoiceMessage .= "\r\n";
                    }
                    $result->close();
                    if($invoiceTax != 0)
                        $invoiceMessage .= "Sub Total: " . sprintf("$%01.2f", $invoicePrice) . "\r\nIL Tax: " . sprintf("$%01.2f", $invoiceTax) . "\r\nTotal Due: "
                            . sprintf("$%01.2f", $invoicePrice + $invoiceTax) . "\r\n";
                    else
                        $invoiceMessage .= "Total Due: " . sprintf("$%01.2f", $invoicePrice) . "\r\n";

                    $mail = new PHPMailer;
					$mail->isSMTP();
					$mail->SMTPAuth = true;
					$mail->Port = 587;
					$mail->Host = "mail.capricon.org";
					$mail->Username = "outgoing@capricon.org";
					$mail->Password = $smtpPass;
					$mail->From = "dealers@capricon.org";
					$mail->FromName = "Capricon Dealers";
                    if($dealer["OnlyUseThisEmail"] == 0)
                        $mail->AddAddress($dealer["Email"], $dealer["FirstName"] . " " . $dealer["LastName"]);
                    if(!empty($dealer["ContactEmail"]))
                        $mail->AddAddress($dealer["ContactEmail"], $dealer["CompanyName"]);
					$mail->WordWrap = 70;
					$mail->Subject = "Invoice for your Capricon Dealer's Hall Application";
                    $msg = "Hello! This email is to let you know that your request to be a Dealer at the next Capricon has been approved. ";
                    if(!empty($message))
                        $msg .= "The Dealers Team has included the following message for you:\r\n\r\n$message\r\n\r\n";
                    $msg .= "An invoice has been generated based on your request, and the details are as follows:\r\n\r\n";
                    $msg .= $invoiceMessage . "\r\n";
                    $msg .= "You may make a payment online within the Registration system at https://registration.capricon.org/invoices.php "
                        . "using a credit card, Paypal, or mailed-in check. If you wish to pay this invoice with a check, select the 'Mail-In Payment' option when paying "
                        . "the invoice. This will let us know you will be mailing in a check for the payment, and it will send you an email with a PDF to print out and "
                        . "send to us with your payment.\r\n\r\nThank you so much for considering Capricon for selling your products!\r\n\r\nSincerely,\r\nThe Capricon Dealers Team";
                    $mail->Body = $msg;
                    $mail->Send();

                    $db->query("UPDATE Invoice SET Status = 'Sent', Sent = NOW() WHERE InvoiceID = $invoiceID");

                }

            }
        }
        else if($status == "Rejected") {
            foreach($presenceIDs as $presenceID) {
                $result = $db->query("SELECT FirstName, LastName, Email, CompanyName, ContactEmail, OnlyUseThisEmail FROM DealerPresence dp " .
                    "JOIN Dealer d on d.DealerID = dp.DealerID JOIN People p ON d.PeopleID = p.PeopleID WHERE DealerPresenceID = $presenceID");
                if($result->num_rows > 0)
                {
                    $dealer = $result->fetch_array();
                    $result->close();
                }
                else {
                    echo '{ "success": false, "message": "An unknown application ID was passed." }';
                    return;
                }

                $result = $db->query("SELECT InvoiceID, Status FROM Invoice WHERE InvoiceType = 'Dealer' AND RelatedRecordID = $presenceID ORDER BY Created DESC LIMIT 1");
                if($result->num_rows > 0) {
                    $row = $result->fetch_array();
                    $invoiceID = $row["InvoiceID"];
                    $invoiceStatus = $row["Status"];
                    $result->close();

                    if($invoiceStatus == "Paid") {
                        echo '{ "success": false, "message": "This application has a paid invoice and cannot be modified." }';
                        return;
                    }
                    $db->query("UPDATE Invoice SET Status = 'Cancelled', Cancelled = NOW() WHERE InvoiceID = $invoiceID");
                }

                if(!$noEmail) {
                    $mail = new PHPMailer;
                    $mail->isSMTP();
                    $mail->SMTPAuth = true;
                    $mail->Port = 587;
                    $mail->Host = "mail.capricon.org";
                    $mail->Username = "outgoing@capricon.org";
                    $mail->Password = $smtpPass;
                    $mail->From = "dealers@capricon.org";
                    $mail->FromName = "Capricon Dealers";
                    if($dealer["OnlyUseThisEmail"] == 0)
                        $mail->AddAddress($dealer["Email"], $dealer["FirstName"] . " " . $dealer["LastName"]);
                    if(!empty($dealer["ContactEmail"]))
                        $mail->AddAddress($dealer["ContactEmail"], $dealer["CompanyName"]);
                    $mail->WordWrap = 70;
                    $mail->Subject = "Rejection of your Capricon Dealer's Hall Application";
                    $msg = "Hello! This email is to let you know that your request to be a Dealer at the next Capricon has been rejected. ";
                    if(!empty($message))
                        $msg .= "The Dealers Team has included the following message for you:\r\n\r\n$message\r\n\r\n";
                    $msg .= "If you have any questions, please contact the team at dealers@capricon.org!\r\n\r\nSincerely,\r\nThe Capricon Dealers Team\r\n";
                    $mail->Body = $msg;
                    $mail->Send();
                }
            }
        }
        else if($status == "Waitlist" || $status == "Pending") {
            foreach($presenceIDs as $presenceID) {
                $result = $db->query("SELECT FirstName, LastName, Email, CompanyName, ContactEmail, OnlyUseThisEmail FROM DealerPresence dp " .
                    "JOIN Dealer d on d.DealerID = dp.DealerID JOIN People p ON d.PeopleID = p.PeopleID WHERE DealerPresenceID = $presenceID");
                if($result->num_rows > 0)
                {
                    $dealer = $result->fetch_array();
                    $result->close();
                }
                else {
                    echo '{ "success": false, "message": "An unknown application ID was passed." }';
                    return;
                }

                $result = $db->query("SELECT InvoiceID, Status FROM Invoice WHERE InvoiceType = 'Dealer' AND RelatedRecordID = $presenceID ORDER BY Created DESC LIMIT 1");
                if($result->num_rows > 0) {
                    $row = $result->fetch_array();
                    $invoiceID = $row["InvoiceID"];
                    $invoiceStatus = $row["Status"];
                    $result->close();
                    if($invoiceStatus == "Paid") {
                        echo '{ "success": false, "message": "This application has a paid invoice and cannot be modified." }';
                        return;
                    }
                    $db->query("UPDATE Invoice SET Status = 'Cancelled', Cancelled = NOW() WHERE InvoiceID = $invoiceID");
                }

                if(!$noEmail) {
                    $mail = new PHPMailer;
                    $mail->isSMTP();
                    $mail->SMTPAuth = true;
                    $mail->Port = 587;
                    $mail->Host = "mail.capricon.org";
                    $mail->Username = "outgoing@capricon.org";
                    $mail->Password = $smtpPass;
                    $mail->From = "dealers@capricon.org";
                    $mail->FromName = "Capricon Dealers";
                    if($dealer["OnlyUseThisEmail"] == 0)
                        $mail->AddAddress($dealer["Email"], $dealer["FirstName"] . " " . $dealer["LastName"]);
                    if(!empty($dealer["ContactEmail"]))
                        $mail->AddAddress($dealer["ContactEmail"], $dealer["CompanyName"]);
                    $mail->WordWrap = 70;
                    if($status == "Pending") {
                        $mail->Subject = "Return of your Capricon Dealer's Hall Application";
                        $msg = "Hello! This email is to let you know that your request to be a Dealer at the next Capricon has been returned to you in the "
                            . "'Pending' status. You can now make edits as you wish. If there was an unpaid Invoice, it has been cancelled. Please contact us "
                            . "when you have finished making your changes. ";
                        if(!empty($message))
                            $msg .= "The Dealers Team has included the following message for you:\r\n\r\n$message\r\n\r\n";
                        $msg .= "If you have any questions, please contact the team at dealers@capricon.org!\r\n\r\nSincerely,\r\nThe Capricon Dealers Team\r\n";
                    }
                    else {
                        $mail->Subject = "Waitlisting of your Capricon Dealer's Hall Application";
                        $msg = "Hello! This email is to let you know that your request to be a Dealer at the next Capricon has been waitlist. ";
                        if(!empty($message))
                            $msg .= "The Dealers Team has included the following message for you:\r\n\r\n$message\r\n\r\n";
                        $msg .= "If you have any questions, please contact the team at dealers@capricon.org!\r\n\r\nSincerely,\r\nThe Capricon Dealers Team\r\n";
                    }
                    $mail->Body = $msg;
                    $mail->Send();
                }
            }
        }
        echo '{ "success": true, "message": "The application has been successfully updated.' . ($noEmail ? ' An email was not generated for this update.' : "") . '" }';
    }
	else
		echo '{ "success": false, "message": "Unknown request submitted." }';

?>