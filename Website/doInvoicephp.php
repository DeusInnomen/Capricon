<?php
session_start();
include_once('includes/functions.php');
if(!isset($_SESSION["PeopleID"]))
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));

$action = $_POST["action"];

if(isset($action))
{
    switch($action)
    {
        case "CreateInvoices":

            //This function will go through and create new invoices for ones that aren't generated yet.
            //Also a good example of using the posting tools:

            //We'll pull any outstanding things that need invoicing using this query (any return set means there's work to be done):
            //We include all detail in these lines, then GROUP and LIMIT the columns for inserts and updates.

            //These are the Posts I was figureing we would start with:
            //Unsold Art Show Items - Pricing with Taxes
            //Dealers - Dealer Fee, Badge or badge packages,

            //Unsold Art Show Items Detail
            //Assuming that QuantitySold is set when the con is over, and QuantitySent is the amount that has been invoiced.
            $sql = "SELECT "
                ." PurchaserBadgeID as peopleID " //Header Info
                ." ,PurchasedBadges.BadgeName as BadgeName "
                ." ,People.Email as Email "
                ." ,People.Address1 as B_Address1 "
                ." ,People.Address2 as B_Address2 "
                ." ,People.City as B_City "
                ." ,People.State as B_State "
                ." ,People.ZipCode as B_Zip "
                ." ,People.Country as B_Country "
                ." ,People.Address1 as S_Address1 "
                ." ,People.Address2 as S_Address2 "
                ." ,People.City as S_City "
                ." ,People.State as S_State "
                ." ,People.ZipCode as S_Zip "
                ." ,People.Country as S_Country "
                ." ,'1' as line_type " //Detail Info
                ." ,ArtSubmissions.Title as description "
                ." ,ArtSubmissions.QuantitySold "
                ." ,ArtSubmissions.QuantitySent "
                ." ,ArtSubmissions.FinalSalePrice as Price "
                ." ,IFNULL(ArtSubmissions.QuantitySold,0)-IFNULL(ArtSubmissions.QuantitySent,0) as qty "
                ." ,ArtSubmissions.ArtID as lot_no "
                ." ,ArtSubmissions.QuantitySold as QuantitySent " //SubmissionUpdate (Could write to ArtSales if needed)
                ." ,ArtSubmissions.ArtID as ArtID "
                ." FROM ArtSubmissions "
                ." LEFT JOIN PurchasedBadges on ArtSubmissions.PurchaserBadgeID=PurchasedBadges.BadgeID "
                ." LEFT JOIN People on PurchasedBadges.PeopleID=People.peopleID "
                ." WHERE QuantitySold>0 and (QuantitySent<QuantitySold OR QuantitySent IS NULL)";

            //The process is kind of like this:
            //Create an SQL that creates the values you need -
            //Use the groupby function to create a line and header.
            //write the header and return the insert id's
            //Use the lookup function to connect the returned insert ID to the key field added during groupby
            //Write the Lines.  (Obviously if it's a single insert you can just pull SQL and push it.)
            $in_artshow = sql_to_array($sql,true);

            /*
            echo "<pre>";
            print_r($in_artshow);
            echo "</pre>";
             */

            //Split out to make changes easier.
            $header_cols = "peopleID,BadgeName,Email,B_Address1,B_Address2,B_City,B_State,B_Zip,B_Country,S_Address1,S_Address2,S_City,S_State,S_Zip,S_Country";
            $line_cols = "invoiceID,line_type,description,qty,lot_no";
            $update_cols = "QuantitySent";

            //Now lets split this into Header and Detail (Instructions below)
            //Should add a column 'preinvoiceID' which will link header to detail.
            $artshow_array = array_groupby($in_artshow
                ,$header_cols
                ,"preinvoiceID");

            //This should return a pre-set of data grouped by the fields and NUMBERED by "preinvoiceID" - so we'll use that as a key field
            //To link the detail to the header for insert (We'll insert the headers FIRST to generate and lock a key, then spread it to the detail and add it)
            //You pass it an array, then a list of columns you want a unique set from and it will do that.
            //If you also add a 'key field' it will return an array with TWO tables- a header and detail linked by that column.  See how it's used below!

            //Debug/View returned split data:
            /*
            echo "<pre>";
            print_r($artshow_array);
            echo "</pre>";
             */

            //So first, insert the header (array of 2 from the groupby if you add a key column)
            //This will also RETURN this back with invoiceID filled in for the records. So we'll just replace it back in.
            //To make this clean, we'll start a transaction, then roll it back if any exceptions are thrown.
            //Roll back to be added later - focusing on invoicing Per Chris:
            try
            {
                //In, Out, Keys, columns, PreID to return
                //Should return the header array after insert INCLUDING the invoiceID column (which is the insert autonumber)
                $out_header = update_from_array($artshow_array[1],"invoice_header",null,$header_cols,"invoiceID");

                //Now we spread the invoiceID from the header using our lookup function and the preinvoiceID added at grouping.
                //The preinvoiceID is the 'lookup' column (it's actually a list of records, you could use multiple columns later)
                //invoiceID is the column that will be written to the detail.
                $out_line = array_lookup($artshow_array[0],$out_header,"preinvoiceID","invoiceID");

                //Now write the detail, including our previously added invoiceID
                update_from_array($out_line,"invoice_line","",$line_cols);

                //Now write back the ArtSubmission Table to stop this set from coming back again
                //(This way we only update the actual ones we wrote, in case new ones appeared between)
                update_from_array($out_line,"ArtSubmissions","ArtID",$update_cols);
            }
            catch(Exception $e)
            {
                //echo "Error writing Invoice: ".$e.message."<br>";
                echo '{ "success": false, "message": "Error writing Invoice: '.html_entity_encode($e.message).' }';
            }

            /*
            echo "<pre>";
            print_r($out_array);
            echo "</pre>";
             */

            echo '{ "success": true, "message": "Invoices Generated/None to Generate" }';
			break;
		default:
			echo '{ "success": false, "message": "Incorrect Action" }';
    }
}
else
    echo '{ "success": false, "message": "Incorrect Action" }';
?>