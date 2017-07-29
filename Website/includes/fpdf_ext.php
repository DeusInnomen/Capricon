<?

//PDF Extension
//Adds Consistant Header, and some simple table functions for use

//Notes: Image Text function, used for rotating headers needs to be ported or the rotate headers won't work.

define('FPDF_FONTPATH','incs/fpdf/font/');
require('incs/fpdf/fpdf.php');

class PDF extends FPDF
{
	//Page header
	function Header()
	{
		if (strlen($this->custom_header)>0)
		{
			//Custom Header (From HTML)
			$this->Ln(5);
			$this->SetX(10);
			$this->SetFont('Arial','B',15);
			$this->SetTextColor(0);
			$this->Cell(0,5,$this->custom_header,0,1);
			$this->Cell(0,2,'',T,1);
		}
		else
		{
			//Logo
			$this->Image('includes/CapReg.png',10,8,33);
			
			//If this is posted, put the info here:
			if($this->posted)
			{
				$this->SetFont('Arial','B',12);
				$this->SetTextColor(145);
				$this->SetX(45);
				$this->Cell(0,5,"POSTED",0,1);		
				$this->Ln(1);
			}
			else
			{
				if($this->post_perc>0)
				{
					//Show Posted Percent
					$this->SetFont('Arial','B',12);
					$this->SetTextColor(195);
					$this->SetX(45);
					$this->Cell(0,5,$this->post_perc."% COMPLETE",0,1);					
					$this->Ln(1);
				}
				else
				{
					//None of the Above.
					$this->Ln(5);
				}
			}
						
			//Title
			$this->SetX(45);
			//Arial bold 15
			$this->SetFont('Arial','B',15);
			$this->SetTextColor(0);
			$this->Cell(0,5,$this->title,0,1);
			$this->SetX(45);
			//Arial bold 15
			$this->SetFont('Arial','B',10);
			$this->Cell(0,7,$this->subtitle_left,0,0,L);
			$this->Cell(0,7,$this->subtitle_right,0,1,R);
			$this->Cell(0,2,'',T,1);
			
		}
	}

