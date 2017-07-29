<?php

//To Prevent scriptlock
//session_start();
//session_write_close();

//Add our functions - should include my FPDF Extensions
include 'includes/functions.php';

//Set Max execution time so the SQL query can take a 60 seconds.
//set_time_limit(60);

//passed report settings (Allowed to be sent through Get)
$reportID=$_REQUEST['reportid'];

//Style settings
$old = $_REQUEST['old'];

//Data Array
$in = invoiceData($reportID);

//Filename used for file (if possible)
$filename = $_REQUEST['filename'];

//FPDF header passes
$report_title=stripslashes($_REQUEST['title']);
$report_subleft=$_REQUEST['subleft'];
$report_subright=$_REQUEST['subright'];
$report_orient=$_REQUEST['orient'];
$report_paper=$_REQUEST['paper'];
$report_measure=$_REQUEST['measure'];
$report_font=$_REQUEST['font'];
$report_rowheight=$_REQUEST['rowheight'];
$report_columns=$_REQUEST['columns'];
$report_column_margin=$_REQUEST['column_margin'];

//Save this value so we can display
$report_paper_save = $report_paper;

//Header/Fill color override
$header_color_hex = $_REQUEST['header_color'];
$fill_color_hex = $_REQUEST['fill_color'];
		
$header_color=array(hexdec(substr($header_color_hex,0,2)),hexdec(substr($header_color_hex,2,2)),hexdec(substr($header_color_hex,4,2)));
$fill_color=array(hexdec(substr($fill_color_hex,0,2)),hexdec(substr($fill_color_hex,2,2)),hexdec(substr($fill_color_hex,4,2)));

$pagebreak=$_REQUEST['pagebreak'];
$rcompress=$_REQUEST['rcompress'];
$rep_total=$_REQUEST['reptotal'];
$debug=$_REQUEST['debug'];

//This sets the header/footer size.  Only need to change this if you change the logo or sizes of header/footer
$header_footer=19;



?>