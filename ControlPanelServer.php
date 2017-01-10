<?php include("SecurePage.php"); ?>
<?php include("GlobalVariables.php"); ?>
<?php include("GlobalFunctions.php"); ?>
<?php

if($_SESSION['Permissions']['view_controls'] != 1)
{
	header("Location:".$MainIndex);
}

Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab);

if(isset($_GET["DisplayPanel"]) == true)
{
	switch ($_GET["DisplayPanel"]) 
	{
    case 0:
		DisplayAddUser();
        break;
    case 1:
		DisplayRemoveUser();
        break;
    case 2:
        DisplayEditUsers();
        break;
	case 3:
		DisplayAddDataField();
        break;
	case 4:
		DisplayRemoveDataField();
        break;
	case 5:
		DisplayEditDataField();
        break;
	case 6:
		DisplayAddDataSet();
		break;
	case 7:
		DisplayRemoveDataSet();
		break;
	case 8:
		DisplayEditDataSet();
		break;
	case 9:
		DisplayAddProject();
		break;
	case 10:
		DisplayRemoveProjects();
		break;
	case 11:
		DisplayEditProjects();
		break;
	case 12:
		DisplayDownloadData();
		break;
	case 13:
		DisplayUploadReviewSet();
		break;
	case 14:
		DisplayROIStatistics();
		break;
	case 15:
		DisplayClearReviewSet();
		break;
	case 16:
		DisplayUploadUserDataSet();
		break;
	case 17:
		DisplayDownloadCounts();
		break;
	case 18:
		DisplayDownloadUsers();
		break;
	}
}

if(isset($_POST["EditUsers"]) == true && isset($_POST['id']) == true && $_POST['id'] != "")
{
	$EditArray = array();
	$DataArray = array();
	$sql = "SELECT `index`,`edit` FROM `users_rep`";
	$result = mysql_query($sql);
	
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$EditArray[$row["index"]] = $row["edit"];
	}
	
	foreach(array_keys($_POST) as $value)
	{
		if(isset($EditArray[$value]) && $EditArray[$value] == true)
		{
			array_push($DataArray, " `" . mysql_prep($value) . "` = '" . mysql_prep($_POST[$value]) . "' ");
		}
	}
	
	$sql = "UPDATE `cialab`.`users_data` SET " . join(",",$DataArray) . " WHERE `id` = " . mysql_prep($_POST["id"]);
	$result = mysql_query($sql);
	if (!$result) 
	{
		$error = "<div style='color:red'>Error: Could not add to database</div>";
		echo $sql;
	}
	else
	{
		echo "<div style='color:green'>User Data Saved Sucessfully</div>
		";
	}
}

if(isset($_POST["RemoveUser"]) == true && isset($_POST['id']) == true && $_POST['id'] != "")
{
	$sql = "DELETE FROM `cialab`.`users_data` WHERE `id` = ".mysql_prep($_POST["id"]).";";
	$result = mysql_query($sql);
	if (!$result) 
	{
		$error = "<div style='color:red'>Error: Could not remove user</div>";
		echo $sql;
	}
	else
	{
		echo "<div style='color:green'>User Removed Sucessfully</div>
			<SCRIPTCOMMAND>removeOption('UserSelection','".mysql_prep($_POST["id"])."');";
	}
}
if(isset($_POST["RemoveDataField"]) == true && isset($_POST['FieldSelection']) == true && $_POST['FieldSelection'] != "")
{
	$sql = "SELECT `index`,`name` FROM `cialab`.`slides_rep` WHERE `id` = '".mysql_prep($_POST["FieldSelection"])."'";
	$row = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
	$index = $row['index'];
	
	$sql = "DELETE FROM `cialab`.`slides_rep` WHERE `id` = '".mysql_prep($_POST["FieldSelection"])."';";
	$result = mysql_query($sql);
	if (!$result) 
	{
		$error = "<div style='color:red'>Error: Could not remove Field Error 1</div>";
		echo $sql;
	}
	else
	{
		$sql = "ALTER TABLE `cialab`.`slides_data` DROP COLUMN `".$index."`;";
		$result = mysql_query($sql);
		if (!$result) 
		{
			$error = "<div style='color:red'>Error: Could not remove Field, Error 2</div>";
			echo $sql;
		}
		else
		{
			echo "<div style='color:green'>Field Removed Sucessfully</div>
			<SCRIPTCOMMAND>removeOption('FieldSelection','".mysql_prep($_POST["FieldSelection"])."');";
		}
	}
}

if(isset($_POST["AddDataField"]) == true && isset($_POST['name']) == true && $_POST['name'] != "")
{	
	//Collect Side_Rep Fields
	$slides_rep_types = array();
	$sql = "SELECT `index` FROM `slides_rep_types`";
	$result = mysql_query($sql);
	$DataFieldNumber = -1;
	while($row = mysql_fetch_array($result, MYSQL_NUM))
	{
		array_push($slides_rep_types,$row[0]);
	}
	
	//Find Next DataField #
	$sql = "SELECT `index` FROM `slides_rep`";
	$result = mysql_query($sql);
	$DataFieldNumber = -1;
	while($row = mysql_fetch_array($result, MYSQL_NUM))
	{
		if (preg_match("/^datafield_(?=\d)/",$row[0]))
		{
			$value = preg_replace("/datafield_/", "", $row[0]);
			if ($value > $DataFieldNumber)
			{
				$DataFieldNumber = $value;
			}
		}
	}
	$DataFieldNumber = $DataFieldNumber +1;
	//echo $DataFieldNumber . "<br>";
	$content = "";
	if($_POST['data_content'] == 0)
	{
		$content = "DOUBLE";
	}
	else
	{
		$content = "TEXT";
	}
	$sql = "ALTER TABLE `slides_data` ADD `datafield_".$DataFieldNumber."` ".$content." NOT NULL;";
	$result = mysql_query($sql);
	if (!$result) 
	{
		
		$error = "<div style='color:red'>Error: Could not add field.</div>";
		echo $sql;
	}
	else
	{
		$array_values = array();
		//Add the row to the slide_rep
		foreach(array_keys($_POST) as $value)
		{
			if($value != "data_content" && in_array($value,$slides_rep_types) == true && $value != "index")
			{
				$array_values[$value] = mysql_prep($_POST[$value]);
			}
		}
		$sql = "INSERT INTO `slides_rep` (`index`,`".join("`,`",array_keys($array_values))."`) VALUES ('datafield_".$DataFieldNumber."','".join("','",$array_values)."')";
		$result = mysql_query($sql);
		if (!$result) 
		{
			
			$error = "<div style='color:red'>Error: Could not add field.</div>";
			echo $sql;
		}
		else
		{
			
			echo "<div style='color:green'>Added Field</div>";
		}
	}
}

