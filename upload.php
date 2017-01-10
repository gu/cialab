<?php 
include("SecurePage.php");
include("GlobalVariables.php");
include("GlobalFunctions.php");
?>
<?php
Connect_To_DB($db_server_official,$db_user_official,$db_pwd_official,$db_cbmarker);

$project_id = "";
$SQLArray = array();
$returnString = "";
$exitString = "<script language='javascript' type='text/javascript'>window.top.window.uploadComplete('".addslashes($returnString)."');</script>";

//Not needed.
//SET UP LARGER TIME LIMITS AND REPORT ERRORS
//error_reporting(-1);
//set_time_limit(200000);
//mysql_query("SET SESSION wait_timeout = 60;");

if($_SESSION['Permissions']['upload_data'] == 1)
{

	//Declare Some Variables
	$file = $SaveDirectory."TempUpload.csv";
	$unlinkAttempt = false;
	$fileField = "UPLOAD";
	$fileCreated = false;
	$uploadName = "";
	$userID="";
	
	if(isset($_POST["UPLOAD_NAME"]))
	{
		$uploadName = mysql_prep($_POST["UPLOAD_NAME"]);
	}
	else
	{
		exitAdd("No UPLOAD_NAME",false);
		die($exitString);
	}
	
	if(isset($_POST["users"]))
	{
		$userID = mysql_prep($_POST["users"]);
	}
	
	//This interperts the command passed to the web page.
	if(isset($_POST["projects"]))
	{
		$project_id = mysql_prep($_POST['projects']);

		//array_push($SQLArray, " `project_id`='".$project_id."' ");
		
		//array_push($SQLArray, " `review_mark`='1' ");
	}
	else
	{
		print_r($_POST);
		exitAdd("No Project ID Set",false);
		die($exitString);
	}
	
	//Check if file exists.
	//If exists delete it else set unlinkAttempt to true.
	//signifying that the new download can be created.
	if(file_exists($file) == true)
	{
		$unlinkAttempt = unlink($file);
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
					exitAdd("Upload: " . $_FILES[$fileField]["name"] . "<br />",false);
					exitAdd("Type: " . $_FILES[$fileField]["type"] . "<br />",false);
					exitAdd("Size: " . ($_FILES[$fileField]["size"] / 1024) . " Kb<br />",false);
					exitAdd("Temp file: " . $_FILES[$fileField]["tmp_name"] . "<br />",false);

					if (file_exists("upload/" . $_FILES[$fileField]["name"]))
					{
						exitAdd($_FILES[$fileField]["name"] . " already exists.",false);
					}
					else
					{
						move_uploaded_file($_FILES[$fileField]["tmp_name"],$file);
						exitAdd("Stored in: " . $file,false);
						$fileCreated = true;
					}
				}
			}
			else
			{
				exitAdd("File Is Too Large",false);
			}
		
		}
		else
		{
			print_r($_FILES);
			print_r($_POST);
			print_r($_GET);
			exitAdd("File could not be found!",false);
		}
	}
	//echo $returnString;

	if($fileCreated == true)
	{
		$row = 1;
		if (($handle = fopen($file, "r")) !== FALSE) 
		{
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
			{
				if($data == true)
				{
					$column = count($data);
					$row++;
					if($column == 3)
					{
						if($uploadName == "UploadReviewSet")
						{
							$sql = "INSERT INTO `cbmarker`.`cbdata` (`userid`,`image`,`x`,`y`,`review_mark`,`project_id`) VALUE ('0','".mysql_prep($data[0])."','".mysql_prep($data[1])."','".mysql_prep($data[2])."','1',".$project_id.")";
							$result = mysql_query($sql);
							if(!$result)
							{
								exitAdd("<BR>ERROR Inserting Items into Database.<BR>$sql",false);
								die($exitString);
							}

							exitAdd("Processed UploadReviewSet",false);
						}
						elseif($uploadName == "UploadUserDataSet" && $userID!="")
						{
							$sql = "INSERT INTO `cbmarker`.`cbdata` (`userid`,`image`,`x`,`y`,`review_mark`,`project_id`) VALUE ('".$userID."','".mysql_prep($data[0])."','".mysql_prep($data[1])."','".mysql_prep($data[2])."','0',".$project_id.")";
							$result = mysql_query($sql);
							if(!$result)
							{
								exitAdd("<BR>ERROR Inserting Items into Database.<BR>$sql",false);
								die($exitString);
							}
							exitAdd("Proccessed UploadUserDataSet",false);
						}
						
						//echo $sql . "<BR>";
						//echo "PROJECT: ".$project_id."  IMAGE:".$data[0]."   X:".$data[1]."   Y:".$data[2]."  <BR><br>";
						/*
						for ($c=0; $c < $column; $c++) 
						{
							echo $data[$c] . "<br />\n";
						}
						*/
					}
					else
					{
						exitAdd("<br>The Format of the CSV file is not correct! - Too Many Columns",false);
						die($exitString);
						break 1;
					}
				}
			}
			fclose($handle);
		}

		//$returnString = $returnString . "<br>File Uploaded Successfuly";
		exitAdd("Successfully Upload Data Set",true);
	}
	
	echo $exitString;
	//echo $returnString;
}
else
{
	echo "Access Denied";
}

function exitAdd($stringValue,$overwrite)
{
	global $returnString, $exitString;
	
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