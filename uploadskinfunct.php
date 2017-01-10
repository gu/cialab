<?php 
include("SecurePage.php");
include("GlobalVariables.php");
include("GlobalFunctions.php");
?>

<?php
Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab);

$project_id = "";
$SQLArray = array();
$returnString = "";
$exitString = "<script language='javascript' type='text/javascript'>window.top.window.uploadComplete('".addslashes($returnString)."');</script>";

//Not needed.
//SET UP LARGER TIME LIMITS AND REPORT ERRORS
//error_reporting(-1);
//set_time_limit(200000);
//mysql_query("SET SESSION wait_timeout = 60;");

if($_SESSION['Permissions']['upload_skindata'] == 1)
{

	//Declare Some Variables
	$unlinkAttempt = false;
	$fileField = "UPLOAD";
	$fileCreated = false;
	$uploadName = "";
	$overwrite = false;
	
	if(isset($_POST["UPLOAD_NAME"]))
	{
		$uploadName = mysql_prep($_POST["UPLOAD_NAME"]);
	}
	else
	{
		exitAdd("No UPLOAD_NAME",false);
		die($exitString);
	}

	
	if(isset($_POST["overwrite"]))
	{
		if(mysql_prep($_POST["overwrite"]) == "true")
		{
			$overwrite = true;
		}
		//print_r($_POST);
	}

	//This interperts the command passed to the web page.
	if(isset($_POST["projects"]))
	{
		$project_id = mysql_prep($_POST['projects']);
		
		$sql = "SELECT * FROM `roi_projects` WHERE `id` = " . $project_id;
		
		$result = mysql_query($sql);
		
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$imgdir = $webDirectory.$row['folder'];
		
		if(file_exists($imgdir) == false)
		{
			mkdir($imgdir,0775);
		}
		
		if(file_exists($imgdir))
		{
			echo "File exists";
		}
		else
		{
			echo "Directory does Not Exist";
		}
		
		$uploadFileName = mysql_prep($_FILES[$fileField]["name"]);
		
		$file = $imgdir.$uploadFileName;
	}
	else
	{
		//print_r($_POST);
		exitAdd("No Project ID Set",false);
		die($exitString);
	}
	
	//Check if file exists.
	//If exists delete it else set unlinkAttempt to true.
	//signifying that the new download can be created.
	
	if(file_exists($file) == true)
	{
		if($overwrite == true)
		{
			$unlinkAttempt = unlink($file);
		}
		else
		{
			exitAdd("<p style='color:red;'>No Upload: File Exists and Overwrite is False</p>",false);
			die($exitString);
		}
	}
	else
	{
		$unlinkAttempt = true;
	}

	if($unlinkAttempt == true)
	{
		
		if(isset($_FILES[$fileField]))
		{
			if ($_FILES[$fileField]["size"] < 200000000)
			{
				if ($_FILES[$fileField]["error"] > 0)
				{
					exitAdd("Return Code: " . $_FILES[$fileField]["error"] . "<br />",false);
				}
				else
				{
					exitAdd("Uploaded: " . $_FILES[$fileField]["name"] . "<br />",false);
					// exitAdd("Type: " . $_FILES[$fileField]["type"] . "<br />",false);
					// exitAdd("Size: " . ($_FILES[$fileField]["size"] / 1024) . " Kb<br />",false);
					// exitAdd("Temp file: " . $_FILES[$fileField]["tmp_name"] . "<br />",false);

					move_uploaded_file($_FILES[$fileField]["tmp_name"],$file);
					// exitAdd("Stored in: " . $file,false);
					$fileCreated = true;
				}
			}
			else
			{
				exitAdd("File Is Too Large",false);
			}
		}
		else
		{
			//print_r($_FILES);
			//print_r($_POST);
			//print_r($_GET);
			exitAdd("File could not be found!",false);
		}
	}
	//echo $returnString;

	if($fileCreated == true)
	{
		$returnString = $returnString . "<br>File Uploaded Successfuly";
		exitAdd("<p style='color:green;'>Successfully Added Images</p>",true);
	}
	
	echo $exitString;
}
else
{
	echo "Access Denied";
}

function exitAdd($stringValue,$overwrite)
{
	global $returnString, $exitString;

	echo $stringValue . "<br>";
	
	if($overwrite == true)
	{
		$returnString = $stringValue;
	}
	else
	{
		$returnString = $returnString . $stringValue;
	}
	
	$exitString = "<script language='javascript' type='text/javascript'>window.top.window.uploadComplete('".addslashes($returnString)."');</script>";
}
?>