if(isset($_POST["AddUser"]) == true)
{
	if(isset($_POST['email']) == true && $_POST['email'] != "" && isset($_POST['password']) == true && $_POST['password'] != "" && isset($_POST['rank']) == true && $_POST['rank'] != "")
	{
		$sql = "SELECT `id` FROM `cialab`.`users_data` WHERE `email` = '".mysql_prep($_POST['email'])."'";
		$result = mysql_query($sql);
		if (mysql_num_rows($result) >= 1)
		{
			echo "<div style='color:red'>The email address is already being used by another user.</div>";
		}
		else
		{
			if (mysql_prep($_POST['email']) == $_POST['email'])
			{
				if(mysql_prep($_POST['password']) == $_POST['password'])
				{
					$EditArray = array();
					$DataArray = array();
					$sql = "SELECT `index`,`edit` FROM `users_rep`";
					$result = mysql_query($sql);
					
					while($row = mysql_fetch_array($result, MYSQL_ASSOC))
					{
						$EditArray[$row["index"]] = $row["edit"];
					}
					
					foreach(array_keys($_POST) as $value)
					{
						if(isset($EditArray[$value]) && isset($_POST[$value]) && $EditArray[$value] == true && mysql_prep($_POST[$value]) != "")
						{
							$DataArray[mysql_prep($value)] = mysql_prep($_POST[$value]);
						}
					}
					
					$sql = "INSERT INTO `cialab`.`users_data` (`" . join("`,`",array_keys($DataArray)) . "`) VALUES ('" . join("','",$DataArray) . "');";
					$result = mysql_query($sql);
					if (!$result) 
					{
						echo "<div style='color:red'>Error: Could not add user to database</div>";
					}
					else
					{
						echo "<div style='color:green'>User Added Sucessfully</div>";
					}
				}
				else
				{
					echo "<div style='color:red'>Password supplied is not valid</div>";
				}
			}
			else
			{
				echo "<div style='color:red'>Enter in a Valid Email Address.</div>";
			}
		}
	}
	else
	{
		echo "<div style='color:red'>You must enter at least an Email, Password, and Rank</div>";
	}
}
if(isset($_POST['AddDataSet']) == true && isset($_POST['dataSetName']) && $_POST['dataSetName'] != "" && isset($_POST['datavalue_0']) && $_POST['datavalue_0'] != "")
{
	//print_r($_POST);
	$dataValues = array();
	$dataSetName = mysql_prep($_POST['dataSetName']);
	foreach(array_keys($_POST) as $value)
	{
		$splitArray = explode("datavalue_",$value);
		if(sizeof($splitArray) == 2 && $splitArray[0] == "" && is_numeric($splitArray[1]) == true && mysql_prep($_POST[$value]) != "")
		{
			array_push($dataValues,mysql_prep($_POST[$value]));
		}
	}
	
	//$sql = "SELECT MAX(`value`) FROM `cialab`.`dataset_0`";
	//$sqlArray = mysql_fetch_array(mysql_query($sql), MYSQL_NUM);
	//$nextNumber = $sqlArray[0] + 1;
	
	//Find Next dataset #
	$sql = "SHOW TABLES";
	$result = mysql_query($sql);
	$DataSetNumber = -1;
	while($row = mysql_fetch_array($result, MYSQL_NUM))
	{
		if (preg_match("/^dataset_(?=\d)/",$row[0]))
		{
			$value = preg_replace("/dataset_/", "", $row[0]);
			if ($value > $DataSetNumber)
			{
				$DataSetNumber = $value;
			}
		}
	}
	$DataSetNumber = $DataSetNumber +1;

	$sql = "INSERT INTO `cialab`.`dataset_0` (`name`,`value`,`edit`) VALUES ('".$dataSetName."','dataset_".$DataSetNumber."','1')";
	$result = mysql_query($sql);
	if (!$result)
	{
		echo "Could not add dataset to list of datasets";
	}
	else
	{		
		$sql = "CREATE TABLE `cialab`.`dataset_".$DataSetNumber."` 
		(
			`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`name` TEXT NOT NULL ,
			`value` INT(5) NOT NULL,
			`order` INT(4) NOT NULL
		) ENGINE = MYISAM ;";
		
		$result = mysql_query($sql);
		if (!$result)
		{
			echo "Could not create Dataset Table.";
		}
		else
		{
			$successful = true;
			foreach(array_keys($dataValues) as $value)
			{
				$sql = "INSERT INTO `cialab`.`dataset_".$DataSetNumber."` (`name`,`value`) VALUES ('".$dataValues[$value]."','".$value."')";
				$result = mysql_query($sql);
				
				if (!$result)
				{
					echo "Could not insert values into dataset";
					$successful = false;
				}
			}
			if ($successful == true)
			{
				echo "<div style='color:green'>Data Set Created Sucessfully</div>";
			}
		}
	}
}
if (isset($_POST["RemoveDataSet"]) == true && isset($_POST['FieldSelection']))
{
	$sql = "SELECT * FROM `cialab`.`dataset_0` WHERE `id` = '".mysql_prep($_POST['FieldSelection'])."';";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	
	$sql = "SELECT COUNT(*) FROM `cialab`.`slides_rep` WHERE `type_id` = '".$row['value']."';";
	$sqlArray = mysql_fetch_array(mysql_query($sql),MYSQL_NUM);

	if ($sqlArray[0] == 0 && $row['crucial'] == false)
	{
		$sql = "DROP TABLE `cialab`.`dataset_".$row['value']."`;";
		$result = mysql_query($sql);
		if (!$result)
		{
			echo "Error occured while droping table.";
		}
		else
		{
			$sql = "DELETE FROM `cialab`.`dataset_0` WHERE `value` = '".$row['value']."'";
			$result = mysql_query($sql);
			if (!$result)
			{
				echo "Error occured while deleting the entry.";
			}
			else
			{
				echo "<div style='color:green'>Data Set Sucessfully</div><SCRIPTCOMMAND>removeOption('FieldSelection','".mysql_prep($_POST["FieldSelection"])."');";
			}
		}
	}
	else
	{
		echo "<div style='color:red'>This data set cannot be deleted because it is currently associated with a field</div>";
	}	
}

if (isset($_POST["RemoveProject"]) == true && isset($_POST['projects']))
{
	if(is_numeric($_POST['projects']))
	{
		$successful = true;
		
		//Clean the projects Id
		$projectID = mysql_prep($_POST['projects']);
		
		//Remove all the users associated with the project
		$sql = "DELETE FROM `cialab`.`roi_projects_members` WHERE `roi_project_id` = '".$projectID."';";
		$result = mysql_query($sql);
		if (!$result) 
		{
			$successful = false;
			echo "<div style='color:red'>Error: Could not remove users from project</div>";
		}
		
		//Remove all the data, CB markings, associated with the project
		$sql = "DELETE FROM `cbmarker`.`cbdata` WHERE `project_id` = '".$projectID."';";
		$result = mysql_query($sql);
		if (!$result) 
		{
			$successful = false;
			echo "<div style='color:red'>Error: Could not delete marking data.</div>";
		}
		
		//Remove project entry and project details
		$sql = "DELETE FROM `cialab`.`roi_projects` WHERE `id` = '".$projectID."';";
		$result = mysql_query($sql);
		if (!$result) 
		{
			$successful = false;
			echo "<div style='color:red'>Error: Could not delete project Entry.</div>";
		}
		
		if($successful == true)
		{
			echo "<div style='color:green'>Project Successfully Removed</div>";
		}
	}
}

if(isset($_POST["EditDataFields"]) == true && isset($_POST['id']) == true && $_POST['id'] != "")
{
	$DataArray = array();
	$ValidIndex = array();
	
	$sql = "SELECT `index` FROM `slides_rep_types`";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		//echo $row['index'];
		$ValidIndex[$row['index']] = $row['index'];
	}
	
	foreach(array_keys($_POST) as $value)
	{
		if(array_key_exists($value,$ValidIndex) == true)
		{
			array_push($DataArray, " `" . mysql_prep($value) . "` = '" . mysql_prep($_POST[$value]) . "' ");
		}
	}
	
	$sql = "UPDATE `cialab`.`slides_rep` SET " . join(",",$DataArray) . " WHERE `id` = " . mysql_prep($_POST["id"]);
	$result = mysql_query($sql);
	if (!$result) 
	{
		$error = "<div style='color:red'>Error: Could not add to database</div>";
	}
	else
	{
		echo "<div style='color:green'>Field Data Saved Sucessfully</div>";
	}
}
if(isset($_POST["AddProject"]) == true && isset($_POST['name']) == true && $_POST['name'] != "" && isset($_POST['folder']) == true && $_POST['folder'] != "" && isset($_POST['selected_users']))
{
	//print_r($_POST);
	
	//Add the project First to the roi_projects table
	
	$EditArray = array();
	$DataArray = array();
	
	$sql = "SELECT `index`,`edit` FROM `roi_projects_rep`";
	$result = mysql_query($sql);
	
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$EditArray[$row["index"]] = $row["edit"];
	}
	
	foreach(array_keys($_POST) as $value)
	{
		if(isset($EditArray[$value]) && isset($_POST[$value]) && $EditArray[$value] == true && mysql_prep($_POST[$value]) != "")
		{
			$DataArray[mysql_prep($value)] = mysql_prep($_POST[$value]);
		}
	}
	
	$DataArray["date_created"] = mysql_prep(date("Y-m-d G:i:s"));
	
	$sql = "INSERT INTO `cialab`.`roi_projects` (`" . join("`,`",array_keys($DataArray)) . "`) VALUES ('" . join("','",$DataArray) . "');";
	$result = mysql_query($sql);
	if (!$result) 
	{
		echo "<div style='color:red'>Error: Could not add Project to database</div>";
	}
	else
	{
		echo "<div style='color:green'>Project Added Sucessfully</div>";
	}
	
	// Then Add the Users to the project in the roi_projects_members
	
	//Change the array to match a select instead of an insert like above
	$selectedUsers = $_POST['selected_users'];
	$selectArray = array();
	
	foreach(array_keys($DataArray) as $value)
	{
		array_push($selectArray,"`".$value . "` = '" . $DataArray[$value]."'");
	}
	
	$sql = "SELECT `id` FROM `cialab`.`roi_projects` WHERE " . join(" AND ",$selectArray) . ";";
	$tempRow = mysql_fetch_array(mysql_query($sql));
	$id = $tempRow['id'];
	
	$errorOccured = false;
	foreach($selectedUsers as $user)
	{
		$sql = "INSERT INTO `cialab`.`roi_projects_members` (`user_id`,`roi_project_id`) VALUES ('".mysql_prep($user)."','$id');";
		$result = mysql_query($sql);
		if (!$result) 
		{
			$errorOccured = true;
			echo "<br><br>".$sql;
		}
	}
	
	if($errorOccured == true)
	{
		echo "<div style='color:red'>Error: Could not add all Users to Project</div><NOFADE>";
	}
	else
	{
		echo "<div style='color:green'>All users added to project.</div>";
	}
}

if(isset($_POST["EditProjects"]) == true && isset($_POST['name']) == true && $_POST['name'] != "" && isset($_POST['folder']) == true && $_POST['folder'] != "" && isset($_POST['selected_users']))
{
//	print_r($_POST);

//First Update the project Data
	$EditArray = array();
	$DataArray = array();
	$sql = "SELECT `index`,`edit` FROM `roi_projects_rep`";
	$result = mysql_query($sql);
	$ProjectID = mysql_prep($_POST["id"]);
	
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$EditArray[$row["index"]] = $row["edit"];
	}
	
	foreach(array_keys($_POST) as $value)
	{
		if(isset($EditArray[$value]) && $EditArray[$value] == true)
		{
			array_push($DataArray, " `" . mysql_prep($value) . "` = '" . mysql_prep($_POST[$value]) . "' ");
		}
	}
	
	$sql = "UPDATE `cialab`.`roi_projects` SET " . join(",",$DataArray) . " WHERE `id` = " . $ProjectID;
	$result = mysql_query($sql);
	if (!$result) 
	{
		$error = "<div style='color:red'>Error: Could not add to database</div>";
		echo $sql;
	}
	else
	{
		echo "<div style='color:green'>Project Data Saved Sucessfully</div>";
	}
	

	// Then remove all users for a project and Add the new Users to the project in the roi_projects_members
	
	$sql = "DELETE FROM `roi_projects_members` WHERE `roi_project_id`='".$ProjectID."'";
	$result = mysql_query($sql);
	
	//Change the array to match a select instead of an insert like above
	$selectedUsers = $_POST['selected_users'];
	
	$errorOccured = false;
	foreach($selectedUsers as $user)
	{
		$sql = "INSERT INTO `cialab`.`roi_projects_members` (`user_id`,`roi_project_id`) VALUES ('".mysql_prep($user)."','".$ProjectID."');";
		$result = mysql_query($sql);
		if (!$result) 
		{
			$errorOccured = true;
			echo "<br><br>".$sql;
		}
	}
	
	if($errorOccured == true)
	{
		echo "<div style='color:red'>Error: Could not add all Users to Project</div><NOFADE>";
	}
	else
	{
		echo "<div style='color:green'>All users added to project.</div>";
	}
}

if(isset($_POST["DownloadData"]) == true && isset($_POST['projects']) == true && $_POST['projects'] != "" && isset($_POST['users']) == true && $_POST['users'] != "")
{

	$project_id = mysql_prep($_POST['projects']);
	$users_id = mysql_prep($_POST['users']);
	echo "<SCRIPTCOMMAND>window.open('".$URL."/download.php?project=".$project_id."&user=".$users_id."');";
	//header("Location: ".$URL."/download.php?project=".$project_id."&user=".$users_id);

}

if(isset($_POST["DownloadCounts"]) == true && isset($_POST['projects']) == true && $_POST['projects'] != "" && isset($_POST['users']) == true && $_POST['users'] != "")
{
	$project_id = mysql_prep($_POST['projects']);
	$users_id = mysql_prep($_POST['users']);
	echo "<SCRIPTCOMMAND>window.open('".$URL."/downloadcounts.php?project=".$project_id."&user=".$users_id."');";
}

if(isset($_POST["DownloadUserTable"]) == true)
{

	$project_id = mysql_prep($_POST['projects']);
	$users_id = mysql_prep($_POST['users']);
	echo "<SCRIPTCOMMAND>window.open('".$URL."/downloadusertable.php');";
}

if(isset($_POST['ClearReviewSet']) == true && isset($_POST['projects']))
{
	$project_id = mysql_prep($_POST['projects']);
	$sql = "DELETE FROM `cbmarker`.`cbdata` WHERE `project_id` = '".$project_id."' AND `review_mark` = '1' AND `userid` = '0';";
	$result = mysql_query($sql);
	if($result)
	{
		echo "Review Set Successfuly Cleared";
	}
	else
	{
		echo "ERROR: Review Set Could Not Be Cleared";
	}
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////DISPLAY CONTROL PANELS FUNCTIONS/////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

function DisplayEditUsers()
{
	echo"<div id='PanelTop'>
		Edit Users
	</div>
	<div id='PanelBottom'>
		<div id='Users' style='Float:left;width:190px;padding:5px;'>
		<b>Users:</b>
		<SELECT NAME='' style=\"Width: 180px;\" SIZE = 12 onchange=\"UpdateUserControls(this.value);\">";


	$sql = "SELECT `first_name`,`last_name`,`id` FROM `users_data`";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
			echo "<OPTION Value='".$row['id']."'>".$row["first_name"]." ".$row["last_name"]."</OPTION>";
	}

	echo"</SELECT>
		</div>
		<form action=\"javascript:get('DataFields','EditUsers','Results')\" name='EditUsersForm' id='EditUsersForm'>
		<div id='DataFields' style='width:400px; float:left;'>

		</div>
		</form>
	</div>";
}

function DisplayAddUser()
{

	echo"<div id='PanelTop'>
			Add Users
		</div>
		<div id='PanelBottom' style='text-align:center;'>
		<form action=\"javascript:get('DataFields','AddUser','Results')\" name='AddUserForm' id='AddUserForm'>
		<div id='DataFields' style='width:100%; float:left;'>
		<table style='margin-right:auto;margin-left:auto;'>
		";

	$sql = "SELECT * FROM `users_rep`";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
			echo ReturnDataField($row['index'],"","","145px",false,"",true,"users_rep",true);
	}

	echo"
			<tr><td><br></td><td></td><td></td></tr>
			<tr>
			<td></td>
			<td></td>
			<td><input type='submit' name='Add User' value='Add User'></td>
			</tr>
		</table>


		</div>
		</form>
	</div>";
}
function DisplayRemoveUser()
{
	echo"<div id='PanelTop'>
			Remove Users
		</div>
		<div id='PanelBottom' style='text-align:center;'>
		<form action=\"javascript:get('DataFields','RemoveUser','Results',true,'Are you sure you want to remove this user? This cannot be reversed. All data for this user will be destroyed, including their ROI Markings.')\" name='RemoveUserForm' id='RemoveUserForm'>
		<div id='DataFields' style='width:100%; float:left;'>
		<table style='margin-right:auto;margin-left:auto;'>	
		<tr><td></td><td></td><td><b>Users:</b></td></tr>
		<tr><td></td><td></td><td>
		<SELECT NAME='id' id='UserSelection' style='Width: 190px;' SIZE = 12>";

		$sql = "SELECT `first_name`,`last_name`,`id`,`email` FROM `users_data`";
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
				echo "<OPTION Value='".$row['id']."'>".$row["first_name"]." ".$row["last_name"]."</OPTION>";
		}

		echo"</SELECT>
		</td></tr>
			<tr><td><br></td><td></td><td></td></tr>
			<tr>
			<td></td>
			<td></td>
			<td><input type='submit' name='Add User' value='Remove User'></td>
			</tr>
		</table>


		</div>
		</form>
	</div>";
}

function DisplayAddDataField()
{
	echo"<div id='PanelTop'>
		Add Data Fields
	</div>
	<div id='PanelBottom' style='text-align:center;'>
	<form action=\"javascript:get('DataFields','AddDataField','Results')\" name='AddDataFieldForm' id='AddDataFieldForm'>
	<div id='DataFields' style='width:100%; float:left;'>
	<table style='margin-right:auto;margin-left:auto;'>
	";
	
	$sql = "SELECT * FROM `slides_rep_types` ORDER BY `id` ASC";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		//echo ReturnDataField($row['index'],"","","145px",false," onfocus=\"document.getElementById('Results').innerHTML = 'test';\"",true,"slides_rep_types");
		echo ReturnDataField($row['index'],"","","145px",false,"",true,"slides_rep_types",true);
	}
	

	echo"
			<tr><td><br></td><td></td><td></td></tr>
			<tr>
			<td></td>
			<td></td>
			<td><input type='submit' name='AddDataField' value='Add Data Field'></td>
			</tr>
		</table>


		</div>
		</form>
	</div>";

}

function DisplayRemoveDataField()
{
	echo"
	<div id='PanelTop'>
		Remove Field
	</div>
	<div id='PanelBottom' style='text-align:center;'>
	<form action=\"javascript:get('DataFields','RemoveDataField','Results',true,'Are you sure you want to remove this field? This cannot be reversed. All data in this field will be destroyed')\" name='RemoveDataField' id='RemoveDataField'>
	<div id='DataFields' style='width:100%; float:left;'>
	<table style='margin-right:auto;margin-left:auto;'>	
	<tr><td></td><td></td><td><b>Fields:</b></td></tr>
	<tr><td></td><td></td><td>
	<SELECT NAME='FieldSelection' id='FieldSelection' style='Width: 190px;' SIZE = 12>";

	$sql = "SELECT `name`,`id`,`index` FROM `slides_rep`";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
			echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
	}

	echo"</SELECT>
	</td></tr>
		<tr><td><br></td><td></td><td></td></tr>
		<tr>
		<td></td>
		<td></td>
		<td><input type='submit' name='RemoveField' value='Remove Field'></td>
		</tr>
	</table>


	</div>
	</form>
	</div>";
}

function DisplayEditDataField()
{
	echo"<div id='PanelTop'>
		Edit Data Fields
	</div>
	<div id='PanelBottom'>
		<div id='Users' style='Float:left;width:190px;padding:5px;'>
		<b>Data Fields:</b>
		<SELECT NAME='' style=\"Width: 180px;\" SIZE = 12 onchange=\"UpdateDataFieldControls(this.value);\">";


	$sql = "SELECT `id`,`index`,`name` FROM `slides_rep`";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
			echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
	}

	echo"</SELECT>
		</div>
		<form action=\"javascript:get('DataFields','EditDataFields','Results')\" name='EditUsersForm' id='EditUsersForm'>
		<div id='DataFields' style='width:400px; float:left;'>

		</div>
		</form>
	</div>";
}

function DisplayAddDataSet()
{
	echo "
	<div id='PanelTop'>
		Add Data Set
	</div>
	<div id='PanelBottom' style='text-align:center;'>
	<form action=\"javascript:get('DataFields','AddDataSet','Results')\" name='AddUserForm' id='AddUserForm'>
	<div id='DataFields' style='width:100%; float:left;'>
	<table id='DataSet' style='margin-right:auto;margin-left:auto;'>
		<TBODY>
			<tr>
				<td style='text-align: right;'>Name of Data Set: </td>
				<td></td>
				<td>
					<input type='text' style='width: 145px;' name='dataSetName' value='' tiptitle='This specifies the Name of the Data Set.'>
				</td>
				<td></td>
			</tr>
			<tr>
				<td style='text-align: right;'>Value: </td>
				<td></td>
				<td>
					<input type='text' style='width: 145px;' name='datavalue_0' value='' tiptitle='This specifies the first name of the user.'>
				</td>
				<td>
				<a href='javascript:AddField();' tiptitle='Add Another Field'>
					<img src='./images/plus.png' border='0px'></img>
				</a>
				</td>
			</tr>
		</TBODY>
		<TBODY id='DataSetFields'>
		</TBODY>
		<TBODY>
			<!--
			<tr>
				<td></td>
				<td></td>
				<td>
				<a href='javascript:AddField();' style='text-decoration:none;'>Add Another Field</a>
				</td>
			</tr>
			-->
			<tr>
				<td></td>
				<td></td>
				<td>
				<input type='submit' value='Add Data Set' name='submit'>
				</td>
			</tr>
		</TBODY>
	</table>
	</div>
	</form>
	</div>
	";
}

function DisplayRemoveDataSet()
{
	echo "
	<div id='PanelTop'>
		Remove Data Set
	</div>
	<div id='PanelBottom' style='text-align:center;'>
	<form action=\"javascript:get('DataFields','RemoveDataSet','Results',true,'Are you sure you want to remove this Data Set? This cannot be reversed. All data in this set will be destroyed')\" name='RemoveDataSet' id='RemoveDataSet'>
	<div id='DataFields' style='width:100%; float:left;'>
	<table style='margin-right:auto;margin-left:auto;'>	
	<tr><td></td><td></td><td><b>Non-Crucial Data Sets:</b></td></tr>
	<tr><td></td><td></td><td>
	<SELECT NAME='FieldSelection' id='FieldSelection' style='Width: 190px;' SIZE = 12>";

	$sql = "SELECT `id`,`name` FROM `dataset_0` WHERE `crucial` = FALSE";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
			echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
	}

	echo"</SELECT>
	</td></tr>
		<tr><td><br></td><td></td><td></td></tr>
		<tr>
		<td></td>
		<td></td>
		<td><input type='submit' name='RemoveDataSet' value='Remove Data Set'></td>
		</tr>
	</table>


	</div>
	</form>
	</div>"; 
}

function DisplayAddProject()
{
	echo"<div id='PanelTop'>
		Add Project
	</div>
	<div id='PanelBottom'>
		<div id='Users' style='Float:left;width:190px;padding:5px;'>
		<b>Add Users:</b>
		<SELECT id='users' style=\"Width: 180px;\" SIZE = 12>";


	$sql = "SELECT `first_name`,`last_name`,`id` FROM `users_data`";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
			echo "<OPTION Value='".$row['id']."'>".$row["first_name"]." ".$row["last_name"]."</OPTION>";
	}

	echo"</SELECT>
		<br>
		<input type='button' name='Add User To Project' value='Add User To Project' onClick='addOption(\"user_selection\",document.getElementById(\"users\").options[document.getElementById(\"users\").selectedIndex].value,document.getElementById(\"users\").options[document.getElementById(\"users\").selectedIndex].text)'>
		</div>
		<form action=\"javascript:sendOptionsToHiddenFields('user_selection','hiddenFields','selected_users');get('DataFields','AddProject','Results');clearDiv('hiddenFields');\" name='AddProjectForm' id='AddProjectForm'>
		<div id='DataFields' style='width:400px; float:left;'>
		<table style='margin-right:auto;margin-left:auto;'>
		";

			$sql = "SELECT * FROM `roi_projects_rep`";
			$result = mysql_query($sql);
			while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
					echo ReturnDataField($row['index'],"","","145px",false,"",true,"roi_projects_rep",true);
			}

			echo"
			<tr>
				<td></td>
				<td></td>
				<td>
					<SELECT id='user_selection' style=\"Width: 145px;\" SIZE=8>
					</SELECT>
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>
					<input type='button' value='Remove User' onClick='document.getElementById(\"user_selection\").remove(document.getElementById(\"user_selection\").selectedIndex);'>
				</td>
			</tr>
			<tr>
			<td></td>
			<td></td>
			<td><input type='submit' name='Add Project' value='Add Project'></td>
			</tr>
		</table>

		<div id='hiddenFields'></div>
		</div>
		</form>
	</div>";
}
function DisplayEditProjects()
{
	echo"<div id='PanelTop'>
		Edit ROI Projects
	</div>
	<div id='PanelBottom'>
		<div id='Projects' style='Float:left;width:190px;padding:5px;'>
		<b>Projects:</b>
		<SELECT NAME='' style=\"Width: 220px;\" SIZE = 12 onchange=\"UpdateProjectControls(this.value,function(){\$('\#users').toChecklist();});\">";

	$sql = "SELECT `name`,`id` FROM `roi_projects`";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
			echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
	}

	echo"</SELECT>
		</div>
		<form action=\"javascript:sendOptionsToHiddenFields('user_selection','hiddenFields','selected_users');get('DataFields','EditProjects','Results');clearDiv('hiddenFields');\" name='EditProjectsForm' id='EditProjectsForm'>
		<div id='DataFields' style='width:400px; float:left;'>

		</div>
		</form>
	</div>";
}

function DisplayEditDataSet()
{
	// This was never completed, because of other higher priority projects
	// General idea is to allow users to change ordering and add and remove from
	// data sets but I am not 100% sure how this is to be done still.
	echo"<div id='PanelTop'>
		Edit Data Set
	</div>
	<div id='PanelBottom'>
		<div id='Users' style='Float:left;width:190px;padding:5px;'>
		<b>Data Sets:</b>
		<SELECT NAME='' style=\"Width: 180px;\" SIZE = 12 onchange=\"UpdateDataSet(this.value);\">";

		$sql = "SELECT `name`,`value`,`edit`,`crucial`,`order` FROM `cialab`.`dataset_0`";
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
				echo "<OPTION Value='".$row['value']."'>".$row["name"]."</OPTION>";
		}

	echo"</SELECT>
		</div>
		<form action=\"javascript:get('DataFields','EditUsers','Results');\" name='EditUsersForm' id='EditUsersForm'>
		<div id='DataFields' style='width:400px; float:left;'>

		</div>
		</form>
	</div>";
}

function DisplayRemoveProjects()
{
	echo"<div id='PanelTop'>
		Remove ROI Projects
	</div>
	<div id='PanelBottom' style='text-align:center;'>
		<form action=\"javascript:get('DataFields','RemoveProject','Results',true,'Are you sure you want to remove this Project? This cannot be reversed. All data in this set will be destroyed')\" name='RemoveProjectsForm' id='RemoveProjectsForm'>
		<div id='DataFields'>
		<b>Projects:</b>
		<br>
		<SELECT NAME='projects' id='projects' style=\"Width: 180px;\" SIZE=12>";
		$sql = "SELECT `name`,`id` FROM `roi_projects`";
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
				echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
		}

		echo"</SELECT>
		<br>
		<input type='submit' value='Remove Project'>
		

		</div>
		</form>
	</div>";
}


function DisplayDownloadData()
{
	echo"<div id='PanelTop'>
		Download Data
	</div>
	<div id='PanelBottom' style='text-align:center;'>
		<form action=\"javascript:get('DataFields','DownloadData','Results')\" name='DownloadDataForm' id='DownloadDataForm'>
		<div id='DataFields'>

		<TABLE>
		<tr>
			<td>
			Project
			</td>
			<td>
			Users
			</td>
			<td>
			</td>
		</tr>
		<tr>
			<td>
				<SELECT NAME='projects' id='projects' style=\"Width: 180px;\" SIZE=1 onchange=\"UpdateSelectUsers(this.value,true,true);\">";
				echo "<OPTION Value='ANY'>Any</OPTION>";
				$sql = "SELECT `name`,`id` FROM `roi_projects`";
				$result = mysql_query($sql);
				while($row = mysql_fetch_array($result, MYSQL_ASSOC))
				{
						echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
				}

				echo"</SELECT>
			</td>
			<td>
				<SELECT NAME='users' id='users' style=\"Width: 180px;\" SIZE=1>
				<OPTION Value='ANY'>Any</OPTION>
				<OPTION Value='REVIEW_SET'>Review Set</OPTION>
				</SELECT>
			</td>
			<td>
			<input type='submit' value='Download Data'>
			</td>
		<tr>

		</TABLE>
		</div>
		</form>
		<form action=\"javascript:get('DataFields','DownloadUserTable','Results')\" name='DownloadUserTableForm' id='DownloadUserTableForm'>
		<input type='submit' value='Download User ID Mapping'>
		</form>
		
	</div>";
}

function DisplayUploadReviewSet()
{
	echo"<div id='PanelTop'>
		Upload Review Set Data
	</div>
	
	<div id='PanelBottom' style='text-align:center;'>
		<form action='upload.php' method='POST' enctype='multipart/form-data' target='upload_target' name='UploadReviewSetForm' id='UploadReviewSetForm'>
		<div id='DataFields'>
		<INPUT type='HIDDEN' value='UploadReviewSet' name='UPLOAD_NAME' id='UPLOAD_NAME'>
		<TABLE>
			<tr>
				<td>
				Project
				</td>
				<td>
				File
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<SELECT NAME='projects' id='projects' style='Width: 180px;' SIZE=1>";
					$sql = "SELECT `name`,`id` FROM `roi_projects`";
					$result = mysql_query($sql);
					while($row = mysql_fetch_array($result, MYSQL_ASSOC))
					{
							echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
					}

					echo"</SELECT>
				</td>
				<td>
					<input type='file' name='UPLOAD' id='UPLOAD'>
				</td>
				<td>
					<input type='submit' value='Upload Review Set' onClick='uploadStart();'>
				</td>
			<tr>
		</TABLE>
		
		<TABLE>
			<tr>
				<td><h3>Note: Only Review Set Data</h2></td>
			</tr>
			<tr>
				<td>
				This only accepts CSV files. The files must be only 3 columns wide with no headers. The files must also be in the format [FileName,X,Y] without the brackets. The filename must also include the extension as well. If this format is not met the file will not upload! Furthermore, the easiest way to create these files is by putting the data into excel and saving it as a CSV file and then uploading that CSV file. This will not overwrite existing Review Sets. To completely replace a review set first delete the current review set markings and then upload the CSV file here.
				</td>
			</tr>
		</TABLE>
		
		</div>
		</form>
		
		<iframe id='upload_target' name='upload_target' src='#' style='width:0px;height:0px;border:0px solid #fff;'></iframe>
		
	</div>";
}

function DisplayUploadUserDataSet()
{
	echo"<div id='PanelTop'>
		Upload User Data Set
	</div>
	
	<div id='PanelBottom' style='text-align:center;'>
		<form action='upload.php' method='POST' enctype='multipart/form-data' target='upload_target' name='UploadUserDataSetForm' id='UploadUserDataSetForm'>
		<div id='DataFields'>
		<INPUT type='HIDDEN' value='UploadUserDataSet' name='UPLOAD_NAME' id='UPLOAD_NAME'>
		<TABLE>
			<tr>
				<td>
				Project
				</td>
				<td>
				File
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<!--
				<td>
					<SELECT NAME='projects' id='projects' style='Width: 180px;' SIZE=1>";
					$sql = "SELECT `name`,`id` FROM `roi_projects`";
					$result = mysql_query($sql);
					while($row = mysql_fetch_array($result, MYSQL_ASSOC))
					{
							echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
					}

					echo"</SELECT>
				</td>
				-->
					<td>
						<SELECT NAME='projects' id='projects' style=\"Width: 180px;\" SIZE=1 onchange=\"UpdateSelectUsers(this.value,false,false);\">";
						$sql = "SELECT `name`,`id` FROM `roi_projects`";
						$result = mysql_query($sql);
						while($row = mysql_fetch_array($result, MYSQL_ASSOC))
						{
								echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
						}

						echo"</SELECT>
					</td>
				<td>
					<input type='file' name='UPLOAD' id='UPLOAD'>
				</td>
				<td>
					<input type='submit' value='Upload User Data' onClick='uploadStart();'>
				</td>
			<tr>
			<tr>
				<td>
				User
				</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
					<td>
						<SELECT NAME='users' id='users' style=\"Width: 180px;\" SIZE=1>
						</SELECT>
					</td>
			</tr>
		</TABLE>
		<TABLE>
			<tr>
				<td><h3>Note: Only User Data</h2></td>
			</tr>
			<tr>
				<td>
				This only accepts CSV files. The files must be only 3 columns wide with no headers. The files must also be in the format [FileName,X,Y] without the brackets. The filename must also include the extension as well. If this format is not met the file will not upload!
				</td>
			</tr>
		</TABLE>
		
		</div>
		</form>
		
		<iframe id='upload_target' name='upload_target' src='#' style='width:0px;height:0px;border:0px solid #fff;'></iframe>
		
	</div>";
}


function DisplayROIStatistics()
{
	echo"<div id='PanelTop'>
		ROI Statistics
	</div>
	<div id='PanelBottom' style='text-align:center;'>
		<form action=\"javascript:UpdateROIStatistics()\" name='ROIStatisticsForm' id='ROIStatisticsForm'>
		<div id='DataFields'>

		<TABLE>
		<tr>
			<td>
			Project
			</td>
			<td>
			Users
			</td>
			<td>
			</td>
		</tr>
		<tr>
			<td>
				<SELECT NAME='projects' id='projects' style=\"Width: 180px;\" SIZE=1 onchange=\"UpdateSelectUsers(this.value,false,true);\">";
				echo "<OPTION Value='ANY'>Any</OPTION>";
				$sql = "SELECT `name`,`id` FROM `roi_projects`";
				$result = mysql_query($sql);
				while($row = mysql_fetch_array($result, MYSQL_ASSOC))
				{
						echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
				}

				echo"</SELECT>
			</td>
			<td>
				<SELECT NAME='users' id='users' style=\"Width: 180px;\" SIZE=1>
				<OPTION Value='ANY'>Any</OPTION>
				</SELECT>
			</td>
			<td>
				<input type='submit' value='Display ROI Stats'>
			</td>
		<tr>
		</TABLE>

		</div>
		<div id='roiStats'>
		</div>
		</form>
	</div>";
}

function DisplayClearReviewSet()
{
	echo"<div id='PanelTop'>
		Clear Review Set Data
	</div>
	<div id='PanelBottom' style='text-align:center;'>
		<form action=\"javascript:get('DataFields','ClearReviewSet','Results',true,'Are You Sure You Want To Permanently Delete This Review Set?')\" name='ClearReviewSetForm' id='ClearReviewSetForm'>
		<div id='DataFields'>

		<TABLE margin-left:auto;margin-right:auto;text-align:center;>
		<tr>
			<td>
			Project
			</td>
			<td>
			</td>
		</tr>
		<tr>
			<td>
				<SELECT NAME='projects' id='projects' style=\"Width: 180px;\" SIZE=1>";
				echo "<OPTION Value='ANY'>Any</OPTION>";
				$sql = "SELECT `name`,`id` FROM `roi_projects`";
				$result = mysql_query($sql);
				while($row = mysql_fetch_array($result, MYSQL_ASSOC))
				{
						echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
				}

				echo"</SELECT>
			</td>
			<td>
				<input type='submit' value='Clear Review Set'>
			</td>
		<tr>
		</TABLE>

		</div>
		</form>
	</div>";
}

function DisplayDownloadCounts()
{
	echo"<div id='PanelTop'>
		Download Marking Counts
	</div>
	<div id='PanelBottom' style='text-align:center;'>
		<form action=\"javascript:get('DataFields','DownloadCounts','Results')\" name='DownloadCountsForm' id='DownloadCountsForm'>
		<div id='DataFields'>

		<TABLE>
		<tr>
			<td>
			Project
			</td>
			<td>
			Users
			</td>
			<td>
			</td>
		</tr>
		<tr>
			<td>
				<SELECT NAME='projects' id='projects' style=\"Width: 180px;\" SIZE=1 onchange=\"UpdateSelectUsers(this.value,false,false);\">
					<OPTION Value='NONE'></OPTION>";
				$sql = "SELECT `name`,`id` FROM `roi_projects`";
				$result = mysql_query($sql);
				while($row = mysql_fetch_array($result, MYSQL_ASSOC))
				{
						echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
				}

				echo"</SELECT>
			</td>
			<td>
				<SELECT NAME='users' id='users' style=\"Width: 180px;\" SIZE=1>
				<OPTION Value='NONE'></OPTION>
				</SELECT>
			</td>
			<td>
			<input type='submit' value='Download Data'>
			</td>
		<tr>

		</TABLE>
		</div>
		</form>

		
	</div>";
}

function DisplayDownloadUsers()
{
	echo"<div id='PanelTop'>
		Download Data
	</div>
	<div id='PanelBottom' style='text-align:center;'>
		<div id='DataFields'></div>
		
		<form action=\"javascript:get('DataFields','DownloadUserTable','Results')\" name='DownloadUserTableForm' id='DownloadUserTableForm'>
		<input type='submit' value='Download User ID Mapping'>
		</form>
		
	</div>";
}

//////////////////////////////////////////////////////////////////////////
///////////////////////// HELPER FUNCTIONS ///////////////////////////////
//////////////////////////////////////////////////////////////////////////

if(isset($_GET["roistatistics"]) && isset($_GET["projectid"]) && isset($_GET["userid"]))
{
	echo "
	<style type='text/CSS'>
	table.data
	{
		border-collapse:collapse;
		border: 1px black solid;
		width: 100%;
	}
	tr.data
	{
		border: 1px black solid;
	}
	td.data
	{
		border: 1px black solid;
	}
	th.data
	{
		border: 1px black solid;
	}
	</style>
	
	<TABLE class='data'>
	<tr class='data'>
		<th class='data'>Name</th>
		<th class='data'>Project Name</th>
		<th class='data'>Total Images</th>
		<th class='data'>Last Image Read</th>
		<th class='data'>Highest Image Marked</th>
		<th class='data'>Last Login</th>
		<th class='data'>Proportion Marked</th>
		<!--<th>SQL</th>-->
	</tr>
	
	";
	
	$projectID = mysql_prep($_GET["projectid"]);
	$userID = mysql_prep($_GET["userid"]);
	$SQLArray = array();
	$sql2 = "";
	$projectIDSQL = "";
	$userIDSQL = "";
	
	if(!($projectID == "ANY" || $projectID ==""))
	{
		$projectIDSQL = " AND `cialab`.`roi_projects`.`id`='".$projectID."' ";
	}
	
	if(!($userID == "ANY" || $userID ==""))
	{
		$userIDSQL = " AND `user_id`='".$userID."' ";
	}
	
	$sql2 = "
	SELECT `cialab`.`users_data`.`first_name`,`cialab`.`users_data`.`last_name`,`cbmarker`.`imgtracking`.`image`, `cbmarker`.`imgtracking`.`date` ,`cialab`.`roi_projects`.`id`,`cialab`.`roi_projects`.`name`,`cialab`.`roi_projects`.`folder`,`cialab`.`roi_projects_members`.`roi_project_id`,`cbmarker`.`imgtracking`.`userid`
	FROM `cialab`.`users_data`,`cialab`.`roi_projects`,`cialab`.`roi_projects_members`,`cbmarker`.`imgtracking` 
	WHERE `cialab`.`roi_projects`.`id`=`cialab`.`roi_projects_members`.`roi_project_id`  
		AND `cbmarker`.`imgtracking`.`userid`=`user_id` 
		AND `cialab`.`roi_projects`.`id`= `cbmarker`.`imgtracking`.`project_id` 
		".$userIDSQL."
		".$projectIDSQL."
		AND `user_id`=`cialab`.`users_data`.`id` 
	ORDER BY `date` ASC;
	";
	
	//echo $sql2;
	
	$result2 = mysql_query($sql2);

	while($row2 = mysql_fetch_array($result2, MYSQL_ASSOC))
	{
		//Get current temp table name
		$TempTableName = "ROI_Project_" . preg_replace("/[^a-zA-Z0-9]/", "", $row2['roi_project_id']);
		
		$resultValue = mysql_query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'cialab' AND table_name = '".$TempTableName."';");
		
		if(mysql_num_rows($resultValue) == 0)
		{
			$sql =  "
			CREATE TABLE `".$TempTableName."`
			(
			`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
			`name` VARCHAR(200) NOT NULL
			) ENGINE=MYISAM;";
				
			$result4 = mysql_query($sql);// or die(mysql_error());
			
			//This adds the projects in order to an array. This caches the information 
			//so it only has to do this once per project.
			if($result4 == true)
			{
				//*****One Method of listing files that are in a folder.
				//$dir_path = ".".$row2['folder'];
				//$files = glob($dir_path . "*.jpg");
				//$count = count($files);
				//$projectsArray[$row2['name']] = $files;
				
				//**Create Table to hold array of files, so mysql can query it.
				
				//$sql =  "DROP TABLE IF EXISTS ".$TempTableName.";";
				//$result4 = mysql_query($sql) or die(mysql_error());

				$handler = opendir(".".$row2['folder']);

				//Because I originally implemented the file finding this
				//way I have to use it to check which file the users are on
				//hopefully I can switch to a better method.
				while ($file = readdir($handler)) 
				{
					if($file != ".")
					{
						if($file != "..")
						{
							$sql = "INSERT INTO `".$TempTableName."` (`name`) VALUES ('".$file."')";
							$result4 = mysql_query($sql) or die(mysql_error());
						}
						
					}
				}
			}
		}
		//Begin Printout of Table
		echo "<tr class='data'>";
		
		//The First and Last Name of the user
		echo "	<td class='data'>";
		echo $row2['first_name']. " " . $row2['last_name'];
		echo "	</td>";
		
		//The Project Name
		echo "	<td class='data'>";
		echo $row2['name'];
		echo "	</td>";
		
		//Total Count of Images
		echo "	<td class='data'>";
		$sql =  "SELECT COUNT(`cialab`.`".$TempTableName."`.`id`) as count_value FROM `cialab`.`".$TempTableName."`";
		$rowValues = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
		$totalCount = $rowValues["count_value"];
		echo $rowValues["count_value"];
		//echo $sql;
		echo "	</td>";
		
		//Last Image Number User Was On
		echo "	<td class='data'>";
		$sql =  "SELECT `id` FROM `cialab`.`".$TempTableName."` WHERE `name` = '".$row2['image']."'";
		$rowValues = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
		echo $rowValues["id"];
		//echo $sql;
		echo "	</td>";
		
		//THIS QUERY IS THE SLOWEST BY FAR
		//SPEED THIS UP
		//Highest Image User Marked

	
		echo "	<td class='data'>";
		
		$sql =  "
		SELECT Max(`id`) as max_id 
		FROM 
		(
			SELECT * FROM 
			(
				SELECT `name` as image,`id` FROM `cialab`.`".$TempTableName."`
			) as t1 
			LEFT JOIN 
			(
				SELECT `image`,`id` as id2 FROM `cbmarker`.`cbdata` WHERE `project_id`='".$row2['roi_project_id']."' AND `userid`='".$row2['userid']."' AND `review_mark`='0'
			) as t2 
			USING (`image`)
		) as p1 
		WHERE id2 IS NOT NULL";
		
		
	//	$sql =  "
	//		SELECT MAX(`cialab`.`".$TempTableName."`.`id`) as max_id, `cbmarker`.`cbdata`.`review_mark`, `cbmarker`.`cbdata`.`project_id`, `cialab`.`".$TempTableName."`.`name`, `cbmarker`.`cbdata`.`userid`, `cbmarker`.`cbdata`.`image` FROM `cialab`.`".$TempTableName."`, `cbmarker`.`cbdata` 
	//		WHERE 
	//			`cbmarker`.`cbdata`.`review_mark`='0' 
	//			AND `cbmarker`.`cbdata`.`project_id` = '".$row2['roi_project_id']."' 
	//			AND `cbmarker`.`cbdata`.`userid` = '".$row2['userid']."' 
	//			AND `cialab`.`".$TempTableName."`.`name` = `cbmarker`.`cbdata`.`image` 
	//		GROUP BY `cbmarker`.`cbdata`.`userid` 
	//		HAVING MAX(`cialab`.`".$TempTableName."`.`id`)";
		
		$rowValues = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
		echo $rowValues["max_id"];
		//echo $sql;
		echo "	</td>";
	
	
		//Last Date User Looked At Image
		echo "	<td class='data'>";
		echo $row2['date'];
		echo "	</td>";
		
		
		//The percentage of images marked
		$sql = "SELECT COUNT(*) AS count FROM (SELECT * FROM `cbmarker`.`cbdata` WHERE `userid`='".$row2['userid']."' AND `project_id`='".$row2['roi_project_id']."' AND `review_mark`='0' GROUP BY `image`) AS p2";
		$rowValues = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
		$numberMarked = $rowValues["count"];
		
		//The total count of the images.
		
		echo " <td class='data'>";
		echo "$numberMarked / $totalCount";
		echo " </td>";
		 
		
		//echo " <td>";
		//echo $sql;
		//echo " </td>";
		
		echo "</tr>";
	}
	
	echo "</TABLE>";
}

