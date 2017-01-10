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
//This section also sets any global variables that might be used.

//Set the data string
$dateString = 'Y-m-d H:i:s';

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

$xCoordSet = isset($_GET['xCoord']);
if($xCoordSet == true)
{
	$xCoord = file_prep($_GET['xCoord']);
}

$yCoordSet = isset($_GET['yCoord']);
if($yCoordSet == true)
{
	$yCoord = file_prep($_GET['yCoord']);
}

$dragCoordSet = isset($_GET['dragCoord']);
if($dragCoordSet == true)
{
	$dragCoord = file_prep($_GET['dragCoord']);
}

$annotTypeSet = isset($_GET['annotType']);
if($annotTypeSet == true)
{
	$annotType = file_prep($_GET['annotType']);
}

$widthSet = isset($_GET['width']);
if($widthSet == true)
{
	$width = file_prep($_GET['width']);	
}

$heightSet = isset($_GET['height']);
if($heightSet == true)
{
	$height = file_prep($_GET['height']);	
}

$newLineSet = isset($_GET['newLine']);
if($newLineSet == true)
{
	$newLine = file_prep($_GET['newLine']);	
}

$isNewLineSet = isset($_GET['isNewLine']);
if($isnewLineSet == true)
{
	$isNewLine = file_prep($_GET['isNewLine']);	
}

$drawingColorSet = isset($_GET['drawingColor']);
if($drawingColorSet == true)
{
	$drawingColor = file_prep($_GET['drawingColor']);	
}

$markerCategorySet = isset($_GET['markerCategory']);
if($markerCategorySet == true)
{
	$markerCategory = file_prep($_GET['markerCategory']);	
}

$M1Set = isset($_GET['M1']);
if($M1Set == true)
{
	$M1 = file_prep($_GET['M1']);	
}

