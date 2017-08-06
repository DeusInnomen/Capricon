<?php

//To Prevent scriptlock
//session_start();
//session_write_close();

$id = $_REQUEST['id']*1;

//Add our functions - should include my FPDF Extensions
include 'includes/functions.php';

//Should also eventually request security to make sure only run by logged in person.

//Defaults / Settings / Calculations
if ($report_title=='') $report_title="Capricon Invoice";
if ($report_subleft=='') $report_subleft="Invoice Number ".str_pad($id,12,"0",STR_PAD_LEFT);
if ($report_subright=='') $report_subright="Invoice Date ".date("m-d-Y");
//Orientation - L=landscape P=portrait
if ($report_orient=='') $report_orient="P";
//Paper Size - 11x17, A4, A5, Letter, Legal
if ($report_paper=='') $report_paper="Letter";
//Save Paper size 'Description' For types that go to an Array.
$report_paper_save=$report_paper;
//Measurement size - mm, cm, in, pt
if ($report_measure=='') $report_measure='mm';
//Report Font Size
if ($report_font=='') $report_font="12";
//Report Row Height
if ($report_rowheight=='') $report_rowheight=8;
//Report Columns
if ($report_columns=='') $report_columns=1;
//Report Column Margin (Between Columns)
if ($report_column_margin=='') $report_column_margin=5;
//Fix 11x17 to mm (for printing)
if ($report_paper=="11x17") $report_paper=array(279.4,431.799999999);
if ($report_paper=="11x17") $report_measure='mm';
//Set default header color to match the Ram
if ($header_color[0]=='') $header_color=array(57,83,164);
//Set default record fill color to GREY
if ($fill_color[0]=='') $fill_color=array(230,230,230);
//Set footer color to halftone of header:
$foot_color=array($header_color[0]/2,$header_color[1]/2,$header_color[2]/2);
//Set default File name: OBCReport_CurrentDate.pdf
if ($filename=='') $filename="OBCReport_".date("m_d_Y").".pdf";
//Page break after grouplevel 1?
if ($pagebreak=='') $pagebreak=0;
//Show the report total? (default yes=1)
if ($rep_total==0) $rep_total=1;
//Only put headers at the top of the page? (default no=0)
if ($rcompress==0) $rcompress=0;

//From paper type and orientation, lets calculate the size of the page in mm.
//These are all Portrait.  if it's set to L then flip them.
switch(strtoupper(trim($report_paper_save)))
{
	case "11X17":
		$mm_width = 279;
		$mm_height = 432;
		break;
	case "LEGAL":
		$mm_width = 216;
		$mm_height = 356;
		break;
	default:
		//Letter
		$mm_width = 216;
		$mm_height = 279;
		break;
	
}

if($report_orient=='L')
{
	//echo "Landscape - Swap<br>";
	//echo "Holding Width $mm_width<Br>";
	$mm_width_hold = $mm_width;
	$mm_width = $mm_height;
	//echo "Setting Width $mm_height<Br>";
	$mm_height = $mm_width_hold;
	unset($mm_width_hold);
}

//mm_width should always be the amount of mm we have to work with on the current page - can be used to calculate 'maximum chars' for a line.
//Based on the smallest character we ever want to display.
/*
$font_size = 12;  //in points (Standard Font size) which should be = .3527
$font_aspect = 0.45;  //This is the aspect ratio of the font.  COnverts Point (which is up/down) to left right.  Currently set for Times (Courier New=0.42, Times=0.45)
*/
$font_size = 16;  //in points (Standard Font size) which should be = .3527
$font_aspect = 0.45;  //This is the aspect ratio of the font.  COnverts Point (which is up/down) to left right.  Currently set for Times (Courier New=0.42, Times=0.45)
//$mm_minchar_width = ($font_size*.3527) * 0.45; //This should be (Height in Pt*mm/pt = height in mm)*(width/height = aspect) = width of font at $font_size in mm. Hopefully.
$mm_minchar_width = ($font_size*.3527) * $font_aspect; //This should be (Height in Pt*mm/pt = height in mm)*(width/height = aspect) = width of font at $font_size in mm. Hopefully.
$max_page_chars = floor($mm_width / $mm_minchar_width);
$max_col_chars = floor($max_page_chars / $report_columns);
		
//Instanciation of inherited class
$pdf=new PDF($report_orient,$report_measure,$report_paper);
$pdf->setvars($report_title,$report_subleft,$report_subright,$report_font,$report_rowheight,$report_columns,$report_column_margin,$header_footer,$header_color,$foot_color,$fill_color,$pagebreak,$rep_total,$rcompress,$debug,$posted,$percent_posted);
$pdf->header_set($in);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

//Header:
$sql = "SELECT * from invoice_header "
	." WHERE invoiceID='".$id."'";
$header_array = sql_to_array($sql);

/*
echo "<pre>";
print_r($header_array);
echo "</pre>";
*/

$name_line = $header_array[0]["FirstName"]." ".$header_array[0]["LastName"]." (".$header_array[0]["BadgeName"].")";
$b_address_line1 = $header_array[0]["B_Address1"];
$b_address_line2 = $header_array[0]["B_Address2"];
$b_address_line3 = $header_array[0]["B_City"].", ".$header_array[0]["B_State"]." ".$header_array[0]["B_Zip"];

$s_address_line1 = $header_array[0]["S_Address1"];
$s_address_line2 = $header_array[0]["S_Address2"];
$s_address_line3 = $header_array[0]["S_City"].", ".$header_array[0]["S_State"]." ".$header_array[0]["S_Zip"];

$top = $pdf->GetY();
$line_height = 4.8;
$col2 = 80;

$pdf->Write($line_height,"Bill To:\n");
$pdf->Write($line_height,$name_line."\n");
$pdf->Write($line_height,$b_address_line1."\n");
$pdf->Write($line_height,$b_address_line2."\n");
$pdf->Write($line_height,$b_address_line3."\n");

$pdf->SetXY($col2,$top);
$pdf->Write($line_height,"Ship To:\n");
$pdf->SetX($col2);
$pdf->Write($line_height,$name_line."\n");
$pdf->SetX($col2);
$pdf->Write($line_height,$s_address_line1."\n");
$pdf->SetX($col2);
$pdf->Write($line_height,$s_address_line2."\n");
$pdf->SetX($col2);
$pdf->Write($line_height,$s_address_line3."\n");

$pdf->Write($line_height,"\n");

//Line Detail:
$sql = "SELECT description as Description, ROUND(qty,0) as Qty1S, ROUND(price,2) as Price, ROUND(qty*price,2) as Line_Ext2S from invoice_line where invoiceID='".$id."'";
$out_array = $pdf->sql_to_table_array($sql);
//To Debug Rendering
//$pdf->debug = true;

//New table is more stylish, but I have to repair the placement functions (I built them into OLD)
//$pdf->New_Table($out_array,120);

$pdf->Old_Table($out_array,140,30);

/*
echo "<pre>";
print_r($out_array);
echo "</pre>";
*/

$pdf->Output($filename,'I');
?>