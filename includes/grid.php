<?php

function table_sql($in_sql)
{
	include("includes/dsn.inc");
	
	$result = $db->query($in_sql);
	
	$people = array();
	if($result) {
		while($row = $result->fetch_assoc())
			$out_data[] = $row;
		$result->close();	
		
		return table_array($out_data);
	}
	else
	{
		return false;
	}
}

function table_array($in_array, $keys = null)
{	
	//First off, only does something if there's a value.  Otherwise returns FALSE.
	if(sizeof($in_array)>0)
	{
		//Output String
		$out_str = "";
		
		//Lets build a list of Keys, on the understanding this works right only if you use with a true associative array.
		//We know it's larger than one line - ASSUMES UNIFORM KEYS IN ARRAY
		if($keys === null)
		{
			$keys = array_keys($in_array[0]);
			/*
			echo "<pre>";
			print_r($keys);
			echo "</pre>";
			*/
		}
		
		//Silly Test
		$out_str .= '<div class="standardTable">'."\r\n";;
		$out_str .= '	<form id="artistForm" method="post">'."\r\n";;
		$out_str .= '	<table>'."\r\n";;
		//$out_str .= '		<tr><th>Name</th><th>Display Name</th></th><th>Artist?</th><th>Email</th><th>Applied?</th><th>Inventories</th></tr>';
		$out_str .= '<tr>';
			foreach($keys as $key)
			{
				$out_str .= '<th>'.$key.'</th>';
			}
		$out_str .= '</tr>'."\r\n";
			foreach($in_array as $row)
			{
				$out_str .= '<tr>';
					foreach($keys as $key)
					{
						$out_str .= '<td>'.$row[$key].'</td>';
					}
					/*
					$out_str .= "<tr><td>" . $person["Name"] . "</td><td>" . $person["DisplayName"] . "</td><td style=\"text-align: center;\"><input type=\"checkbox\" id=\"" . 
						$person["PeopleID"] . "\" current=\"" . $person["Artist"] . "\" " .
						($person["Artist"] == 1 ? "checked" : "") . "/></td><td>" . $person["Email"] . "</td><td style=\"text-align: center;\"><input type=\"checkbox\" " .
						($person["Applied"] == 1 ? "checked" : "") . " disabled /></td><td style=\"text-align: center;\">" . 
						($person["Applied"] == 1 && DoesUserBelongHere("ArtShowLead") ? "<a href=\"artistSubmissions.php?attendID=" . $person["ArtistAttendingID"] . 
						"\">Manage Inventory</a>" : "") . "</td></tr>\r\n";
					*/
				$out_str .= '</tr>'."\r\n"; //Nice use of new line to make the source readible.
			}
		$out_str .= '	</table>'."\r\n";;
		$out_str .= '	<br />';
		$out_str .= '	</form>';
		$out_str .= '</div>'."\r\n";

		return $out_str;
	}
	else
	{
		return false;
	}
}


?>