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
$ForceReview = false;

//Try to load the project they request via the pid value
if(isset($_GET['pid']) && $_GET['pid']!="")
{
	$sql = "SELECT `roi_projects`.`image_height`,`roi_projects`.`image_width`,`roi_projects`.`id`,`roi_projects`.`name`,`roi_projects`.`folder`,`roi_projects_members`.`roi_project_id` FROM `roi_projects`,`roi_projects_members` WHERE `roi_projects`.`id`=`roi_projects_members`.`roi_project_id` AND `user_id`='".$_SESSION['Id']."' AND `roi_projects_members`.`roi_project_id`='".mysql_prep($_GET['pid'])."';"; 
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
	$sql = "SELECT `cialab`.`roi_projects`.`image_height`,`cialab`.`roi_projects`.`image_width`,`cialab`.`roi_projects`.`id`,`cialab`.`roi_projects`.`name`,`cialab`.`roi_projects`.`folder`,`cialab`.`roi_projects_members`.`roi_project_id` FROM `cialab`.`roi_projects`,`cialab`.`roi_projects_members`, (SELECT `cbmarker`.`imgtracking`.* FROM `cbmarker`.`imgtracking`,(SELECT MAX(p.date) AS maxdate FROM `cbmarker`.`imgtracking`,(SELECT * FROM `cbmarker`.`imgtracking` WHERE `userid`='".$_SESSION['Id']."') AS p) AS x WHERE `x`.`maxdate` = `cbmarker`.`imgtracking`.`date` AND `userid` = '".$_SESSION['Id']."') AS y WHERE `cialab`.`roi_projects`.`id`=`cialab`.`roi_projects_members`.`roi_project_id` AND `user_id`=y.userid AND `cialab`.`roi_projects_members`.`roi_project_id`=y.project_id;";
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
//IF pid not specified and user has not worked on a specific project
// the first project they are a member of.
if($ROIFolder == "") 
{
	$sql = "SELECT `roi_projects`.`image_height`,`roi_projects`.`image_width`,`roi_projects`.`id`,`roi_projects`.`name`,`roi_projects`.`folder`,`roi_projects_members`.`roi_project_id` FROM `roi_projects`,`roi_projects_members` WHERE `roi_projects`.`id`=`roi_projects_members`.`roi_project_id` AND `user_id`='".$_SESSION['Id']."'";
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
$ForceReview = $row["reviewable"];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML style='height:100%;'>

<style type='text/css'>
html, body 
{
	margin:0;
	padding:0;
	font-family: Arial;
}
/* commented backslash hack v2 \*/ 
html, body
{
	height:100%;
	width: 100%;
}
/* for ie mac*/
#top-bar
{
	position:fixed;
	top:0px;
	left:0px;
	height:50px;
	width: 100%;
	z-index:999;
	overflow:visible;
}
#topbar-inner
{
	height:50px;
	background:white;
}

.inner 
{
	padding: 50px 0;
	position:relative;
	width:100%;
}
p
{
	margin:0 0 1em;
}

</style>

<!--[if lte IE 6]>
<style type="text/css">
html, body{
	overflow: auto;
	padding: 50px 0;
	margin:-50px 0;
	padd\ing:0;
	ma\rgin:0;
	height:99.9%;
}
#outer
{ 
	overflow:auto;
	height:99.9%;
}
/* hide form ie5*/
#contain-all{
	p\osition:absolute;
	o\verflow-y:scroll;
	o\verflow-x:scroll;
	w\idth: 100%;
	he\ight:100%;
	z-i\ndex:1;
}
/* end hack */ 
#top-bar,#footer{position:absolute;}

/* reduce page to allow the scrollbar to remain visible */
#topbar-inner,#footer-inner {margin-right:17px;}

</style>
<![endif]-->

<head>
<TITLE>Centroblast Marker</TITLE>
<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.css">
<link rel="stylesheet" type="text/css" href="css/ui.css">
<script type="text/javascript" src="js/jquery-1.txt"></script>
<script type="text/javascript" src="js/jquery-ui-1.txt"></script>
<script type="text/javascript" src="js/ui.txt"></script>

<div id='information' style='text-align:center; margin-left:auto; margin-right:auto;'></div>


<script language="JavaScript">

$(window).resize(function() 
{
	//alert('test');
  //$('#pointer_div').css('height',$(window).height()- 50);
  
});


var reviewing = false;
var markerColors = ["#FFFF00","#FF00FF","#00FFFF","#FF6F00","#FFFCCC","#FFCC00","#C0C0C0","#009000","#F99CF9"];
var markerHolder = new Array();
var hideBox = true;
var actionArray = new Array();
var pid = <?php echo $pid; ?>;
var userid = <?php echo $_SESSION['Id'] ?>;
var debug = <?php if(isset($_GET['debug']) == true && $_GET['debug'] == 1){echo "true";}else{echo "false";} ?>;
var currentServer = "<?php if(isset($MarkerServer)){echo $MarkerServer;} ?>";
var roiDirectory = "<?php if(isset($ROIDirectory)){echo $ROIDirectory;} ?>";
var localDirectory = "<?php if(isset($LocalDirectory)){echo $LocalDirectory;} ?>";
var forceReview = <?php if(isset($ForceReview)){echo $ForceReview;} ?>;
var connected = false;
var connectID = 1;
var markerArray = new Array();
var movementCounter = 0;
var point = new Array();
var defaultMarkerColor = "#5EFF00";
var CurrentImage = "";
var CurrentIndex = 0;
var DropDownCode = "";
var images = [<?php 
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
		echo implode(",",$files);
		closedir($handler);?>];
var offSetLeft = 0;
var offSetTop = 0;
var IEVersion = 0;
var posLeftOffset = 0;
var posTopOffset = 0;

$(document).ready(
function() 
{
	offSetLeft = document.getElementById("pointer_div").offsetLeft;
	offSetTop = document.getElementById("pointer_div").offsetTop;
	loadImage(true,"",pid);
	IEVersion = msieversion();
	DropDownCode = document.getElementById('user_review').innerHTML;
	loadDropDown();
	$('#pointer_div').bind('click', function(event){point_it(event,$(this));});

	if(IEVersion == 6 || IEVersion == 7) //IE 6 & 7 have a pixel offset, so this corrects that.
	{
		posLeftOffset = 6;
		posTopOffset = 11;
	}
	else
	{
		posLeftOffset = -4;
		posTopOffset = -4;
	}	
});


function marksDisplayed(userID)
{
	for(c=0;c<markerHolder.length;c++)
	{
		if(markerHolder[c].name == userID)
		{
			return true;
		}
	}
	return false;
}

function loadDropDown()
{
		$("#s3").dropdownchecklist( { firstItemChecksAll: true, width: 200, zIndex: 10000 
			,onItemClick: function(checkbox, selector)
				{
					var justChecked = checkbox.prop("checked");

					if(checkbox.prop("checked") == true)
					{
						if(checkbox.prop("value") == "all")
						{
							for( i = 0; i < selector.options.length; i++ )
							{
								selector.options[i].selected = true;
								//alert(selector.options[i].value);
								if (isNaN(selector.options[i].value) == false)
								{
									loadJSON(currentServer+'?action=getUsersImgMarks&pic='+CurrentImage+'&pid='+pid+'&userid='+selector.options[i].value,"Loading Image Marks");
								}
							}
						}
						else if(checkbox.prop("value") == "review_set")
						{
							//Check to see if review set is already loaded (This can happen if the user is reviewing a slide)
							if(marksDisplayed("review_set") == false)
							{
								//get review set.
								loadJSON(currentServer+'?action=getReviewImgMarks&pic='+CurrentImage+'&pid='+pid,"Loading Review Image Marks");
							}
						}
						else
						{
							//load specific user.
							loadJSON(currentServer+'?action=getUsersImgMarks&pic='+CurrentImage+'&pid='+pid+'&userid='+checkbox.prop("value"),"Loading Image Marks");
						}
					}
					else
					{
						if(checkbox.prop("value") == "all")
						{
							for( i = 0; i < selector.options.length; i++ )
							{
								selector.options[i].selected = false;
								if (isNaN(selector.options[i].value) == false)
								{
									//Remove Marker Set
									$('div[name='+'Marker_' + selector.options[i].value +']').remove();
									
									//Add colors back to color holder array.
									for(c=0;c<markerHolder.length;c++)
									{
										if(markerHolder[c].name == selector.options[i].value)
										{
											markerColors.splice(0,0,markerHolder[c].color);
											markerHolder.splice(c,c+1);
										}
									}
									
								}
							}
						}
						else
						{
							//Remove Marker Set
							$('div[name='+'Marker_' + checkbox.prop("value") +']').remove();
							
							for(c=0;c<markerHolder.length;c++)
							{
								if(markerHolder[c].name == checkbox.prop("value"))
								{
									//alert("C: " + c + "  |Color: " + markerHolder[c].color);
									markerColors.splice(0,0,markerHolder[c].color);
									markerHolder.splice(c,c+1);
								}
							}
							

							//markerColors.splice(0,0,currentMarkerColor);//add the color back to the colors array so it can be reused

						}
					}
				}
			}
			);
}

jQuery.fn.center = function () 
{
    this.css("position","absolute");
    this.css("top", (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
    this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
    return this;
}


function DialogBox(Message,isYesNo,yesFunction,noFunction)
{
	//Hide select, This is a bug in IE 6 that puts a select argument on the highest z-index
	$('#project_selection').css('visibility','hidden');

	//Set CSS for Blackout Box
	$('#blackout').css('display','block');
	$('#blackout').css('opacoty',.5);
	$('#blackout').css('width',$(document).width());
	$('#blackout').css('height',$(document).height());
   
	//Set CSS and create infobox for info box
	var infoBox = jQuery('<div/>');
	infoBox.attr('id','infoBox');
	infoBox.css('width','400px');
	infoBox.css('zIndex','2000');
	infoBox.css('display','block');
	infoBox.css('textAlign','center');
	infoBox.css('position','absolute');
	infoBox.css('top','50%');
	infoBox.css('left','50%');
	infoBox.css('margin-top','-300px');
	infoBox.css('margin-left','-200px');
	infoBox.css('background','white');
	infoBox.css('border','solid 1px black');
	infoBox.css('padding','10px');
	infoBox.css('fontFamily','Arial');
	
	//Create Message Window
	var dialogMessage = jQuery('<div/>');
	dialogMessage.attr('id','dialogMessage');
	dialogMessage.append(Message);

	//Add Message to Box
	infoBox.append(dialogMessage);
	
	
	//Check if YesNo box or Ok box, then add the appropriate buttons
	if(isYesNo == true)
	{
		var yesButton = CreateButton('yesButton','Yes');
		yesButton.bind('click',yesFunction);
		infoBox.append(yesButton);
		
		var noButton = CreateButton('noButton','No');
		noButton.bind('click',noFunction);
		infoBox.append(noButton);
	}
	else
	{
		var okButton = CreateButton('okButton','Ok');
		okButton.click(function(event){HideDialog();});
		infoBox.append(okButton);
	}
	$('body').append(infoBox);

   /*
<div id='infobox' style='text-align:center;display:none;z-index:2000;position:absolute;top:50%;margin-top:-300px;left:50%;margin-left:-200px;width:400px;background-color:white;border:solid 1px black;padding:10px;font-family:Arial;'>
	<div id='OKDialog'></div>
	<div id='OKBox' style='width:120px;height:50px;position:relative;margin-left:auto;margin-right:auto;margin-top:20px;'>
		<input type='button' value='Ok' name='Ok' style='width:120px;' onClick='HideOKDialog();'>
	</div>
</div>
	*/
}

function CreateButton(idValue,textValue)
{
	var buttonObj = jQuery('<input/>');
	buttonObj.attr('id',idValue);
	buttonObj.attr('type','button');
	buttonObj.attr('value',textValue);
	buttonObj.css('fontSize','14px');
	buttonObj.css('height','30px');
	buttonObj.css('width','120px');
	buttonObj.css('float','left');
	buttonObj.css('display','block');
	buttonObj.css('marginLeft','10px');
	buttonObj.css('marginRight','10px');
	buttonObj.css('marginTop','30px');
	return buttonObj;
}

function HideDialog()
{
	$('#project_selection').css('visibility','visible');
	$('#blackout').css('display','none');
	$('#infoBox').remove();
}

function point_it(event,obj)
{	
	var offset = obj.offset();
	
	ieMoustOffSetLeft = 0;
	ieMoustOffSetRight = 0;
	
	//IE 6 and IE 7 do not calculate mouse position the same way
	if(IEVersion == 6 || IEVersion == 7)
	{
		ieMoustOffSetLeft = (document.documentElement.scrollLeft ? document.documentElement.scrollLeft :document.body.scrollLeft);
		ieMoustOffSetRight = (document.documentElement.scrollTop ? document.documentElement.scrollTop :document.body.scrollTop);
	}
	
	pos_x = event.pageX-offset.left;//-ieMoustOffSetLeft;
	pos_y = event.pageY-offset.top;//-ieMoustOffSetRight;
	
	$('#testtextbox').attr('value','X: ' + pos_x + '   Y: ' + pos_y);
	
	//Create and Add Marker
	var Marker = createMarker(userid,defaultMarkerColor,pos_x,pos_y,100);

	$('#pointer_div').append(Marker);
	
	//alert("pos_x: " + pos_x + "  pos_y: " + pos_y);
	
	//Add it to the point array
	markerArray[userid].push([pos_x,pos_y]);

	//Save to Server
	loadJSON(currentServer+"?x=" + pos_x + "&y=" + pos_y + "&pic=" + CurrentImage + "&action=add" + '&pid='+pid+'&after_review=' + convertBoolToInt(reviewing),"Saving Marker");

	//Update the CB count
	updateCBCount();
}

function convertBoolToInt(testBool)
{
	return (testBool)?1:0;
}

function AddMarkersFromJSON(markersData,imageName,color,isReviewSet,idValue,zIndex)
{
	//This function is optimzed to speed up the initial loading of the markings
	//looping through the AddMarker function would take so long it would freeze
	//the webbrowser so this function was made to handle loading a large number of
	//markings very quickly.
	//This also allows the loading of review sets.

	var ElementText = "";
	var markerStyle = "";

	//Create the new array for the user ID
	if(markerArray[idValue] == null)
	{
		markerArray[idValue] = new Array();
	}
	
	var markerJoinedText = "";
	var Markers = jQuery.makeArray();
	
	for(var x=0;x<markersData.length;x++)
	{
		if(imageName == CurrentImage)
		{	
			markerArray[idValue].push([markersData[x].x,markersData[x].y]);
			x_point = markersData[x].x;
			y_point = markersData[x].y;
			var Marker = createMarker(idValue,color,x_point,y_point,zIndex);
			
			//Check if set is review set, if so remove option to delete marker.
			if(isReviewSet)
			{
				Marker.unbind();
			}
			
			Markers.push(Marker);
		}
	}
	//Add the markers to the pointer div
	$('#pointer_div').append.apply($('#pointer_div'), $.isArray( Markers) ? Markers : [Markers]);
	updateCBCount();
}

function createMarker(userid_value,color_value,x_point_value,y_point_value,zIndex_value)
{
	//This is the optimal look but because IE sucks this doesnt work...
	/*
	var Marker = jQuery('<div/>', 
	{  
		id: '',
		name: 'Marker_'+ userid_value,
		css: 
		{  
			background: color_value,
			fontSize: '0px',
			height: '6px',
			width: '6px',
			position: 'absolute',
			left: (x_point_value + offSetLeft+posLeftOffset),
			top: (y_point_value + offSetTop+posTopOffset),
			zIndex: zIndex_value,
			border: '1px solid black',
		},  
		mouseover: function()
		{  
			$(this).css('background','red');  
		},
		mouseout: function()
		{  
			$(this).css('background',color_value); 
		},
		click: function(event)
		{  
			removeMarker(event,$(this));
		}
	});
	*/
	
	var offset = $('#pointer_div').offset();
	
	//Create Object
	var Marker = jQuery('<div/>');
	
	//Set Attributes
	Marker.attr('name','Marker_'+ userid_value);
	
	//Set CSS
	Marker.css('background',color_value);
	Marker.css('fontSize','0px');
	Marker.css('height','6px');
	Marker.css('width','6px');
	Marker.css('position','absolute');
	Marker.css('left',(x_point_value - 4));
	Marker.css('top',(y_point_value - 4 + 50));
	Marker.css('zIndex',zIndex_value);
	Marker.css('border','1px solid black');
	
	
	//Set Functions
	Marker.mouseover(function(){$(this).css('background','red');});
	Marker.mouseout(function(){$(this).css('background',color_value);});
	Marker.click(function(event){removeMarker(event,$(this));});
	
	//Set Properties
	Marker.prop('x_point',x_point_value);
	Marker.prop('y_point',y_point_value);
	
	return Marker;
}

function removeMarker(event,obj)
{
	obj.remove();
	RemovePoint(userid,obj.prop("x_point"),obj.prop("y_point"));
	loadJSON(currentServer+"?x=" + obj.prop("x_point") + "&y=" + obj.prop("y_point") + "&pic=" + CurrentImage + "&action=remove"+'&pid='+pid,"Saving Remove Marker");
	//Stop event from bubbling, (this stops it from making a new mark)
	event.stopImmediatePropagation();
	
	updateCBCount();
}

function RemovePoint(userid_value,x_point,y_point)
{
	var pointArray = markerArray[userid_value];
	
	for(var x=0;x<pointArray.length;x++)
	{
		var point = pointArray[x];
		var xPos = point[0];
		var yPos = point[1];
		
		if (xPos==x_point && yPos==y_point)
		{
			pointArray.splice(x,1);
			return;
		}
	}
}

function removeField(divObj)
{
	divObj.parentNode.removeChild(divObj); 
	updateCBCount();
}

function updateCBCount()
{
	if(markerArray[userid] == null)
	{
		markerArray[userid] = new Array();
	}
	document.getElementById('centroblasts').innerHTML = markerArray[userid].length;
}
function getCBCount()
{
	if(markerArray[userid] == null)
	{
		markerArray[userid] = new Array();
	}
	return markerArray[userid].length;
}
function msieversion()
{
	var ua = window.navigator.userAgent
	var msie = ua.indexOf("MSIE ")

	if ( msie > 0 )      // If Internet Explorer, return version number
	{
		return parseInt (ua.substring (msie+5, ua.indexOf (".", msie )))
	}
	else                 // If another browser, return 0
	{
		return 0
	}
}
function OverDiv()
{
	hideBox = false;
}
function OutDiv()
{
	hideBox = true;
	var t=setTimeout("if(hideBox == true){document.getElementById('RemoveDiv').style.display = 'none';}",100);
}

function RemoveAllMarkers()
{
	//Reset Marker Array
	markerArray[userid] = new Array();
	
	//Remove All Markers
	$('div[name='+'Marker_' + userid +']').remove();

	//Update CB Count
	updateCBCount();
	
	//Save Action on Server
	loadJSON(currentServer+"?pic=" + CurrentImage + "&action=removeAll"+'&pid='+pid,"Remove All Markers");
}

function printMarkerPoints()
{
	var tempStr = '';
	var pointArray = markerArray[userid];
	for (var x=0;x<pointArray.length;x++)
	{
		tempStr = tempStr + "X: " + pointArray[x][0] + " Y: " + pointArray[x][1] + "<br>";
	}
	document.getElementById('PrintArea').innerHTML = tempStr;
}
function CheckMovement(Id)
{
	//alert(actionArray[Id].complete);
	if(actionArray[Id].complete == false)
	{
		DialogBox("There was an Error<br><br>Sorry for the inconvenience.<br><br> If you could email us the Error Report to schmidt.553@osu.edu we can fix any errors faster.<br><br>Error Report<br><textarea style='width:350px;height:100px;margin-left:25px;margin-right:25px;'>"+dump(actionArray,0)+"</textarea><br><br>We reccomend not using this application until we can resolve the error, as data might not be saved.<br><br>Thank You!");
	}
}
function loadJSON(url,action) 
{
	var d=new Date();
	setTimeout("CheckMovement("+movementCounter+");",10000); 
	actionArray[movementCounter] = {"action":action,"move":movementCounter,"complete":false,"errors":"","date":d.toUTCString()};
	var headID = document.getElementsByTagName("head")[0];         
	var newScript = document.createElement('script');
      newScript.type = 'text/javascript';
      newScript.src = url+"&move="+movementCounter;
	headID.appendChild(newScript);
	movementCounter = movementCounter +1;
	if(debug == true)
	{
		document.getElementById('PrintArea').innerHTML = dump(actionArray,0,true);
	}
}
function dump(arr,level,useBR) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				if(useBR == true)
				{
					dumped_text += level_padding + "'" + item + "' ...<br>";
				}
				else
				{
					dumped_text += level_padding + "'" + item + "' ...\n";
				}
				dumped_text += dump(value,level+1,useBR);
			} else {
				if(useBR == true)
				{
					dumped_text += level_padding + "'" + item + "' => '" + value + "'<br>";
				}
				else
				{
					dumped_text += level_padding + "'" + item + "' => '" + value + "'\n";
				}
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

//This handles recieving the JSON requests
function processJSON(data)
{
	connected = true;
	if(data.move != "" && data.status == true)
	{
		actionArray[data.move].complete = true;
		if(debug == true)
		{
			document.getElementById('PrintArea').innerHTML = dump(actionArray,0,true);
		}
	}
	if(data.name == "lastViewedImage")
	{
		loadImage(false,data.image);
	}
	else if(data.name == "getImgMarks")
	{
		AddMarkersFromJSON(data.marks,data.image,defaultMarkerColor, false,userid,102);		
	}
	else if(data.name == "getUsersImgMarks")
	{
		markerColor = markerColors.splice(0,1);
		markerHolder.splice(0,0,{"name":data.user,"color":markerColor});
		AddMarkersFromJSON(data.marks,data.image,markerColor, true,data.user,101);
	}
	else if(data.name == "getReviewImgMarks")
	{
		markerColor = markerColors.splice(0,1);
		markerHolder.splice(0,0,({"name":"review_set","color":markerColor}));
		$("#s3").val("review_set").selected = true;
		$("#s3").dropdownchecklist("refresh");
		AddMarkersFromJSON(data.marks,data.image,markerColor, true,"review_set",100);
	}
	else if(data.name == "imgTackingUpdated")
	{

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
	else if(data.name == "getUsersForImage")
	{
		
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
	}
	else
	{
	}
	
}
function backward()
{
	if(CurrentIndex -1 < 0)
	{
		DialogBox('You cannot go back any farther');
	}
	else
	{
		reviewing = false;
		CurrentIndex = CurrentIndex -1;
		loadImage(false,images[CurrentIndex]);
	}
}
function forward()
{
	if(CurrentIndex +1 >= images.length)
	{
		DialogBox('You cannot go ahead any farther');
	}
	else
	{
		reviewing = !reviewing;
		if(reviewing == false || forceReview == false)
		{
			if(getCBCount() == 0)
			{
				var yesFunction = function(){CurrentIndex++;loadImage(false,images[CurrentIndex]);HideDialog();}
				var noFunction = function(){reviewing=true;HideDialog();}
				DialogBox("It appears there are no markings. Are you sure you want to skip to the next image",true,yesFunction,noFunction);
			}
			else
			{
				CurrentIndex = CurrentIndex +1;
				loadImage(false,images[CurrentIndex]);
			}
		}
		else
		{
			loadImage(false,images[CurrentIndex]);
		}
		
	}
}

function loadImage(first,image)
{
	if(reviewing == false || forceReview == false)
	{
	
		if(forceReview)
		{
			document.getElementById('forward').value = ">>| (CaIA)";
		}
		
		if(first == true)
		{
			//document.getElementById('PrintArea').innerHTML += "<br> Getting Last Image";
			loadJSON(currentServer+'?action=getImgTracking&pid='+pid,"Getting Last Image");
		}
		else
		{
			//Remove All Markers On Page
			for(key in markerArray)
			{
				$('div[name='+'Marker_' + key+']').remove();
			}
			
			//Add colors back to the color Array
			while(markerHolder.length > 0)
			{
				markerColors.splice(0,0,markerHolder[0].color);
				markerHolder.splice(0,1);
			}

			//Reset the Marker Array
			markerArray = new Array();

			//Update the CB count
			updateCBCount();
		
			
			var ImageName = images[0];
			
			CurrentImage = ImageName;
			if (image != "")
			{
				CurrentImage=image;
			}
			
			document.getElementById('centroblasts').innerHTML = "<img src='loading.gif' border='0'>";
			loadJSON(currentServer+'?action=getImgMarks&pic='+CurrentImage+'&pid='+pid,"Loading Image Marks");
			
			for(var i=0;i<images.length;i++)
			{
				if(images[i] == CurrentImage)
				{
					CurrentIndex = i;
				}
			}

			document.getElementById('pointer_div').style.backgroundImage = "URL('"+roiDirectory+CurrentImage+"')";
			
			document.getElementById('imageNumber').innerHTML = (CurrentIndex+1) + "/" + images.length;
			
			loadJSON(currentServer+'?pic=' + CurrentImage + '&action=setImgTracking&pid='+pid,'Saving Image Tracker');
	
		
			//Load all the people who marked that image and reset the color marker array counter.
			loadJSON(currentServer+'?action=getUsersForImage&pid='+pid + '&pic=' + CurrentImage ,"Getting All Users Who Marked This Image");
		}
	}
	else
	{
		//This occurs when the user is reviewing the image. This calls to get the review marks for the particular image.
		
		//This disables the user from removing previous marks
		$('div[name='+'Marker_' + userid+']').unbind();
		
		document.getElementById('forward').value = ">>|";
		loadJSON(currentServer+'?action=getReviewImgMarks&pic='+CurrentImage+'&pid='+pid,"Loading Review Image Marks");
	}
}
function goToImage()
{
	reviewing = false;
	var imageText = document.getElementById('imageText').value;
	if(imageText != "" && Number(imageText) > 0 && Number(imageText) <= images.length)
	{
		loadImage(false,images[(imageText-1)]);
	}
}

function Filter()
{
	if($("#Filter").length > 0)
	{
		$("#Filter").remove();
	}
	else
	{
		var Filter = jQuery('<div/>');
		
		Filter.attr('id','Filter');
		//Set CSS
		Filter.css('background','black');
		Filter.css('fontSize','0px');
		Filter.css('height','100%');
		Filter.css('width','100%');
		Filter.css('display','block');
		Filter.css('zIndex','0');
		Filter.css('opacity',.3);

		$("#pointer_div").append(Filter);
	}
}
</script>
</head>
<body style='height:100%;width:100%;'>

<div id='blackout' style='display:none;position:absolute;height:200%;z-index:1000;width:100%;left:0px;top:0px;opacity:0.4;filter:alpha(opacity=40);background-color:black;'></div>


<div id="outer">
	<div id="contain-all">
		<div class="inner">
		
		<div id="pointer_div" style = "background-image:url('');width:<?php echo $image_width; ?>px;height:<?php echo $image_height; ?>px;float:left;clear:both;"></div>
		<!--
		<div id='PrintArea' style='float:left;clear:both;'></div>
		<div id='messageID' style='display:none;'></div>
		-->
		</div>
	</div>
</div>

<div id="top-bar">
	<div id="topbar-inner">
		<div id='controls' style='float:left; clear:both; overflow:hidden; height:50px; padding: 0px 0px;'>
			<input type='hidden' value='Print Array' style='font-family:Verdana;' onclick='printMarkerPoints()'>
			<FORM id='LogOut' name='LogOut' action='<?php echo $LogOut; ?>' method='post' style='display:inline;'>
			<input type='submit' value='Logout' style='font-family:Verdana;'>
			</FORM>
			<input type='button' value='Clear All Points' style='font-family:Verdana;' onclick='RemoveAllMarkers()'>
			<input type='button' value='|<<' id='backward' style='font-family:Verdana;' onclick='JavaScript:backward()'>
			<input type='button' value='>>|' id='forward' style='font-family:Verdana;' onclick='JavaScript:forward()'>
			<b>CB Count:</b> <div id='centroblasts' style='min-width:16px;min-height:16px;display:inline;font-family:Verdana;padding-right:10px;'>0</div>
			<b>Image:</b> <div id='imageNumber' style='display:inline;font-family:Verdana;padding-right:20px;'>0/0</div>
			<input type='text' value='' id='imageText' style='font-family:Verdana;width:40px;'>
			<input type='button' value='Load Image' id='goToImage' style='font-family:Verdana;' onclick='goToImage()'>
			<input type='button' value='Help' id='help' style='font-family:Verdana;' onclick="DialogBox('<H2>Directions:</H2><b>How to Annotate:</b><br> To add markings to the images simply click on the image and where ever you click a green dot will appear. This is automatically saved in the background.<br><br><b>How to Remove a Mark:</b><br>To remove a mark hover over a previous marking and the marker should turn red. When it turns red click on the mark and it will be removed.<br><br><b>How to Change Images:</b><br>To move ahead to the next image click the >>| button. To move backwards to the previous image click the |<< button. Sometimes the image may take a short while to load.<br><br><b>Contact Us:</b><Br>You can contact us at schmidt.553@osu.edu with any other questions.')">
			<input type='button' value='Toggle Filter' id='filterBtn' style='font-family:Verdana;' onClick="Filter();">

			<?php
			//This selects the projects that the user is apart of.
			
			$sql = "SELECT COUNT(roi_projects_members.user_id) AS count FROM `roi_projects_members` WHERE `user_id` = '".$_SESSION['Id']."'";
			$row  = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
			if($row['count'] > 1)
			{
				echo "	<SELECT id='project_selection' style='Width: 200px;z-index:10;' onChange=\"window.location='".$MarkerIndex."?pid='+document.getElementById('project_selection').options[document.getElementById('project_selection').selectedIndex].value;\">
				<OPTION Value=''>Select Project</OPTION>";
				$sql = "SELECT `roi_projects`.`id`,`roi_projects`.`name`,`roi_projects`.`folder`,`roi_projects_members`.`roi_project_id` FROM `roi_projects`,`roi_projects_members` WHERE `roi_projects`.`id`=`roi_projects_members`.`roi_project_id` AND `user_id`='".$_SESSION['Id']."';";
				$result = mysql_query($sql);
				while($row = mysql_fetch_array($result, MYSQL_ASSOC))
				{
						echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
				}
				echo "</SELECT>";
			}
			?>
			
				<div id='user_review' style='display:inline;'>
				<?php
					if($_SESSION['Permissions']['view_all_markings'] == 1)
					{
						echo "
							<select style='display: none;' id='s3' multiple='multiple'>
							<option value='all'>(All)</option>
							<optgroup label='Review Marks'></optgroup>
							<option value='review_set'>Review Set</option>
							<optgroup label='Users'>
							</optgroup>
							</select>
						";
					}
				?>
				</div>
				
			<input type='textbox' value='ok' id='testtextbox'>
			
		</div>
	</div>
</div>

<!--
<div id="footer">
	<div id="footer-inner">
	
	</div>
</div>
-->

</body>
</html>