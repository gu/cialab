<?php
include("GlobalVariables.php"); 
session_start();

$_SESSION['IP_DEMO'] = "";
$_SESSION['UserName_DEMO'] = "";
$_SESSION['Password_DEMO'] = "";
$_SESSION['id_DEMO'] = "1";
$_SESSION['Active_DEMO'] = true;
$_SESSION['admin_DEMO'] = 0;
$_SESSION['demo_DEMO'] = true;
$_SESSION['IsAuthenticated_DEMO'] = True;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML style='height:100%;'>
<TITLE>Centroblast Marker</TITLE>
<div id='information' style='text-align:center; margin-left:auto; margin-right:auto;'></div>
<div id='WholeWebPage' style='font-family:Verdana;'>
<head>
<script language="JavaScript">

var actionArray = new Array();
var debug = <?php if(isset($_GET['debug']) == true && $_GET['debug'] == 1){echo "true";}else{echo "false";} ?>;
var currentServer = "<?php if(isset($MarkerServer)){echo $MarkerServer;} ?>";
var roiDirectory = "<?php if(isset($ROIDirectory)){echo $ROIDirectory;} ?>";
var localDirectory = "<?php if(isset($LocalDirectory)){echo $LocalDirectory;} ?>";
var connected = false;
var connectID = 1;
var markerArray = new Array();
var markerCounter = 0;
var markerCounter2 = 0;
var movementCounter = 0;
var X_Remove = "";
var Y_Remove = "";
var ID_Remove = "";
var PIC_Remove = "";
var MARKER_Remove = "";
var point = new Array();
var markerColor = "5EFF00";
var CurrentImage = "";
var CurrentIndex = 0;
var images = [<?php 
		$results = array();
		$handler = opendir("./ROI/");
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

window.onload = function() 
{
     offSetLeft = document.getElementById("pointer_div").offsetLeft;
     offSetTop = document.getElementById("pointer_div").offsetTop;
	 loadImage(true,"",pid);
	 IEVersion = msieversion();
}
		
function OKDialog(Message)
{
   document.getElementById('OKDialog').innerHTML = Message;
   document.getElementById('blackout').style.display = "block";
   document.getElementById('infobox').style.display = "block";
}
function HideOKDialog()
{
	document.getElementById('blackout').style.display = "none";
	document.getElementById('infobox').style.display = "none";
}

function point_it(event,obj)
{	
	loc_x = event.offsetX?(event.offsetX):event.pageX;
	loc_y = event.offsetY?(event.offsetY):event.pageY;
	
	pos_x = event.offsetX?(event.offsetX):event.pageX-document.getElementById("pointer_div").offsetLeft;
	pos_y = event.offsetY?(event.offsetY):event.pageY-document.getElementById("pointer_div").offsetTop;

	AddMarker(pos_x,pos_y)

	loadJSON(currentServer+"?x=" + pos_x + "&y=" + pos_y + "&pic=" + CurrentImage + "&action=add","Saving Marker");
}

function AddMarkersFromJSON(markersData,imageName)
{
	//This function is optimzed to speed up the initial loading of the markings
	//looping through the AddMarker function would take so long it would freeze
	//the webbrowser so this function was made to handle loading a large number of
	//markings very quickly.
	var ElementText = "";
	var markerStyle = "";
	var posLeftOffset = 0;
	var posTopOffset = 0;
	
	if(IEVersion == 6 || IEVersion == 7) //IE 6 & 7 for some reason reads points with an offset, so this corrects that.
	{
		posLeftOffset = 6;
		posTopOffset = 11;
	}
	else
	{
		posLeftOffset = -4;
		posTopOffset = -4;
	}	
	
	var markerJoinedText = "";
	for(var x=0;x<markersData.length;x++)
	{
		if(imageName == CurrentImage)
		{	
			markerArray.push([markersData[x].x,markersData[x].y]);
			markerJoinedText += "<DIV id='"+"Marker"+markerCounter2+"' style='" + "background-color:#"+markerColor+
			";height:6px;width:6px;position:absolute;left:"+(markersData[x].x +offSetLeft+posLeftOffset)+"px;top:"+(markersData[x].y + offSetTop+posTopOffset)+
			"px;z-index:100;border: 1px solid black;font-size:0px;" + "' name='Marker' " + 
			"onMouseOver=\"this.style.backgroundColor='red';\" onMouseOut=\"this.style.backgroundColor='#"+markerColor+"';\" onMouseDown=\"RemoveMarker("+markersData[x].x+
			","+markersData[x].y+",'Marker"+markerCounter2+"',event)\"'></DIV>";
			
			markerCounter2 = markerCounter2 +1;
			markerCounter = markerCounter +1;
		}
	}
	
	document.getElementById('centroblasts').innerHTML = markerCounter;
	document.getElementById('pointer_div').innerHTML = document.getElementById('pointer_div').innerHTML + markerJoinedText;	
}

function AddMarker(pos_x,pos_y)
{
	posLeft = pos_x + offSetLeft;
	posTop = pos_y + offSetTop;
	
	point = [pos_x,pos_y];
	markerArray.push(point);
	
	var ElementText = "";
	
	if(IEVersion == 6 || IEVersion == 7) //IE 6 & 7 for some reason reads points with an offset, so this corrects that.
	{
		var IEmarkerStyle = "background-color:#"+markerColor+";height:6px;width:6px;position:absolute;left:"+(posLeft+6)+"px;top:"+(posTop+11)+"px;z-index:100;border: 1px solid black;font-size:0px;";
		ElementText = "<DIV id='"+"Marker"+markerCounter2+"' style='" + IEmarkerStyle + "' name='Marker' " + "onMouseOver=\"this.style.backgroundColor='red';\" onMouseOut=\"this.style.backgroundColor='#"+markerColor+"';\" onMouseDown=\"RemoveMarker("+pos_x+","+pos_y+",'Marker"+markerCounter2+"',event)\"'></DIV>";
	}
	else
	{
		var markerStyle = "background-color:#"+markerColor+";height:6px;width:6px;position:absolute;left:"+(posLeft-4)+"px;top:"+(posTop-4)+"px;z-index:10;border: 1px solid black;font-size:0px;";
		ElementText = "<DIV id='"+"Marker"+markerCounter2+"' style='" + markerStyle + "' name='Marker' " + "onMouseOver=\"this.style.backgroundColor='red';\" onMouseOut=\"this.style.backgroundColor='#"+markerColor+"';\" onMouseDown=\"RemoveMarker("+pos_x+","+pos_y+",'Marker"+markerCounter2+"',event)\"'></DIV>";
	}	
	document.getElementById('pointer_div').innerHTML = document.getElementById('pointer_div').innerHTML + ElementText;	

	markerCounter2 = markerCounter2 +1;
	markerCounter = markerCounter +1;
	document.getElementById('centroblasts').innerHTML = markerCounter;
}


function removeField(FieldId)
{
	var p2 = document.getElementById(FieldId);
	p2.parentNode.removeChild(p2); 
	markerCounter = markerCounter - 1;
	document.getElementById('centroblasts').innerHTML = markerCounter;
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

var hideBox = true;

function ShowRemove(left,top,x,y,divName)
{
/*
	X_Remove = x;
	Y_Remove = y;
	ID_Remove = UserId;
	PIC_Remove = CurrentImage;
	MARKER_Remove = divName;
*/
	
	document.getElementById('RemoveDiv').style.display = 'block';
	document.getElementById('RemoveDiv').style.position = 'absolute';
	document.getElementById('RemoveDiv').style.left = (left-4)+'px';
	document.getElementById('RemoveDiv').style.top = (top-4)+'px';
	document.getElementById('RemoveDiv').style.zindex = '20000';
}
function HideRemove()
{
	var t=setTimeout("if(hideBox == true){document.getElementById('RemoveDiv').style.display = 'none';}",100);
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
function RemoveMarker(x_point,y_point,divName,event)
{
	var MarkerName = divName;
	if(MarkerName != "")
	{
		var p2 = document.getElementById(MarkerName);
		p2.parentNode.removeChild(p2); 
		markerCounter = markerCounter - 1;
		document.getElementById('centroblasts').innerHTML = markerCounter;
	}
	
	var element = -1;
	for(var x=0;x<markerArray.length;x++)
	{
		var point = markerArray[x];
		var xPos = point[0];
		var yPos = point[1];
		
		if (xPos==x_point && yPos==y_point)
		{
			element = x;
		}
	}
	
	if(element != -1)
	{
	
		markerArray.splice(element,1);
	}
	
	document.getElementById('RemoveDiv').style.display = 'none';
	var xPos = x_point;
	var yPos = y_point;

	loadJSON(currentServer+"?x=" + xPos + "&y=" + yPos + "&pic=" + CurrentImage + "&action=remove","Saving Remove Marker");
	
	event.preventDefault?
	event.preventDefault() : // FF2, K3, S3, O9
	event.returnValue = false; // IE6
}

function RemoveAllMarkers()
{
	markerArray = new Array();
	//var markerElements = document.getElementsByName('Marker'); //this doesnt work for IE6...
	for(var x=0;x<=markerCounter2;x++)
	{
		//var p2 = document.getElementById(markerElements[x].id);
		//p2.parentNode.removeChild(p2); 
		var markerElement = document.getElementById('Marker'+x);
		if(markerElement != null)
		{
			markerElement.parentNode.removeChild(markerElement);
		}
	}
	markerCounter = 0;
	markerCounter2 = 0;
	document.getElementById('centroblasts').innerHTML = markerCounter;
	
	loadJSON(currentServer+"?pic=" + CurrentImage + "&action=removeAll","Remove All Markers");
	
}

function printMarkerPoints()
{
	var tempStr = '';
	for (var x=0;x<markerArray.length;x++)
	{
		tempStr = tempStr + "X: " + markerArray[x][0] + " Y: " + markerArray[x][1] + "<br>";
	}
	document.getElementById('PrintArea').innerHTML = tempStr;
}
function CheckMovement(Id)
{
	//alert(actionArray[Id].complete);
	if(actionArray[Id].complete == false)
	{
		OKDialog("There was an Error<br><br>Sorry for the inconvenience.<br><br> If you could email us the Error Report to #### we can fix any errors faster.<br><br>Error Report<br><textarea style='width:350px;height:100px;margin-left:25px;margin-right:25px;'>"+dump(actionArray,0)+"</textarea><br><br>We reccomend not using this application until we can resolve the error, as data might not be saved.<br><br>Thank You!");
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
	else if(data.name == "getImage")
	{
		AddMarkersFromJSON(data.marks,data.image);
		if(data.marks.length == 0)
		{
			document.getElementById('centroblasts').innerHTML = "0";
		}
		
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
	else
	{
	}
	
}
function backward()
{
	if(CurrentIndex -1 < 0)
	{
		OKDialog('You cannot go back any farther');
	}
	else
	{
		CurrentIndex = CurrentIndex -1;
		loadImage(false,images[CurrentIndex]);
	}
}
function forward()
{
	if(CurrentIndex +1 >= images.length)
	{
		OKDialog('You cannot go ahead any farther');
	}
	else
	{
		CurrentIndex = CurrentIndex +1;
		loadImage(false,images[CurrentIndex]);
	}
}

function loadImage(first,image)
{
	if(first == true)
	{
		//document.getElementById('PrintArea').innerHTML += "<br> Getting Last Image";
		loadJSON(currentServer+'?action=getImgTracking',"Getting Last Image");
	}
	else
	{
		
		markerArray = new Array();
		for(var x=0;x<=markerCounter2;x++)
		{
			var markerElement = document.getElementById('Marker'+x);
			if(markerElement != null)
			{
				markerElement.parentNode.removeChild(markerElement);
			}
		}
		
		markerCounter2 = 0;
		markerCounter = 0;
		
		var ImageName = images[0];
		
		CurrentImage = ImageName;
		if (image != "")
		{
			CurrentImage=image;
		}
		
		document.getElementById('centroblasts').innerHTML = "<img src='loading.gif' border='0'>";
		loadJSON(currentServer+'?action=getImgMarks&pic='+CurrentImage,"Loading Image Marks");
		
		
		//alert(images.length);
		for(var i=0;i<images.length;i++)
		{
			//alert(images[i] + " : " + CurrentImage);
			if(images[i] == CurrentImage)
			{
				CurrentIndex = i;
			}
		}

		document.getElementById('pointer_div').style.backgroundImage = "URL('"+roiDirectory+CurrentImage+"')";
		
		document.getElementById('imageNumber').innerHTML = (CurrentIndex+1) + "/" + images.length;
		
		loadJSON(currentServer+"?pic=" + CurrentImage + "&action=setImgTracking","Saving Image Tracker");
	}
}
function goToImage()
{
	var imageText = document.getElementById('imageText').value;
	if(imageText != "" && Number(imageText) > 0 && Number(imageText) <= images.length)
	{
		loadImage(false,images[(imageText-1)]);
	}
}
</script>
</head>
<body style='height:100%'>
<div id='blackout' style='display:none;position:absolute;height:200%;z-index:100;width:100%;left:0px;top:0px;opacity:0.4;filter:alpha(opacity=40);background-color:black;'></div>
	<div id='infobox' style='text-align:center;display:none;z-index:200;position:absolute;top:50%;margin-top:-200px;left:50%;margin-left:-200px;width:400px;background-color:white;border:solid 1px black;padding:10px;font-family:Arial;'>
	<div id='OKDialog'></div>
	<div id='OKBox' style='width:120px;height:50px;position:relative;margin-left:auto;margin-right:auto;margin-top:20px;'>
		<input type='button' value='Ok' name='Ok' style='width:120px;' onClick='HideOKDialog();'>
	</div>
</div>
<div id='controls' style='float:left; clear:both;'>
	<input type='hidden' value='Print Array' style='font-family:Verdana;' onclick='printMarkerPoints()'>
	<input type='button' value='Clear All Points' style='font-family:Verdana;' onclick='RemoveAllMarkers()'>
	<input type='button' value='|<<' id='backward' style='font-family:Verdana;' onclick='JavaScript:backward()'>
	<input type='button' value='>>|' id='forward' style='font-family:Verdana;' onclick='JavaScript:forward()'>
	<b>CB Count:</b> <div id='centroblasts' style='min-width:16px;min-height:16px;display:inline;font-family:Verdana;padding-right:10px;'>0</div>
	<b>Image:</b> <div id='imageNumber' style='display:inline;font-family:Verdana;padding-right:20px;'>0/0</div>
	<input type='text' value='' id='imageText' style='font-family:Verdana;width:40px;'>
	<input type='button' value='Load Image' id='goToImage' style='font-family:Verdana;' onclick='goToImage()'>
	<input type='button' value='Help' id='hekp' style='font-family:Verdana;' onclick="OKDialog('<H2>Directions:</H2><b>How to Annotate:</b><br> To add markings to the images simply click on the image and where ever you click a green dot will appear. This is automatically saved in the background.<br><br><b>How to Remove a Mark:</b><br>To remove a mark hover over a previous marking and the marker should turn red. When it turns red click on the mark and it will be removed.<br><br><b>How to Change Images:</b><br>To move ahead to the next image click the >>| button. To move backwards to the previous image click the |<< button. Sometimes the image may take a short while to load.<br><br><b>Contact Us:</b><Br>You can contact us at #### with any other questions.')">
</div>

<div id="pointer_div" onclick="point_it(event,this)" style = "background-image:url('');width:2168px;height:1353px;float:left;clear:both;"></div>


<div id='PrintArea' style='float:left;clear:both;'></div>
<div id='messageID' style='display:none;'></div>
<div id='RemoveDiv' style='display:none;height:8px;width:8px;background-color:red;z-index:2000;position:absolute;font-size:0px;' onMouseOver='OverDiv()' onMouseOut='OutDiv()' onMouseDown='RemoveMarker(event);'></div>

<div id='RemoveDiv2' style='display:none;padding-left:1px;padding-right:1px;color:black;border: 1px solid black;background-color:white;z-index:999999;font-family:Verdana;font-size:14;' onMouseOver='OverDiv()' onMouseOut='OutDiv()'><a href='JavaScript:RemoveMarker(event);' style='color:black;text-decoration:none;'>Remove</a></div>
</div>
</body>
</html>