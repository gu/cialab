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
	$sql = "SELECT `roi_projects`.`image_height`,`roi_projects`.`image_width`,`roi_projects`.`id`,`roi_projects`.`name`,`roi_projects`.`folder`,`roi_projects_members`.`roi_project_id`,`roi_projects`.`reverseorder` FROM `roi_projects`,`roi_projects_members` WHERE `roi_projects`.`id`=`roi_projects_members`.`roi_project_id` AND `roi_projects_members`.`project_viewable` = '1' AND `user_id`='".$_SESSION['Id']."' AND `roi_projects_members`.`roi_project_id`='".mysql_prep($_GET['pid'])."';"; 
	try
	{
		$row = mysql_fetch_array(mysql_query($sql));
		$pid = $row['id'];
		$ROIFolder = $row['folder'];
		$ROIDirectory = $URL . $row['folder'];
		$image_height = $row['image_height'];
		$image_width = $row['image_width'];
		$reverse_order = $row['reverseorder'];
	}
	catch (Exception $e){}
}
//try to find the last project they were working on if pid not specified.
if($ROIFolder == "")
{
	$sql = "SELECT `cialab`.`roi_projects`.`image_height`,`cialab`.`roi_projects`.`reverseorder`,`cialab`.`roi_projects`.`image_width`,`cialab`.`roi_projects`.`id`,`cialab`.`roi_projects`.`name`,`cialab`.`roi_projects`.`folder`,`cialab`.`roi_projects_members`.`roi_project_id` FROM `cialab`.`roi_projects`,`cialab`.`roi_projects_members`, (SELECT `cbmarker`.`imgtracking`.* FROM `cbmarker`.`imgtracking`,(SELECT MAX(p.date) AS maxdate FROM `cbmarker`.`imgtracking`,(SELECT * FROM `cbmarker`.`imgtracking` WHERE `userid`='".$_SESSION['Id']."') AS p) AS x WHERE `x`.`maxdate` = `cbmarker`.`imgtracking`.`date` AND `userid` = '".$_SESSION['Id']."') AS y WHERE `cialab`.`roi_projects`.`id`=`cialab`.`roi_projects_members`.`roi_project_id` AND `cialab`.`roi_projects_members`.`project_viewable`='1' AND `user_id`=y.userid AND `cialab`.`roi_projects_members`.`roi_project_id`=y.project_id;";
	try
	{
		$row = mysql_fetch_array(mysql_query($sql));
		$pid = $row['id'];
		$ROIFolder = $row['folder'];
		$ROIDirectory = $URL . $row['folder'];
		$image_height = $row['image_height'];
		$image_width = $row['image_width'];
		$reverse_order = $row['reverseorder'];
	}
	catch (Exception $e){}
}
//	IF pid not specified and user has not worked on a specific project
// the first project they are a member of.
if($ROIFolder == "") 
{
	$sql = "SELECT `roi_projects`.`image_height`,`roi_projects`.`reverseorder`,`roi_projects`.`image_width`,`roi_projects`.`id`,`roi_projects`.`name`,`roi_projects`.`folder`,`roi_projects_members`.`roi_project_id` FROM `roi_projects`,`roi_projects_members` WHERE `roi_projects`.`id`=`roi_projects_members`.`roi_project_id` AND `roi_projects_members`.`project_viewable`='0' AND `user_id`='".$_SESSION['Id']."'";
	//try
	//{
		$row = mysql_fetch_array(mysql_query($sql));
		$pid = $row['id'];
		$ROIFolder = $row['folder'];
		$ROIDirectory = $URL . $row['folder'];
		$image_height = $row['image_height'];
		$image_width = $row['image_width'];
		$reverse_order = $row['reverseorder'];
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
//		positive and then clicking on the cells. (IHC Dropdown)
//		
// 5 :	This project type if for full slide images
//
// 6 :  This project type is for the annotion drawer
//
// 7 :  This project type is used to estimate the percentage of positive cells in an image
//
// 8 :  This project type is used to count the number of positive and negative cells in an image
//////////////////////////////////////////////////////////////////////////////////////

//Send them to the full slide image if their project type is 5 
if($ProjectType == 5)
{
	header("Location:"."http://140.254.126.245/fullslide/index.php?pid=" . $pid);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML style='height:100%;'>

<HEAD>
	<TITLE>CellMarker</TITLE>
	
	<!-- Load jQuery Styles -->
	<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.css">
	<link rel="stylesheet" type="text/css" href="css/ui.css">

	<!-- Load Style Sheet for Webpage -->
	<link rel="stylesheet" type="text/css" href="css/cbmarker.css">

	<!-- Load jQuery Functions -->
	<!--script language="Javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script-->
	<script language="Javascript" src="js/jquery-1.txt"></script>
	<script language="Javascript" src="js/jquery-ui-1.txt"></script>
	<script language="Javascript" src="js/ui.txt"></script>
	
	<!--OpenGL stuff-->
	<script id="vertex" type="x-shader">
		attribute vec2 aVertexPosition;
		
		void main()
		{
			gl_Position = vec4(aVertexPosition, 0.0, 1.0);
		}
	</script>
	<script id="fragment" type="x-shader">
		#ifdef GL_ES
		precision highp float;
		#endif
		
		uniform vec4 uColor;
		
		void main()
		{
			gl_FragColor = uColor;
		}
	</script>
	
	<!--Other Libraries-->
	<script src="js/kinetic-v5.0.2.js"></script>
	<script language="Javascript" src="js/cbmarker/AnnotationDrawing.js"></script>
	
	<!-- Load cbMarker Functions -->
	<script language="Javascript" src="js/cbmarker/globalFunctions.js"></script>
	<script language="Javascript" src="js/cbmarker/uiFunctions.js"></script>
	<!--Molly edited this so that it would load. There is an issue with the cache clearing on some computers so the best way to fix was to rename the file. This file adds the skip functionality in the percentage projects and removes unnecessary features for that project.-->
	<script language="Javascript" src="js/cbmarker/coreFunctions.js"></script>

	<script language="JavaScript">
	//Declare Global JavaScript Variables (Clean This up in future versions)
	var reviewing = false;
	var markerColors = ["#FFFF00","#FF00FF","#00FFFF","#FF6F00","#FFFCCC","#FFCC00","#C0C0C0","#009000","#F99CF9"];
	var markerHolder = new Array();
	var actionArray = new Array();
	var pid = <?php echo $pid; ?>;
	var userid = <?php echo $_SESSION['Id'] ?>;
	var debug = <?php if(isset($_GET['debug']) == true && $_GET['debug'] == 1){echo "true";}else{echo "false";} ?>;
	var currentServer = "<?php if(isset($MarkerServer)){echo $MarkerServer;} ?>";
	var roiDirectory = "<?php if(isset($ROIDirectory)){echo $ROIDirectory;} ?>";
	var localDirectory = "<?php if(isset($LocalDirectory)){echo $LocalDirectory;} ?>";
	var projectType = <?php if(isset($ProjectType)){echo $ProjectType;} ?>;
	var connected = false;
	var connectID = 1;
	var dataSetText = '';
	var dataSetName = '';
	var markerArray = new Array();
	var movementCounter = 0;
	var point = new Array();
	var defaultMarkerColor = "#5EFF00";
	var CurrentImage = "";
	var CurrentIndex = 0;
	var DropDownCode = "";
	var images = [<?php 
			$sendAlert = false;
			if(file_exists("..".$ROIFolder) == true)
			{
				$results = array();
				$handler = opendir("..".$ROIFolder);
				$firstFile = true;
				$ImageName = "";
				$files = array();
				$x = 0;
				while ($file = readdir($handler)) 
				{
					if($file != ".")
					{
						if($file != "..")
						{
							array_push($files,'"'.$file.'"');
							$x++;
						}
					}
				}
				//New by Freddy
				sort($files);
				//End new
				echo implode(",",$files);
				closedir($handler);
			}
			else
			{
			echo "";
			$sendAlert = true;
			}
			?>];
	var reversevar = [<?php echo $reverse_order; ?>];
	var offSetLeft = 0;
	var offSetTop = 0;
	var IEVersion = 0;
	var posLeftOffset = 0;
	var posTopOffset = 0;
	var currentServerImage = "none";
	var drawingColor="red";
	var currentColor="red"; //if the drawingColor is erase
	var newScriptArray = new Array();
	var newScriptCounter = 0;
	var currTool = "pencil";
	var lineCoords = new Array();
	var lineColors = new Array();
	var rectCoords = new Array();
	var rectColors = new Array();
	var markerCoords = new Array();
	var markerColors = new Array();
	var catArray = new Array();
	var isSaved = true;
	var isSaving = false;
	var isValidPercentages = false;
	var arrayOfM1 = new Array();
	var arrayOfM2 = new Array();
	var posCount = 0;
	var negCount = 0;
	var RvRCounts = new Array();
	var acneCounts = new Array();
	//Riffer (will be erased later)
	var tempMarkersX = new Array();
	var tempMarkersY = new Array();
	var tempFileName;
	function generatePoints()
	{
	<?php
		$dir = "C:/UniServerZ/www/cbmarker/js/cbmarker/test/";
		$files = scandir($dir);
		for($i = 0; $i < count($files); $i++)
		{
			if($files[$i] != "." && $files[$i] != "..")
			{
				//get rid of .txt at the end
				$fileName = substr($files[$i], 0, strlen($files[$i]) - 4);
				echo "tempFileName = '". $fileName ."';";
				$f = fopen($dir . $files[$i], 'r');
				
				$xCoordinates = fgets($f);
				$yCoordinates = fgets($f);
				
				//for x coordinates
				$number = "";
				for($j = 0; $j <= strlen(trim($xCoordinates)); $j++)
				{
					if($xCoordinates[$j] != ',' && $j != strlen(trim($xCoordinates)))
					{
						$number = $number . $xCoordinates[$j];
					}
					else
					{					
						echo "tempMarkersX.push(". $number .");";
						$number = "";
					}
				}
				
				//for y coordinates
				$number = "";
				for($j = 0; $j <= strlen(trim($yCoordinates)); $j++)
				{
					if($yCoordinates[$j] != ',' && $j != strlen(trim($yCoordinates)))
					{
						$number = $number . $yCoordinates[$j];
					}
					else
					{	
						echo "tempMarkersY.push(". $number .");";
						$number = "";
					}
				}
				
				fclose($f);
				echo "tempFunction();";
			}
		}
	?>
	}
	
	if (reversevar[0] == 1)
	{
			images.reverse();
	}

	<?php
		
		if ($sendAlert == true)
		{
			echo "alert('Error: Can Not Locate Images. Please contact schmidt.553@osu.edu with this error.');";
		}
	?>
	
	//When document loads, setup CBMarker
	$(document).ready
	(
		function() 
		{
			<?php
			if ($sendAlert == false)
			{
				echo "setupCBMarker();";
			}
			?>
		}
	);

	</script>
</head>

<div id='information' style='text-align:center; margin-left:auto; margin-right:auto;'></div>

<body style='height:100%;width:100%;'>

<div id='blackout' style='display:none;position:absolute;height:200%;z-index:1000;width:100%;left:0px;top:0px;opacity:0.4;filter:alpha(opacity=40);background-color:black;'></div>

<div id="outer">
	<div id="contain-all">
		<div class="inner">
		
		<?php
		if($ProjectType == 4)
		{
			echo "
			<div id='previewDiv' style='margin-left:auto;margin-right:auto;width:50em;text-align:center;'>
			<b>This is a Whole Image Preview</b>
			<img id='previewImage' width='100%' src='' border=0>
			</div>
			";
		}

		//Checks to make sure dataSetName is not grade
		$pid = mysql_prep($pid);
		$sql = "SELECT `dropdown_dataset` FROM `roi_projects` WHERE `id` = '$pid'";
		$row  = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
		$dataSetName = mysql_prep($row['dropdown_dataset']);
		
		//if dataset is grade
		if($dataSetName == "dataset_2"){
			echo 
			"<div id='pointer_div' style='margin-left:auto;margin-right:auto;width:1100px;text-align:center;'>
			<b>This is a Whole Image Preview</b>
			<img id='previewImage' width='100%' src='' border=0>
			</div>
			";
		}
		//temporary change by Riffer
		else if($ProjectType != 6 && $ProjectType != 7 && $ProjectType != 11){
			echo "<div id='pointer_div' style = \"background-image:url('');";
			echo "width:" . $image_width . "px !important;";
			echo "height:" . $image_height . "px !important;";
			echo "float:left;clear:both;background-repeat:no-repeat;\"></div>;";
		}
		else{ //$ProjectType is annotation drawing
			echo "<div id='pointer_div'></div>";
		}
		?>
	
		<div id='PrintArea' style='float:left;clear:both;'></div>
		<div id='messageID' style='display:none;'></div>
		
		</div>
	</div>
</div>

<div id="info-bar" style='z-index:40000;'>
	<div id="infobar-inner" style='z-index:40001;'>
	</div>
</div>

<div id="top-bar">
	<div id="topbar-inner">
		<div id='controls'  style='float:left; clear:both; overflow:hidden; height:50px; padding: 0px 0px;'>
			<div class='buttons' style='display:inline;vertical-align:middle;'>
				<input type='hidden' value='Print Array' onclick='printMarkerPoints()'>
				<?php if($ProjectType != 6 && $ProjectType != 7 && $ProjectType != 8 && $ProjectType != 9 && $ProjectType != 10 && $ProjectType != 11)
				{
				?>
					<FORM id='LogOut' name='LogOut' action='<?php echo $LogOut; ?>' method='post' style='display:inline;'>
					<input type='submit' value='Logout'>
					</FORM>
				<?php
				}
				?>
				<?php 
				//Checks to make sure dataSetName is not grade
				$pid = mysql_prep($pid);
				$sql = "SELECT `dropdown_dataset` FROM `roi_projects` WHERE `id` = '$pid'";
				$row  = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
				$dataSetName = mysql_prep($row['dropdown_dataset']);
		
				if($ProjectType != 3 && $dataSetName != "dataset_2" && $ProjectType != 6 && $ProjectType != 7 && $ProjectType != 11)
				{
					ECHO "
					<input type='button' value='Clear All Points' onclick='RemoveAllMarkers()'>
					";
				}
				?>
				<!--Eric-->
				<input type='button' value='|<<' id='backward' onclick='JavaScript:backward()'>
				<input type='button' value='>>|' id='forward' onclick='JavaScript:forward()'>

				<?php
				if($ProjectType == 3)
				{
					ECHO "
					<div id='centroblasts' style='height: 27px;line-height: 27px;margin: 10px 1px;display:none;font-family:Verdana;padding-right:10px;'>0</div>
					";
				}
				else
				{
					//Eric added project 11 for special annotation drawer
					if($ProjectType == 4 || $ProjectType == 6 || $ProjectType == 7 || $ProjectType == 8 || $ProjectType == 9 || $ProjectType == 10 || $ProjectType == 11){
						//The project doesn't work correctly without this div, I still haven't figured out why 
						//but I don't want "Count:" to display so this is a temporary hack.
						echo "<div id='centroblasts' style='height: 27px;line-height: 27px;margin: 10px 1px;display:inline;font-family:Verdana;padding-right:10px;'>0</div>";
					}
					else{
						echo "
						<b>Count:</b> <div id='centroblasts' style='height: 27px;line-height: 27px;margin: 10px 1px;display:inline;font-family:Verdana;padding-right:10px;'>0</div>
						";
					}
				}
				?>

				<b>Image:</b> <div id='imageNumber' style='height: 27px;line-height: 27px;margin: 10px 1px;display:inline;font-family:Verdana;padding-right:20px;'>0/0</div>

				<?php
				if($ProjectType == 2)
				{
					echo "<input type='button' value='Reset Review Set' id='resetReview' onClick='resetReviewSet();'>";
				}
				?>
				
			<?php
			//This selects the projects that the user is apart of. IHC Dropdown
			if($ProjectType == 4)
			{				
				$pid = mysql_prep($pid);
				$sql = "SELECT `dropdown_dataset` FROM `roi_projects` WHERE `id` = '$pid'";
				$row  = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
				$dataSetName = mysql_prep($row['dropdown_dataset']);
				
				if($dataSetName != "")
				{
					$sql = "SELECT * FROM `dataset_0` WHERE `value`='$dataSetName'";
					$row  = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
					$dataSetText = mysql_prep($row['name']);
				
				echo "	
					<SELECT id='data_selection' style='Width: 200px;z-index:10;' disabled='disabled' onChange=\"dataSelected(true);\">
					<OPTION Value=''>Select $dataSetText</OPTION>";
					$sql = "SELECT * FROM `$dataSetName`";
					$result = mysql_query($sql);
					while($row = mysql_fetch_array($result, MYSQL_ASSOC))
					{
							echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
					}
					echo "</SELECT>";
					
					//This is to pass the dataset text name to Javascript
					echo "
						<script language='javascript'>
						dataSetText = '$dataSetText';
						dataSetName = '$dataSetName';
						</script>
						";
				}
				
				if($dataSetName == "dataset_2"){
					echo "<SELECT id='data_selection_2' style='Width: 200px;z-index:10;' onChange=\"dataSelected(false);\">
					<OPTION Value='EMPTY'>Select Secondary Grade</OPTION><OPTION Value='2'>II</OPTION>
					<OPTION Value='3'>III</OPTION><OPTION Value='4'>IV</OPTION><OPTION Value='5'>V</OPTION><OPTION Value='0'>PASS</OPTION></SELECT>";
				}
			}
			?>			
			
			<?php
			//This selects the projects that the user is apart of. dataset 2 and 17 have their own setups (2 - primary grade - above, 17 - percentile - below)
			// FROM FREDDY: Added project type 4 to those that don't use the project_selection drop down.
			if($dataSetName != "dataset_2" && $dataSetName != "dataset_17" && $dataSetName != "dataset_19" && $ProjectType != 4 && $ProjectType != 6 && $ProjectType != 7 && $ProjectType != 8 && $ProjectType != 9 && $ProjectType != 10 && $ProjectType != 11){
				$sql = "SELECT COUNT(roi_projects_members.user_id) AS count FROM `roi_projects_members` WHERE `roi_projects_members`.`project_viewable`='1' AND `user_id` = '".$_SESSION['Id']."'";
				$row  = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
				if($row['count'] > 1)
				{
					echo "	<SELECT id='project_selection' style='Width: 200px;z-index:10;' onChange=\"window.location='".$MarkerIndex."?pid='+document.getElementById('project_selection').options[document.getElementById('project_selection').selectedIndex].value;\">
					<OPTION Value=''>Select Project</OPTION>";
					$sql = "SELECT `roi_projects`.`id`,`roi_projects`.`name`,`roi_projects`.`folder`,`roi_projects_members`.`roi_project_id` FROM `roi_projects`,`roi_projects_members` WHERE `roi_projects`.`id`=`roi_projects_members`.`roi_project_id` AND `roi_projects_members`.`project_viewable`='1' AND `user_id`='".$_SESSION['Id']."';";
					$result = mysql_query($sql);
					while($row = mysql_fetch_array($result, MYSQL_ASSOC))
					{
							echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
					}
					echo "</SELECT>";
				}
				echo "<input type='text' value='' id='imageText' style='font-size:11px; width:40px; cursor: text; margin-right:0px; border-radius: 3px 0px 0px 3px; height: 24px; border-right:0px; padding-top:1px;'><input type='button' value='Jump to Image' id='goToImage' onclick='goToImage()' style='margin-left:0px; border-radius: 0px 3px 3px 0px;'>";
				echo "<input type='button' value='Help' id='help' onclick=\"DialogBox('<H2>Directions:</H2><b>How to Annotate:</b><br> To add markings to the images simply click on the image and where ever you click a green dot will appear. This is automatically saved in the background.<br><br><b>How to Remove a Mark:</b><br>To remove a mark hover over a previous marking and the marker should turn red. When it turns red click on the mark and it will be removed.<br><br><b>How to Change Images:</b><br>To move ahead to the next image click the >>| button. To move backwards to the previous image click the |<< button. Sometimes the image may take a short while to load.<br><br><b>Contact Us:</b><Br>You can contact us at schmidt.553@osu.edu with any other questions.')\">";
				echo "<input type='button' value='Filter' id='filterBtn' onClick=\"Filter();\">";
			}
			
			?>
			<?php
			if($ProjectType == 6)
			{		
				$v2url = "http://".$_SERVER["SERVER_NAME"]."/cbmarkerv2/index.php?".$pid.",".$_SESSION['Id'];
				//header($v2url);
				//exit();
				die('<script type="text/javascript">window.location=\''.$v2url.'\';</script>');
				//echo $_SERVER["SERVER_NAME"];
			?>
				<!--Jump to Image
				<input type='text' value='' id='imageText' style='font-size:11px; width:40px; 
				cursor: text; margin-right:0px; border-radius: 3px 0px 0px 3px; height: 24px; border-right:0px; 
				padding-top:1px;'>
				<input type='button' value='Jump to Image' id='goToImage' onclick='goToImage()' 
					style='margin-left:0px; border-radius: 0px 3px 3px 0px;'>
				<!--Tools
				<img src='js/cbmarker/images/pencil-icon.png' id='pencil-icon'
					style='width:36px; height:36px; margin: 0px 5px 0px 20px; border: 2px solid #996633' 
					onclick='changeTool("pencil")' />	   
				<img src='js/cbmarker/images/marker-icon.png' id='marker-icon'
					style='width:36px; height:36px; margin: 0px 5px 0px'
					onclick='changeTool("marker")' />  	   	   
				<img src='js/cbmarker/images/rect-icon.png' id='rect-icon'
					style='width:36px; height:36px; margin: 0px 5px 0px'
					onclick='changeTool("rect")' />   
				<!--img src='js/cbmarker/images/dragging-icon.png' id='dragging-icon'
					style='width:36px; height:36px; margin: 0px 5px 0px' 
					onclick='changeTool("dragging")' /
				<img src='js/cbmarker/images/eraser-icon.png' id='eraser-icon'
					style='width:36px; height:36px; margin: 0px 5px 0px' 
					onclick='changeTool("eraser")' />
				
				<!--Colors
				<img src='js/cbmarker/images/red-icon.png' id='red-icon'
				   style='margin:0px 5px 0px; border: 2px solid #FF99CC'
				   onclick='changeColor("red")' />
				<img src='js/cbmarker/images/green-icon.png' id='green-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("green")' />
				<img src='js/cbmarker/images/blue-icon.png' id='blue-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("blue")' />
				<img src='js/cbmarker/images/black-icon.png' id='black-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("black")' />
				  
				<!--Additional Buttons
				<a download href='XML_Data.xml'><input type='button' value='XML Data' onmouseover='loadXML()'></a>
				<input type='button' value='Save Annotations' onclick='saveAnnotations("button")'>
				<input type='button' value='Clear All Annotations' onclick='clearAllAnnotations()'>
				<input type='button' value='Undo' onclick='undoOrRedoAnnotation("undo")'>
				<input type='button' value='Redo' onclick='undoOrRedoAnnotation("redo")'>
				
				<!--Logout Button
				<FORM id='LogOut' name='LogOut' action='<?php //echo $LogOut; ?>' method='post' style='display:inline;'>
				<input type='submit' value='Logout'>
				</FORM>	-->		
			<?php
			}
			
			//Eric added for special annotation drawer 
			if($ProjectType == 11)
			{		
				$v2url = "http://".$_SERVER["SERVER_NAME"]."/cbmarkerv2/index.php?pid=".$pid.",".$_SESSION['Id'];
				//header($v2url);
				//exit();
				die('<script type="text/javascript">window.location=\''.$v2url.'\';</script>');
				
			?>
				<!--Jump to Image
				<input type='text' value='' id='imageText' style='font-size:11px; width:40px; 
				cursor: text; margin-right:0px; border-radius: 3px 0px 0px 3px; height: 24px; border-right:0px; 
				padding-top:1px;'>
				<input type='button' value='Jump to Image' id='goToImage' onclick='goToImage()' 
					style='margin-left:0px; border-radius: 0px 3px 3px 0px;'>-->
				<!--Tools
				<img src='js/cbmarker/images/pencil-icon.png' id='pencil-icon'
					style='width:36px; height:36px; margin: 0px 5px 0px 20px; border: 2px solid #996633' 
					onclick='changeTool("pencil")' />	   
				<img src='js/cbmarker/images/marker-icon.png' id='marker-icon'
					style='width:36px; height:36px; margin: 0px 5px 0px'
					onclick='changeTool("marker")' />  	   	   
				<img src='js/cbmarker/images/rect-icon.png' id='rect-icon'
					style='width:36px; height:36px; margin: 0px 5px 0px'
					onclick='changeTool("rect")' />   
				<!--img src='js/cbmarker/images/dragging-icon.png' id='dragging-icon'
					style='width:36px; height:36px; margin: 0px 5px 0px' 
					onclick='changeTool("dragging")' /
				<img src='js/cbmarker/images/eraser-icon.png' id='eraser-icon'
					style='width:36px; height:36px; margin: 0px 5px 0px' 
					onclick='changeTool("eraser")' />
				
				<!--Colors--
				<img src='js/cbmarker/images/red-icon.png' id='red-icon'
				   style='margin:0px 5px 0px; border: 2px solid #FF99CC'
				   onclick='changeColor("red")'>Tumor</img>
				<img src='js/cbmarker/images/orange-icon.png' id='orange-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("orange")'>Stromal</img>
				<img src='js/cbmarker/images/green-icon.png' id='green-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("green")'>Lyp.</img>
				  
				<!--Additional Buttons-->
				<!--<a download href='XML_Data.xml'><input type='button' value='XML Data' onmouseover='loadXML()'></a>-->
				<!--<input type='button' value='Save Annotations' onclick='saveAnnotations("button")'>
				<input type='button' value='Clear All Annotations' onclick='clearAllAnnotations()'>
				<input type='button' value='Undo' onclick='undoOrRedoAnnotation("undo")'>
				<input type='button' value='Redo' onclick='undoOrRedoAnnotation("redo")'>
				
				<!--Logout Button--
				<FORM id='LogOut' name='LogOut' action='<?php //echo $LogOut; ?>' method='post' style='display:inline;'>
				<input type='submit' value='Logout'>
				</FORM>			-->
			<?php
			}
			
			if($ProjectType == 7)
			{
			?>
				<!--Jump to Image-->
				<input type='text' value='' id='imageText' style='font-size:11px; width:40px; 
				cursor: text; margin-right:0px; border-radius: 3px 0px 0px 3px; height: 24px; border-right:0px; 
				padding-top:1px;'>
				<input type='button' value='Jump to Image' id='goToImage' onclick='goToImage()' 
					style='margin-left:0px; border-radius: 0px 3px 3px 0px;'>
				<!--Download XML button-->
				<?php
					if($_SESSION['Id'] == 90 || $_SESSION['Id'] == 74)
					{
					?>
						<a download href='XML_Data.xml'><input type='button' value='XML Data' onmouseover='loadEstimationXML()'></a>
					<?php
					}
				?>
				<!--Help button-->
				<input type='button' value='Instructions' id='help' 
				onclick="DialogBox('<H2>Instructions:</H2><b>Specify the percentages of M1 and M2.</b><br><br><b>M1:</b><br> Area covered by+ve Cells(Brown)<br>_________________________________________________________<br><br>Area covered by+ve Cells(Brown) + Area covered by-ve cells(Blue)<br><br><br><b>M2:</b><br> Area covered by+ve cells(Brown)<br>____________________________________<br><br>Length*Width of the image<br><br><ol style=\'text-align:left\'><li>Please estimate the measure M1 and M2 based on the formulae shown above.</li><li>The estimation should be based on the area covered by a particular cell type, not the number of cells.</li><li>The values entered must be integers from 0 to 100.</li><li>Theoretically, M1 is always larger than M2.</li><li>You may click the \'Save Values\' button to save your estimates.</li></ol><br><b>NOTE:Make sure that you exit the text box when entering your values, otherwise they will not save.</b><br>')">
			<?php
			}
			
			if($ProjectType == 8)
			{
			?>
				<!--Jump to Image-->
				<input type='text' value='' id='imageText' style='font-size:11px; width:40px;
				cursor: text; margin-right:0px; border-radius: 3px 0px 0px 3px; height: 24px; border-right:0px; 
				padding-top:1px;'>
				<input type='button' value='Jump to Image' id='goToImage' onclick='goToImage()' 
					style='margin-left:0px; border-radius: 0px 3px 3px 0px;'>
				<!--Help button-->
				<input type='button' value='Directions' id='help' 
				onclick="DialogBox('<H2>Directions:</H2><b>Mark the +ve cells(Brown) with the green marker and the -ve cells(Blue) with the red marker.</b><br><br><img src=js/cbmarker/images/green-icon-plus.png /><br> Selects the green marker<br><br><img src=js/cbmarker/images/red-icon-minus.png /><br> Selects the red marker<br><br><ol style=\'text-align:left\'><li>A single click represents a single cell. Please do not click more than one point on a single cell.</li><li>To unselect a cell, click a selected point</li><li>Once you are done with the selection, you may click \'Calculate +/- Marker\' to view your selections.</li></ol>')">
				<!--Color Icons-->
				<script>
					drawingColor = "green";
				</script>
				<img src='js/cbmarker/images/green-icon-plus.png' id='green-icon'
				   style='margin:0px 5px 0px; border: 2px solid #FF99CC'
				   onclick='changeColor("green")' />
				<img src='js/cbmarker/images/red-icon-minus.png' id='red-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("red")' />	
				<!--Get number of markers button-->
				<input type='button' value='Calculate +/- Markers' onclick="getPosNegCount()">
				<!--Download XML button-->
				<?php
					if($_SESSION['Id'] == 90 || $_SESSION['Id'] == 74)
					{
					?>
					<SELECT id='user_selection'>
					<OPTION Value='none'>Select User</OPTION>
					<?php
						$pid = mysql_prep($pid);
						$sql = "SELECT user_id FROM cialab.roi_projects_members WHERE roi_project_id=" . $pid;
						$result = mysql_query($sql);
						
						while($row = mysql_fetch_array($result, MYSQL_ASSOC))
						{
							$user_query = "SELECT first_name, last_name FROM cialab.users_data WHERE id=" . $row['user_id'];
							$user_data = mysql_query($user_query);
							$user_row = mysql_fetch_array($user_data, MYSQL_ASSOC);
							echo "<OPTION Value='". $row['user_id'] ."'>" . $user_row['first_name']. " " . $user_row['last_name']. "</OPTION>";
						}
					?>

					</SELECT>
					<a download href='XML_Data.xml'><input type='button' value='XML Data' onmouseover='loadMarkerCountXML()'></a>
					<?php
					}
				?>
				
				<script>
					function loadMarkerCountXML()
					{
						var selectedUser = $('#user_selection').val();
						if(selectedUser != "none")
						{
							url = currentServer + "?action=writeMarkerCountXMLData&pid=" + pid + "&selectedUser=" + selectedUser;
							for(var i = 0; i < images.length; i++)
							{
								url += "&imageArray[]=" + images[i];						
							}
							
							setTimeout(function(){
								loadJSON(url);
							}, 3000);
						}
					}
					
					function getPosNegCount()
					{
						alert("Image Name: " + CurrentImage + "\nPositive Count: " + posCount + "\nNegative Count: " + negCount);
					}
				</script>
			<?php
			}
			
			if ($ProjectType == 9)
			{
			?>
				<!--Jump to Image-->
				<input type='text' value='' id='imageText' style='font-size:11px; width:40px;
				cursor: text; margin-right:0px; border-radius: 3px 0px 0px 3px; height: 24px; border-right:0px; 
				padding-top:1px;'>
				<input type='button' value='Jump to Image' id='goToImage' onclick='goToImage()' 
					style='margin-left:0px; border-radius: 0px 3px 3px 0px;'>
				<!--Help button-->
				<input type='button' value='Directions' id='help' 
				onclick="DirectionsDialog()">
				<!--Color Icons-->
				<script>
					drawingColor = "green";
				</script>
				<img src='js/cbmarker/images/green-icon.png' id='green-icon'
				   style='margin:0px 5px 0px; border: 2px solid #FF99CC'
				   onclick='changeColor("green")' />
				<img src='js/cbmarker/images/red-icon.png' id='red-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("red")' />
				<img src='js/cbmarker/images/blue-icon.png' id='blue-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("blue")' />	
				<img src='js/cbmarker/images/black-icon.png' id='black-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("black")' />			
				<img src='js/cbmarker/images/pink-icon.png' id='pink-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("pink")' />		
				<img src='js/cbmarker/images/purple-icon.png' id='purple-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("purple")' />						   
				<!--Get number of markers button-->
				<input type='button' value='Calculate Counts' onclick="getRvRCount()">
				<!--Download XML button-->
				<?php
					if($_SESSION['Id'] == 90 || $_SESSION['Id'] == 74)
					{
					?>
					<SELECT id='user_selection'>
					<OPTION Value='none'>Select User</OPTION>
					<?php
						$pid = mysql_prep($pid);
						$sql = "SELECT user_id FROM cialab.roi_projects_members WHERE roi_project_id=" . $pid;
						$result = mysql_query($sql);
						
						while($row = mysql_fetch_array($result, MYSQL_ASSOC))
						{
							$user_query = "SELECT first_name, last_name FROM cialab.users_data WHERE id=" . $row['user_id'];
							$user_data = mysql_query($user_query);
							$user_row = mysql_fetch_array($user_data, MYSQL_ASSOC);
							echo "<OPTION Value='". $row['user_id'] ."'>" . $user_row['first_name']. " " . $user_row['last_name']. "</OPTION>";
						}
					?>

					</SELECT>
					<a download href='XML_Data.xml'><input type='button' value='XML Data' onmouseover='loadMarkerCountXML()'></a>
					<?php
					}
				?>
				
				<script>
					function loadMarkerCountXML()
					{
						var selectedUser = $('#user_selection').val();
						if(selectedUser != "none")
						{
							url = currentServer + "?action=writeMarkerCountXMLData&pid=" + pid + "&selectedUser=" + selectedUser;
							for(var i = 0; i < images.length; i++)
							{
								url += "&imageArray[]=" + images[i];						
							}
							
							setTimeout(function(){
								loadJSON(url);
							}, 3000);
						}
					}
					
					function getRvRCount()
					{
						var alertMessage = "Image Name: " + CurrentImage;
						alertMessage += "\nPyrimidal Neuron: " + RvRCounts[0];
						alertMessage += "\nNeoplastic Glia: " + RvRCounts[1];
						alertMessage += "\nNon-neoplastic Oligodendrocyte: " + RvRCounts[2];
						alertMessage += "\nNon-neoplastic Reactive Astrocyte: " + RvRCounts[3];
						alertMessage += "\nTreatment induced cytologically Atypical Astrocyte: " + RvRCounts[4];
						alertMessage += "\nUndetermined: " + RvRCounts[5];
						alert(alertMessage);
					}
					
					function DirectionsDialog()
					{
						var dialogText = "<H2>Directions:</H2>";
						dialogText += "<b>Mark the cells with the appropriate markers</b><br><br>";
						dialogText += "<img src=js/cbmarker/images/green-icon.png /><br>Pyrimidal Neuron<br><br>";
						dialogText += "<img src=js/cbmarker/images/red-icon.png /><br>Neoplastic Glia<br><br>";
						dialogText += "<img src=js/cbmarker/images/blue-icon.png /><br>Non-neoplastic Oligodendrocyte<br><br>";
						dialogText += "<img src=js/cbmarker/images/black-icon.png /><br>Non-neoplastic Reactive Astrocyte<br><br>";
						dialogText += "<img src=js/cbmarker/images/pink-icon.png /><br>Treatment induced cytologically Atypical Astrocyte<br><br>";
						dialogText += "<img src=js/cbmarker/images/purple-icon.png /><br>Undetermined<br><br>";
						dialogText += "<ol style=\'text-align:left\'><li>A single click represents a single cell. Please do not click more than one point on a single cell.</li>"
						dialogText += "<li>To unselect a cell, click a selected point</li>";
						dialogText += "<li>Once you are done with the selection, you may click \'Calculate Counts\' to view your selections.</li></ol>";
						
						DialogBox(dialogText);
					}
				</script>
			<?php
			}

			if ($ProjectType == 10)
			{
			?>
				<!--Jump to Image-->
				<input type='text' value='' id='imageText' style='font-size:11px; width:40px;
				cursor: text; margin-right:0px; border-radius: 3px 0px 0px 3px; height: 24px; border-right:0px; 
				padding-top:1px;'>
				<input type='button' value='Jump to Image' id='goToImage' onclick='goToImage()' 
					style='margin-left:0px; border-radius: 0px 3px 3px 0px;'>
				<!--Help button-->
				<input type='button' value='Directions' id='help' 
				onclick="DirectionsDialog()">
				<!--Color Icons-->
				<script>
					drawingColor = "green";
				</script>
				<img src='js/cbmarker/images/green-icon.png' id='green-icon'
				   style='margin:0px 5px 0px; border: 2px solid #FF99CC'
				   onclick='changeColor("green")' />
				<img src='js/cbmarker/images/red-icon.png' id='red-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("red")' />
				<img src='js/cbmarker/images/blue-icon.png' id='blue-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("blue")' />	
				<img src='js/cbmarker/images/black-icon.png' id='black-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("black")' />
				<img src='js/cbmarker/images/pink-icon.png' id='pink-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("pink")' />		
				<img src='js/cbmarker/images/purple-icon.png' id='purple-icon'
				   style='margin:0px 5px 0px'
				   onclick='changeColor("purple")' />				   
				<!--Get number of markers button-->
				<input type='button' value='Calculate Counts' onclick="getAcneCount()">
				<!--Download XML button-->
				<?php
					if($_SESSION['Id'] == 90 || $_SESSION['Id'] == 74)
					{
					?>
					<SELECT id='user_selection'>
					<OPTION Value='none'>Select User</OPTION>
					<?php
						$pid = mysql_prep($pid);
						$sql = "SELECT user_id FROM cialab.roi_projects_members WHERE roi_project_id=" . $pid;
						$result = mysql_query($sql);
						
						while($row = mysql_fetch_array($result, MYSQL_ASSOC))
						{
							$user_query = "SELECT first_name, last_name FROM cialab.users_data WHERE id=" . $row['user_id'];
							$user_data = mysql_query($user_query);
							$user_row = mysql_fetch_array($user_data, MYSQL_ASSOC);
							echo "<OPTION Value='". $row['user_id'] ."'>" . $user_row['first_name']. " " . $user_row['last_name']. "</OPTION>";
						}
					?>

					</SELECT>
					<a download href='XML_Data.xml'><input type='button' value='XML Data' onmouseover='loadMarkerCountXML()'></a>
					<?php
					}
				?>
				
				<script>
					function loadMarkerCountXML()
					{
						var selectedUser = $('#user_selection').val();
						if(selectedUser != "none")
						{
							url = currentServer + "?action=writeMarkerCountXMLData&pid=" + pid + "&selectedUser=" + selectedUser;
							for(var i = 0; i < images.length; i++)
							{
								url += "&imageArray[]=" + images[i];						
							}
							
							setTimeout(function(){
								loadJSON(url);
							}, 3000);
						}
					}
					
					function getAcneCount()
					{
						var alertMessage = "Image Name: " + CurrentImage;
						alertMessage += "\nClosed Comedone(White Head): " + acneCounts[0];
						alertMessage += "\nOpen Comedone(Black Head): " + acneCounts[1];
						alertMessage += "\nPustules: " + acneCounts[2];
						alertMessage += "\nPapules: " + acneCounts[3];
						alertMessage += "\nCyst or Nodule: " + acneCounts[4];
						alertMessage += "\nScar: " + acneCounts[5];
						alert(alertMessage);
					}
					
					function DirectionsDialog()
					{
						var dialogText = "<H2>Directions:</H2>";
						dialogText += "<b>Mark the cells with the appropriate markers</b><br><br>";
						dialogText += "<img src=js/cbmarker/images/green-icon.png /><br>Closed Comedone (White Head)<br><br>";
						dialogText += "<img src=js/cbmarker/images/red-icon.png /><br>Open Comedone (Black Head)<br><br>";
						dialogText += "<img src=js/cbmarker/images/blue-icon.png /><br>Pustules<br><br>";
						dialogText += "<img src=js/cbmarker/images/black-icon.png /><br>Papules<br><br>";
						dialogText += "<img src=js/cbmarker/images/pink-icon.png /><br>Cyst or Nodule<br><br>";
						dialogText += "<img src=js/cbmarker/images/purple-icon.png /><br>Scar<br><br>";
						dialogText += "<ol style=\'text-align:left\'><li>A single click represents a single cell. Please do not click more than one point on a single cell.</li>"
						dialogText += "<li>To unselect a cell, click a selected point</li>";
						dialogText += "<li>Once you are done with the selection, you may click \'Calculate Counts\' to view your selections.</li></ol>";
						
						DialogBox(dialogText);
					}
				</script>
			<?php
			}
			
			if($ProjectType == 7 || $ProjectType == 8 || $ProjectType == 9 || $ProjectType == 10)
			{
			?>
				<a href="../roimarker.php"><input type='button' value='Home'></a>
			<?php
			}

//Molly edited this so that there are not as many buttons to choose from in the percentile projects 
	else if($dataSetName == "dataset_17" || $dataSetName == "dataset_19"){
	$sql = "SELECT COUNT(roi_projects_members.user_id) AS count FROM `roi_projects_members` WHERE `roi_projects_members`.`project_viewable`='1' AND `user_id` = '".$_SESSION['Id']."'";
				$row  = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
				if($row['count'] > 1)
				{
					//echo "	<SELECT id='project_selection' style='Width: 200px;z-index:10;' onChange=\"window.location='".$MarkerIndex."?pid='+document.getElementById('project_selection').options[document.getElementById('project_selection').selectedIndex].value;\">
					//<OPTION Value=''>Select Project</OPTION>";
					$sql = "SELECT `roi_projects`.`id`,`roi_projects`.`name`,`roi_projects`.`folder`,`roi_projects_members`.`roi_project_id` FROM `roi_projects`,`roi_projects_members` WHERE `roi_projects`.`id`=`roi_projects_members`.`roi_project_id` AND `roi_projects_members`.`project_viewable`='1' AND `user_id`='".$_SESSION['Id']."';";
					$result = mysql_query($sql);
					while($row = mysql_fetch_array($result, MYSQL_ASSOC))
					{
							echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
					}
					//echo "</SELECT>";
				}
				//echo "<input type='text' value='' id='imageText' style='font-size:11px; width:40px; cursor: text; margin-right:0px; border-radius: 3px 0px 0px 3px; height: 24px; border-right:0px; padding-top:1px;'><input type='button' value='Jump to Image' id='goToImage' onclick='goToImage()' style='margin-left:0px; border-radius: 0px 3px 3px 0px;'>";
				//echo "<input type='button' value='Help' id='help' onclick=\"DialogBox('<H2>Directions:</H2><b>How to Annotate:</b><br> To add markings to the images simply click on the image and where ever you click a green dot will appear. This is automatically saved in the background.<br><br><b>How to Remove a Mark:</b><br>To remove a mark hover over a previous marking and the marker should turn red. When it turns red click on the mark and it will be removed.<br><br><b>How to Change Images:</b><br>To move ahead to the next image click the >>| button. To move backwards to the previous image click the |<< button. Sometimes the image may take a short while to load.<br><br><b>Contact Us:</b><Br>You can contact us at schmidt.553@osu.edu with any other questions.')\">";
				//echo "<input type='button' value='Filter' id='filterBtn' onClick=\"Filter();\">";
			
}
			?>
			
			</div>
				<div id='user_review' style='display:inline;'>
				<?php
				// FROM FREDDY: Added project 4 to list of project types not to use the user_review
					if($_SESSION['Permissions']['view_all_markings'] == 1 && $ProjectType != 4 && $ProjectType != 3 && $ProjectType != 6 && $ProjectType != 7 && $ProjectType != 8 && $ProjectType != 9 && $ProjectType != 10 && $ProjectType != 11 && $dataSetName != "dataset_2")
					{
						//Riffer temp button (erase later)
						echo "
							<select style='display: none;' id='s3' multiple='multiple'>
							<option value='all'>(All)</option>
							<optgroup label='Review Marks'></optgroup>
							<option value='review_set'>Review Set</option>
							<optgroup label='Users'>
							</optgroup>
							<option disabled='disabled' selected='selected'>Select View Set</option>
							<input type='button' value='Temp Generate Points button' onclick='generatePoints()'>
							</select>
						";
					}
				?>
				</div>			
		</div>
	</div>
</div>

<?php
//additional styling for annotation drawer
if($ProjectType == 6)
{
?>	
	<!--Marker Categories-->
	<textarea id='redMarkerCategory' cols='25' rows='1' style='background-color:#FF9191' onblur="saveMarkerCategory('red')">red marker category</textarea>
	<textarea id='greenMarkerCategory' cols='25' rows='1' style='background-color:#94D194' onblur="saveMarkerCategory('green')">green marker category</textarea>
	<textarea id='blueMarkerCategory' cols='25' rows='1' style='background-color:#A7A7E6' onblur="saveMarkerCategory('blue')">blue marker category</textarea>
	<textarea id='blackMarkerCategory' cols='25' rows='1' style='background-color:#4D4D4D;color:white' onblur="saveMarkerCategory('black')">black marker category</textarea>
<?php
}

?>

<script>
//Temporary viewing solution for Khalid on Debra's grade data
//Added more people and may have to be refactored if used in the future for other people. However this is temporary. 
function toggle() {
	if( document.getElementById("Grades").style.display=='none' ){
	    document.getElementById("Grades").style.display = '';
	    document.getElementById("GradesDownload").style.display = '';
	}else{
	    document.getElementById("Grades").style.display = 'none';
	    document.getElementById("GradesDownload").style.display = 'none';
	}
 }

//Ajmal's toggle
function toggle2() {
	if( document.getElementById("Grades2").style.display=='none' ){
	    document.getElementById("Grades2").style.display = '';
		document.getElementById("GradesDownload2").style.display = '';
	}else{
	    document.getElementById("Grades2").style.display = 'none';
		document.getElementById("GradesDownload2").style.display = 'none';
	}
 }

//Keluo's toggle
function toggle3() {
	if( document.getElementById("Grades3").style.display=='none' ){
	    document.getElementById("Grades3").style.display = '';
		document.getElementById("GradesDownload2").style.display = '';
	}else{
	    document.getElementById("Grades3").style.display = 'none';
		document.getElementById("GradesDownload3").style.display = 'none';
	}
 }
 
 //Otero's toggle added just for dataset_17 functionality
function toggle4() {
	if( document.getElementById("Grades4").style.display=='none' ){
	    document.getElementById("Grades4").style.display = '';
		document.getElementById("GradesDownload4").style.display = '';
	}else{
	    document.getElementById("Grades4").style.display = 'none';
		document.getElementById("GradesDownload4").style.display = 'none';
	}
 }

</script>

<?php
	if($dataSetName == "dataset_2" && ($_SESSION['Id'] == 65 || $_SESSION['Id'] == 68)){
		//Debra's button
		echo '<br /><br />
		<table align="center">
		<tr><td>
		<input type="submit" value="View Debra\'s Data (Khalid Only)" onclick="toggle()" />';
		echo '</td></tr></table>';

		//Ajmal's button
		echo '<br />
		<table align="center">
		<tr><td>
		<input type="submit" value="View Ajmal\'s Data (Khalid Only)" onclick="toggle2()" />';
		echo '</td></tr></table>';

		//Keluo's button
		echo '<br />
		<table align="center">
		<tr><td>
		<input type="submit" value="View Keluo\'s Data (Khalid Only)" onclick="toggle3()" />';
		echo '</td></tr>
		</table><br />';

		//Forces csv files to download instead of opening in browser
		echo '<form action="forceDownload.php" method="post">
		<table align="center">
		<tr><td>
		<input type="submit" value="Download Data" />
		</td></tr>
		</table>
		</form>
		<p align="center">(You may need to refresh to update changes.)</p>';
	}	
?>
<?php
	//This gives Molly the ability to view Dr. Otero's percentile rankings without downloading anything.
	if($dataSetName == "dataset_17" && $_SESSION['Id'] == 68){
		//Otero's button
		echo '<br /><br />
		<table align="center">
		<tr><td>
		<input type="submit" value="View Otero\'s Data" onclick="toggle4()" />';
		echo '</td></tr></table>';
		
		//Forces csv files to download instead of opening in browser
		echo '<form action="forceDownload.php" method="post">
		<table align="center">
		<tr><td>
		<input type="submit" value="Download Data" />
		</td></tr>
		</table>
		</form>
		<p align="center">(You may need to refresh to update changes.)</p>';
	}	
//Otero's data table - Sets the data table for Dr. Otero's information which Molly can view - this is a temporary fix that Molly (and only Molly) can see what Dr. Otero has put in without downloading the file or anything.
	if($dataSetName == "dataset_17" && $_SESSION['Id'] == 68){
		$pid = mysql_prep($pid);
		$sql = "SELECT image, dropdowndata_id, dropdowndata_id2 FROM cbmarker.imagedata WHERE project_id='$pid' AND user_id=85 ORDER BY id ASC"; 
		$result = mysql_query($sql);
		echo '<table border="1" id="Grades" align="center">';
		echo '<tr><td>Otero\'s Data</td></tr>';
		echo '<tr><td>Image Number</td><td>Image Name</td><td>Percentage</td></tr>';
		$imageNumber = 1;
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			echo '<tr><td>';
			echo $imageNumber . '</td>';
			echo '<td>';
			echo $row['image'] . '</td>';
			echo '<td>';
			echo $row['dropdowndata_id'] . '</td>';
			echo '<td>';
			$imageNumber++;
		}
		echo '</table><br />';
	}
	
?>

<?php
	//Debra's data table
	if($dataSetName == "dataset_2" && ($_SESSION['Id'] == 65 || $_SESSION['Id'] == 68)){
		$pid = mysql_prep($pid);
		$sql = "SELECT image, dropdowndata_id, dropdowndata_id2 FROM cbmarker.imagedata WHERE project_id='$pid' AND user_id=81 ORDER BY id ASC"; 
		$result = mysql_query($sql);
		echo '<table border="1" id="Grades" align="center">';
		echo '<tr><td>Debra\'s Data</td></tr>';
		echo '<tr><td>Image Number</td><td>Image Name</td><td>Primary Grade</td><td>Secondary Grade</td></tr>';
		$imageNumber = 1;
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			echo '<tr><td>';
			echo $imageNumber . '</td>';
			echo '<td>';
			echo $row['image'] . '</td>';
			echo '<td>';
			echo $row['dropdowndata_id'] . '</td>';
			echo '<td>';
			echo $row['dropdowndata_id2'] . '</td>';
			echo '</tr>';
			$imageNumber++;
		}
		echo '</table><br />';
	}
	

	//Debra's csv file
	$filePath = 'DebraData.csv';
	$file = fopen($filePath, 'w');
	$pid = mysql_prep($pid);
	$sql = "SELECT image, dropdowndata_id, dropdowndata_id2 FROM cbmarker.imagedata WHERE project_id='$pid' AND user_id=81 ORDER BY id ASC"; 
	$result = mysql_query($sql);
	$titles = array("Image Number", "Image Name", "Primary Grade", "Secondary Grade");
	fputcsv($file, $titles);
	$imageNumber = 1;
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
		fputcsv($file, array_merge((array)$imageNumber, $row));
		$imageNumber++;
	}
	fclose($file);

	//Ajmal's data table
	if($dataSetName == "dataset_2" && ($_SESSION['Id'] == 65 || $_SESSION['Id'] == 68)){
		$pid = mysql_prep($pid);
		$sql = "SELECT image, dropdowndata_id, dropdowndata_id2 FROM cbmarker.imagedata WHERE project_id='$pid' AND user_id=79 ORDER BY id ASC"; 
		$result = mysql_query($sql);
		echo '<table border="1" id="Grades2" align="center">';
		echo '<tr><td>Ajmal\'s Data</td></tr>';
		echo '<tr><td>Image Number</td><td>Image Name</td><td>Primary Grade</td><td>Secondary Grade</td></tr>';
		$imageNumber = 1;
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			echo '<tr><td>';
			echo $imageNumber . '</td>';
			echo '<td>';
			echo $row['image'] . '</td>';
			echo '<td>';
			echo $row['dropdowndata_id'] . '</td>';
			echo '<td>';
			echo $row['dropdowndata_id2'] . '</td>';
			echo '</tr>';
			$imageNumber++;
		}
		echo'</table><br />';
	}
	
	//Ajmal's csv file
	$filePath = 'AjmalData.csv';
	$file = fopen($filePath, 'w');
	$pid = mysql_prep($pid);
	$sql = "SELECT image, dropdowndata_id, dropdowndata_id2 FROM cbmarker.imagedata WHERE project_id='$pid' AND user_id=79 ORDER BY id ASC"; 
	$result = mysql_query($sql);
	$titles = array("Image Number", "Image Name", "Primary Grade", "Secondary Grade");
	fputcsv($file, $titles);
	$imageNumber = 1;
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
		fputcsv($file, array_merge((array)$imageNumber, $row));
		$imageNumber++;
	}
	fclose($file);

	//Keluo's data table
	if($dataSetName == "dataset_2" && ($_SESSION['Id'] == 65 || $_SESSION['Id'] == 68)){
		$pid = mysql_prep($pid);
		$sql = "SELECT image, dropdowndata_id, dropdowndata_id2 FROM cbmarker.imagedata WHERE project_id='$pid' AND user_id=80 ORDER BY id ASC"; 
		$result = mysql_query($sql);
		echo '<table border="1" id="Grades3" align="center">';
		echo '<tr><td>Keluo\'s Data</td></tr>';
		echo '<tr><td>Image Number</td><td>Image Name</td><td>Primary Grade</td><td>Secondary Grade</td></tr>';
		$imageNumber = 1;
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			echo '<tr><td>';
			echo $imageNumber . '</td>';
			echo '<td>';
			echo $row['image'] . '</td>';
			echo '<td>';
			echo $row['dropdowndata_id'] . '</td>';
			echo '<td>';
			echo $row['dropdowndata_id2'] . '</td>';
			echo '</tr>';
			$imageNumber++;
		}
		echo'</table><br />';
		echo '<table id="GradesDownload3" align="center">';
	}
	
	//Keluo's csv file
	$filePath = 'KeluoData.csv';
	$file = fopen($filePath, 'w');
	$pid = mysql_prep($pid);
	$sql = "SELECT image, dropdowndata_id, dropdowndata_id2 FROM cbmarker.imagedata WHERE project_id='$pid' AND user_id=80 ORDER BY id ASC"; 
	$result = mysql_query($sql);
	$titles = array("Image Number", "Image Name", "Primary Grade", "Secondary Grade");
	fputcsv($file, $titles);
	$imageNumber = 1;
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
		fputcsv($file, array_merge((array)$imageNumber, $row));
		$imageNumber++;
	}
	fclose($file);
