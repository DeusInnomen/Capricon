<?php
	session_start();
	include_once('includes/functions.php');
	
	DoCleanup();	
	if(isset($_POST["action"]))
	{
		if($_POST["action"] == "DoBulk")
		{
			$IDs = explode("|", $db->real_escape_string($_POST["IDs"]));
			$result = $db->query("SELECT Permission FROM PermissionDetails WHERE Permission NOT IN ('SuperAdmin', 'ConChair')");
			$permissions = array();
			while($row = $result->fetch_array())
				$permissions[] = $row["Permission"];
			$result->close();
			
			foreach($permissions as $permission)
			{
				if(isset($_POST['delPerm' . $permission]))
				{
					foreach($IDs as $id)
						$db->query("DELETE FROM Permissions WHERE PeopleID = $id AND Permission = '$permission'");
				}	
				if(isset($_POST['addPerm' . $permission]))
				{
					foreach($IDs as $id)
					{
						// Delete it just in case they already had it, prevents doubled-up permissions.
						$db->query("DELETE FROM Permissions WHERE PeopleID = $id AND Permission = '$permission'");
						
						// Concom permissions expire on March 31st after the current convention year.
						if(strtolower($permission) == "concom")
						{
							$year = date("n") > 3 ? date("Y") + 1: date("Y");
							$db->query("INSERT INTO Permissions (PeopleID, Permission, Expiration) VALUES ($id, '$permission', '$year-03-31')");
						}
						else
							$db->query("INSERT INTO Permissions (PeopleID, Permission) VALUES ($id, '$permission')");
					}
				}
			}
			echo '{ "success": true, "message": "Permissions successfully applied." }';
		}
		else
			echo '{ "success": false, "message": "An unknown error has occurred." }';
	}
	else
		echo '{ "success": false, "message": "An unknown error has occurred." }';
?>


			