	//Page footer
	function Footer()
	{
		if (strlen($this->custom_footer)>0)
		{
			//Custom Header (From HTML)
			//Position at 1.5 cm from bottom
	    		$this->SetY(-15);
	    		$this->Cell(0,2,'',T,1);
	    		//Arial italic 8
	    		$this->SetFont('Arial','I',8);
	    		$this->SetTextColor(0);
	    		$this->Cell((1/3)*($this->w-$this->lMargin-$this->rMargin),7,$this->custom_footer,0,0,'L');
			$this->Cell((1/3)*($this->w-$this->lMargin-$this->rMargin),7,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		}
		else
		{
			//Position at 1.5 cm from bottom
	    		$this->SetY(-15);
	    		$this->Cell(0,2,'',T,1);
	    		//Arial italic 8
	    		$this->SetFont('Arial','I',8);
	    		$this->SetTextColor(0);
	    						
	    		//Page number
	    		$this->Cell((1/3)*($this->w-$this->lMargin-$this->rMargin),7,$this->title." ".$this->subtitle_left,0,0,'L');
	    		$this->Cell((1/3)*($this->w-$this->lMargin-$this->rMargin),7,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	    		$this->Cell((1/3)*($this->w-$this->lMargin-$this->rMargin),7,$this->subtitle_right,0,0,'R');
    		}
	}
	
	//Multi-Column Table
	function Table($data)
	{
		//Variable pre-set
    		$fill=1;
    		$debug=$this->debug;
    		
    		if($debug)
		{
			$this->Write(5,"Running New_Table\n");
			$this->Write(5,"Passed Maximum Chars/Col ".$data['max_col_chars']."\n");
			//echo "Passed Maximum Chars/Col ".$data['max_col_chars']."<br>";
		}
    		
    		$totalsize = $data['totalsize'];
    		
    		//First we want to calculate cell sizes, Font sizes and column widths.
    		$column_w = ($this->w - $this->lMargin - $this->rMargin - (($this->columns-1)*$this->column_margin)) / $this->columns;
    		$column_h = ($this->h - $this->tMargin - $this->bMargin - $this->header_footer);
    		//$column_font = ($column_w/($data['totalsize']+$data['highgroup']*5))/.3527;
    		$column_font = (($column_w-$data['highgroup']*5)/($totalsize))/.25;
    		
    		//echo "$column_font = (($column_w-".$data['highgroup']."*5)/($totalsize))/.25<br>";
    		
    		$column_maxchars = $data['max_col_chars'];
    		    		
    		$current_high_size=$totalsize;
    		    		
    		//Calculate Font size - must take into account headers
    		for($x=0;$x<=$data['highgroup'];$x++)
		{
    			if ($data['groupsize'.$x]>$current_high_size)
			{
    				$column_font = (($column_w-$x*5)/($data['groupsize'.$x]))/.3527;
			}
		}
    		
    		//if ($column_font > 14) $column_font = 14;
    		//if ($column_font < 3) $column_font = 3;
    		//Make the cells linearly related to the font.
    		$cell_h = $column_font*.75;
    		//Number of cells - Always round down (chop decimals)
    		
    		//echo "Creating ColCellNum $column_cellnum=($column_h/$cell_h)<br>";
    		
    		$column_cellnum = intval($column_h/$cell_h);
    		//Tab Size (for grouping - each group is indented this amount:
    		$tab_size=(5/$totalsize)*$column_w;
    		$data_w=$column_w-($data['highgroup']*$tab_size);
    		$headersize=$data['highgroup']*2;
		//Number of cells per page (for a page-skip)
		$cell_per_page=$column_cellnum*$this->columns;	

    		for($g=1;$g <= $data['highgroup'];$g++)
		{
    			$cell_w[$g]=$column_w-($tab_size*($g-1));
    			if($debug)
    			{
    				$this->Write(5,"Setting Group Width $g: ".$cell_w[$g]."\n");
    			}
		}
    		
    		if($debug)
    		{
    			$this->Write(5,"Columns: ".$this->columns."\n");
			$this->Write(5,"Page Height: ".$this->h."\n");
    			$this->Write(5,"Column Height: ".$column_h."\n");
    			$this->Write(5,"Column Height First: ".$column_h_first."\n");
    			$this->Write(5,"Cell Height: ".$cell_h."\n");
    			
    			$this->Write(5,"Column Width: ".$column_w."\n");
    			$this->Write(5,"Tabwidth: ".$tab_size."\n");
    			
    			$this->Write(5,"Starting Height: ".$start_y."\n");
    			$this->Write(5,"Cells/Column: ".$column_cellnum."\n");
    			$this->Write(5,"Cells/Column First: ".$column_cellnum_first."\n");
    			$this->Write(5,"Column Font: ".$column_font."\n");
    			$this->Write(5,"TotalSize: ".$totalsize."\n");	
    			
    			if($column_font<5) $column_font=5;
    			
    			//Now, for each column, we go down the column filling in data, then go to the next column, etc.  Down then Right.
			for($y=0;$y < $data['num_fields'];$y++)
    			{
    				$this->Write(5,"Column $y: ".$data[$y]['maxsize']."\n");
    			}
    		}
    		
    		//Unset our Arrays:
		//Page array contains each page of cells to be rendered below
		unset($page_array);
		//Lastgroup array is an array of the last field values to check against current to see if we need a header keyed as [Field]
		unset($lastgroup_array);
		//Update array is an array of GROUPS and a 1 if that group needs a header.  (Changing header doesn't mean another group is done) Keyed as [group]
		unset($update_array);
		//Sum array is an array of fields that need sums at the bottom of a group.  Keyed as [group][field]
		unset($sum_array);
		//Report Total array is an array of fields that need sums at the bottom of the report keyed as [field]
		unset($report_total_array);
		//BlobArray is for keeping information about the blobs (starting, width and leftover chars to distribute)
		unset($blob_array);
		
		//Preset our Flags:
		//header is meant to signify that a header needs to be printed because there's something in $update_array
		$header=0;
		//Skipthese is used to skip cells until the next column (or page) starts.
		$skipthese=0;
		//Skiplines is used to skip a certain number of cells.  Good for adding spaces after a Sum line.  0=off
		$skiplines=0;
		//SumLine is used to signify that there is a sumline that needs to be printed.
		$sumline=0;
		//headerdata is the flag used to signify that we need to print the header data for the group included.
		$headerdata=0;
		//Record is the current record number in the dataset, starting with Zero.
		$record=0;
		//Starting page is 0.
		$page=0;
		//Blob flag
		$blob_on = false;
		//Timeout
		$blob_timeout=0;
		$cell_timeout=0;
		
		//Now we need to generate our 'Page Array' array to contain what will be rendered
		//	We'll keep going until we've read through all the records:
		while ($record < $data['num_rows'] || intval($sumline)==(-1) || intval($sumline)>0)
		{
			//Cell check (Protection from Neverending wrapping)
			//Don't try and pull 500,000 rows or more.  Bad mojo anyway.
			$cell_timeout++;
			
			if($cell_timeout>500000)
			{
				echo "Dying: <Br>";
				echo "<pre>";
				print_r($page_array);
				echo "<pre>";
				die();
			}
			
			//Now, for each column, we go down the column filling in data, then go to the next column, etc.  Down then Right.
			for($y=0;$y < $this->columns;$y++)
			{
				//echo "Column $y/".$this->columns." There are $column_cellnum cells to make: <Br>";
    				//Then we do the lines down the page.
				for($x=1;$x <= $column_cellnum;$x++)
				{
					//echo "Cell: $x/$column_cellnum<Br>";
    					//Page array is [page][Row][Column] and equates to the cells on the page.
    					$cur_cell=$x+($column_cellnum*$y);
    					
    					//If this is the first cell on the page, then preload to show the first header:
    					if ($cur_cell==1)
					{
    						for($h=1;$h <= $data['highgroup'];$h++)
						{
    							$update_array[$h]=1;
						}
    						if ($data['highgroup']>0)
						{
    							$header=1;
						}
    						else
						{
    							$header=0;
						}
					}
    					
    					//For debugging record the Start of the cell prep.
    					if ($debug==1) $this->Cell(0,7,"Cell Start ($cur_cell/$cell_per_page) Column=$y, Col_Cell=$x page=$page header=$header skipthese=$skipthese sumline=$sumline headerdata=$headerdata record=$record UpdateArray=".implode(",",$update_array)." rep_total=".$this->rep_total,1,1);
    					    					
    					//This is a new column if x==1.  If you're skipping for any reason (End of column/page skip) then stop skipping
    					if ($x==1)
					{
    						$skipthese=0;
						if ($debug==1) $this->Cell(0,7,"   Turning off CELLSKIP",1,1);
					}

					//Now we're going to use the Switch function.  Hopefully, the flags are initialized right, because we're going to
					//	Either output headers or data or sums.  Shouldn't do anything else here.

					//Possible States of the Machine:
					//	1: SumLine - If $lastgroup_array (and not $cur_cell==1) and there's at least one sum we need a sumline.
					//	2: Skip - If a header was called, but we're too close to the bottom we skip $skipthese=1;
					//	3: Header Data - headerdata==group - Spit out the data for the group included (Done first because of Flags)
					//	4: Headers - cur_cell==1 (First cell on a page) or a change in $lastgroup_array
					//	5: Data - If not any of the others then we export Data and increment record
					//		Also, For Data we need to update $lastgroup_array and any other flags that need to be reset.

					switch (true)
    			{				
    						Case $blob_on:
    							//There's at least one more line below for blobs
    							for($g=0;$g < $data['num_fields'];$g++)
	    						{
	    							//For each 'field' we'll export the data
	    							//Type=1 is Data
	    							if ($data[$g]['grouplevel']>0 || $record >= $data['num_rows'])
	    							{
    									//Somehow insert group information and force this to do another cell
    									$page_array[$page][$x][$y][$g]="~~~skip~~~";
    								}	
										else
										{
											//~~~top~~~ for convienience
		    							if(isset($blob_array[$g]['array']))
		    							{
			    							//Here are the array values:
			    							$blobsize = $blob_array[$g]['min'];
												$stringsize = $blob_array[$g]['total'];
												$colsize = $blob_array[$g]['limit'];
												$out_pointer = $blob_array[$g]['array_pointer'];
												$out_remain = $blob_array[$g]['line_remain'];
												$fill = $blob_array[$g]['fill'];
												
												if ($debug==1) $this->Cell(0,7,"    $g: Restore Fill Value of $fill",1,1);
										
			    							$blob_data = $blob_array[$g]['array'];
		
			    							$test_out = substr($blob_data[$out_pointer],0,$colsize);
			    							
			    							if ($debug==1) $this->Cell(0,7,"    $g: Blob Process ".$out_pointer."/".sizeof($blob_data)." '$test_out' remaining '".$out_remain."' Counter [".$blob_array['counter']."]",1,1);
			    							
			    							if(strlen($out_remain)>0)
			    							{
													//Then we need to finish processing the line_remain of the last pointer			    								
			    								if(strlen($out_remain)>$colsize)
			    								{
			    									//Then Process it and pass the remaining back.
			    									$blob_array[$g]['line_remain'] = substr($out_remain,$colsize);
			    									
			    									//Blob.  Right and left border only. (Or bottom if bottom row.
						    						$page_array[$page][$x][$y]['type']=6;
														$page_array[$page][$x][$y]['fill']=$fill;
														$page_array[$page][$x][$y]['tabs']=$data['highgroup'];
														$page_array[$page][$x][$y][$g]=substr($out_remain,0,$colsize);
												
														if ($debug==1) $this->Cell(0,7,"    $g: Processing Remaining ".strlen($blob_array[$g]['line_remain'])." chars",1,1);
			    								}
			    								else
			    								{
			    									//Then whatever remains is fine.
						    						$page_array[$page][$x][$y]['type']=6;
														$page_array[$page][$x][$y]['fill']=$fill;
														$page_array[$page][$x][$y]['tabs']=$data['highgroup'];
														$page_array[$page][$x][$y][$g]=$out_remain;
														
														$blob_array[$g]['line_remain'] = "";
														
														$out_pointer++;
												
														//Pointer up!
														$blob_array[$g]['array_pointer'] = $out_pointer;
														
														//Decide if we need to go on.
														if($out_pointer>sizeof($blob_data)-1)
							    					{
					    								//Then we have a new pointer to process
					    								unset($blob_array[$g]['array']);
					    								if ($debug==1) $this->Cell(0,7,"     $g: Blob Stopped because $out_pointer > ".sizeof($blob_data)."-1",1,1);
					    								
					    								//$fill=!$fill;
															
															$page_array[$page][$x][$y][$g] = "~~~bot~~~".$page_array[$page][$x][$y][$g];
															
															//We need to roll through the columns and see if any blob_data is left.
															$found_old = false;
															for($oldg=0;$oldg<=$data['num_fields'];$oldg++)
															{
																if(sizeof($blob_array[$oldg]['array'])>0)
																{
																	if ($debug==1) $this->Cell(0,7,"    Found a blob still active ($oldg), won't stop it.",1,1);		
																	$found_old = true;
																}
															}
															
															if(!$found_old) 
															{
																if ($debug==1) $this->Cell(0,7,"    $g: Data: (Fill) Blob Record Increment ".$data[$g]['field_name']." thischeck=$thischeck group_data=$group_data record=$record UpdateArray=".implode(",",$update_array),1,1);
															
																$blob_on=false;
																//$fill=!$fill;
																$record++;
																$blob_array[$g]['fill']=$fill;
															}
							    					}	
							    					else
							    					{
							    						if ($debug==1) $this->Cell(0,7,"     $g: Blob Continues because $out_pointer ! > ".sizeof($blob_data)."-1",1,1);
							    					}	
							    				if ($debug==1) $this->Cell(0,7,"     $g: Finishing Line - Pointer is ".$blob_array[$g]['array_pointer'],1,1);
						    					}
						    				}
			    							elseif($out_pointer<sizeof($blob_data))
			    							{
			    								//Then we have a new pointer to process, but no current remaining.
		    									$out_remain = $blob_data[$out_pointer];
		    									if(strlen($out_remain)>$colsize)
			    								{
			    									//Then Process it and pass the remaining back.
			    									$blob_array[$g]['line_remain'] = substr($out_remain,$colsize);
			    									
			    									//Blob.  Right and left border only. (Or bottom if bottom row.
						    						$page_array[$page][$x][$y]['type']=6;
														$page_array[$page][$x][$y]['fill']=$fill;
														$page_array[$page][$x][$y]['tabs']=$data['highgroup'];
														$page_array[$page][$x][$y][$g]=substr($out_remain,0,$colsize);
														
														if ($debug==1) $this->Cell(0,7,"    $g: Grabbing new line from pointer",1,1);
			    								}
			    								else
			    								{
				    									//Then whatever remains is fine.
							    					$page_array[$page][$x][$y]['type']=6;
														$page_array[$page][$x][$y]['fill']=$fill;
														$page_array[$page][$x][$y]['tabs']=$data['highgroup'];
														$page_array[$page][$x][$y][$g]=$out_remain;
													
														//Pointer up!
														$blob_array[$g]['array_pointer']++;
														
														//Decide if we need to go on.
														if($out_pointer>=sizeof($blob_data)-1)
							    					{
					    								//Then we have a new pointer to process
					    								unset($blob_array[$g]['array']);
					    								if ($debug==1) $this->Cell(0,7,"     $g: Blob unset for Column",1,1);
					    								
					    								//$fill=!$fill;
														
															$page_array[$page][$x][$y][$g] = "~~~bot~~~".$page_array[$page][$x][$y][$g];
															
															//We need to roll through the columns and see if any blob_data is left.
															$found_old = false;
															for($oldg=0;$oldg<=$data['num_fields'];$oldg++)
															{
																if(sizeof($blob_array[$oldg]['array'])>0)
																{
																	if ($debug==1) $this->Cell(0,7,"    Found a blob still active ($oldg), won't stop it.",1,1);		
																	$found_old = true;
																}
															}
															
															if(!$found_old) 
															{
																$blob_on=false;
																$record++;	
																if ($debug==1) $this->Cell(0,7,"    $g: (Fill) Data: Blob Record Increment ".$data[$g]['field_name']." thischeck=$thischeck group_data=$group_data record=$record UpdateArray=".implode(",",$update_array),1,1);
																//$fill=!$fill;
																$blob_array[$g]['fill']=$fill;
															}
															
															
							    					}		
							    					if ($debug==1) $this->Cell(0,7,"     $g: Finishing Line from Pointer",1,1);
					    						}		
				    						}
				    						else
			    							{
			    								//All pointers done.  we shouldn't be here, so just turn off blob.
			    								if ($debug==1) $this->Cell(0,7,"     $g: Broken Bloboff",1,1);
			    								unset($blob_array[$g]['array']);
			    								
			    								//We need to roll through the columns and see if any blob_data is left.
													$found_old = false;
													for($oldg=0;$oldg<=$data['num_fields'];$oldg++)
													{
														if(sizeof($blob_array[$oldg]['array'])>0)
														{
															if ($debug==1) $this->Cell(0,7,"    Found a blob still active ($oldg), won't stop it.",1,1);		
															$found_old = true;
														}
													}
													
													if(!$found_old) $blob_on=false;
			    								
			    								//$blob_on=false;
			    								/*
			    								
			    								$page_array[$page][$x][$y][$g] = "~~~bot~~~".$page_array[$page][$x][$y][$g];
			    								
			    								$record++;
													$fill=!$fill;
													if ($debug==1) $this->Cell(0,7,"    $g: Data: Blob Record Increment ".$data[$g]['field_name']." thischeck=$thischeck group_data=$group_data record=$record UpdateArray=".implode(",",$update_array),1,1);
													*/
	    									}
	    									
			    							//Blob.  Right and left border only. (Or bottom if bottom row.
				    						/*
				    						$page_array[$page][$x][$y]['type']=6;
												$page_array[$page][$x][$y]['fill']=$blob_array[$g]['fill'];
												$page_array[$page][$x][$y]['tabs']=$data['highgroup'];
												$page_array[$page][$x][$y][$g]=$blob_data[1];
												*/								
											}
											else
											{
												//Blank! Type 6 column block (No Box)
												$page_array[$page][$x][$y]['type']=6;
												$page_array[$page][$x][$y]['fill']=$fill;
												$page_array[$page][$x][$y]['tabs']=$data['highgroup'];
												$page_array[$page][$x][$y][$g]="~~~blank~~~";
											}
										}
										$blob_array['counter']++;
												
										if($blob_array['counter']>500)
										{
											if ($debug==1) $this->Cell(0,7,"    $g: Blob Timeout at ".$blob_array['counter'],1,1);
											$blob_array['counter']=0;
			    								$blob_on=false;
										}
	  							}
    							break;
    						Case $skiplines>0:
    							//SKIP
    							//Then we fill them with Blanks the right size.
    							for($g=0;$g < $data['num_fields'];$g++)
	    						{
		    						//Dump out blanks - Type 4 - Dummy Row
		    						$page_array[$page][$x][$y]['tabs']=0;
		    						$page_array[$page][$x][$y]['type']=4;
    								$page_array[$page][$x][$y][$g]="";
    							}
    							$skiplines--;
    							break;
    						Case $sumline>0:
    							//SUMLINE for Groups
    							//Will Sum for each group that has an update_set (Top Down i.e. highgroup->1)
							if ($debug==1) $this->Cell(0,7,"      Begin Sumline for $sumline - Grouplevel is ".$data[$g]['grouplevel'],1,1);	
    							for($g=0;$g < $data['num_fields'];$g++)
	    						{
	    							//Only for Data fields, so skip Group Fields UNLESS it's the current Group.
	    							if ($data[$g]['grouplevel']>0)
								{
    									//Somehow insert group information and force this to do another cell
    									$page_array[$page][$x][$y][$g]="~~~skip~~~";
    								}	
    								else
	    							{
	    								//For each 'field' we'll export the data
	    								//Type=5 which is SumType = Where if it's BLANK it will be ignored.
	    								if ($data[$g]['sumtype']>0)
		    							{
		    								/*
    										$page_array[$page][$x][$y]['type']=5;
    										$page_array[$page][$x][$y]['fill']=0;
    										$page_array[$page][$x][$y]['tabs']=$sumline-1;
    										if ($sum_array[$sumline][$g]==0)
    										{
    											$page_array[$page][$x][$y][$g]=0;
    										}
    										else
    										{
    											$page_array[$page][$x][$y][$g]=$sum_array[$sumline][$g];
    										}
    										if ($debug==1) $this->Cell(0,7,"      316:Data: SUM field ".$data[$g]['field_name']." record=$record UpdateArray=".implode(",",$update_array)." Using Amount ".$sum_array[$sumline][$g]." for Group ".$data[$g]['grouplevel'],1,1);	
    										$sum_array[$sumline][$g]=0;
    										*/
    										$page_array[$page][$x][$y]['type']=5;
										$page_array[$page][$x][$y]['fill']=0;
										$page_array[$page][$x][$y]['tabs']=$sumline-1;
										if ($sum_array[$sumline][$g]==0)
										{
											if ($debug==1) $this->Cell(0,7,"   325:Formatting Sum as ".$data[$g]['sumtype'],1,1);
											//SUMSECTION
											switch($data[$g]['sumtype'])
			    								{	
			    									case 2:
			    										$page_array[$page][$x][$y][$g]="$".number_format(preg_replace('/,|\$/','',0),2);
			    										break;
			    									case 3:
			    										$page_array[$page][$x][$y][$g]=number_format(preg_replace('/,|\$/','',0),0);
			    										break;
			    									case 4:
			    										$page_array[$page][$x][$y][$g]=number_format(preg_replace('/,|\$/','',0),2);
			    										break;
			    									case 5:
			    										if($sum_array_nums[$sumline][$g]<>0)
			    										{
			    											$page_array[$page][$x][$y][$g]=number_format(preg_replace('/,|\$/','',$sum_array[$sumline][$g]/$sum_array_nums[$sumline][$g]),2)." (".$sum_array_nums[$sumline][$g].")";;
			    										}
			    										else
			    										{
			    											$page_array[$page][$x][$y][$g]='Inf';	
			    										}
			    										break;
			    									default:		
			    										$page_array[$page][$x][$y][$g]=0;
			    										break;
		    									}
										}
										else
										{
											if ($debug==1) $this->Cell(0,7,"   348:Formatting Sum as ".$data[$g]['sumtype'],1,1);
											//Exporting SumLine. Lets see if we need to pretty it up.
											switch($data[$g]['sumtype'])
			    								{	
			    									case 2:
			    										$page_array[$page][$x][$y][$g]="$".number_format(preg_replace('/,|\$/','',$sum_array[$sumline][$g]),2);
			    										break;
			    									case 3:
			    										$page_array[$page][$x][$y][$g]=number_format(preg_replace('/,|\$/','',$sum_array[$sumline][$g]),0);
			    										break;			    								
			    									case 4:
			    										$page_array[$page][$x][$y][$g]=number_format(preg_replace('/,|\$/','',$sum_array[$sumline][$g]),2);
			    										break;	
			    									case 5:
			    										if($sum_array_nums[$sumline][$g]<>0)
			    										{
			    											$page_array[$page][$x][$y][$g]=number_format(preg_replace('/,|\$/','',$sum_array[$sumline][$g]/$sum_array_nums[$sumline][$g]),2)." (".$sum_array_nums[$sumline][$g].")";;
			    										}
			    										else
			    										{
			    											$page_array[$page][$x][$y][$g]='Inf';	
			    										}
			    										break;		
			    									default:		
			    										$page_array[$page][$x][$y][$g]=$sum_array[$sumline][$g];
			    										break;
		    									}
										}
										if ($debug==1) $this->Cell(0,7,"      1066:Data: SUM field ".$data[$g]['field_name']." record=$record UpdateArray=".implode(",",$update_array)." Using Amount ".$sum_array[$sumline][$g]." for Group ".$data[$g]['grouplevel'],1,1);	
										$sum_array[$sumline][$g]=0;
										$sum_array_nums[$sumline][$g]=0;
    									}	
    									else
		    							{
	    									$page_array[$page][$x][$y]['type']=5;
    										$page_array[$page][$x][$y]['fill']=0;
    										$page_array[$page][$x][$y]['tabs']=$sumline-1;
    										$page_array[$page][$x][$y][$g]='';
    										if ($debug==1) $this->Cell(0,7,"      Data: NonSUM field ".$data[$g]['field_name']." record=$record UpdateArray=".implode(",",$update_array)." Resetting ".$sum_array[$sumline][$g]." to Zero",1,1);	
    									}
    								}
    							}
    							//Now we need to figure out if we need ANOTHER sumline or not.  If this was the only update one, then yes.
    							//Assuming we start at 'Highgroup' and decrements until we're at Zero (no more)
    							//echo "Ending Sumline=$sumline<br>";
    							$sumline--;
    							While ($update_array[$sumline]!=1 && $sumline>0)
    							{
								//Cycle down the groups
								$sumline--;
								//echo "Sumline=$sumline<br>";
    							}
    							//echo "Next Sumline=$sumline<br>";
    							if ($sumline==0) 
    								{
    								//we need to decide how much to skip.  On a non-Grouplevel=1 we do one.  On a grouplevel>1 we need a space.
    								if ($this->pagebreak==1 && $header==1)
    									{
    									if ($debug==1) $this->Cell(0,7,"  FOUND PAGEBREAK - Setting Skip to $cell_per_page-$cur_cell",1,1);
    									$skiplines=$cell_per_page-$cur_cell;
    									}
    								else
    									{
    									if ($debug==1) $this->Cell(0,7,"  NO PAGEBREAK",1,1);
    									$skiplines=1;
    									}
    								}
    							break;
    						Case $sumline<0:
    							//SUMLINE for Report
    							//Will Sum for each group that has an update_set (Top Down i.e. highgroup->1)

    							if ($sumline==-1)
    							{
    								for($g=0;$g < $data['num_fields'];$g++)
			    					{
	    								//Only for Data fields, so skip Group Fields UNLESS it's the current Group.
	    								if ($data[$g]['grouplevel']>0)
		    							{
    										//Somehow insert group information and force this to do another cell
    										$page_array[$page][$x][$y][$g]="~~~skip~~~";
    									}	
    									else
		    							{
	    									//For each 'field' we'll export the data
	    									//Type=5 which is SumType = Where if it's BLANK it will be ignored.
	    									if ($data[$g]['sumtype']>0)
			    							{
    											$page_array[$page][$x][$y]['type']=5;
    											$page_array[$page][$x][$y]['reportsum']=1;
    											$page_array[$page][$x][$y]['fill']=0;
    											$page_array[$page][$x][$y]['tabs']=$data['highgroup'];
    											if ($report_total_array[$g]==0)
	    										{
    												$page_array[$page][$x][$y][$g]=0;
	    										}
    											else
	    										{
    												switch($data[$g]['sumtype'])
				    								{	
				    									case 2:
				    										$page_array[$page][$x][$y][$g]="$".number_format(preg_replace('/,|\$/','',$report_total_array[$g]),2);
				    										break;
				    									case 3:
				    										$page_array[$page][$x][$y][$g]=number_format(preg_replace('/,|\$/','',$report_total_array[$g]),0);
				    										break;  	
				    									case 4:
				    										$page_array[$page][$x][$y][$g]=number_format(preg_replace('/,|\$/','',$report_total_array[$g]),2);
				    										break;							
				    									case 5:
				    										if ($report_total_array[$g]<>0)
				    										{
				    											if($report_sum_array_nums[$g]<>0)
				    											{
				    												$page_array[$page][$x][$y][$g]=number_format(preg_replace('/,|\$/','',$report_total_array[$g]/$report_sum_array_nums[$g]),2)." (".$report_sum_array_nums[$g].")";
				    											}
				    											else
				    											{
				    												$page_array[$page][$x][$y][$g] = '';	
				    											}
				    										}
				    										else
				    										{
				    											$page_array[$page][$x][$y][$g]='';
				    										}
				    										break;	
				    									default:		
				    										$page_array[$page][$x][$y][$g]=$report_total_array[$g];
				    										break;
												}
    												//$page_array[$page][$x][$y][$g]=$report_total_array[$g];
    											}
    											if ($debug==1) $this->Cell(0,7,"      441:Data: REPORTSUM field ".$data[$g]['field_name']." record=$record UpdateArray=".implode(",",$update_array)." Using Amount ".$report_total_array[$g]." to for ".$data[$g]['grouplevel'],1,1);	
    											$sum_array[$sumline][$g]=0;
    										}	
    										else
			    							{
	    										$page_array[$page][$x][$y]['type']=5;
    											$page_array[$page][$x][$y]['fill']=0;
    											$page_array[$page][$x][$y]['reportsum']=1;
    											$page_array[$page][$x][$y]['tabs']=$data['highgroup'];
    											$page_array[$page][$x][$y][$g]='';
    											if ($debug==1) $this->Cell(0,7,"      451:Data: REPORTSUM field ".$data[$g]['field_name']." record=$record UpdateArray=".implode(",",$update_array)." Setting to Zero",1,1);	
    										}
    									}
    								}
    								$sumline=-2;
    								if ($debug==1) $this->Cell(0,7,"  SUMLINE SET to -2",1,1);
    								}
    							else
    								{
    								//Then this is Sumline==-2 - Skip until this page is done.
    								//SKIP
    								//Then we fill them with Blanks the right size.
    								for($g=0;$g < $data['num_fields'];$g++)
		    							{
		    							//Dump out blanks - Type 4 - Dummy Row
		    							$page_array[$page][$x][$y]['tabs']=0;
		    							$page_array[$page][$x][$y]['type']=4;
    									$page_array[$page][$x][$y][$g]="";
    									}
    								}
    							break;
    						Case $skipthese==1:
    							//SKIP
    							//Then we fill them with Blanks the right size.
    							for($g=0;$g < $data['num_fields'];$g++)
		    						{
		    						//Dump out blanks - Type 4 - Dummy Row
		    						$page_array[$page][$x][$y]['tabs']=0;
		    						$page_array[$page][$x][$y]['type']=4;
    								$page_array[$page][$x][$y][$g]="";
    								}
    							break;
    						Case $headerdata>0:
    							//HEADER DATA
    							for($g=0;$g < $data['num_fields'];$g++)
			    				{
    								//Type=2 is GroupHeader
    								if ($data[$g]['grouplevel']==$headerdata)
		    						{	
    									//Group DATA
    									$page_array[$page][$x][$y]['type']=3;
	    								$page_array[$page][$x][$y]['fill']=0;
    									$page_array[$page][$x][$y]['group']=$data[$g]['grouplevel'];
    									$page_array[$page][$x][$y]['tabs']=$data[$g]['grouplevel']-1;
    									$page_array[$page][$x][$y][$g]=$data[$g][$record];
    								}	
    								else
			    					{
    									$page_array[$page][$x][$y][$g]="~~~skip~~~";
    								}
    							}
    							if (intval($this->rcompress)==0)
    							{	
    								$headerdata=0;
    								if ($debug==1) $this->Cell(0,7,"    Group_Data: headerdata=$headerdata",1,1);
    							}
    							else
    							{
    								//Find the next headerdata that changed
    								$headerdata++;
    								While ($update_array[$headerdata]!=1 && $headerdata<=$data['highgroup'])
    								{
    									//Cycle through the headers
    									$headerdata++;
    									
    								}
    								if ($headerdata>$data['highgroup']) $headerdata=0;
    								if ($debug==1) $this->Cell(0,7,"    CompressedPage: HeaderSkipto: $headerdata",1,1);
    							}
    							break;
    						Case $header>=0:
    							//HEADER
    							for($g=0;$g < $data['num_fields'];$g++)
			    				{
    								//Type=2 is GroupHeader
    								if ($data[$g]['grouplevel']==$header)
		    						{	
    									//Somehow insert group information and force this to do another cell
    									//For Group 0 (No group) We do different stuff, so here:
    									if ($header==0)
    									{
    										$page_array[$page][$x][$y]['type']=0;
    										$page_array[$page][$x][$y]['fill']=1;
    										$page_array[$page][$x][$y]['tabs']=$data['highgroup'];
    									}
    									else
    									{
    										$page_array[$page][$x][$y]['type']=2;
	    									$page_array[$page][$x][$y]['fill']=1;
    										$page_array[$page][$x][$y]['group']=$data[$g]['grouplevel'];
    										$page_array[$page][$x][$y]['tabs']=$data[$g]['grouplevel']-1;
    									}
    									
    									$vert = $data[$g]['vert'];
    									
    									if($vert)
    									{
    										$page_array[$page][$x][$y][$g]="~~~ver~~~".$data[$g]['field_name'];
    									}
    									else
    									{
    										$page_array[$page][$x][$y][$g]=$data[$g]['field_name'];
    									}
    									
    									if ($debug==1) $this->Cell(0,7,"  Header for $header: Set ".$data[$g]['field_name']." thischeck=$thischeck group_data=$group_data record=$record UpdateArray=".implode(",",$update_array)." Vert=".$vert,1,1);
    								}	
    								else
			    					{
    									$page_array[$page][$x][$y][$g]="~~~skip~~~";
    								}	
    								//Record the lastgroup so we don't generate another header due to it not being set.
    								$lastgroup_array[$g]=$data[$g][$record];
    								//echo "Done with Header $header<br>";
    							}
    							//Now, if we just exported Zero, we'll turn off header (-1)
    							if ($header==0)
    							{
    								$header=-1;
    								if ($reportsum==1) $sumline=-1;
    							}
    							else
    							{
    								//Else, we'll Move to the next header
    								//Set Headerdata to run this header.
    								$headerdata=$header;
    								//Set this header as done.
    								$update_array[$header]=0;
    								//Set next header
    								//  We should only export headers that have CHANGED.  So check to be sure the update is set before you go outputting it.
    								//  Find the first one with an update flag in the array or quit
    								While ($update_array[$header]!=1 && $header<=$data['highgroup'])
    								{
    									//Cycle through the headers
    									$header++;
    									//echo "$header<br>";
    								}
    								//echo "Found update for header=$header for cell $cur_cell<br>";
    								//If there aren't any more headers, then turn off header and allow Data to run.
    								if ($header>$data['highgroup']) $header=0;
    								if ($debug==1) $this->Cell(0,7,"    Header: Next header is $header UpdateArray=".implode(",",$update_array),1,1);
    							}
    							break;
    						Case $record < $data['num_rows']:
    							//DATA
    							for($g=0;$g < $data['num_fields'];$g++)
		    					{
	    							//For each 'field' we'll export the data
	    							//Type=1 is Data
	    							if ($data[$g]['grouplevel']>0 || $record >= $data['num_rows'])
	    							{
    									//Somehow insert group information and force this to do another cell
    									$page_array[$page][$x][$y][$g]="~~~skip~~~";
    								}	
    								else
	    							{
	    								$page_array[$page][$x][$y]['type']=1;
											$page_array[$page][$x][$y]['fill']=$fill;
											$page_array[$page][$x][$y]['tabs']=$data['highgroup'];
											//Lets see if we need to make it 'pretty'
											//SUMSECTION
											switch($data[$g]['sumtype'])
			    						{	
	    									case 2:
	    										$page_array[$page][$x][$y][$g]="$".number_format(preg_replace('/,|\$/','',$data[$g][$record]),2);
	    										break;
	    									case 3:
	    										$page_array[$page][$x][$y][$g]=number_format(preg_replace('/,|\$/','',$data[$g][$record]),0);
	    										break;  	
	    									case 4:
	    										$page_array[$page][$x][$y][$g]=number_format(preg_replace('/,|\$/','',$data[$g][$record]),2);
	    										break;							
	    									case 5:
	    										if ($data[$g][$record]<>0)
	    										{
	    											$page_array[$page][$x][$y][$g]=number_format(preg_replace('/,|\$/','',$data[$g][$record]),2);
	    										}
	    										else
	    										{
	    											$page_array[$page][$x][$y][$g]='Inf';
	    										}
	    										break;	
	    									default:		
	    										//Check Record for blobtype (and possibly just oversized line)
	    										$page_array[$page][$x][$y][$g]=$data[$g][$record];
	    										break;
											}
									
									//Okay, before we finish, lets track what we're putting in, 
									//and record if we need more lines
									if($data[$g]['blobsize']>0)
									{
										//Lets play with this data
										$in_record = $data[$g][$record];
										$colsize = $data[$g]['maxsize'];
										
										$blobsize = $data[$g]['blobsize'];
										$stringsize = $data[$g]['stringsize'];
																				
										//We shall split by <br> and /n first
										//And then any 'bit' larger than colsize will get split by the 
										//Last whitespace.  Should make an effective 'wordwrap'.
										$data_array = preg_split('/\R|<br>/', $in_record, -1);
										
										if ($debug==1) $this->Cell(0,7,"    $g: Splitting ".strlen($in_record)." gives us ".sizeof($data_array)." with first line sized=".strlen($data_array[0]),1,1);
										
										//Preset the outpointer to 0 (meaning we haven't gone forward yet.
										$out_pointer = 0;
										if(strlen($data_array[0])>$colsize)
										{
											//Okay, we have us a game, lets split this sucka.
											$cur_line_data = $data_array[0];
											
											$out_line = "~~~top~~~".substr($cur_line_data,0,$colsize);
											
											if ($debug==1) $this->Cell(0,7,"$g: Splitting ".strlen($cur_line_data)." by $colsize",1,1);
											
											$out_remain = substr($cur_line_data,$colsize);
											
											//Set pointer to zero since we didn't make it.
											$out_pointer = 0;
											if ($debug==1) $this->Cell(0,7,"    $g: Split first line on column limit $colsize, Leave pointer at zero and pass on remaining ".strlen($out_remain)." chars",1,1);
											
											$blob_on = true;
										}
										else
										{
											if(sizeof($data_array)==1)
											{
												//First records is good enough.
												$out_line = $data_array[0];
												$out_remain = "";
		
												//Set pointer to 1 to finish it up.
												$out_pointer = 1;	
												if ($debug==1) $this->Cell(0,7,"    $g: First Line Fits, All Done.",1,1);
											}
											else
											{
												//First records is good enough.
												$out_line = "~~~top~~~".$data_array[0];
												$out_remain = "";

												//Set pointer to the next line.
												$out_pointer = 1;	
												if ($debug==1) $this->Cell(0,7,"    $g: First Line Fits, more coming.",1,1);
												
												$blob_on = true;
											}
										}
										
										$blob_array[$g]['min'] = $blobsize;
										$blob_array[$g]['total'] = $stringsize;
										$blob_array[$g]['limit'] = $colsize;
										$blob_array[$g]['array'] = $data_array;
										$blob_array[$g]['array_pointer'] = $out_pointer;
										$blob_array[$g]['line_remain'] = $out_remain;
										$blob_array[$g]['fill']=$fill;
										$blob_array['counter']=0;
										
										/*
										if(sizeof($data_array)>1)
										{
											$blob_on = true;
										}
										*/
										
										$page_array[$page][$x][$y][$g]=$out_line;
										
										if ($debug==1) $this->Cell(0,7,"         620: $g: Blob found - Using '$out_line' Min:$blobsize Total:$stringsize limit:$colsize Pointer:$out_pointer out of ".sizeof($data_array)." Lines and $out_remain Chars Remain",1,1);
									}
									
    									if ($debug==1) $this->Cell(0,7,"      625: $g: Data: Export field ".$data[$g]['field_name']." sumtype=".$data[$g]['sumtype']." thischeck=$thischeck group_data=$group_data record=$record UpdateArray=".implode(",",$update_array),1,1);	
    								}
    							}
    							//before we update the record number, we should figure out if it needs Summation  							
    							for($g=0;$g < $data['num_fields'];$g++)
    							{
								if ($data[$g]['sumtype']>0)
	    							{
    									//For each GroupLevel on the report, Up it's amount.
    									for($h=1;$h <= $data['highgroup'];$h++)
    									{
    										switch($data[$g]['sumtype'])
	    									{	
		    									case 2:
		    										//Sumtype of 2 = Dollars so add them together and remove any $
	    											$sum_array[$h][$g]+=preg_replace('/,|\$/','',$data[$g][$record]);
	    											break;
		    									case 3:
		    										//SumType of 3 - Numbers but remove crap
		    										$sum_array[$h][$g]+=preg_replace('/,/','',$data[$g][$record]);
		    										break;  								
		    									case 4:
		    										//SumType of 4 - Numbers but remove crap and put to 2 digits
		    										$sum_array[$h][$g]+=preg_replace('/,/','',$data[$g][$record]);
		    										break;
		    									case 5:
		    										//SumType of 5 - Average.
		    										$sum_array[$h][$g]+=preg_replace('/,/','',$data[$g][$record]);
		    										if ($data[$g][$record]<>0) $sum_array_nums[$h][$g]++;
		    										break;
		    									default:		
		    										//Sumtype of 1 (or unknown)
		    										$sum_array[$h][$g]+=preg_replace('/,/','',$data[$g][$record]);
		    										break;
	    									}
    										if ($debug==1) $this->Cell(0,7,"    660: THIS IS A GROUP SUMMATION FIELD WITH TYPE ".$data[$g]['sumtype']." Adding ".$data[$g][$record]." to ".$sum_array[$h][$g]." for group $h ",1,1);
										if ($debug==1 && $data[$g]['sumtype']=='5') $this->Cell(0,7,"         Average Field - record nums ".$sum_array_nums[$h][$g],1,1);
    									}
    									//Only count the report total once.
    									if ($data[$g]['sumtype']>0)
	    								{
    										//Sumtype of 1 = SUM so add them
    										$report_total_array[$g]+=preg_replace('/,/','',$data[$g][$record]);
    										if ($debug==1) $this->Cell(0,7,"         and field ".$data[$g]['field_name']." Report total is ".$report_total_array[$g],1,1);
    										if ($data[$g]['sumtype']=='5') 
    										{
    											//Average stuff
    											$report_sum_array_nums[$g]++;
    											if ($debug==1) $this->Cell(0,7,"         Average Field - record nums reporttot is ".$report_sum_array_nums[$g],1,1);
    										}
    									}
    									if ($debug==1) $this->Cell(0,7,"    670: THIS IS A SUMMATION FIELD WITH TYPE ".$data[$g]['sumtype']." Adding ".$data[$g][$record]." to ".$report_total_array[$g]." for group $h and field ".$data[$g]['field_name']." Report total is ".$report_total_array[$g],1,1);
    								}
    							}
    							
    							if(!$blob_on)
    							{
	    							$record++;
		    						//$fill=!$fill;
		    						$blob_array[$g]['fill']=$fill;
		    						if ($debug==1) $this->Cell(0,7,"    Data: (Fill) Record Increment ".$data[$g]['field_name']." thischeck=$thischeck group_data=$group_data record=$record UpdateArray=".implode(",",$update_array),1,1);
	    						}
    							//echo "Record==$record<br>";
    							
    							//First we'll go through the fields one by one and find ones that have 
    							//Groups - Increment record and decied what the next move is:
    							
    							for($g=0;$g < $data['num_fields'];$g++)
    								{
    								if (intval($data[$g]['grouplevel'])>0)
		    							{
    									//Then this is a group, set the last setting (to be checked below for differences)
    									if ($lastgroup_array[$g]===$data[$g][$record])
		    								{
    										//NOTHING.  Don't bother!
    										//$update_array[$data[$g]['grouplevel']]=0;
    										}
    									else
		    								{
    										//Then holy crap we have a change.  
    										
    										//If we're within one header of the end of this Column, We'll skip that number of cells then continue.
    										if ($x>=($column_cellnum-($headersize+1)))
	    										{
											if ($debug==1) $this->Cell(0,7,"  TOO CLOSE TO BOTTOM, WANT TO SKIP ".($column_cellnum-$x)." cells and continue",1,1);
											$skipthese=1;
    											}
    										
    										$update_array[$data[$g]['grouplevel']]=1;
	    									$lastgroup_array[$g]=$data[$g][$record];
    											
	    									//Forget Headers.
	    									//If COMPRESS is OFF then Output a header for this group!
	    									if (intval($this->rcompress)==0)
    											{
	    										//There's at least ONE, but it might be two.  So lets go to the earliest just in case
    											$header=1;
    											While ($update_array[$header]!=1 && $header<=$data['highgroup'])
	    											{
    												//Cycle through the headers
    												$header++;
    												//echo "$header<br>";
    												}
    											//echo "Found update for header=$header for cell $cur_cell<br>";
    											//If there aren't any more headers, then turn off header and allow Data to run.
    											if ($header>$data['highgroup']) $header=0;
	    										
    											//$header=$data[$g]['grouplevel'];
	    										
    											//Also set sumline to 1 for checking if there was a sum.
    											//(Use Update array to see which one to check)
    											if (intval($data['sumfields'])>0) 
    												{
    												//Then we want to decide if this is the last one, and if so we do the page break, or we allow it to go next.
    												if ($debug==1) $this->Cell(0,7,"  Need SUMMATION",1,1);
    												$sumline=$data['highgroup'];
    											
	    											}
    											else
	    											{
												if ($debug==1) $this->Cell(0,7,"  No SUMMATION",1,1);
												}
											}
										else
											{
											//Then we need to just spit out headerdata
											$headerdata=1;
											}
    										if ($debug==1) $this->Cell(0,7,"  FOUND GROUPCHANGE in group ".$data[$g]['grouplevel']." ".$data[$g]['field_name']." groupheader=$groupheader record=$record cell $x of $column_cellnum in column headersize=$headersize new_val=".$data[$g][$record]." UpdateArray=".implode(",",$update_array),1,1);
    										}
    									}
    								}
    							//Last thing to decide is if this is a non-group report and the report is DONE, we can spit out the final line
							if ($record == ($data['num_rows']) && $sumline==0 && $data['highgroup']==0) 
							{
								$sumline=-1;
								if ($data['highgroup']>0) $skiplines=1;
								if ($debug==1) $this->Cell(0,7,"  SUMLINE SET to -1",1,1);
							}
							//Also, if this is the last record on a Grouped report, set the fields needed for sums.
							if ($record == ($data['num_rows']) && $data['highgroup']>0) 
								{
								//Unless it's overridden with incoming $rep_total=0    							
    								if ($this->rep_total==0)
    									{
    									//Then Set Sumline to DONE to skip this from happening.
    									//$sumline=-2;	
    									if ($debug==1) $this->Cell(0,7,"  Rep_Total SET to 0",1,1);
    									}
    								else
									{
									//Go ahead and export it.
									for($h=1;$h <= $data['highgroup'];$h++)
    										{
    										$update_array[$h]=1;
    										}
    									$sumline=$data['highgroup'];
    									if ($debug==1) $this->Cell(0,7,"  Setting Final Sum Lines",1,1);
    									$header=0;
    									$reportsum=1;
    									}
								} 
							if ($this->pagebreak==1 && $header==1 && intval($data['sumfields'])==0)
    								{
    								//Then there's a page break, bu nobody is here to make it happen.  Lets make it happen.
    								if ($debug==1) $this->Cell(0,7,"  FOUND PAGEBREAK - Setting Skip to $cell_per_page-$cur_cell",1,1);
    								$skiplines=$cell_per_page-$cur_cell;
    								}
    							break;
    						Case 1==1:
    							//TRUE=Nothing - Skip!
    							for($g=0;$g < $data['num_fields'];$g++)
		    						{
		    						//Dump out blanks - Type 4 - Dummy Row
		    						$page_array[$page][$x][$y]['tabs']=0;
		    						$page_array[$page][$x][$y]['type']=4;
    								$page_array[$page][$x][$y][$g]="";
    								}
    							break;
    					}

					$fill=!$fill;
					if ($debug==1) $this->Cell(0,7,"Cell Done ($cur_cell/$cell_per_page) Column=$y, Col_Cell=$x page=$page header=$header skipthese=$skipthese sumline=$sumline headerdata=$headerdata record=$record UpdateArray=".implode(",",$update_array)." num_recs=".$data['num_rows'],1,1);
				}				
			}
			//Next page!
			$page++;
		}
			
		if ($debug==1) $this->Cell(0,7,"End Array_Load",1,1);
		
		//This section Renders my Page Array to the Screen.
		if ($debug==1) $this->AddPage();
		
		/*
		//Array Debug:
		echo "<pre>";
		print_r($page_array);		
		echo "</pre>";
		
		die();
		*/
		
		//if ($debug==1) $this->Cell(0,7,"Begin Render",1,1);
		
		//if ($debug==1) $this->AddPage();
						
		$record=0;
		$start_cell=0;
		$cur_cell=0;
		//Output Table
		//Page First
		for($y=0;$y < $page;$y++)
		{
			//Then Column
    			for($x=1;$x <= $column_cellnum;$x++)
    			{
    				//This is the beginning of one line
    				for($i=0;$i < $this->columns;$i++)
	    			{
    					$cur_cell=$start_cell+($x+($column_cellnum*$i));
    					$this->SetFont('Courier','B',$column_font,0,1);
    					//Insert the Group Tabbing (from the page_array)
    					if ($page_array[$y][$x][$i]['tabs']>0) $this->Cell(($tab_size)*intval($page_array[$y][$x][$i]['tabs']),0,'',0,0);
    					
    					//Default Cell Size is Data
    					$local_totalsize=$data['totalsize'];
    					$cell_room=$data_w;
    					
    					if ($page_array[$y][$x][$i]['type']==0)
    					{
    						//Header
    						//$this->SetFillColor(255,0,0);
    						$this->SetFillColor($this->header_color[0],$this->header_color[1],$this->header_color[2]);
    						$this->SetTextColor(255);
    						$fill=1;
    					}
    					if ($page_array[$y][$x][$i]['type']==1)
    					{
    						//Data
    						$this->SetFillColor($this->fill_color[0],$this->fill_color[1],$this->fill_color[2]);
    						$this->SetTextColor(0);
    					}
    					if ($page_array[$y][$x][$i]['type']==2)
    					{
    						//Group Header
    						$local_totalsize=$data['groupsize'.$page_array[$y][$x][$i]['group']];
    						$cell_room=$cell_w[$page_array[$y][$x][$i]['group']];
    						$this->SetFillColor($this->header_color[0],$this->header_color[1],$this->header_color[2]);
    						$this->SetTextColor(255);
    						$fill=1;
					}
					if ($page_array[$y][$x][$i]['type']==3)
    					{
    						//Group Data
    						$local_totalsize=$data['groupsize'.$page_array[$y][$x][$i]['group']];
    						$cell_room=$cell_w[$page_array[$y][$x][$i]['group']];
    						$this->SetTextColor(0);
    					}
    					if ($page_array[$y][$x][$i]['type']==4)
    					{
    						//Then this is a DUMMY row
    						$cellsize=$column_w;
    						$this->Cell($cellsize,$cell_h,'',0,0,'L',0);
    						//$this->Cell($cellsize,$cell_h,"Cell $cur_cell",1,0,'L',1);
    						$this->Cell($this->column_margin,0,'',0,0);
    					}
    					if ($page_array[$y][$x][$i]['type']==5)
    					{
    						//Then this is a SumLine
						$this->SetFillColor($this->foot_color[0],$this->foot_color[1],$this->foot_color[2]);
    						$this->SetTextColor(255);
    						//We insert the needed tab slotter that says 'Total' and goes from the Tab->Sumline
    						if ($data['highgroup']>0 && $page_array[$y][$x][$i]['reportsum']!=1) $this->Cell($tab_size*($data['highgroup']-$page_array[$y][$x][$i]['tabs']),$cell_h,"Total:",1,0,'L',1);
    						$this->SetTextColor(0);
    					}
    						
    					//This is the Left Right Border Type.
    					if ($page_array[$y][$x][$i]['type'] == 6)
    					{
    						$this->SetFillColor($this->fill_color[0],$this->fill_color[1],$this->fill_color[2]);
    						for($g=0;$g < $data['num_fields'];$g++)
		    				{
	    						//Unless this is a 'SKIP' (~~skip~~)
	    						if ($page_array[$y][$x][$i][$g]==="~~~skip~~~")
		    					{
	    							//Display NOTHING!
	    						}
	    						elseif ($page_array[$y][$x][$i][$g]==="~~~blank~~~")
		    					{
	    							//Display with only left/right borders
	    							//If the type==5 and the field is blank, then we'll fill with color ($fill)
	    							$cellsize=($data[$g]['maxsize']/$local_totalsize)*$cell_room;
    								$this->Cell($cellsize,$cell_h,'',0,0,'',0);
	    						}
	    						else
		    					{
	    							//If the type==5 and the field is blank, then we'll fill with color ($fill)
	    							//Lets see, is this the last cell? (added tag bot for bottom)
	    							if (substr($page_array[$y][$x][$i][$g],0,9)==="~~~bot~~~")
		    						{
			    						//Fill bottom too (Last record of a blob data)
			    						$out_string = substr($page_array[$y][$x][$i][$g],9);
		    						
			    						$cellsize=($data[$g]['maxsize']/$local_totalsize)*$cell_room;
	    								$this->Cell($cellsize,$cell_h,$out_string,'LBR',0,'',$page_array[$y][$x][$i]['fill']);
			    					}
			    					else
			    					{
		    							$cellsize=($data[$g]['maxsize']/$local_totalsize)*$cell_room;
	    								$this->Cell($cellsize,$cell_h,$page_array[$y][$x][$i][$g],'LR',0,'',$page_array[$y][$x][$i]['fill']);
	    							}
							}
						}
						$this->SetTextColor(0);
						if ($i<$this->columns && $this->columns!=1) $this->Cell($this->column_margin,0,'',0,0);
					}
						
    					//Inside this 'Cell' we want to spit out the individual cells.
    					if ($page_array[$y][$x][$i]['type'] != 4 && $page_array[$y][$x][$i]['type'] < 6)
    					{
    						for($g=0;$g < $data['num_fields'];$g++)
		    				{
	    						//Unless this is a 'SKIP' (~~skip~~)
	    						if ($page_array[$y][$x][$i][$g]==="~~~skip~~~")
		    					{
	    							//Display NOTHING!
	    						}
	    						elseif(substr($page_array[$y][$x][$i][$g],0,9)==="~~~ver~~~")
		    					{
		    						//Vertical Display (Won't this be fun.)
		    						$out_string = substr($page_array[$y][$x][$i][$g],9);
		    						//$out_string = $page_array[$y][$x][$i][$g];
		    						
	    							//Pull top off and use this.  It just fixes the borders.
	    							$cellsize=($data[$g]['maxsize']/$local_totalsize)*$cell_room;
    								if ($page_array[$y][$x][$i]['type']==5)
    								{
    									$this->Cell($cellsize,$cell_h,$out_string,1,0,'C',$page_array[$y][$x][$i][$g]==='');
    								}
    								else
    								{
    									//Get the current spot first
    									$cur_x = $this->GetX();
    									$cur_y = $this->GetY();
    									//Place an image there
    									//now go ahead and make the 
    									$this->Cell($cellsize,$cell_h,"",1,0,'C',$page_array[$y][$x][$i]['fill']);
    									
    									$this->Image("http://216.1.1.251/image_text.php?type=gif&bold=1&height=20&text=".urlencode($out_string)."&degree=90&fakeend=.gif",$cur_x,($cur_y-(strlen($out_string)*2.5))+$cell_h);
    								}	
		    					}
	    						elseif (substr($page_array[$y][$x][$i][$g],0,9)==="~~~top~~~")
		    					{
		    						$out_string = substr($page_array[$y][$x][$i][$g],9);
		    						//$out_string = $page_array[$y][$x][$i][$g];
		    						
	    							//Pull top off and use this.  It just fixes the borders.
	    							$cellsize=($data[$g]['maxsize']/$local_totalsize)*$cell_room;
    								if ($page_array[$y][$x][$i]['type']==5)
    								{
    									$this->Cell($cellsize,$cell_h,$out_string,'LTR',0,'L',$page_array[$y][$x][$i][$g]==='');
    								}
    								else
    								{
    									$this->Cell($cellsize,$cell_h,$out_string,'LTR',0,'L',$page_array[$y][$x][$i]['fill']);
    								}
							}
	    						else
		    					{
	    							//If the type==5 and the field is blank, then we'll fill with color ($fill)
	    							$cellsize=($data[$g]['maxsize']/$local_totalsize)*$cell_room;
    								if ($page_array[$y][$x][$i]['type']==5)
    								{
    									$this->Cell($cellsize,$cell_h,$page_array[$y][$x][$i][$g],1,0,'L',$page_array[$y][$x][$i][$g]==='');
    								}
    								else
    								{
    									$this->Cell($cellsize,$cell_h,$page_array[$y][$x][$i][$g],1,0,'L',$page_array[$y][$x][$i]['fill']);
    								}
							}
						}
						$this->SetTextColor(0);
						if ($i<$this->columns && $this->columns!=1) $this->Cell($this->column_margin,0,'',0,0);
					}
    				}
    				$this->SetTextColor(0);
    				$this->Cell(0,$cell_h,'',0,1);
    				//This is the end of the Line
    			}		
    		}
    		//if ($debug==1) $this->Cell(0,7,"End Render",1,1);
	}
}

?>