?>
</script>

<?php
	//Riffer, this adds the line drawing functionality
	if($ProjectType == 6 || $ProjectType == 11){
?>

<div id="canvasDiv" style="width: <?php echo $image_width. "px; height:" . $image_height . "px"; ?>;">
<canvas id='canvas'></canvas>
</div>

<script> 
	$(document).ready(function() {
    	prepareCanvas();
	});
</script>

<?php
	$picSet = isset($_GET['pic']);
	if($picSet == true)
	{
		$pic = $_GET['pic'];
		echo "<script>currentServerImage = \"" . $pic . "\";</script>";
	}
	else
	{
		$pic = "none";
	}
	
	//LINE
	//Get the colors and coords for a given line
	$sql = "SELECT coords, color, newLine FROM cbmarker.annot_test WHERE
			project_id='$pid' 
			AND userid=" . $_SESSION['Id'] .
			" AND image=\"" . $pic .
			"\" AND annot_type=\"line\"
			ORDER BY id ASC";
	$result = mysql_query($sql);
	$coords = array();
	$colors = array();
	$first = true;
	
	//put respective values in arrays
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if($row['newLine'] == 1 && $first == false)
		{
			array_push($coords, -1);
		}	
		array_push($coords, $row['coords']);
		$first = false;

		//$first = false;
		if($row['newLine'] == 1)
		{
			array_push($colors, $row['color']);
		}
	} 
	
	//indicate end of lines
	array_push($coords, -1);

	//get the values in javascript arrays instead of php arrays
	echo "<script>";
	echo "var coordArray = new Array();";
	echo "lineCoords = new Array();";
	echo "lineColors = new Array();";
	echo "var lineNum = 0;";
	
	$currIndex = 0;
	$first = true;
	for($i = 0; $i < sizeof($coords); $i++)
	{
		//If newLine
		if($coords[$i] == -1 && !$first)
		{
			echo "lineCoords[lineNum] = coordArray;";
			echo "lineNum++;";
			echo "coordArray = new Array();";
			$currIndex = 0;
		}
		else{
			echo "coordArray[" . $currIndex . "] = " . $coords[$i] . ";";	
			$currIndex++;
		}
		$first = false;
	}
	
	for($i = 0; $i < sizeof($colors); $i++)
	{
		echo "lineColors[" . $i . "] = '" . $colors[$i]. "';";
	}
	
	echo "</script>;";
	
	//RECTANGLE
	//get colors and coords
	$sql = "SELECT coords, color, newLine FROM cbmarker.annot_test WHERE
			project_id='$pid' 
			AND userid=" . $_SESSION['Id'] .
			" AND image=\"" . $pic .
			"\" AND annot_type=\"rect\"
			ORDER BY id ASC";
	$result = mysql_query($sql);
	$coords = array();
	$colors = array();
	
	//put respective values in arrays
	$count = 0;
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{	
		array_push($coords, $row['coords']);
		
		//if new rectangle
		if($count % 4 == 0)
		{
			array_push($colors, $row['color']);
		}
		$count++;
	} 
	
	//get the values in javascript arrays instead of php arrays
	echo "<script>";
	echo "var coordArray = new Array();";
	echo "rectCoords = new Array();";
	echo "rectColors = new Array();";
	echo "var rectNum = 0;";
	
	$currIndex = 0;
	for($i = 0; $i < sizeof($coords); $i++)
	{
		echo "coordArray[" . $currIndex . "] = " . $coords[$i] . ";";	
		$currIndex++;
		
		//new rectangle
		if($currIndex == 4)
		{
			echo "rectCoords[rectNum] = coordArray;";
			echo "rectNum++;";
			echo "coordArray = new Array();";
			$currIndex = 0;
		}
	}
	
	for($i = 0; $i < sizeof($colors); $i++)
	{
		echo "rectColors[" . $i . "] = '" . $colors[$i]. "';";
	}
	
	echo "</script>;";
	
	//MARKER
	//get colors and coords
	$sql = "SELECT coords, color, newLine FROM cbmarker.annot_test WHERE
			project_id='$pid' 
			AND userid=" . $_SESSION['Id'] .
			" AND image=\"" . $pic .
			"\" AND annot_type=\"marker\"
			ORDER BY id ASC";
	$result = mysql_query($sql);
	$coords = array();
	$colors = array();

	//put respective values in arrays
	$count = 0;
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{	
		array_push($coords, $row['coords']);
		
		//if new marker
		if($count % 2 == 0)
		{
			array_push($colors, $row['color']);
		}
		$count++;
	} 
	
	//get the values in javascript arrays instead of php arrays
	echo "<script>";
	echo "var coordArray = new Array();";
	echo "markerCoords = new Array();";
	echo "markerColors = new Array();";
	echo "var markerNum = 0;";
	
	$currIndex = 0;
	for($i = 0; $i < sizeof($coords); $i++)
	{
		echo "coordArray[" . $currIndex . "] = " . $coords[$i] . ";";	
		$currIndex++;
		
		//new rectangle
		if($currIndex == 2)
		{
			echo "markerCoords[markerNum] = coordArray;";
			echo "markerNum++;";
			echo "coordArray = new Array();";
			$currIndex = 0;
		}
	}
	
	for($i = 0; $i < sizeof($colors); $i++)
	{
		echo "markerColors[" . $i . "] = '" . $colors[$i]. "';";
	}
	
	echo "</script>;";
	
	//MARKER CATEGORIES
	$sql = "SELECT color, markerCategory FROM cbmarker.annot_test WHERE
			project_id='$pid' 
			AND userid=" . $_SESSION['Id'] .
			" AND annot_type=\"markerCategory\"
			ORDER BY id ASC";
	$result = mysql_query($sql);
	
	//save javascript associative array for each category color
	echo "<script>";
	echo "catArray = new Array();";

	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		echo "catArray['" . $row['color'] . "'] = '" . $row['markerCategory'] . "';";
	}
	
	echo "</script>";
