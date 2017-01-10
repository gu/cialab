<?php
include("SecurePage.php");
include("GlobalVariables.php");
//Connect to Database
Connect_To_DB($db_server_official,$db_user_official,$db_pwd_official,$db_name_official);

//-----------------------------Helper Functions-------------------------------//
function file_prep($value)
{
	$value = preg_replace("/[^a-z0-9_.@\\-| ]/i", "", $value);
    return $value;
}
function Connect_To_DB($db_server_value, $db_user_value, $db_pwd_value, $db_name_value)
{
	$conn = mysql_connect($db_server_value, $db_user_value, $db_pwd_value) or die('Error connecting to mysql');
	mysql_select_db($db_name_value);
}

//-----------------------------Set Variables-------------------------------//
//Filtering Variables for dangerous characters and determine if values are set

//Set the userID variable
$userIDSet = isset($_SESSION['id_DEMO']);
if($userIDSet == true)
{
	$userID = file_prep($_SESSION['id_DEMO']);
}

//Set the pic variable
$picSet = isset($_GET['pic']);
if($picSet == true)
{
	$pic = file_prep($_GET['pic']);
}

//Set the Action variable
$actionSet = isset($_GET['action']);
if($actionSet == true)
{
	$action = file_prep($_GET['action']);
}

//Set the x variable
$xSet = isset($_GET['x']);
if($xSet == true)
{
	$x = file_prep($_GET['x']);
}

//Set the y variable
$ySet = isset($_GET['y']);
if($ySet == true)
{
	$y = file_prep($_GET['y']);
}

//Set the y variable
$moveSet = isset($_GET['move']);
if($moveSet == true)
{
	$move = file_prep($_GET['move']);
}

//-----------------------------Proccess Commands-------------------------------//

if($actionSet && $moveSet)
{
	//Process add markers command
	if($action == 'add' && $userIDSet && $picSet && $xSet && $ySet)
	{
		$sql = "INSERT INTO `cbdata` (`userid`,`date`,`image`,`x`,`y`) VALUES ('".$userID."','".date('j-m-y h:i:s')."','".$pic."','".file_prep($_GET['x'])."','".file_prep($_GET['y'])."')";
		$result = mysql_query($sql);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"addMarker","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
	}

	//Process remove markers command
	if($action == 'remove' && $userIDSet && $picSet && $xSet && $ySet)
	{
		$sql = "DELETE FROM `cbdata` WHERE `userid`='".$userID."' AND `image`='".$pic."' AND `x`='".$x."' AND `y`='".$y."'";
		$result = mysql_query($sql);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"removeMarker","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
	}

	//Process removeAll markers command
	if($action == 'removeAll' && $userIDSet && $picSet)
	{
		$sql = "DELETE FROM `cbdata` WHERE `userid`='".$userID."' AND `image`='".$pic."'";
		$result = mysql_query($sql);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"removeAllMarkers","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
	}

	//Process the setImgTacking request
	if($action == 'setImgTracking' && $userIDSet && $picSet)
	{
		$sql = "SELECT `userid` FROM `imgtracking` WHERE `userid` = '".$userID."';"; 
		$result = mysql_query($sql);
		if (mysql_num_rows($result) == 1)
		{
			$sql = "UPDATE `imgtracking` SET `date`='".date('j-m-y h:i:s')."',`image`='".$pic."' WHERE `userid`='".$userID."'";
			$result = mysql_query($sql);
			
			header('Content-Type: application/json');
			header('Content-Disposition: attachment; filename=json.run');
			echo 'processJSON({"name":"imgTackingUpdated","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
		}
		else
		{
			$sql = "INSERT INTO `imgtracking` (`date`,`image`,`userid`) VALUES ('".date('j-m-y h:i:s')."','".$pic."','".$userID."')";
			$result = mysql_query($sql);
			
			header('Content-Type: application/json');
			header('Content-Disposition: attachment; filename=json.run');
			echo 'processJSON({"name":"imgTackingSet","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
		}
	}

	//Proccess the getImgMarks request
	if($action == 'getImgMarks' && $userIDSet && $picSet)
	{

		$sql = "SELECT `x`,`y` FROM `cbdata` WHERE `userid` = '".$userID."' AND `image`='".$pic."'";
		$result = mysql_query($sql);
		$marks = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($marks,'{"x":'.$row['x'].',"y":'.$row['y'].'}');
		}
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"getImage","status":true,"move":"'.$move.'","image":"'.$pic.'","marks":['.implode(",",$marks).']})';
	}

	//Process the getImgTacking Request
	if($action == 'getImgTracking' && $userIDSet)
	{
		$sql = "SELECT `image` FROM `imgtracking` WHERE `userid` = '".$userID."'";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$lastImage = $row['image'];
		
		$sql = "SELECT `x`,`y` FROM `cbdata` WHERE `userid` = '".$userID."' AND `image`='".$lastImage."'";
		$result = mysql_query($sql);
		$marks = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($marks,'{"x":'.$row['x'].',"y":'.$row['y'].'}');
		}
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		
		echo 'processJSON({"name":"lastViewedImage","status":true,"move":"'.$move.'","image":"'.$lastImage.'","marks":['.implode(",",$marks).']})';
	}
}
?>