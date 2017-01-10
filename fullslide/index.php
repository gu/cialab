<?php 
include("../SecurePage.php");
include("../GlobalVariables.php"); 
include("../GlobalFunctions.php");

Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab);

$ROIDirectory = "";
$image_height = 0;
$image_width = 0;
$pid = 0;
$ROIFolder = 0;
$ProjectType = 0;

//Try to load the project they request via the pid value
if(isset($_GET['pid']) && $_GET['pid']!="")
{
	$sql = "SELECT `roi_projects`.`image_height`,`roi_projects`.`image_width`,`roi_projects`.`id`,`roi_projects`.`name`,`roi_projects`.`folder`,`roi_projects_members`.`roi_project_id` FROM `roi_projects`,`roi_projects_members` WHERE `roi_projects`.`id`=`roi_projects_members`.`roi_project_id` AND `roi_projects_members`.`project_viewable` = '1' AND `user_id`='".$_SESSION['Id']."' AND `roi_projects_members`.`roi_project_id`='".mysql_prep($_GET['pid'])."';"; 
	try
	{
		$row = mysql_fetch_array(mysql_query($sql));
		$pid = $row['id'];
		$ROIFolder = $row['folder'];
		$ROIDirectory = $URL . $row['folder'];
		$image_height = $row['image_height'];
		$image_width = $row['image_width'];
	}
	catch (Exception $e){}
}
//try to find the last project they were working on if pid not specified.
if($ROIFolder == "")
{
	$sql = "SELECT `cialab`.`roi_projects`.`image_height`,`cialab`.`roi_projects`.`image_width`,`cialab`.`roi_projects`.`id`,`cialab`.`roi_projects`.`name`,`cialab`.`roi_projects`.`folder`,`cialab`.`roi_projects_members`.`roi_project_id` FROM `cialab`.`roi_projects`,`cialab`.`roi_projects_members`, (SELECT `cbmarker`.`imgtracking`.* FROM `cbmarker`.`imgtracking`,(SELECT MAX(p.date) AS maxdate FROM `cbmarker`.`imgtracking`,(SELECT * FROM `cbmarker`.`imgtracking` WHERE `userid`='".$_SESSION['Id']."') AS p) AS x WHERE `x`.`maxdate` = `cbmarker`.`imgtracking`.`date` AND `userid` = '".$_SESSION['Id']."') AS y WHERE `cialab`.`roi_projects`.`id`=`cialab`.`roi_projects_members`.`roi_project_id` AND `cialab`.`roi_projects_members`.`project_viewable`='1' AND `user_id`=y.userid AND `cialab`.`roi_projects_members`.`roi_project_id`=y.project_id;";
	try
	{
		$row = mysql_fetch_array(mysql_query($sql));
		$pid = $row['id'];
		$ROIFolder = $row['folder'];
		$ROIDirectory = $URL . $row['folder'];
		$image_height = $row['image_height'];
		$image_width = $row['image_width'];
	}
	catch (Exception $e){}
}
//	IF pid not specified and user has not worked on a specific project
// the first project they are a member of.
if($ROIFolder == "") 
{
	$sql = "SELECT `roi_projects`.`image_height`,`roi_projects`.`image_width`,`roi_projects`.`id`,`roi_projects`.`name`,`roi_projects`.`folder`,`roi_projects_members`.`roi_project_id` FROM `roi_projects`,`roi_projects_members` WHERE `roi_projects`.`id`=`roi_projects_members`.`roi_project_id` AND `roi_projects_members`.`project_viewable`='0' AND `user_id`='".$_SESSION['Id']."'";
	//try
	//{
		$row = mysql_fetch_array(mysql_query($sql));
		$pid = $row['id'];
		$ROIFolder = $row['folder'];
		$ROIDirectory = $URL . $row['folder'];
		$image_height = $row['image_height'];
		$image_width = $row['image_width'];
	//}
	//catch (Exception $e){}
}


if(isset($_SESSION['Id']) == false || $ROIDirectory == "")
{
	//print_r($_SESSION);
	header('Location: '. $LogOut);
}

$sql = "SELECT `reviewable` FROM `cialab`.`roi_projects` WHERE `id` = '".$pid."';";
$row = mysql_fetch_array(mysql_query($sql));
$ProjectType = $row["reviewable"];

