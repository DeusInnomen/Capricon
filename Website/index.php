<?php
session_start();
include_once('includes/functions.php');
$interests = UserInterests();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Capricon Registration System -- Main Menu</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="includes/style.css?<?php echo filemtime("includes/style.css"); ?>" />
    
    <link rel="icon" href="includes/favicon.png" />
    <link rel="shortcut icon" href="includes/favicon.ico" />
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="includes/global.js"></script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content">
        <?php if(!isset($_SESSION["PeopleID"])) { ?>
        <div class="infobox">
            <span>
                Welcome to the Capricon Registration System website. This site will allow you to create an account that will
			be used to manage your Capricon experience, including purchasing and reviewing badges, showing interest in certain
			activities like volunteering or being a dealer or participating in the art show, and other functionality.
                <br />
                <br />If you
			would rather not create an account, you can print out a blank registration form and mail it in. Use the "Download a Blank
			Registration Form (PDF)" link to open a PDF you can print, fill out, and mail in.
            </span>
        </div>
        <?php } ?>
        <div class="centerboxnarrow">
            <div class="menubar">
                <h3>
                    Capricon Registration
                    <br />Main Menu
                </h3>
                <ul>
                    <?php	if(isset($_SESSION["PeopleID"]))
                             {
                                 echo '<div class="headertitle">Your Profile</div>' . "\r\n";
                                 echo '<li><a href="manageAccount.php">Manage Your Account</a></li>' . "\r\n";
                                 echo '<li><a href="manageInterests.php">Manage Convention Interests</a></li>' . "\r\n";
                                 echo '<li><a href="manageRelatedPeople.php">Manage Related People</a></li>' . "\r\n";
                                 echo '<li><a href="manageMailingLists.php">Manage Mailing List Subscriptions</a></li>' . "\r\n";
                                 echo '<hr><div class="headertitle">Convention Participation</div>' . "\r\n";
                                 //echo '<li><a href="https://zambia.capricon.org" target="_new">Program Participant Application</a></li>' . "\r\n";
                                 echo '<li><a href="https://forms.gle/jmDXVuWxMN3zxQi1A" target="_new">Program Participant Application</a></li>' . "\r\n";                                 
                                 if(DoesUserBelongHere("Program"))
                                 {
                                     echo '<hr><div class="headertitle">Programming Team</div>' . "\r\n";
                                     echo '<li><a href="getProgramIdeasCSV.php">Download Capricon ' . (date("Y") - 1979) . ' Program Panel Ideas (CSV)</a></li>' . "\r\n";
                                     echo '<li><a href="programSurveyResponses.php">View Programming Survey Responses</a></li>' . "\r\n";
                                     echo '<li><a href="getProgramIdeasCSV.php">View Program Panel Ideas Responses</a></li>' . "\r\n";
                                 }
                                 echo '<hr><div class="headertitle">Your Purchases</div>' . "\r\n";
                                 echo '<li><a href="badges.php">Purchase Badges and Other Items</a></li>' . "\r\n";
                                 echo '<li><a href="giftCerts.php">View or Purchase Gift Certificates</a></li>' . "\r\n";
                                 echo '<li><a href="purchases.php">View Your Purchase History</a></li>' . "\r\n";
                                 echo '<li><a href="invoices.php">Manage Your Invoices</a></li>' . "\r\n";
                                 if(DoesUserBelongHere("Artist"))
                                 {
                                     echo '<hr><div class="headertitle">Artist Information</div>' . "\r\n";
                                     echo '<li><a href="artistInformation.php">Exhibitor Details</a></li>' . "\r\n";
                                     echo '<li><a href="artistSubmissions.php">Your Artwork Inventory</a></li>' . "\r\n";
                                 }
                                 if(DoesUserBelongHere("ArtShowStaff"))
                                 {
                                     echo '<hr><div class="headertitle">Art Show Functions</div>' . "\r\n";
                                     if(DoesUserBelongHere("ArtShowLead")) {
                                         echo '<li><a href="manageArtists.php">Manage Artists List</a></li>' . "\r\n";
                                         echo '<li><a href="manageCharities.php">Manage Charities</a></li>' . "\r\n";
                                     }
                                     echo '<li><a href="manageArtistRequests.php">View Requests to Show Art</a></li>' . "\r\n";
                                     echo '<li><a href="artistExhibitsSummary.php">Artist Exhibits Summary</a></li>' . "\r\n";
                                     echo '<li><a href="showArtshowSummary.php">Art Show Overall Summary</a></li>' . "\r\n";
                                     echo '<li><a href="artShowEnterFinalBids.php">Enter Final Auction Bids</a></li>' . "\r\n";
                                 }
                                 if(DoesUserBelongHere("Dealer"))
                                 {
                                     echo '<hr><div class="headertitle">Dealer Information</div>' . "\r\n";
                                     echo '<li><a href="dealerDetails.php">Dealer Business Details</a></li>' . "\r\n";
                                     echo '<li><a href="dealerApplication.php">Application for Capricon Dealer\'s Hall</a></li>' . "\r\n";
                                 }
                                 if(DoesUserBelongHere("DealerStaff"))
                                 {
                                     echo '<hr><div class="headertitle">Dealer Staff Functions</div>' . "\r\n";
                                     if(DoesUserBelongHere("DealerLead")) {
                                         echo '<li><a href="manageDealers.php">Manage Dealers</a></li>' . "\r\n";
                                         echo '<li><a href="manageDealerApps.php">Manage Dealer Applications</a></li>' . "\r\n";
                                         echo '<li><a href="dealerConfig.php">Dealer Application Configuration</a></li>' . "\r\n";
                                         echo '<li><a href="dealerTablePrices.php">Configure Dealer Table Prices</a></li>' . "\r\n";
                                     }
                                     echo '<li><a href="dealerReportApproved.php">Download Approved Dealer Report (CSV)</a></li>' . "\r\n";
                                     echo '<li><a href="dealerReportAll.php">Download Full Dealer Report (CSV)</a></li>' . "\r\n";
                                 }
                                 if(DoesUserBelongHere("Marketing"))
                                 {
                                     echo '<hr><div class="headertitle">Marketing</div>' . "\r\n";
                                     echo '<li><a href="getMailingAddressList.php">Download Mailing Address List (CSV)</a></li>' . "\r\n";
                                 }
                                 if(DoesUserBelongHere("RegLead") || DoesUserBelongHere("Ops"))
                                 {
                                     echo '<hr><div class="headertitle">Administrative Functions</div>' . "\r\n";
                                     if(DoesUserBelongHere("Treasurer"))
                                         echo '<li><a href="promoCodes.php">Promotional Codes</a></li>' . "\r\n";
                                     echo '<li><a href="manageAllAccounts.php">Manage Member Accounts</a></li>' . "\r\n";
                                     if(DoesUserBelongHere("RegLead"))
                                         echo '<li><a href="giftCertsAdmin.php">Manage Gift Certificates</a></li>' . "\r\n";
                                 }
								 if(DoesUserBelongHere("RegStaff") || DoesUserBelongHere("Marketing")) {
									echo '<hr><div class="headertitle">Registration Functions</div>' . "\r\n";
									 echo '<li><a href="viewRegistrations.php">View Current Registrations</a></li>' . "\r\n";
									 if(DoesUserBelongHere("RegLead") || DoesUserBelongHere("Marketing"))
										echo '<li><a href="enterRegistrations.php">Enter Manual Registrations</a></li>' . "\r\n";
									 echo '<li><a href="registrationStats.php">Registration Statistics</a></li>' . "\r\n";
									 echo '<li><a href="registrationStatsGraph.php">Registration Statistics Graph</a></li>' . "\r\n";
									 echo '<li><a href="getCurrentRegCSV.php">Download Registration List (CSV)</a></li>' . "\r\n";
									 if(DoesUserBelongHere("RegLead"))
									 {
										 echo '<li><a href="badgeRates.php">View Available Badges and Rates</a></li>' . "\r\n";
										 echo '<li><a href="viewPendingRegistrations.php">View Pending Registrations</a></li>' . "\r\n";
										 echo '<li><a href="issueCompBadges.php">Issue Complimentary Badges</a></li>' . "\r\n";
										 echo '<li><a href="staffBadges.php">Issue Staff Badges</a></li>' . "\r\n";
										 echo '<li><a href="promoCodesUsed.php">View Promo Codes Used</a></li>' . "\r\n";
									 }
									 if(DoesUserBelongHere("Treasurer")) {
										 echo '<hr><div class="headertitle">Treasurer Functions</div>' . "\r\n";
										 echo '<li><a href="manageMailedInvoices.php">Manage Mailed Invoice Payments</a></li>' . "\r\n";
										 echo '<li><a href="artShowSalesSummary.php">Art Show Sales Summary</a></li>' . "\r\n";
                                         echo '<li><a href="artistSalesTotal.php">Art Show Checks Owed</a></li>' . "\r\n";
                                         echo '<li><a href="artistSalesWithoutPrintShop.php">Art Show Checks Owed (Auction Only)</a></li>' . "\r\n";
                                         echo '<li><a href="artistSalesWithPrintShop.php">Art Show Checks Owed (Print Shop Only)</a></li>' . "\r\n";
										 echo '<li><a href="registrationStats.php">Registration Statistics</a></li>' . "\r\n";
										 echo '<li><a href="registrationSalesSummary.php">Registration Sales Summary</a></li>' . "\r\n";
										 echo '<li><a href="treasurerStats.php">Treasurer Statistics</a></li>' . "\r\n";
									 }
								 }
                                 echo '<hr><li><a href="logout.php">Log Out</a></li>' . "\r\n";
                                 echo '</ul>';
                             }
                             else
                             {
                                 echo '<div class="headertitle">Need an Account?</div>' . "\r\n";
                                 echo '<li><a href="register.php">Register a New Account</a></li>' . "\r\n";
                                 echo '<div class="headertitle">Already Have An Account?</div>' . "\r\n";
                                 echo '<li><a href="login.php">Log In To Your Account</a></li>' . "\r\n";
                                 echo '<li><a href="forgotpassword.php">Reset Your Account Password</a></li>' . "\r\n";
                                 echo "<hr>\r\n";
                                 echo '<div class="headertitle">Other Useful Links</div>' . "\r\n";
                                 echo '<li><a href="manageMailingLists.php">Manage Mailing List Subscriptions</a></li>' . "\r\n";
                                 echo "<hr>\r\n";
                                 echo '<li><a href="blankRegForm.php">Download a Blank Registration Form (PDF)</a></li>' . "\r\n";
                                 echo '</ul>' . "\r\n";
                                 echo '<div style="margin-top: 40px;"></div>' . "\r\n";
                                 echo '<p style="position: absolute; left: 10px; bottom: 0px; right: 10px; font-size: 0.6em;">Notice: This site requires ' . "\r\n";
                                 echo 'JavaScript to run. If you are using blockers such as NoScript, please consider whitelisting this ' . "\r\n";
								echo 'domain or you may have problems using the site. Thank you for your understanding.</p>' . "\r\n";
							} ?>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>