if(isset($_GET["userfield"]) == true)
{
	echo ReturnUserField(mysql_prep($_GET["userfield"]));
}
if(isset($_GET["datafield"]) == true)
{
	//echo "test";
	echo ReturnDataFields(mysql_prep($_GET["datafield"]));
}
if(isset($_GET["dataset"]) == true)
{
	//echo "test";
	echo ReturnDataSet(mysql_prep($_GET["dataset"]));
}
if(isset($_GET["projectfield"]) == true)
{
	echo ReturnProjectField(mysql_prep($_GET["projectfield"]));
}
if(isset($_GET["project"]) && isset($_GET["selectid"]) && isset($_GET["addReview"]) && isset($_GET["addAny"]))
{
	$addReview = mysql_prep($_GET["addReview"]);
	$project_id = mysql_prep($_GET["project"]);
	$select_id = mysql_prep($_GET["selectid"]);
	$add_any = mysql_prep($_GET["addAny"]);
	
	if($add_any == "true")
	{
		echo "addOption('".$select_id."','ANY','Any');";
	}
	
	if($addReview == "true")
	{
		echo "addOption('".$select_id."','REVIEW_SET','Review Set');";
	}
	
	if($project_id != "ANY")
	{
		$sql = "SELECT * FROM `roi_projects_members`,`users_data` WHERE `users_data`.`id`=`roi_projects_members`.`user_id` AND `roi_projects_members`.`roi_project_id`='".$project_id."'";
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
				echo "addOption('".$select_id."','".$row['id']."','".$row["first_name"]." ".$row["last_name"]."');";
		}
	}
}