////////////////////////////////////////////////////////////////////////////////////
// Currently the project Type is an interger value of 0,1,2,3,4,5
// 
// Values:
// 0 :	Zero stands for the normal reader only project, where the reader marks the
//		images and does not see any input from the computer.
//
// 1 :	One stands for the Reader / Computer markings, where the reader marks the 
//		the images and then the computer displays markings and the reader has the
//		option of marking on top of the computers markings or not marking with the
//		computer.
//
// 2 :	Two stands for Computer / Reader / Computer. This is where the computer marks
//		marks the images first then the reader adds more markings to the image or removes
//		the computer markings, or  both.
//
// 3 :	Three stands for viewing the images only. This does not allow users to mark on the 
//		images or make any modifications.
//
// 4 :	This project type is for first estimating the percentage of the cells that are
//		positive and then clicking on the cells. 
//		
// 5 :	This project type is for full slide images
//
//////////////////////////////////////////////////////////////////////////////////////

if($ProjectType != 5)
{
	header("Location:".$MainIndex);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html lang="en" >

 <head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
  <meta http-equiv="X-UA-Compatible" content="IE=9" />

  <link rel="stylesheet" type="text/css" media="all" href="css/iip.css" />
  
<!--[if lt IE 9]>
  <link rel="stylesheet" type="text/css" media="all" href="css/ie.css" />
<![endif]-->

<!--
  <link rel="shortcut icon" href="images/iip-favicon.png" />
  <link rel="apple-touch-icon" href="images/iip.png" />
-->

  <title>Full Slide Annotation</title>
  <script type="text/javascript" src="javascript/mootools-core-1.3.2-full-nocompat.js"></script>
  <script type="text/javascript" src="javascript/mootools-more-1.3.2.1.js"></script>
  <script type="text/javascript" src="javascript/protocols.js"></script>
  <script type="text/javascript" src="javascript/iipmooviewer-2.0.js"></script>
  <script src="scripts/easeljs-0.6.0.min.js"></script>

<!--[if lt IE 7]>
  <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE7.js">IE7_PNG_SUFFIX = ".png";</script>
<![endif]-->


  <script type="text/javascript">
    // The iipsrv server path (/fcgi-bin/iipsrv.fcgi by default)
    var server = '/fcgi-bin/iipsrv.fcgi';

    // The *full* image path on the server. This path does *not* need to be in the web
    // server root directory. On Windows, use Unix style forward slash paths without
    // the "c:" prefix
    //var images = '/var/www/slide_images/demo.jp2';
	
	var fullimages = [<?php 
			$sendAlert = false;
			if(file_exists($ROIFolder) == true)
			{
				$results = array();
				$handler = opendir($ROIFolder);
				$firstFile = true;
				$ImageName = "";
				$files = array();
				$x = 0;
				while ($file = readdir($handler)) 
				{
					if(!($file == "." || $file == ".."))
					{
						array_push($files,'"'.$file.'"');
						$x++;
					}
				}
				echo implode(",",$files);
				closedir($handler);
			}
			else
			{
			echo "";
			$sendAlert = true;
			}
			?>];
	var slidesLoc = '<?php echo $ROIFolder; ?>';
	var movementCounter = 0;
	var actionArray = new Array();
	var connected = false;
	var projID = <?php echo $pid ?>;
	var CurrentImage = "";
	var CurrentIndex = 0;
	var currentServer = "<?php if(isset($MarkerServer)){echo $MarkerServer;} ?>";
	var userid = <?php echo $_SESSION['Id'] ?>;
	var colorsarray = [<?php $sql = "SELECT * FROM `cialab`.`dataset_18` WHERE `projectid`='".$pid."'"; 
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$colormenus = array();
		array_push($colormenus,'\''.$row['color'].'\'');
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($colormenus,'\''.$row['color'].'\'');
		}
		echo implode(",",$colormenus);
		
		?>];
	var marksnamearray = [<?php $sql = "SELECT * FROM `cialab`.`dataset_18` WHERE `projectid`='".$pid."'"; 
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$colormenus = array();
		array_push($colormenus,'\''.$row['name'].'\'');
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($colormenus,'\''.$row['name'].'\'');
		}
		echo implode(",",$colormenus);
		
		?>];
	var markersidarray = [<?php $sql = "SELECT * FROM `cialab`.`dataset_18` WHERE `projectid`='".$pid."'"; 
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$colormenus = array();
		array_push($colormenus,'\''.$row['id'].'\'');
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($colormenus,'\''.$row['id'].'\'');
		}
		echo implode(",",$colormenus);
		
		?>];
	
	function baseLoadJSON(url,action,attempt) 
	{
		var d=new Date();
		setTimeout("CheckMovement("+movementCounter+");",10000); 
		actionArray[movementCounter] = {"action":action,"url":url,"move":movementCounter,"attempt":attempt,"complete":false,"errors":"","date":d.toUTCString()};
		var headID = document.getElementsByTagName("head")[0];         
		var newScript = document.createElement('script');
		  newScript.type = 'text/javascript';
		  newScript.src = url+"&move="+movementCounter;
		headID.appendChild(newScript);
		movementCounter = movementCounter +1;
	}

	function CheckMovement(Id)
	{
		//The number of times the system attempts to submit a query.
		//This includes markings, img trackers, review marks and so on...
		maxAttemptCount = 3;
		
		//alert(actionArray[Id].complete);
		if(actionArray[Id].complete == false && actionArray[Id].attempt < maxAttemptCount)
		{
			//Increment attempt counter
			actionArray[Id].attempt = actionArray[Id].attempt + 1;
			
			alert("Attempt: " + actionArray[Id].attempt + "\n" + "URL: " + actionArray[Id].url);
			
			//Resubmit query again
			baseLoadJSON(actionArray[Id].url,actionArray[Id].action,actionArray[Id].attempt);
		}
		else if(actionArray[Id].complete == false && actionArray[Id].attempt >= maxAttemptCount)
		{
			alert('The program can not connect to server. Refresh the page and try again. If this continues log off and email schmidt.553@osu.edu');
			//DialogBox("There was a network connection error.<br><br>Sorry for the inconvenience.<br><br> If you could email us the Error Report to schmidt.553@osu.edu we can fix any errors faster.<br><br>Error Report<br><textarea style='width:350px;height:100px;margin-left:25px;margin-right:25px;'>"+dump(actionArray,0)+"</textarea><br><br>We recomend not using this application until we can resolve the error, as data might not be saved.<br><br>Thank You!");
		}
	}
	
	function loadJSON(url,action) 
	{
		baseLoadJSON(url,action,0);
	}
	
	//This handles recieving the JSON requests
	function processJSON(data)
	{
		connected = true;
		if(data.move != "" && data.status == true)
		{
			actionArray[data.move].complete = true;
		}
		
		if(data.name == "getImgTracking")
		{
			var index = 0;
			for(x=0; x<fullimages.length; x++)
			{
				if(fullimages[x]==data.image)
				{
					index = x;
				}
			}
			
			iipmooviewer.options.startIndex = index;
			iipmooviewer.setUp(iipmooviewer.mainID,iipmooviewer.options);
			//loadImage(data.image);
		}
		else if(data.name == "getImgMarks")
		{
			iipmooviewer.addJSONMarkers(data.marks);
			//AddMarkersFromJSON(data.marks,data.image,defaultMarkerColor, false,userid,102,1);		
		}
		else if(data.name == "getUsersImgMarks")
		{
			/*
			markerColor = markerColors.splice(0,1);
			markerHolder.splice(0,0,{"name":data.user,"color":markerColor});
			AddMarkersFromJSON(data.marks,data.image,markerColor, true,data.user,101,1);
			*/
		}
		else if(data.name == "getReviewImgMarks")
		{
			/*
			//printOutDataMarkers(data.marks);
			markerColor = markerColors.splice(0,1);
			markerHolder.splice(0,0,({"name":"review_set","color":markerColor}));
			$("#s3").val("review_set").selected = true;
			$("#s3").dropdownchecklist("refresh");
			AddMarkersFromJSON(data.marks,data.image,markerColor, true,"review_set",100,.6);
			*/
		}
		else if(data.name == "getEditableReviewImgMarks")
		{
			/*
			//printOutDataMarkers(data.marks);
			markerColor = markerColors.splice(0,1);
			markerHolder.splice(0,0,({"name":"editable_review_set","color":markerColor}));
			AddMarkersFromJSON(data.marks,data.image,markerColor, false,"editable_review_set",100,1,function(event){removeReviewMarker(event,$(this));});
			*/
		}
		else if(data.name == "imgTackingUpdated")
		{

		}
		else if(data.name == "resetReviewSet")
		{
			/*
			//alert('reset review set');
			var i = 0;
			while(markerHolder.length > i)
			{
				if(markerHolder[i].name == "editable_review_set")
				{
					markerColors.splice(0,0,markerHolder[0].color);
					markerHolder.splice(i,1);
				}
				i++;
			}
			
			loadEditableReviewMarkings();
			*/
		}
		else if(data.name == "removeReviewMarker")
		{
			//alert('removed');
		}
		else if(data.name == "imgTackingSet")
		{

		}
		else if(data.name == "addMarker")
		{
		
		}
		else if(data.name == "removeMarker")
		{

		}
		else if(data.name == "removeAllMarkers")
		{

		}
		else if(data.name == "getAllImgMarks")
		{
		}
		else if(data.name == "getDropDownData")
		{
			/*
			if(data.status = 'true' && data.dropDownData!= '')
			{
				//Dropdown data already set
				//set the selected value and disable the select
				$('#data_selection').val(data.dropDownData).attr('selected',true);
				$('#data_selection').attr('disabled', 'disabled');
				
				//Setup the action for clicking on the image
				if(projectType == 4)
				{	
					disablePreview();
					$('#pointer_div').unbind();
					$('#pointer_div').bind('click', function(event){point_it(event,$(this));});
				}
			}
			else if(data.status = 'true' && data.dropDownData== '')
			{
				//Drop Down Data not set yet.
				//set the default select value and enable the select
				$('#data_selection').val('').attr('selected',true);
				$('#data_selection').removeAttr('disabled');
				
				//Setup the action for clicking on the image
				if(projectType == 4)
				{	
					//Setup a popup to remind the user that they must select the drop down data first
					$('#pointer_div').unbind();
					$('#pointer_div').bind('click', function(event){DialogBox("Please select the "+dataSetText+" first",false,function(){},function(){});});
				}
			}
			else
			{
				//Do nothing allow the user to select the correct Drop Down Data
			}
			*/
		}
		else if(data.name == "getUsersForImage")
		{
			/*
			if(data.users.length != 0)
			{
				document.getElementById('user_review').innerHTML = DropDownCode;
				var selectObj = document.getElementById('s3');
				for(x=0;x<data.users.length;x++)
				{
					if(data.users[x].id != userid)
					{
						$('#s3').append($("<option></option>").attr("value",data.users[x].id).text(data.users[x].firstName + " " + data.users[x].lastName));
					}
				}
				loadDropDown();
			}
			*/
		}
		else if(data.name == "getColorMenu")
		{
			//iipmooviewer.addJSONMarkers(data.marks);
			//AddMarkersFromJSON(data.marks,data.image,defaultMarkerColor, false,userid,102,1);		
		}
		else if(data.name == 'updatecolor')
		{
		//alert('here in index.php!');
			//$sql = "UPDATE `cbdata` SET `colorval`='".$colorValueId."' WHERE `userid`='".$userID."' AND `review_mark`='0' AND `image`='".$pic."' AND `x`='".$x."' AND `y`='".$y."' AND `project_id`='".$pid."';";
			//$result = mysql_query($sql);
			
			//header('Content-Type: application/json');
			//header('Content-Disposition: attachment; filename=json.run');
			//echo 'processJSON({"name":"imgTackingUpdated","move":"'.$move.'","status":true,"image":"'.$pic.'"})';
		}
		else
		{
		}	
	}
		
	
	var fullimage = '/var/www/slide_images/DEFAULT.jp2';
	//var fullimage = '/var/www/slide_images/TEST/BOB.jp2';

    // Copyright or information message
    var credit = '';

    // Create our viewer object
    // See documentation for more details of options
    var iipmooviewer = new IIPMooViewer( "viewer", {
		markercorsids: markersidarray,
		markercors: colorsarray, // marker colors array
		markernames: marksnamearray, // marker names array
		htmlviewer: 'htmlviewer',
		image: fullimage,
		server: server,
		showNavWindow: true,
		showNavButtons: true,
		winResize: true,
		protocol: 'iip',
		slidesLocation: slidesLoc,
		slides: fullimages,
		startIndex: 0,
		userID: userid,
		projectID: projID,
		scale:true,
    });

  </script>

  <style type="text/css">
    body{ height: 100%; margin:0px; padding:0px;}
    div#viewer{ width: 100%; height: 100%; margin:0px; padding:0px;}
	
  </style>

 </head>
	 <body>
		<div id="viewer"></div>
	</body>
</html>
