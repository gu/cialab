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
	
	array_push($SQLArray, " `review_mark`='0' ");
	
	if(!($project_id == "ANY" || $project_id ==""))
	{
		array_push($SQLArray, " `project_id`='".$project_id."' ");
	}
	
	if(!($user_id == "ANY" || $user_id ==""))
	{
		array_push($SQLArray, " `userid`='".$user_id."' ");
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
	echo $file;
	
	//If the previous file deleted successfully then 
	//give the go ahead to create the new download for
	//user. (In the future this may be upgraded to allow)
	//for simultaneous downloads of data, but as of right now
	//only 1 user can download data at a time.
	if($unlinkAttempt)
	{
		$sql = "SELECT * INTO OUTFILE \"".$file."\"
				FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
				LINES TERMINATED BY \"\n\"
				FROM `cbmarker`.`cbdata`";
				
		if(sizeof($SQLArray) > 0)
		{
			$sql = $sql . " WHERE " . join(" AND ", $SQLArray) .";";
		}
		
		$result = mysql_query($sql);
	}
	else
	{
		//This error can be thrown if the apache can not delete the file that
		//mysql created that is sent to the user. change file permissions for 
		//apache to have control over the file to fix this.
		echo "Can Not Remove Previous File, check write permissions for apache and read this script";
	}

	//IF new file exists then outpu the file.
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
}
else
{
	echo "Access Denied";
}
?>