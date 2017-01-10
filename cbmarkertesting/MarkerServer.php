<?php
include("../SecurePage.php");
include("../GlobalVariables.php");
//Connect to Database
Connect_To_DB($db_server_official,$db_user_official,$db_pwd_official,$db_cbmarker);

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
$userIDSet = isset($_SESSION['Id']);
if($userIDSet == true)
{
	$userID = file_prep($_SESSION['Id']);
}

//Set the requested UserID Variable
$requestedUserIDSet = isset($_GET['userid']);
if($requestedUserIDSet == true)
{
	$requestedUserID = file_prep($_GET['userid']);
}

//Set permissions
$viewAllMarkings = $_SESSION['Permissions']['view_all_markings'];

//Set the Project_ID variable (pid)
$pidSet = isset($_GET['pid']);
if($pidSet == true)
{
	$pid = file_prep($_GET['pid']);
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

//Set the move variable
$moveSet = isset($_GET['move']);
if($moveSet == true)
{
	$move = file_prep($_GET['move']);
}

//Set the after_review
$afterReviewSet = isset($_GET['after_review']);
if($afterReviewSet == true)
{
	$afterReview = file_prep($_GET['after_review']);
}

//-----------------------------Proccess Commands-------------------------------//

if($actionSet && $moveSet)
{
	//Process add markers command
	if($action == 'add' && $userIDSet && $picSet && $xSet && $ySet && $pidSet && $afterReviewSet)
	{
		$sql = "INSERT INTO `cbdata` (`userid`,`date`,`image`,`x`,`y`,`project_id`,`after_review`) VALUES ('".$userID."','".date('Y-m-d h:i:s')."','".$pic."','".$x."','".$y."','".$pid."','".$afterReview."')";
		$result = mysql_query($sql);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"addMarker","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
	}

	//Process remove markers command
	if($action == 'remove' && $userIDSet && $picSet && $xSet && $ySet && $pidSet)
	{
		$sql = "DELETE FROM `cbdata` WHERE `userid`='".$userID."' AND `review_mark`='0' AND `image`='".$pic."' AND `x`='".$x."' AND `y`='".$y."' AND `project_id`='".$pid."';";
		$result = mysql_query($sql);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"removeMarker","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
	}

	//Process removeAll markers command
	if($action == 'removeAll' && $userIDSet && $picSet && $pidSet)
	{
		$sql = "DELETE FROM `cbdata` WHERE `userid`='".$userID."' AND `review_mark`='0' AND `image`='".$pic."'  AND `project_id`='".$pid."';";
		$result = mysql_query($sql);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"removeAllMarkers","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
	}

	//Process the setImgTacking request
	if($action == 'setImgTracking' && $userIDSet && $picSet && $pidSet)
	{
		$sql = "SELECT `userid` FROM `imgtracking` WHERE `userid` = '".$userID."' AND `project_id`='".$pid."';"; 
		$result = mysql_query($sql);
		if (mysql_num_rows($result) == 1)
		{
			$sql = "UPDATE `imgtracking` SET `date`='".date('Y-m-d h:i:s')."',`image`='".$pic."' WHERE `userid`='".$userID."' AND `project_id`='".$pid."';";
			$result = mysql_query($sql);
			
			header('Content-Type: application/json');
			header('Content-Disposition: attachment; filename=json.run');
			echo 'processJSON({"name":"imgTackingUpdated","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
		}
		else
		{
			$sql = "INSERT INTO `imgtracking` (`date`,`image`,`userid`,`project_id`) VALUES ('".date('Y-m-d h:i:s')."','".$pic."','".$userID."','".$pid."')";
			$result = mysql_query($sql);
			
			header('Content-Type: application/json');
			header('Content-Disposition: attachment; filename=json.run');
			echo 'processJSON({"name":"imgTackingSet","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
		}
	}

	//Proccess the getImgMarks request for current user
	if($action == 'getImgMarks' && $userIDSet && $picSet && $pidSet)
	{

		$sql = "SELECT `x`,`y` FROM `cbdata` WHERE `userid` = '".$userID."' AND `image`='".$pic."' AND `review_mark`=FALSE AND `project_id`='$pid';";
		$result = mysql_query($sql);
		$marks = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($marks,'{"x":'.$row['x'].',"y":'.$row['y'].'}');
		}
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"getImgMarks","status":true,"move":"'.$move.'","image":"'.$pic.'","marks":['.implode(",",$marks).']})';
	}
	
	//Return the users with marks on a specific Image
	if($action == 'getUsersForImage' && $picSet && $pidSet)
	{
		$sql = "SELECT DISTINCT `cbmarker`.`cbdata`.`userid`,`cialab`.`users_data`.`first_name`,`cialab`.`users_data`.`last_name` FROM `cbmarker`.`cbdata`, `cialab`.`users_data` WHERE `image` = '".$pic."' AND `cbmarker`.`cbdata`.`project_id` = '".$pid."' AND `cialab`.`users_data`.`id`=`cbmarker`.`cbdata`.`userid` ORDER BY `cbmarker`.`cbdata`.`userid` DESC;";
		$result = mysql_query($sql);
		$users = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($users,'{"firstName":"'.$row['first_name'].'","lastName":"'.$row['last_name'].'","id":'.$row['userid'].'}');
		}
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"getUsersForImage","status":true,"move":"'.$move.'","image":"'.$pic.'","users":['.implode(",",$users).']})';
	}
	
	//Proccess the getUsersImgMarks request for specific user
	if($action == 'getUsersImgMarks' && $picSet && $pidSet && $viewAllMarkings && $requestedUserIDSet)
	{
		$sql = "SELECT `x`,`y` FROM `cbdata` WHERE `userid` = '".$requestedUserID."' AND `image`='".$pic."' AND `review_mark`=FALSE  AND `project_id`='".$pid."';";
		$result = mysql_query($sql);
		$marks = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($marks,'{"x":'.$row['x'].',"y":'.$row['y'].'}');
		}
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"getUsersImgMarks","status":true,"move":"'.$move.'","image":"'.$pic.'","user":"'.$requestedUserID.'","marks":['.implode(",",$marks).']})';
	}
	
	//Proccess the getReviewImgMarks
	if($action == 'getReviewImgMarks' && $picSet && $pidSet)
	{
		$sql = "SELECT `x`,`y` FROM `cbdata` WHERE `review_mark`=TRUE AND `image`='".$pic."' AND `project_id`='".$pid."';";
		$result = mysql_query($sql);
		$marks = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($marks,'{"x":'.$row['x'].',"y":'.$row['y'].'}');
		}
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"getReviewImgMarks","status":true,"move":"'.$move.'","image":"'.$pic.'","marks":['.implode(",",$marks).']})';
	}
	
	//Proccess the getAllImgMarks request for markings on an image from all users.
	if($action == 'getAllImgMarks' && $picSet && $pidSet && $viewAllMarkings)
	{
		$sql = "SELECT `userid`,`x`,`y` FROM `cbdata` WHERE `image`='".$pic."' AND `project_id`='$pid' AND `review_mark`=FALSE;";
		$result = mysql_query($sql);
		$marks = array();
		$allUserMarks = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if(isset($marks[$row['userid']]) == false)
			{
				$marks[$row['userid']] = array();
			}
			array_push($marks[$row['userid']],'{"x":'.$row['x'].',"y":'.$row['y'].'}');
		}
		foreach(array_keys($marks) as $user)
		{
			array_push($allUserMarks,'{"'.$user.'":[' . implode(",",$marks[$user]) . ']}');
		}
		
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"getImage","status":true,"move":"'.$move.'","image":"'.$pic.'","users":['.implode(',',$allUserMarks).']})';
	}

	//Process the getImgTacking Request
	if($action == 'getImgTracking' && $userIDSet && $pidSet)
	{
		$sql = "SELECT `image` FROM `imgtracking` WHERE `userid` = '".$userID."' AND `project_id`='$pid';";
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