function ReturnProjectField($ProjectID)
{
	$ReturnValue = "";
	
	$ProjectID = mysql_prep($ProjectID);
	$sql = "SELECT * FROM `roi_projects` WHERE `id` = " . $ProjectID . ";";
	$ProjectData = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
	//print_r(array_keys($ProjectData));
	
	foreach(array_keys($ProjectData) as $value)
	{
		$ReturnValue = $ReturnValue . ReturnDataField($value,"",$ProjectData[$value],"145px",false,"",true,"roi_projects_rep",true);
	}
	
	$ReturnValue = $ReturnValue .  "<tr>
										<td>
											All Users:
											<br>
											<SELECT id='users' style=\"Width: 145px;\" SIZE=8>";
											
	$sql = "SELECT `first_name`,`last_name`,`id` FROM `users_data`";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
			$ReturnValue = $ReturnValue . "<OPTION Value='".$row['id']."'>".$row["first_name"]." ".$row["last_name"]."</OPTION>";
	}
	$ReturnValue = $ReturnValue . "
											</SELECT>
											<br>
											<input type='button' name='Add User To Project' value='Add User To Project' onClick='addOption(\"user_selection\",document.getElementById(\"users\").options[document.getElementById(\"users\").selectedIndex].value,document.getElementById(\"users\").options[document.getElementById(\"users\").selectedIndex].text)'>
										</td>
										<td></td>
										<td>
										Project Users:
										<SELECT id='user_selection' style=\"Width: 145px;\" SIZE=8>";
										
	$sql = "SELECT * FROM `users_data`,(SELECT * FROM `roi_projects_members` WHERE `roi_project_id` = '".$ProjectID."') AS P1 WHERE `P1`.`user_id` = `users_data`.`id`";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
			$ReturnValue = $ReturnValue . "<OPTION Value='".$row['id']."'>".$row["first_name"]." ".$row["last_name"]."</OPTION>";
	}
	
	$ReturnValue = $ReturnValue . "		</SELECT>
										<br>
										<input type='button' value='Remove User' onClick='document.getElementById(\"user_selection\").remove(document.getElementById(\"user_selection\").selectedIndex);'>
										</td>
									</tr>";
	
	return $ReturnValue;
}