?>

<script>
	//had to put these lines in coreFunctions for custom canvas sizes based on image size
	//loadAnnotations(lineCoords, lineColors, rectCoords, rectColors, markerCoords, markerColors, <?php echo $image_width . "," , $image_height; ?>);
	//loadMarkerCategories(catArray);
</script>

<?php
}



if($ProjectType == 7)			
{
?>
	<table>
	<tr>
	<td>M1:</td>
	<td><textarea id='M1Percent' class='percentBox' cols='5' rows='1' style="overflow:auto">0</textarea></td>
	<td>%</td>
	<td>M2:</td>
	<td><textarea id='M2Percent' class='percentBox' cols='5' rows='1' style="overflow:auto">0</textarea></td>
	<td>%</td>
	<td><input type='button' value='Save Values' onclick='savePercentValues()'></td>
	</tr>
	</table>
	<div id="project7ImageDiv" style="width: <?php echo $image_width. "px; height:" . $image_height . "px"; ?>;"></div>

<?php
	$picSet = isset($_GET['pic']);
	if($picSet == true)
	{
		$pic = $_GET['pic'];
		echo "<script>currentServerImage = \"" . $pic . "\";</script>";
	}
	else
	{
		$pic = "none";
	}
	//load positive percentages
	$sql = "SELECT M1, M2 FROM cbmarker.pospercentestimation WHERE project_id='$pid' AND userid=" . $_SESSION['Id'] . " AND image='$pic'";
	$result = mysql_query($sql);
	echo "<script>";
	echo "var M1 = 0;";
	echo "var M2 = 0;";
	if(mysql_num_rows($result) != 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		echo "M1 = " . $row['M1'] . ";";
		echo "M2 = " . $row['M2'] . ";";
	}
	echo "loadPositivePercentage(M1, M2);";
	echo "</script>";
	?>
	<script>
	$('.percentBox').blur(function(){
		savePercentValues(true);
	});
	
	function isFloat(n)
	{
		return n === +n && n !== (n|0);
	}
	
	function savePercentValues(auto)
	{
		var M1 = parseFloat($('#M1Percent').val());
		var M2 = parseFloat($('#M2Percent').val());
		
		if((!isFloat(M1) && isNaN(M1)) || (!isFloat(M2) && isNaN(M2)))
		{
			$("<div style='color:red'><b>Only numbers</b> can be put in for percentages</div>").dialog(
			{
				buttons:
				{
				    Ok: function() 
					{
						$(this).dialog( "close" );
					}
				}
			});
			if(isNaN(M1))
			{
				$('#M1Percent').val(0);
			}
			if(isNaN(M2))
			{
				$('#M2Percent').val(0);
			}
		}
		else if(M1 > 100 || M2 > 100)
		{
			$("<div style='color:red'>Percentages <b>cannot be greater than 100%</b></div>").dialog(
			{
				buttons:
				{
				    Ok: function() 
					{
						$(this).dialog( "close" );
					}
				}
			});
			if(M1 > 100)
			{
				$('#M1Percent').val(0);
			}
			if(M2 > 100)
			{
				$('#M2Percent').val(0);
			}
		}
		/*else if(M1 <= M2)
		{
			$("<div style='color:red'>M1 <b>must be greater than</b> M2</div>").dialog(
			{
				width: 350,
				buttons:
				{			
				    Ok: function() 
					{
						$(this).dialog( "close" );
					}
				}
			});
			$('#M2Percent').val(0);
		}*/
		else if(M1 < 0 || M2 < 0)
		{
			$("<div style='color:red'>Percentages <b>must be greater than 0</b></div>").dialog(
			{
				buttons:
				{
				    Ok: function() 
					{
						$(this).dialog( "close" );
					}
				}
			});
			if(M1 < 0)
			{
				$('#M1Percent').val(0);
			}
			if(M2 < 0)
			{
				$('#M2Percent').val(0);
			}
		}
		else
		{
			savePositivePercentage(M1, M2);
			if(!auto)
			{
				//alert('Values have successfully been saved');
				console.log("saved successfully.");
				forward();
			}
			isValidPercentages = true;
		}	
	}
	
	function loadEstimationXML()
	{
		loadJSON(currentServer + "?action=writeEstimationXMLData&pid=" + pid);
	}
	</script>
	
<?php
}
?>
</body>
</html>
