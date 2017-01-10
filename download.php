<?php 
include("SecurePage.php");
include("GlobalVariables.php");
include("GlobalFunctions.php");
?>
<?php
Connect_To_DB($db_server_official,$db_user_official,$db_pwd_official,$db_cbmarker);

$project_id = "";
$user_id = "";
$SQLArray = array();

//Not needed.
//SET UP LARGER TIME LIMITS AND REPORT ERRORS
//error_reporting(-1);
//set_time_limit(200000);
//mysql_query("SET SESSION wait_timeout = 60;");


//This interperts the command passed to the web page.
if(isset($_GET["user"]) && isset($_GET["project"]))
{
	$project_id = mysql_prep($_GET['project']);
	$user_id = mysql_prep($_GET['user']);
	
	if(!($project_id == "ANY" || $project_id ==""))
	{
		array_push($SQLArray, " `project_id`='".$project_id."' ");
	}
	
	if($user_id == "REVIEW_SET")
	{
		array_push($SQLArray, " `review_mark`='1' ");
	}
	else
	{
		if(!($user_id == "ANY" || $user_id ==""))
		{
			array_push($SQLArray, " `userid`='".$user_id."' ");
		}
		array_push($SQLArray, " `review_mark`='0' ");
	}
}


if($_SESSION['Permissions']['download_data'] == 1)
{

	$file = $SaveDirectory."dbdownload.csv";
	$unlinkAttempt = false;
	
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
	//echo $file;
	
	//If the previous file deleted successfully then 
	//give the go ahead to create the new download for
	//user. (In the future this may be upgraded to allow
	//for simultaneous downloads of data, but as of right now
	//only 1 user can download data at a time.)
	if($unlinkAttempt)
	{
		//Determine if is a Positive Cells Estimation project or Annotation Drawing project or IHC DropDown project
		$row;
		$sql;
		if($project_id != "ANY")
		{
			$sql = "SELECT reviewable FROM cialab.roi_projects WHERE id=". $project_id; 
			$result = mysql_query($sql);
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
		}
		
		if($project_id == "ANY" || ($row['reviewable'] != 7 && $row['reviewable'] != 6 && $row['reviewable'] != 4 && $row['reviewable'] != 11))
		{
			$sql = "SELECT * INTO OUTFILE \"".$file."\"
					FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
					LINES TERMINATED BY \"\n\"
					FROM `cbmarker`.`cbdata`";
					
			if(sizeof($SQLArray) > 0)
			{
				$sql = $sql . " WHERE " . join(" AND ", $SQLArray);
			}
		}
		
		//For Positive Cells Estimation project types
		else if($row['reviewable'] == 7)
		{		
			$sql = "SELECT 'id', 'userid', 'project_id', 'image', 'date', 'M1', 'M2'
					UNION ALL
					SELECT * INTO OUTFILE \"".$file."\"
					FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
					LINES TERMINATED BY \"\n\"
					FROM `cbmarker`.`pospercentestimation`
					WHERE project_id=" . $project_id;
					
			if($user_id != "ANY")
			{
				$sql = $sql . " AND userid=" . $user_id;	
			}
		 }
		 
		 
		 //Eric added for Annotation Drawing project types
		 else if($row['reviewable'] == 6 || $row['reviewable'] == 11)
		{		
			/*$sql = "SELECT 'id', 'userid', 'project_id', 'image', 'date', 'coords', 'newLine', 'color', 'annot_type', 'markerCategory' 
					UNION ALL 
					SELECT * INTO OUTFILE \"".$file."\"
					FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
					LINES TERMINATED BY \"\n\"
					FROM `cbmarker`.`annot_test`
					WHERE project_id=" . $project_id;
					
			if($user_id != "ANY")
			{
				$sql = $sql . " AND userid=" . $user_id;	
			}
			$sql = $sql . " ORDER BY 'id'";
			*/
			
			$zipName = $SaveDirectory."data.zip";
			$zipArchive = new ZipArchive();

			$res = $zipArchive->open($zipName, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
			
			//echo 'failed, '.$res;
			
			//if (!)
			//	die("Failed to create archive\n");
				
			$path = "./cbmarkerv2/data/storage/".$project_id;
			
			$len = 42;
			
			if($user_id != "ANY") {
				$path = $path."/".$user_id."/";
			}
			
			$files = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($path),
				RecursiveIteratorIterator::LEAVES_ONLY
			);
			foreach ($files as $name => $f)
			{
				// Skip directories (they would be added automatically)
				if (!$f->isDir())
				{
					// Get real and relative path for current file
					$filePath = $f->getRealPath();
					$relativePath = substr($filePath, $len);
					
					if($user_id != "ANY") {
						$relativePath = basename($relativePath);
					}
					
					//echo $filePath;
					// Add current file to archive
					if (file_exists($filePath)) {
					$zipArchive->addFile($filePath, $relativePath);
					}
				}
			}
			$zipArchive->close();
			
			header("Content-type: application/zip"); 
			header("Content-Disposition: attachment; filename=".$zipName);
			header("Content-length: " . filesize($zipName));
			header("Pragma: no-cache"); 
			header("Expires: 0"); 
			readfile($zipName);
			exit();
		 }
		 
		 //Eric added for IHC Drop Down project types
		 else if($row['reviewable'] == 4)
		{		
			$sql = "SELECT 'id', 'user_id', 'project_id', 'image', 'date', 'dropdowndata_id', 'dropdowndata_id2', 'dropdowndata_set', 'no_markings', 'no_markings_set'
					UNION ALL
					SELECT * INTO OUTFILE \"".$file."\"
					FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
					LINES TERMINATED BY \"\n\"
					FROM `cbmarker`.`imagedata`
					WHERE project_id=" . $project_id;
					
			if($user_id != "ANY")
			{
				$sql = $sql . " AND user_id=" . $user_id;	
			}
		 }
		
		$result = mysql_query($sql);
		
		if(!$result)
		{
			Echo "Error Creating File";
			Echo "<br>" . $project_id . " " . $user_id . "<br>";
			echo "<br>".$sql . "<br>";
		}
	}
	else
	{
		//This error can be thrown if the apache can not delete the file that
		//mysql created that is sent to the user. change file permissions for 
		//apache to have control over the file to fix this.
		//
		// Specifically one fix is to change the group, owner and folder permissions
		// of the webcache folder located at tmp/webcache/
		//
		// Example Commands: (something close to this)
		// cd tmp
		// chgrp mysql webcache
		// usermod -a -G apache mysql
		// chmod 775 webcache
		
		echo "Can Not Remove Previous File, check write permissions for apache and read this script";
	}

	//IF new file exists then output the file.
	if (file_exists($file)) 
	{
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		ob_clean();
		flush();
		readfile($file);
		exit;
	}
	else
	{
		echo "File Could Not be found.";
	}
}
else
{
	echo "Access Denied";
}

?>