function ReturnUserField($UserID)
{
	$ReturnValue = "";
	
	$UserID = mysql_prep($UserID);
	$sql = "SELECT * FROM `users_data` WHERE `id` = " . $UserID . ";";
	$UserData = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
	//print_r(array_keys($UserData));
	
	foreach(array_keys($UserData) as $value)
	{
		if ($value == 'password')
		{
			$ReturnValue = $ReturnValue . ReturnDataField($value,"","","145px",false,"",true,"users_rep",true);
		}
		else
		{
			$ReturnValue = $ReturnValue . ReturnDataField($value,"",$UserData[$value],"145px",false,"",true,"users_rep",true);
		}
	}
	return $ReturnValue;
}


function ReturnDataFields($FieldID)
{
	
	$ReturnValue = "";
	
	$FieldID = mysql_prep($FieldID);
	$sql = "SELECT * FROM `slides_rep` WHERE `id` = " . $FieldID . ";";
	$UserData = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
	//print_r(array_keys($UserData));
	
	$sql = "SELECT * FROM `slides_rep_types` ORDER BY `id` ASC";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if (isset($UserData[$row['index']]))
		{
			$ReturnValue = $ReturnValue . ReturnDataField($row['index'],"",$UserData[$row['index']],"145px",false,"",true,"slides_rep_types",true);
		}
	}
	
	return $ReturnValue;
}

function ReturnDataSet($SetId)
{
	echo "
			<div id='Users' style='Float:left;width:190px;padding-left:5px;padding-top:5px;padding-bottom:5px;margin-right:-5px;'>
		<b>Set Data:</b>
		<SELECT NAME='' style=\"Width: 180px;\" SIZE=12 >";


	$sql = "SELECT * FROM `cialab`.`dataset_".$SetId."`";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
			echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
	}

	echo"</SELECT>
		</div>
		<div id='updownbox' style='width:18px;height:200px;float:left;margin-top:30px;'>
		<img src='../images/up.png'></img>
		<img src='../images/down.png'></img>
		</div>
		";
}

?>
