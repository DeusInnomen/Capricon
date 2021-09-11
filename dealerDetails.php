<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
elseif(!DoesUserBelongHere("Dealer"))
    header('Location: index.php');
else
{
    $year = date("n") >= 3 ? date("Y") + 1: date("Y");
    $result = $db->query("SELECT DealerID, CompanyName, LegalName, URL, ContactEmail, OnlyUseThisEmail, Description, Address1, Address2, Address3, City, State, ZipCode, Country, " .
        "Phone, PhoneType, TaxNumber FROM Dealer WHERE PeopleID = " . $_SESSION["PeopleID"]);

    if($result->num_rows > 0)
    {
        $info = $result->fetch_array();
        $result->close();
    }
    else
    {
        $info = array();
        $info["DealerID"] = "";
        $info["CompanyName"] = "";
        $info["LegalName"] = "";
        $info["URL"] = "";
        $info["ContactEmail"] = "";
        $info["OnlyUseThisEmail"] = 1;
        $info["Description"] = "";
        $info["Address1"] = "";
        $info["Address2"] = "";
        $info["Address3"] = "";
        $info["City"] = "";
        $info["State"] = "";
        $info["ZipCode"] = "";
        $info["Country"] = "USA";
        $info["Phone"] = "";
        $info["PhoneType"] = "Work";
        $info["TaxNumber"] = "";
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Dealer Information</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
    <link rel="icon" href="includes/favicon.png" />
    <link rel="shortcut icon" href="includes/favicon.ico" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="includes/global.js"></script>
    <script type="text/javascript">
		$(document).ready(function() {
			$("select#state option").each(function() { this.selected = (this.value == "<?php echo $info["State"]; ?>"); });
			$("select#phonetype option").each(function() { this.selected = (this.text == "<?php echo $info["PhoneType"]; ?>"); });
			$("#detailsForm").submit(function () {
				$("#detailsForm :input").prop("readonly", true);
				$("#description").prop("readonly", true);
				$("#accountSaveMessage").removeClass("goodMessage");
				$("#accountSaveMessage").removeClass("errorMessage");
				$("#accountSaveMessage").html("&nbsp;");
				$.post("doDealerFunctions.php", $(this).serialize(), function(result) {
					$("#accountSaveMessage").html(result.message);
					if(result.success)
						$("#accountSaveMessage").addClass("goodMessage");
					else
						$("#accountSaveMessage").addClass("errorMessage");
					$("#detailsForm :input").prop("readonly", false);
					$("#description").prop("readonly", false);
				}, 'json');
				return false;
			});
		});
    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <div class="centerboxmedium">
            <div style="margin-bottom: 30px;">
                <form id="detailsForm" class="accountForm" method="post" action="">
                    <h1>Dealer Information</h1>
                    <p>Please fill in the following information about the business you represent and wish to bring to the Capricon Dealer's Hall. Once this information
                    has been saved, you may place an application to present at the upcoming Capricon when the application period is open.</p>
                    <label for="companyName" class="fieldLabelShort"><span class="requiredField">*</span>Company Name: </label><br />
                    <input type="text" name="companyName" id="displayName" placeholder="Required" style="width: 98%;" value="<?php echo $info["CompanyName"]; ?>" /><br />
                    <label for="legalName" class="fieldLabelShort"><span class="requiredField">*</span>Legal Name: </label><br />
                    <input type="text" name="legalName" id="legalName" placeholder="Required" style="width: 98%;" value="<?php echo $info["LegalName"]; ?>" /><br />
                    <label for="url" class="fieldLabelShort">Website URL: </label><br />
                    <input type="text" name="url" id="url" placeholder="Will Be Linked On Our Website" style="width: 98%;" value="<?php echo $info["URL"]; ?>" /><br />
                    <label for="contactEmail" class="fieldLabelShort">Business Contact Email: </label><br />
                    <input type="email" name="contactEmail" id="contactEmail" placeholder="Only If Different Than Login Email" style="width: 70%;" value="<?php echo $info["ContactEmail"]; ?>" /><br />
                    <label for="onlyThisEmail">Please Contact Me Only At The Above Address <input type="checkbox" name="onlyThisEmail" id="onlyThisEmail" <?php if($request["OnlyUseThisEmail"] == 1) echo "checked"; ?>/></label><br />
                    <span style="font-style: italic; font-size: 0.8em;">If the above checkbox is not checked, we will send emails related to your application to the above email address (if provided)
                    and to your profile's email address.</span><br />
                    <label for="description" class="fieldLabelShort">Company Description: </label><br />
                    <textarea id="description" name="description" maxlength="500" rows="4" placeholder="To Be Shared On Our Website And Program Book, If Provided" style="width: 98%;"><?php echo $info["Description"]; ?></textarea>
                    <label><span class="requiredField">*</span>Address (Line 1): </label><br />
                    <input type="text" name="address1" id="address1" style="width: 70%;" placeholder="Required" value="<?php echo $info["Address1"]; ?>" /><br />
                    <label>Address (Line 2): </label><br />
                    <input type="text" name="address2" id="address2" style="width: 70%;" value="<?php echo $info["Address2"]; ?>" /><br />
                    <label>Address (Line 3): </label><br />
                    <input type="text" name="address3" id="address3" style="width: 70%;" value="<?php echo $info["Address3"]; ?>" /><br />
                    <label for="city" class="fieldLabelShort"><span class="requiredField">*</span>City: </label><br />
                    <input type="text" name="city" id="city" placeholder="Required" style="width: 40%;" value="<?php echo $info["City"]; ?>" /><br />
                    <label for="state" class="fieldLabelShort">State: </label><br />
                    <select id="state" name="state" style="width: 40%;">
                        <option value="AK">AK - Alaska</option>
                        <option value="AL">AL - Alabama</option>
                        <option value="AR">AR - Arkansas</option>
                        <option value="AZ">AZ - Arizona</option>
                        <option value="CA">CA - California</option>
                        <option value="CO">CO - Colorado</option>
                        <option value="CT">CT - Connecticut</option>
                        <option value="DC">DC - District Of Columbia</option>
                        <option value="DE">DE - Delaware</option>
                        <option value="FL">FL - Florida</option>
                        <option value="GA">GA - Georgia</option>
                        <option value="HI">HI - Hawaii</option>
                        <option value="IA">IA - Iowa</option>
                        <option value="ID">ID - Idaho</option>
                        <option value="IL">IL - Illinois</option>
                        <option value="IN">IN - Indiana</option>
                        <option value="KS">KS - Kansas</option>
                        <option value="KY">KY - Kentucky</option>
                        <option value="LA">LA - Louisiana</option>
                        <option value="MA">MA - Massachusetts</option>
                        <option value="MD">MD - Maryland</option>
                        <option value="ME">ME - Maine</option>
                        <option value="MI">MI - Michigan</option>
                        <option value="MN">MN - Minnesota</option>
                        <option value="MO">MO - Missouri</option>
                        <option value="MS">MS - Mississippi</option>
                        <option value="MT">MT - Montana</option>
                        <option value="NC">NC - North Carolina</option>
                        <option value="ND">ND - North Dakota</option>
                        <option value="NE">NE - Nebraska</option>
                        <option value="NH">NH - New Hampshire</option>
                        <option value="NJ">NJ - New Jersey</option>
                        <option value="NM">NM - New Mexico</option>
                        <option value="NV">NV - Nevada</option>
                        <option value="NY">NY - New York</option>
                        <option value="OH">OH - Ohio</option>
                        <option value="OK">OK - Oklahoma</option>
                        <option value="OR">OR - Oregon</option>
                        <option value="PA">PA - Pennsylvania</option>
                        <option value="RI">RI - Rhode Island</option>
                        <option value="SC">SC - South Carolina</option>
                        <option value="SD">SD - South Dakota</option>
                        <option value="TN">TN - Tennessee</option>
                        <option value="TX">TX - Texas</option>
                        <option value="UT">UT - Utah</option>
                        <option value="VA">VA - Virginia</option>
                        <option value="VT">VT - Vermont</option>
                        <option value="WA">WA - Washington</option>
                        <option value="WI">WI - Wisconsin</option>
                        <option value="WV">WV - West Virgina</option>
                        <option value="WY">WY - Wyoming</option>
                        <option value="">Other/Unused</option>
                    </select><br />
                    <label for="zip" class="fieldLabelShort"><span class="requiredField">*</span>Zip Code: </label><br />
                    <input type="text" name="zip" id="zip" size="10" maxlength="10" placeholder="Required" style="width: 20%;" value="<?php echo $info["ZipCode"]; ?>" /><br />
                    <label for="country" class="fieldLabelShort">Country:</label><br />
                    <input type="text" name="country" id="country" style="width: 40%;" value="<?php echo $info["Country"]; ?>" /><br />
                    <label for="phone" class="fieldLabelShort">Phone Number: </label><br />
                    <input type="tel" name="phone" id="phone" style="width: 40%;" value="<?php echo $info["Phone"]; ?>" />
                    <select id="phonetype" name="phonetype">
                        <option value="Home">Home</option>
                        <option value="Mobile">Mobile</option>
                        <option value="Work">Work</option>
                        <option value="Other">Other</option>
                    </select><br />
                    <label for="taxNumber" class="fieldLabelShort">Illinois State Tax Number: </label>
                    <br />
                    <input type="text" name="taxNumber" id="taxNumber" style="width: 40%;" value="<?php echo $info["TaxNumber"]; ?>" />
                    <br />
                    <br />
                    <input type="hidden" name="task" value="SaveDealerDetails" />
                    <input style="float: right;" type="submit" name="save" value="Save Information" />
                    <br />
                    <span id="accountSaveMessage">&nbsp;</span>
                    <br />
                </form>
            </div>
            <div class="goback">
                <a href="index.php">Return to the Main Menu</a>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>