$M2Set = isset($_GET['M2']);
if($M2Set == true)
{
	$M2 = file_prep($_GET['M2']);	
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

//Set the Drop Down Data ID
$dropDownDataIDSet = isset($_GET['dropdowndataid']);
if($dropDownDataIDSet == true)
{
	$dropDownDataID = file_prep($_GET['dropdowndataid']);
}

//Set the Secondary Grade Drop Down Data ID
$dropDownDataIDSet2 = isset($_GET['dropdowndataid2']);
if($dropDownDataIDSet2 == true)
{
	$dropDownDataID2 = file_prep($_GET['dropdowndataid2']);
}

//Set the Color Value Id
$colorValueIdSet = isset($_GET['colorvalueid']);
$colorValueId = 0;
if($colorValueIdSet == true)
{
	$colorValueId = file_prep($_GET['colorvalueid']);
}

//Set coordinate array
$coordArrayValueIdSet = isset($_GET['coordArray']);
if($coordArrayValueIdSet == true)
{
	$coordArray = file_prep($_GET['coordArray']);
}

//Set image array
$imageArraySet = isset($_GET['imageArray']);
if($imageArraySet == true)
{
	$imageArray = file_prep($_GET['imageArray']);
}

//Set selected user
$selectedUserSet = isset($_GET['selectedUser']);
if($selectedUserSet == true)
{
	$selectedUser = file_prep($_GET['selectedUser']);
}

//-----------------------------Proccess Commands-------------------------------//

if($actionSet && $moveSet)
{
	//riffer (will be erased later)
	if($action == 'tempAction')
	{
		$sql = "INSERT INTO `cbdata` (`userid`,`image`,`x`,`y`,`project_id`,`after_review`,`colorval`) VALUES ('".$userID."','".$pic."','".$x."','".$y."','".$pid."','0','" . $colorValueId . "')";
		$result = mysql_query($sql);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"query":"'. $sql . '"})';
	}
	
	//Process add markers command
	if($action == 'add' && $userIDSet && $picSet && $xSet && $ySet && $pidSet && $afterReviewSet)
	{
		$sql = "INSERT INTO `cbdata` (`userid`,`image`,`x`,`y`,`project_id`,`after_review`,`colorval`) VALUES ('".$userID."','".$pic."','".$x."','".$y."','".$pid."','".$afterReview."','".$colorValueId."')";
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
	
	//Process to remove a review marker
	if($action == 'removeReviewMarker' && $userIDSet && $picSet && $xSet && $ySet && $pidSet)
	{
		$sql = "INSERT INTO `cbdata` (`userid`,`image`,`x`,`y`,`project_id`,`review_mark_removal`) VALUES ('".$userID."','".$pic."','".$x."','".$y."','".$pid."',1)";
		$result = mysql_query($sql);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"removeReviewMarker","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
	}

	//Process removeAllMarkers markers command
	if($action == 'removeAllMarkers' && $userIDSet && $picSet && $pidSet)
	{
		$sql = "DELETE FROM `cbdata` WHERE `userid`='".$userID."' AND `review_mark`='0' AND `review_mark_removal`='0' AND `image`='".$pic."'  AND `project_id`='".$pid."';";
		$result = mysql_query($sql);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"removeAllMarkers","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
	}
	
	//Process resetReviewSet markers command
	if($action == 'resetReviewSet' && $userIDSet && $picSet && $pidSet)
	{
		$sql = "DELETE FROM `cbdata` WHERE `userid`='".$userID."' AND `review_mark`='0' AND `review_mark_removal`='1'  AND `image`='".$pic."'  AND `project_id`='".$pid."';";
		$result = mysql_query($sql);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"resetReviewSet","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
	}

	//Process the setImgTacking request
	if($action == 'setImgTracking' && $userIDSet && $picSet && $pidSet)
	{
		$sql = "SELECT `userid` FROM `imgtracking` WHERE `userid` = '".$userID."' AND `project_id`='".$pid."';"; 
		$result = mysql_query($sql);
		if (mysql_num_rows($result) == 1)
		{
			$sql = "UPDATE `imgtracking` SET `date`='".date($dateString)."',`image`='".$pic."' WHERE `userid`='".$userID."' AND `project_id`='".$pid."';";
			$result = mysql_query($sql);
			
			header('Content-Type: application/json');
			header('Content-Disposition: attachment; filename=json.run');
			echo 'processJSON({"name":"imgTackingUpdated","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
		}
		else
		{
			$sql = "INSERT INTO `imgtracking` (`image`,`userid`,`project_id`) VALUES ('".$pic."','".$userID."','".$pid."')";
			$result = mysql_query($sql);
			
			header('Content-Type: application/json');
			header('Content-Disposition: attachment; filename=json.run');
			echo 'processJSON({"name":"imgTackingSet","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
		}
	}
	
	//Process the changeMarkerColor request
	if($action == 'updatecolor' && $userIDSet && $picSet && $xSet && $ySet && $pidSet && $colorValueIdSet)
	{
			$sql = "DELETE FROM `cbdata` WHERE `userid`='".$userID."' AND `review_mark`='0' AND `image`='".$pic."' AND `x`='".$x."' AND `y`='".$y."' AND `project_id`='".$pid."';";
		$result = mysql_query($sql);
		
			$sql = "INSERT INTO `cbdata` (`userid`,`image`,`x`,`y`,`project_id`,`colorval`) VALUES ('".$userID."','".$pic."','".$x."','".$y."','".$pid."','".$colorValueId."')";
			$result = mysql_query($sql);
			
			header('Content-Type: application/json');
			header('Content-Disposition: attachment; filename=json.run');
			echo 'processJSON({"name":"updatecolor","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
	}

	//Proccess the getImgMarks request for current user
	if($action == 'getImgMarks' && $userIDSet && $picSet && $pidSet)
	{

		$sql = "SELECT `x`,`y`,`r`,`colorval` FROM `cbdata` WHERE `userid` = '".$userID."' AND `image`='".$pic."' AND `review_mark`=FALSE AND `review_mark_removal`=FALSE AND `project_id`='$pid';";
		$result = mysql_query($sql);
		$marks = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($marks,'{"x":'.$row['x'].',"y":'.$row['y'].',"r":'.$row['r'].',"colorval":'.$row['colorval'].'}');
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
		$sql = "SELECT `x`,`y`,`r`,`colorval` FROM `cbdata` WHERE `userid` = '".$requestedUserID."' AND `image`='".$pic."' AND `review_mark`=FALSE AND `review_mark_removal`=FALSE  AND `project_id`='".$pid."';";
		$result = mysql_query($sql);
		$marks = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($marks,'{"x":'.$row['x'].',"y":'.$row['y'].',"r":'.$row['r'].',"colorval":'.$row['colorval'].'}');
		}
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"getUsersImgMarks","status":true,"move":"'.$move.'","image":"'.$pic.'","user":"'.$requestedUserID.'","marks":['.implode(",",$marks).']})';
	}
	
	//Proccess the getReviewImgMarks
	if($action == 'getReviewImgMarks' && $picSet && $pidSet)
	{
		$sql = "SELECT `x`,`y`,`r`,`colorval` FROM `cbdata` WHERE `review_mark`=TRUE AND `image`='".$pic."' AND `project_id`='".$pid."';";
		$result = mysql_query($sql);
		$marks = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($marks,'{"x":'.$row['x'].',"y":'.$row['y'].',"r":'.$row['r'].',"colorval":'.$row['colorval'].'}');
		}
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"getReviewImgMarks","status":true,"move":"'.$move.'","image":"'.$pic.'","marks":['.implode(",",$marks).']})';
	}
	
	//Proccess the getEditableReviewImgMarks
	//This retrevies the review marks that users can "remove". In reality
	//users can never modify review marks but they can add a mark that says 
	//that a particular review mark is not correct. This is done in the db
	//by setting the `review_mark_removal` flag to 1
	if($action == 'getEditableReviewImgMarks' && $picSet && $pidSet)
	{
		$sql = "
		SELECT *
		FROM (

		SELECT *
		FROM (

		SELECT *
		FROM `cbdata`
		WHERE `project_id` = '".$pid."'
		AND `image` = '".$pic."'
		AND `review_mark` = '1'
		) AS p1
		LEFT JOIN (
		
		SELECT `x` , `y` , `r` ,  `colorval` , `project_id` AS 'project_id2', `image` AS 'image2', `review_mark_removal` AS 'review_mark_removal2'
		FROM `cbdata`
		WHERE `project_id` = '".$pid."'
		AND `image` = '".$pic."'
		AND `review_mark_removal` = '1'
		) AS p2
		USING ( `x` , `y`, `r` , `colorval` )
		) AS table1
		WHERE `image2` IS NULL ";
		
		//echo $sql;
		
		$result = mysql_query($sql);
		$marks = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($marks,'{"x":'.$row['x'].',"y":'.$row['y'].',"r":'.$row['r'].',"colorval":'.$row['colorval'].'}');
		}
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"getEditableReviewImgMarks","status":true,"move":"'.$move.'","image":"'.$pic.'","marks":['.implode(",",$marks).']})';
	}
	
	//Proccess the getAllImgMarks request for markings on an image from all users.
	if($action == 'getAllImgMarks' && $picSet && $pidSet && $viewAllMarkings)
	{
		$sql = "SELECT `userid`,`x`,`y`,`r`,`colorval` FROM `cbdata` WHERE `image`='".$pic."' AND `project_id`='$pid' AND `review_mark`=FALSE AND `review_mark_removal`=FALSE;";
		$result = mysql_query($sql);
		$marks = array();
		$allUserMarks = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if(isset($marks[$row['userid']]) == false)
			{
				$marks[$row['userid']] = array();
			}
			array_push($marks,'{"x":'.$row['x'].',"y":'.$row['y'].',"r":'.$row['r'].',"colorval":'.$row['colorval'].'}');
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
		
		$sql = "SELECT `x`,`y`,`r`,`colorval` FROM `cbdata` WHERE `userid` = '".$userID."' AND `image`='".$lastImage."'";
		$result = mysql_query($sql);
		$marks = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($marks,'{"x":'.$row['x'].',"y":'.$row['y'].',"r":'.$row['r'].',"colorval":'.$row['colorval'].'}');
		}
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		//lastViewedImage
		echo 'processJSON({"name":"getImgTracking","status":true,"move":"'.$move.'","image":"'.$lastImage.'","marks":['.implode(",",$marks).']})';
	}
	
	//Process the setDropDownData Request
	if($action == 'setDropDownData' && $userIDSet && $dropDownDataIDSet && $pidSet && $picSet)
	{
		$sql = "SELECT * FROM `cbmarker`.`imagedata` WHERE `user_id` = '".$userID."' AND `project_id`='".$pid."' AND `image`='$pic' AND `dropdowndata_set`=true;"; 
		$result = mysql_query($sql);
		if (mysql_num_rows($result) == 1)
		{
			//If the image exists there then the drop down data has already been set once and can not be set again
			echo 'processJSON({"name":"setDropDownData","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
		}
		else
		{
			$sql = "INSERT INTO `cbmarker`.`imagedata` (`image`,`user_id`,`project_id`,`dropdowndata_id`,`dropdowndata_id2`,`dropdowndata_set`) VALUES ('".$pic."','".$userID."','".$pid."','".$dropDownDataID."','".$dropDownDataID2."',true)";
			$result = mysql_query($sql);
			
			header('Content-Type: application/json');
			header('Content-Disposition: attachment; filename=json.run');
			if($result)
			{
				echo 'processJSON({"name":"setDropDownData","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
			}
			else
			{
				echo 'processJSON({"name":"setDropDownData","move":"'.$move.'","false":false,"image":"'.$pic.'"})';
			}
		}
	}
	
	//Process the getDropDownData Request
	if($action == 'getDropDownData' && $userIDSet && $pidSet && $picSet)
	{
		$sql = "SELECT * FROM `cbmarker`.`imagedata` WHERE `user_id` = '".$userID."' AND `project_id`='".$pid."' AND `image`='$pic' AND `dropdowndata_set`=true"; 
		$result = mysql_query($sql);

		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		
		if(mysql_num_rows($result) == 1)
		{
			$row = mysql_fetch_array($result);
			$dropdowndataID = $row['dropdowndata_id'];
			$dropdowndataID2 = $row['dropdowndata_id2'];
			echo 'processJSON({"name":"getDropDownData","move":"'.$move.'","status":true,"image":"'.$pic.'","dropDownData":"'.$dropdowndataID.'","dropDownData2":"'.$dropdowndataID2.'"})';
		}
		else
		{
			//more than 1 Drop Down Data set...
			echo 'processJSON({"name":"getDropDownData","move":"'.$move.'","status":true,"image":"'.$pic.'","dropDownData":""})';
		}
	}
	
	//Process the setNoMarks Request
	if($action == 'setNoMarks' && $userIDSet && $pidSet && $picSet)
	{
		$sql = "SELECT * FROM `cbmarker`.`imagedata` WHERE `user_id` = '".$userID."' AND `project_id`='".$pid."' AND `image`='$pic' AND `no_markings_set`=true;"; 
		$result = mysql_query($sql);
		if (mysql_num_rows($result) == 1)
		{
			//If the image exists there then do nothing
			echo 'processJSON({"name":"setNoMarks","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
		}
		else
		{
			$sql = "INSERT INTO `cbmarker`.`imagedata` (`image`,`user_id`,`project_id`,`no_markings`,`no_markings_set`) VALUES ('".$pic."','".$userID."','".$pid."',true,true)";
			$result = mysql_query($sql);
			
			header('Content-Type: application/json');
			header('Content-Disposition: attachment; filename=json.run');
			if($result)
			{
				echo 'processJSON({"name":"setNoMarks","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
			}
			else
			{
				//something went wrong with sql command
				echo 'processJSON({"name":"setNoMarks","move":"'.$move.'","false":false,"image":"'.$pic.'"})';
			}
		}
	}
	
	if($action == 'setAnnotCoord' && $userIDSet && $pidSet && $annotTypeSet)
	{
		//lines
		if($annotType == "line")
		{
			//indicates new line
			if($isNewLineSet)
			{
				$sql = "INSERT INTO cbmarker.annot_test (userid, project_id, image, date, coords, newLine, color, annot_type)
					VALUES (" . $userID . "," . $pid . ",'". $pic . "', NOW()," . $coordArray[0] . ", true ,'" . $drawingColor . "','" . $annotType ."')";
				$result = mysql_query($sql);
			}
			else //if isNewLine == 0
			{
				$sql = "INSERT INTO cbmarker.annot_test (userid, project_id, image, date, coords, newLine, color, annot_type)
					VALUES (" . $userID . "," . $pid . ",'". $pic . "', NOW()," . $coordArray[0] . ", false ,'" . $drawingColor . "','" . $annotType ."')";
				$result = mysql_query($sql);
			}
			
			//vertices after new line
			for($i = 1; $i < sizeof($coordArray); $i++)
			{
				$sql = "INSERT INTO cbmarker.annot_test (userid, project_id, image, date, coords, newLine, color, annot_type)
					VALUES (" . $userID . "," . $pid . ",'". $pic . "', NOW()," . $coordArray[$i] . ", false ,'" . $drawingColor . "','" . $annotType . "')";
				$result = mysql_query($sql);
			}
		}

		//rectangles and markers
		else if($annotType == "rect" || $annotType == "marker")
		{
			for($i = 0; $i < sizeof($coordArray); $i++)
			{
				$sql = "INSERT INTO cbmarker.annot_test (userid, project_id, image, date, coords, newLine, color, annot_type)
					VALUES (" . $userID . "," . $pid . ",'". $pic . "', NOW()," . $coordArray[$i] . ", false ,'" . $drawingColor . "','" . $annotType ."')";
				$result = mysql_query($sql);
			}
		}
		
		//marker category
		else if($annotType = "markerCategory")
		{
			$sql = "INSERT INTO cbmarker.annot_test (userid, project_id, date, color, annot_type, markerCategory)
				VALUES (" . $userID . "," . $pid . ", NOW(),'" . $drawingColor . "','" . $annotType ."', '" . $markerCategory . "')";
			$result = mysql_query($sql);	
		}
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"setAnnotCoord","image":"'.$isNewLineSet.'","coords":"'.$coordArray.'","type:":"'.$annotType.'"})';
	}

	if($action == 'eraseAnnotCoord' && $userIDSet && $pidSet && $annotTypeSet)
	{
		if($annotType == "line")
		{
			$sql = "DELETE FROM cbmarker.annot_test WHERE userid=" . $userID . " AND project_id=" . $pid
				   . " AND image='" . $pic ."' AND annot_type='line'";	   
			$result = mysql_query($sql);
		}
		else if($annotType == "rect")
		{
			$sql = "DELETE FROM cbmarker.annot_test WHERE userid=" . $userID . " AND project_id=" . $pid
				   . " AND image='" . $pic ."' AND annot_type='rect'";	   
			$result = mysql_query($sql);
		}
		else if($annotType == "marker")
		{
			$sql = "DELETE FROM cbmarker.annot_test WHERE userid=" . $userID . " AND project_id=" . $pid
				   . " AND image='" . $pic ."' AND annot_type='marker'";	   
			$result = mysql_query($sql);
		}
		else if ($annotType == "markerCategory")
		{
			$sql = "DELETE FROM cbmarker.annot_test WHERE userid=" . $userID . " AND project_id=" . $pid
				. " AND annot_type='markerCategory' AND color='" . $drawingColor. "'";
				$result = mysql_query($sql);
		}
			   
	    header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"eraseAnnotCoord","image":"'.$sql.'",
			 "coords":"'.$annotType.'"})';
	}
	
	if($action == 'writeXMLData')
	{
		//clear the file
		file_put_contents("XML_Data.xml", "");
		
		//open the file
		$handle = fopen("XML_Data.xml", "w+");
		fwrite($handle, "<ANNOTATIONS>\r\n");
		
		//markers
		$sql = "SELECT coords, color, annot_type FROM cbmarker.annot_test WHERE
			project_id=" . $pid .
			" AND userid=" . $userID .
			" AND image=\"" . $pic .
			"\" AND annot_type='marker'
			ORDER BY id ASC";
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{			
			$markerX = $row['coords'];
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$markerY = $row['coords'];
			
			fwrite($handle, "  <annotation annot_type='marker' color='" . $row['color'] . "' X='" . $markerX . "' Y='" . $markerY . "' />\r\n");
		} 
		
		//rectangles
		$sql = "SELECT coords, color, annot_type FROM cbmarker.annot_test WHERE
		project_id='$pid' 
		AND userid=" . $_SESSION['Id'] .
		" AND image=\"" . $pic .
		"\" AND annot_type='rect'
		ORDER BY id ASC";
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{			
			$topLeftX = $row['coords'];
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$topLeftY = $row['coords'];
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$bottomRightX = $row['coords'];
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$bottomRightY = $row['coords'];
			
			//swap top left and bottom right if rectangle drawn backwards
			if($bottomRightX < $topLeftX)
			{
				$temp = $topLeftX;
				$topLeftX = $bottomRightX;
				$bottomRightX = $temp;
			}
			if($bottomRightY < $topLeftY)
			{
				$temp = $topLeftY;
				$topLeftY = $bottomRightY;
				$bottomRightY = $temp;
			}
			
			fwrite($handle, "  <annotation annot_type='rect' color='" . $row['color'] . 
			"' top_left_X='" . $topLeftX . "' 
			top_left_Y='" . $topLeftY . "'
			bottom_right_X='" . $bottomRightX . "'
			bottom_right_Y='" . $bottomRightY . "'
			/>\r\n");
		} 
	
		//lines
		$sql = "SELECT coords, color, annot_type, newLine FROM cbmarker.annot_test WHERE
		project_id='$pid' 
		AND userid=" . $_SESSION['Id'] .
		" AND image=\"" . $pic .
		"\" AND annot_type='line'
		ORDER BY id ASC";
		$result = mysql_query($sql);
		
		$counter = 0;
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if($row['newLine'] == true)
			{	
				$currXCoord = $row['coords'];
				$row = mysql_fetch_array($result, MYSQL_ASSOC);
				$currYCoord = $row['coords'];
				
				fwrite($handle, "  <annotation annot_type='line' color='". $row['color'] . "' X='" . $currXCoord . "' Y='" . $currYCoord . "' />\r\n");
				$counter = -1;
			}
			else if ($counter % 2 == 0)
			{
				$currXCoord = $row['coords'];
			}	
			else if	($counter % 2 != 0)
			{
				$currYCoord = $row['coords'];
				fwrite($handle, "  <annotation X='". $currXCoord ."' Y='". $currYCoord . "' />\r\n");
			}
			$counter++;
		} 
		
		//close the file
		fwrite($handle, "</ANNOTATIONS>\r\n");
		fclose($handle);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		//echo 'processJSON("' . $sql . '")';
	}
	
	if($action == 'clearAllAnnotations' && $userIDSet && $picSet && $pidSet)
	{
		$sql = "DELETE FROM cbmarker.annot_test 
			   WHERE userid=". $userID . 
			   " AND image='" . $pic . 
			   "' AND project_id=" . $pid;
		$result = mysql_query($sql);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"clearAllAnnotations","status":true,"image":"'.$pic.'"})';
	}
	
	if($action == 'erasePosPercent')
	{
		$sql = "DELETE FROM cbmarker.pospercentestimation
				WHERE userid=". $userID .
				" AND image='" .$pic .
				"' AND project_id=" .$pid;
				$result = mysql_query($sql);
				
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"erasePosPercent","image":"'.$pic.'"})';
	}
	
	if($action == 'setPosPercent')
	{
		$sql = "INSERT INTO cbmarker.pospercentestimation (userid, project_id, image, date, M1, M2)
				VALUES (" . $userID . "," . $pid . ",'" . $pic . "', NOW()," . $M1 . " , ". $M2 . ")";
		$result = mysql_query($sql);

		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"setPosPercent","image":"'.$pic.'"})';
	}
	
	if($action == 'getImagePercent')
	{
		$arrayOfM1 = array();
		$arrayOfM2 = array();
		
		for($i = 0; $i < sizeof($pic); $i++)
		{
			$sql = "SELECT M1, M2 FROM cbmarker.pospercentestimation WHERE userid=". $userID ." AND image='". $pic[$i] . "' AND project_id=" . $pid;	
			$result = mysql_query($sql);
			
			if($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$arrayOfM1[$i] = $row['M1'];
				$arrayOfM2[$i] = $row['M2'];
			}
			else
			{
				$arrayOfM1[$i] = 0;
				$arrayOfM2[$i] = 0;
			}
		}
	
		$sql = "SELECT M1, M2 FROM cbmarker.pospercentestimation WHERE userid=". $userID ." AND image='". $pic ."' AND project_id=" . $pid;
		$result = mysql_query($sql);
				
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		echo 'processJSON({"name":"getImagePercent","M1":'. json_encode($arrayOfM1) .',"M2":'. json_encode($arrayOfM2) .'})';
	}
	
	if($action == 'writeEstimationXMLData')
	{
		//clear the file
		file_put_contents("XML_Data.xml", "");
		
		//open the file
		$handle = fopen("XML_Data.xml", "w+");
		
		$sql = "SELECT M1, M2, userid, image FROM cbmarker.pospercentestimation WHERE 
			project_id=" . $pid .
			" ORDER BY userid";
			//. " AND userid=" . $userID;
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			fwrite($handle, "<Image>\r\n");
			fwrite($handle, "  <Name>" . $row['image'] . "</Name>\r\n");		
			$user_query = "SELECT first_name, last_name FROM cialab.users_data WHERE id=" . $row['userid'];
			$user_data = mysql_query($user_query);
			$user_row = mysql_fetch_array($user_data, MYSQL_ASSOC);
			fwrite($handle, "  <User>" . $user_row['first_name'] . " " . $user_row['last_name'] . "</User>\r\n");
			fwrite($handle, "  <M1>" . $row['M1'] . "</M1>\r\n");
			fwrite($handle, "  <M2>" . $row['M2'] . "</M2>\r\n");
			fwrite($handle, "</Image>\r\n\r\n");
		}
		
		fclose($handle);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		//echo 'processJSON("' . $sql . '")';
	}
	
	if($action == 'writeMarkerCountXMLData')
	{	
		//clear the file
		file_put_contents("XML_Data.xml", "");
		
		//open the file
		$handle = fopen("XML_Data.xml", "w+");
		
		for($imageIndex = 0; $imageIndex < sizeof($imageArray); $imageIndex++)
		{
			$sql = "SELECT image, x, y, userid, colorval FROM cbmarker.cbdata WHERE
			project_id=" . $pid .
			" AND userid=" . $selectedUser .
			" AND image='" . $imageArray[$imageIndex] .
			"' ORDER BY userid ASC";
			$result = mysql_query($sql);
			
			if($row = mysql_fetch_array($result, MYSQL_ASSOC));
			{				
				//get username
				if($imageIndex == 0)
				{
					$user_query = "SELECT first_name, last_name FROM cialab.users_data WHERE id=" . $row['userid'];
					$user_data = mysql_query($user_query);
					$user_row = mysql_fetch_array($user_data, MYSQL_ASSOC);
					fwrite($handle, "<User>" . $user_row['first_name'] . " " . $user_row['last_name'] . "</User>\r\n\r\n");
				}				
				
				fwrite($handle, "<Image>\r\n");
				fwrite($handle, "  <Name>" . $imageArray[$imageIndex] . "</Name>\r\n");
			
				$posCount = 0;
				$negCount = 0;	
				$index = 0;
				$coords = array();
				while($row)
				{	
					if($row['colorval'] == 100)
					{
						$posCount++;
						$coords[$index * 3] = "positive";
					}
					else
					{
						$negCount++;
						$coords[$index * 3] = "negative";
					}
					
					$coords[$index * 3 + 1] = $row['x'];
					$coords[$index * 3 + 2] = $row['y'];	
						
					$row = mysql_fetch_array($result, MYSQL_ASSOC);
					$index++;
				}
				fwrite($handle, "  <Pos_Count>" . $posCount . "</Pos_Count>\r\n");
				fwrite($handle, "  <Neg_Count>" . $negCount . "</Neg_Count>\r\n");
				for($i = 0; $i < sizeof($coords)/3; $i++)
				{
					fwrite($handle, "  <Point>\r\n");
					fwrite($handle, "    <PosOrNeg>" . $coords[$i * 3] . "</PosOrNeg>\r\n");
					fwrite($handle, "    <X>" . $coords[$i * 3 + 1] . "</X>\r\n");
					fwrite($handle, "    <Y>" . $coords[$i * 3 + 2] . "</Y>\r\n");
					fwrite($handle, "  </Point>\r\n");
				}
				
				fwrite($handle, "</Image>\r\n\r\n");
			}
		}
		
		fclose($handle);
		
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename=json.run');
		//echo 'processJSON("' . sizeof($imageArray) . '")';
	}
}
?>