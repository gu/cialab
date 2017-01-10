//Riffer (erase later)
function tempFunction()
{
	//alert(tempMarkersX + "\n" + tempMarkersY);
	//alert(CurrentImage);
	//console.info(tempMarkersX);
	//console.info(tempMarkersY);
	for(var i = 0; i < tempMarkersX.length; i++)
	{
		loadJSON(currentServer+"?x=" + tempMarkersX[i] + "&y=" + tempMarkersY[i] + "&pic=" + tempFileName + "&action=tempAction" + '&pid='+ pid + '&colorvalueid=' + 5);
	}
	tempMarkersX = new Array();
	tempMarkersY = new Array();
}

function point_it(event,obj)
{	
	var offset = obj.offset();
	var Marker;
	
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
	
	//$('#testtextbox').attr('value','X: ' + pos_x + '   Y: ' + pos_y);
	
	//Create and Add Marker
	if(projectType == 8)
	{
		if(drawingColor == "green")
		{
			Marker = createMarker(userid,"#00FF00",pos_x,pos_y,0,100,1);
			colorVal = 100; 
			posCount++;
		}
		else //red
		{
			Marker = createMarker(userid,"#FF0000",pos_x,pos_y,0,100,1);
			colorVal = 200;
			negCount++;
		}
	}
	else if(projectType == 9)
	{
		if(drawingColor == "green")
		{
			Marker = createMarker(userid,"#00FF00",pos_x,pos_y,0,100,1);
			colorVal = 100; 
			RvRCounts[0]++;
		}
		else if(drawingColor == "red")
		{
			Marker = createMarker(userid,"#FF0000",pos_x,pos_y,0,100,1);
			colorVal = 200; 
			RvRCounts[1]++;		
		}
		else if(drawingColor == "blue")
		{
			Marker = createMarker(userid,"#0000FF",pos_x,pos_y,0,100,1);
			colorVal = 300; 
			RvRCounts[2]++;		
		}
		else if(drawingColor == "black")
		{
			Marker = createMarker(userid,"#000000",pos_x,pos_y,0,100,1);
			colorVal = 400; 
			RvRCounts[3]++;		
		}
		else if(drawingColor == "pink")
		{
			Marker = createMarker(userid,"#FF99FF",pos_x,pos_y,0,100,1);
			colorVal = 500; 
			RvRCounts[4]++;		
		}		
		else if(drawingColor == "purple")
		{
			Marker = createMarker(userid,"#CC00FF",pos_x,pos_y,0,100,1);
			colorVal = 600; 
			RvRCounts[5]++;
		}
	}
	else if(projectType == 10)
	{
		if(drawingColor == "green")
		{
			Marker = createMarker(userid,"#00FF00",pos_x,pos_y,0,100,1);
			colorVal = 100; 
			acneCounts[0]++;
		}
		else if(drawingColor == "red")
		{
			Marker = createMarker(userid,"#FF0000",pos_x,pos_y,0,100,1);
			colorVal = 200; 
			acneCounts[1]++;		
		}
		else if(drawingColor == "blue")
		{
			Marker = createMarker(userid,"#0000FF",pos_x,pos_y,0,100,1);
			colorVal = 300; 
			acneCounts[2]++;		
		}
		else if(drawingColor == "black")
		{
			Marker = createMarker(userid,"#000000",pos_x,pos_y,0,100,1);
			colorVal = 400; 
			acneCounts[3]++;		
		}
				else if(drawingColor == "pink")
		{
			Marker = createMarker(userid,"#FF99FF",pos_x,pos_y,0,100,1);
			colorVal = 500; 
			acneCounts[4]++;		
		}		
		else if(drawingColor == "purple")
		{
			Marker = createMarker(userid,"#CC00FF",pos_x,pos_y,0,100,1);
			colorVal = 600; 
			acneCounts[5]++;
		}
	}
	else
	{
		Marker = createMarker(userid,defaultMarkerColor,pos_x,pos_y,0,100,1);
	}

	$('#pointer_div').append(Marker);
	
	//alert("pos_x: " + pos_x + "  pos_y: " + pos_y);
	
	//Add it to the point array
	markerArray[userid].push([pos_x,pos_y]);

	//Save to Server
	if(projectType == 8 || projectType == 9 || projectType == 10)
	{
		loadJSON(currentServer+"?x=" + pos_x + "&y=" + pos_y + "&pic=" + CurrentImage + "&action=add" + '&pid='+pid+'&after_review=' + convertBoolToInt(reviewing) + "&colorvalueid=" + colorVal,"Saving Marker");
	}
	else
	{
		loadJSON(currentServer+"?x=" + pos_x + "&y=" + pos_y + "&pic=" + CurrentImage + "&action=add" + '&pid='+pid+'&after_review=' + convertBoolToInt(reviewing),"Saving Marker");
	}

	//Update the CB count
	updateCBCount();
}

function AddMarkersFromJSON(markersData,imageName,color,isReviewSet,idValue,zIndex,opacity,customRemovalFunction)
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
			r_point = markersData[x].r;
			//riffer
			if(projectType == 8)
			{
				if(markersData[x].colorval == 100)
				{
					//green
					color = "#00FF00";
					posCount++;
				}
				else
				{
					//red
					color = "#FF0000";
					negCount++;
				}
			}
			else if(projectType == 9)
			{
				if(markersData[x].colorval == 100)
				{
					//green
					color = "#00FF00";
					RvRCounts[0]++;
				}
				else if(markersData[x].colorval == 200)
				{
					//red
					color = "#FF0000";
					RvRCounts[1]++;
				}
				else if(markersData[x].colorval == 300)
				{
					//blue
					color = "#0000FF";
					RvRCounts[2]++;
				}
				else if(markersData[x].colorval == 400)
				{
					//black
					color = "#000000";
					RvRCounts[3]++;
				}
				else if(markersData[x].colorval == 500)
				{
					//pink
					color = "#FF99FF";
					RvRCounts[4]++;
				}
				else if(markersData[x].colorval == 600)
				{
					//purple
					color = "#CC00FF";
					RvRCounts[5]++;
				}
			}
			else if(projectType == 10)
			{
				if(markersData[x].colorval == 100)
				{
					//green
					color = "#00FF00";
					acneCounts[0]++;
				}
				else if(markersData[x].colorval == 200)
				{
					//red
					color = "#FF0000";
					acneCounts[1]++;
				}
				else if(markersData[x].colorval == 300)
				{
					//blue
					color = "#0000FF";
					acneCounts[2]++;
				}
				else if(markersData[x].colorval == 400)
				{
					//black
					color = "#000000";
					acneCounts[3]++;
				}
				else if(markersData[x].colorval == 500)
				{
					//pink
					color = "#FF99FF";
					acneCounts[4]++;
				}
				else if(markersData[x].colorval == 600)
				{
					//purple
					color = "#CC00FF";
					acneCounts[5]++;
				}
			}
			else if(markersData[x].colorval == 5)
			{
				//red
				color = "#FF0000";
			}
			var Marker = createMarker(idValue,color,x_point,y_point,r_point,zIndex,opacity);
			
			//Check if set is review set, if so remove option to delete marker.
			if(isReviewSet)
			{
				Marker.unbind();
			}
			
			if(customRemovalFunction != null)
			{
				Marker.unbind('click');
				Marker.click(customRemovalFunction);
			}
			
			Markers.push(Marker);
		}
	}
	//Add the markers to the pointer div
	$('#pointer_div').append.apply($('#pointer_div'), $.isArray( Markers) ? Markers : [Markers]);
	updateCBCount();
}

function printOutDataMarkers(markersData)
{
	var tempString = "<br>";
	for(var x=0;x<markersData.length;x++)
	{
		tempString += "x: " + markersData[x].x + "  y: " +markersData[x].y + "<br>";
	}
	document.getElementById('PrintArea').innerHTML = tempString;
}

function removeMarker(event,obj)
{
	obj.remove();
	RemovePoint(userid,obj.prop("x_point"),obj.prop("y_point"));
	loadJSON(currentServer+"?action=remove" + "&x=" + obj.prop("x_point") + "&y=" + obj.prop("y_point") + "&pic=" + CurrentImage +'&pid='+pid,"Saving Remove Marker");
	//Stop event from bubbling, (this stops it from making a new mark)
	event.stopImmediatePropagation();
	
	updateCBCount();
}

function removeReviewMarker(event,obj)
{
	obj.remove();
	RemovePoint(userid,obj.prop("x_point"),obj.prop("y_point"));
	loadJSON(currentServer+"?action=removeReviewMarker" + "&x=" + obj.prop("x_point") + "&y=" + obj.prop("y_point") + "&pic=" + CurrentImage +'&pid='+pid,"Saving Remove Marker Review");
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

function getCBCount()
{
	if(markerArray[userid] == null)
	{
		markerArray[userid] = new Array();
	}
	return markerArray[userid].length;
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
	loadJSON(currentServer+"?pic=" + CurrentImage + "&action=removeAllMarkers"+'&pid='+pid,"Remove All Markers");
	
	posCount = 0;
	negCount = 0;
	
	for(var i = 0; i < 6; i++)
	{
		RvRCounts[i] = 0;
	}
	
	for(var i = 0; i < 6; i++)
	{
		acneCounts[i] = 0;
	}
}

function resetReviewSet()
{
	$('div[name='+'Marker_' + 'editable_review_set' + ']').remove();
	markerArray['editable_review_set'] = new Array();
	
	//Save Action on Server
	loadJSON(currentServer+"?pic=" + CurrentImage + "&action=resetReviewSet"+'&pid='+pid,"Reset Review Set");
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
		
		//alert("Attempt: " + actionArray[Id].attempt + "\n" + "URL: " + actionArray[Id].url);
		
		//Resubmit query again
		baseLoadJSON(actionArray[Id].url,actionArray[Id].action,actionArray[Id].attempt);
	}
	else if(actionArray[Id].complete == false && actionArray[Id].attempt >= maxAttemptCount)
	{
		DialogBox("There was a network connection error.<br><br>Sorry for the inconvenience.<br><br> If you could email us the Error Report to schmidt.553@osu.edu we can fix any errors faster.<br><br>Error Report<br><textarea style='width:350px;height:100px;margin-left:25px;margin-right:25px;'>"+dump(actionArray,0)+"</textarea><br><br>We recomend not using this application until we can resolve the error, as data might not be saved.<br><br>Thank You!");
	}
}

function loadJSONarray(urlArray, currentColor, annotType)
{
	var headID = document.getElementById;
	var headID = document.getElementsByTagName("head")[0];
	
	var url = currentServer + "?action=setAnnotCoord" + "&pic=" + CurrentImage +'&pid=' + pid + 
			  '&drawingColor=' + currentColor + '&annotType=' + annotType + "&isNewLine=1";

	//if(annotType == "line")
	//{
	//	url += "&isNewLine=1";
	//}
			  
	for(var i = 0; i < urlArray.length; i++)
	{
		url += "&coordArray[]=" + urlArray[i];
		
		//send a new request every 400 points
		if(i % 400 == 0 && i != 0)
		{
			var newScript = document.createElement('script');
			newScript.type = 'text/javascript';
			newScript.src = url+"&move="+movementCounter;
			headID.appendChild(newScript);
			movementCounter = movementCounter + 1;
			sleep(100);
			url = currentServer + "?action=setAnnotCoord" + "&pic=" + CurrentImage +'&pid=' + pid + 
				  '&drawingColor=' + currentColor + '&annotType=' + annotType;
		}
	}
	
	var newScript = document.createElement('script');
    newScript.type = 'text/javascript';
    newScript.src = url+"&move="+movementCounter;
    headID.appendChild(newScript);
	movementCounter = movementCounter + 1;
}

function loadJSONimageArray()
{
	var headID = document.getElementById;
	var headID = document.getElementsByTagName("head")[0];
	
	var url = currentServer + '?action=getImagePercent&pid=' + pid;
	
	for(var i = 0; i < images.length; i++)
	{
		url += "&pic[]=" + images[i];
	}
	
	var newScript = document.createElement('script');
    newScript.type = 'text/javascript';
    newScript.src = url+"&move="+movementCounter;
    headID.appendChild(newScript);
	movementCounter = movementCounter + 1;
}

function baseLoadJSON(url,action,attempt) 
{
	var d=new Date();
	var reAttempt = setTimeout("CheckMovement("+movementCounter+");",10000); 
	actionArray[movementCounter] = {"action":action,"url":url,"move":movementCounter,"attempt":attempt,"complete":false,"errors":"","date":d.toUTCString()};
	var headID = document.getElementsByTagName("head")[0];     
	var newScript = document.createElement('script');
      newScript.type = 'text/javascript';
      newScript.src = url+"&move="+movementCounter;
      
	headID.appendChild(newScript);
	movementCounter = movementCounter + 1;
	if(debug == true)
	{
		document.getElementById('PrintArea').innerHTML = dump(actionArray,0,true);
	}
	clearTimeout(reAttempt);
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
		if(debug == true)
		{
			document.getElementById('PrintArea').innerHTML = dump(actionArray,0,true);
		}
	}
	if(data.name == "getImgTracking")
	{
		loadImage(data.image);
	}
	else if(data.name == "getImgMarks")
	{
		AddMarkersFromJSON(data.marks,data.image,defaultMarkerColor, false,userid,102,1);	
	}
	else if(data.name == "getUsersImgMarks")
	{
		markerColor = markerColors.splice(0,1);
		markerHolder.splice(0,0,{"name":data.user,"color":markerColor});
		AddMarkersFromJSON(data.marks,data.image,markerColor, true,data.user,101,1);
	}
	else if(data.name == "getReviewImgMarks")
	{
		//printOutDataMarkers(data.marks);
		markerColor = markerColors.splice(0,1);
		markerHolder.splice(0,0,({"name":"review_set","color":markerColor}));
		$("#s3").val("review_set").selected = true;
		$("#s3").dropdownchecklist("refresh");
		AddMarkersFromJSON(data.marks,data.image,markerColor, true,"review_set",100,.6);
	}
	else if(data.name == "getEditableReviewImgMarks")
	{
		//printOutDataMarkers(data.marks);
		markerColor = markerColors.splice(0,1);
		markerHolder.splice(0,0,({"name":"editable_review_set","color":markerColor}));
		AddMarkersFromJSON(data.marks,data.image,markerColor, false,"editable_review_set",100,1,function(event){removeReviewMarker(event,$(this));});
	}
	else if(data.name == "imgTackingUpdated")
	{

	}
	else if(data.name == "resetReviewSet")
	{
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
		if(data.status = 'true' && data.dropDownData!= '')
		{
			//Dropdown data already set
			//set the selected value and disable the select
			$('#data_selection').val(data.dropDownData).attr('selected',true);
			$('#data_selection').attr('disabled', 'disabled');
			
			//Dropdown data for Secondary Grade if needed		
			$('#data_selection_2').val(data.dropDownData2).attr('selected',true);
			$('#data_selection_2').attr('disabled', 'disabled');
			
			//Setup the action for clicking on the image
			if(projectType == 4)
			{	
				
				disablePreview();
				$('#pointer_div').unbind();
				if(dataSetName != "dataset_2"){
					$('#pointer_div').bind('click', function(event){point_it(event,$(this));});
				
				}
			}
		}
		else if(data.status = 'true' && data.dropDownData== '')
		{
			//Drop Down Data not set yet.
			//set the default select value and enable the select
			$('#data_selection').val('').attr('selected',true);
			$('#data_selection').removeAttr('disabled');
			
			//Drop Down Data for Secondary Grade if needed
			$('#data_selection_2').val('EMPTY').attr('selected',true);
			$('#data_selection_2').attr('disabled', 'disabled');
			
			//Setup the action for clicking on the image
			if(projectType == 4)
			{	
				//Setup a popup to remind the user that they must select the drop down data first
				$('#pointer_div').unbind();
				if(dataSetName == "dataset_2"){
					$('#pointer_div').bind('click', function(event){DialogBox("Please select the Primary and Secondary Grades first",false,function(){},function(){});});
				}
				else{
					$('#pointer_div').bind('click', function(event){DialogBox("Please select the "+dataSetText+" first",false,function(){},function(){});});
				}
			}
		}
		else
		{
			//Do nothing allow the user to select the correct Drop Down Data
		}
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
	//riffer
	else if(data.name == "getImagePercent")
	{
		arrayOfM1 = data.M1;
		arrayOfM2 = data.M2;
	}
	
	else
	{
	}	
}

function backward()
{
	if(projectType != 7 && projectType != 6 && projectType != 11)
	{
		if(CurrentIndex -1 < 0)
		{
			DialogBox('You cannot go back any farther');
		}
		else
		{
			reviewing = false;
			CurrentIndex = CurrentIndex -1;
			loadImage(images[CurrentIndex]);
			posCount = 0;
			negCount = 0;
		}
	}
	else if(projectType == 6)
	{
		if((CurrentIndex -1) < 0 && isSaved)
		{
			DialogBox('You cannot go back any farther');
		}
		else if(!isSaved)
		{
			saveAnnotations("fromBackward");
		}
		else
		{
			loadPreviousImage();
		}
	}
	
	//Eric Doesn't ask if you want to save, it just does it
	else if(projectType == 11)
	{
		if((CurrentIndex -1) < 0 && isSaved)
		{
			DialogBox('You cannot go back any farther');
		}
		else if(!isSaved)
		{
			saveAnnotations("fromBackward");
		}
		else
		{
			loadPreviousImage();
		}
	}
	
	else
	{
		if(CurrentIndex - 1 < 0)
		{
			savePercentValues(true);
			if(isValidPercentages)
			{
				DialogBox('You cannot go back any farther');
				isValidPercentages = false;
			}
		}
		else
		{
			savePercentValues(true);
			if(isValidPercentages)
			{
				var M1 = parseFloat($('#M1Percent').val());
				var M2 = parseFloat($('#M2Percent').val());
				if(M1 != 0 && M2 != 0)
				{
					setTimeout(function()
					{
						loadPreviousImage();
					}, 1000);
				}
			}
			isValidPercentages = false;
		}
	}
}
function forward()
{
	//Check project types
	
	//This can be simplified but it is easier to read when broken up by project
	//types.
	if(projectType == 0)
	{
		if(CurrentIndex +1 >= images.length)
		{
			DialogBox('You cannot go ahead any farther');
		}
		else if(getCBCount() == 0)
		{
			var yesFunction = function()
			{
				loadJSON(currentServer + "?action=setNoMarks" +"&pic=" + CurrentImage +'&pid='+pid,"Set that the user confirmed no markings on this image");
				CurrentIndex = CurrentIndex +1;
				loadImage(images[CurrentIndex]);
				HideDialog();
			}
			
			var noFunction = function()
			{
				HideDialog();
			}
			
			DialogBox("It appears there are no markings. Are you sure you want to skip to the next image?",true,yesFunction,noFunction);
		}
		else
		{
			CurrentIndex = CurrentIndex +1;
			loadImage(images[CurrentIndex]);
		}
	}
	else if(projectType == 1)
	{
		if((CurrentIndex +1) >= images.length && reviewing == true)
		{
			DialogBox('You cannot go ahead any farther');
		}
		else if(getCBCount() == 0 && reviewing == true)
		{
			var yesFunction = function()
			{
				loadJSON(currentServer + "?action=setNoMarks" +"&pic=" + CurrentImage +'&pid='+pid,"Set that the user confirmed no markings on this image");
				reviewing=false;
				loadNextImage();
				document.getElementById('forward').value = ">>| (CaIA)";
				HideDialog();
			}
			var noFunction = function(){HideDialog();}
			DialogBox("It appears there are no markings. Are you sure you want to skip to the next image?",true,yesFunction,noFunction);
		}
		else if(getCBCount() == 0 && reviewing == false)
		{
			var yesFunction = function(){reviewing = true;loadReviewMarks();HideDialog();}
			var noFunction = function(){HideDialog();}
			DialogBox("It appears there are no markings. Are you sure you want to review this image?",true,yesFunction,noFunction);
		}
		else if(getCBCount() != 0 && reviewing == false)
		{
			reviewing = true;
			loadReviewMarks();
		}
		else
		{
			document.getElementById('forward').value = ">>| (CaIA)";
			reviewing = false;
			loadNextImage();
		}
	}
	else if(projectType == 2)
	{
		if((CurrentIndex +1) >= images.length)
		{
			DialogBox('You cannot go ahead any farther');
		}
		else if(getCBCount() == 0)
		{
			var yesFunction = function()
			{
				loadJSON(currentServer + "?action=setNoMarks" +"&pic=" + CurrentImage +'&pid='+pid,"Set that the user confirmed no markings on this image");
				loadNextImage();
				HideDialog();
			}
			var noFunction = function(){HideDialog();}
			DialogBox("It appears you have made no markings. Are you sure you want to skip to the next image?",true,yesFunction,noFunction);
		}
		else
		{
			loadNextImage();
		}
	}
	else if(projectType == 3)
	{
		if((CurrentIndex +1) >= images.length)
		{
			DialogBox('You cannot go ahead any farther');
		}
		else
		{
			loadNextImage();
		}
	}
	else if(projectType == 4)
	{
		if((CurrentIndex +1) >= images.length)
		{
			DialogBox('You cannot go ahead any farther');
		}
		else if($('#data_selection option:selected').val()=='')
		{
			DialogBox("It appears you have not selected the "+dataSetText+" yet.",false,function(){},function(){});
		}
		//If primary grade is selected and secondary is not
		else if($('#data_selection option:selected').val()!='' && dataSetName == "dataset_2" && $('#data_selection_2 option:selected').val()=='EMPTY')
		{
			DialogBox("It appears you have not selected the Secondary Grade yet.",false,function(){},function(){});	
		}
		//Molly commented all of this else if out to eliminate the extra box telling the user that they did not mark any points.
		//This functionality is not needed currently and creates an extra click for the pathologist which is unnecessary. 
		//else if(getCBCount() == 0 && dataSetName != "dataset_2")
		//{
			//var yesFunction = function()
			//{
				//loadJSON(currentServer + "?action=setNoMarks" +"&pic=" + CurrentImage +'&pid='+pid,"Set that the user confirmed no markings on this image");
				//CurrentIndex = CurrentIndex +1;
				//loadImage(images[CurrentIndex]);
				//HideDialog();
			//}
			//var noFunction = function(){HideDialog();}
			//DialogBox("It appears there are no markings. Are you sure you want to skip to the next image?",true,yesFunction,noFunction);
		//}
		else
		{
			loadNextImage();
		}
	}
	else if(projectType == 6)
	{
		if((CurrentIndex +1) >= images.length && isSaved)
		{
			DialogBox('You cannot go ahead any farther');
		}
		else if(!isSaved)
		{
			saveAnnotations("fromForward");
		}
		else
		{
			loadNextImage();
		}
	}
	
	//Eric: Doesn't ask if you want to save, it just does it
	else if(projectType == 11)
	{
		//saveAnnotations("button");
		if((CurrentIndex +1) >= images.length && isSaved)
		{
			DialogBox('You cannot go ahead any farther');
		}
		
		else if(!isSaved)
		{
			saveAnnotations("fromForward");
			/*saveAnnotationsToDB("line");
			saveAnnotationsToDB("rect");
			saveAnnotationsToDB("marker");
			isSaved = true;
			//HideDialog();
			loadNextImage();*/
		}
		
		else
		{
			loadNextImage();
		}
		
		
	}
	
	else if(projectType == 8)
	{
		if((CurrentIndex +1) >= images.length)
		{
			DialogBox('You cannot go ahead any farther');
		}
		else
		{
			loadNextImage();	
			posCount = 0;
			negCount = 0;
		}
	}
	else if(projectType == 9)
	{
		if((CurrentIndex +1) >= images.length)
		{
			DialogBox('You cannot go ahead any farther');
		}
		else
		{
			for(var i = 0; i < 6; i++)
			{
				RvRCounts[i] = 0;
			}
			loadNextImage();	

		}	
	}
	else if(projectType == 10)
	{
				if((CurrentIndex +1) >= images.length)
		{
			DialogBox('You cannot go ahead any farther');
		}
		else
		{
			for(var i = 0; i < 6; i++)
			{
				acneCounts[i] = 0;
			}
			loadNextImage();	

		}	
	}
	
	else if(projectType == 7)
	{
		if((CurrentIndex +1) >= images.length)
		{
			savePercentValues(true);
			if(isValidPercentages)
			{	
				//fill arrayOfM1 and arrayOfM2
				loadJSONimageArray();
				
				$.ui.dialog.prototype.test = function()
				{

					alert("test");
				}
				$("<div style='text-align:center;color:red' id='loadingDE'>Loading Data Evaluation. Please wait.</div>").dialog(
				{
					open: function()
					{
						setTimeout(function()
						{
							$('#loadingDE').dialog("close");
						}, 3000);
					}
				});
				
				setTimeout(function()
				{		
					var tableString = "<div><table border='1'><tr><td><b>Image Number</b></td><td><b>Image Name</b></td><td><b>M1</b></td><td><b>M2</b></td></tr>";
					for(var i = 0; i < images.length; i++)
					{
						if(arrayOfM1[i] == 0 || arrayOfM2[i] == 0)
						{
							tableString += "<tr style='color:red'><td>" + (i+1) + "</td><td>";
						}
						else
						{
							tableString += "<tr><td>" + (i+1) + "</td><td>";
						}
						tableString += images[i] + "</td><td>";
						tableString += arrayOfM1[i];
						tableString += "</td><td>";	
						tableString += arrayOfM2[i];					
						tableString += "</td></tr>";
					}
					tableString += "</table>";
					
					$(tableString).dialog({
						width: 1000
					});				
				}, 3000);
				
				isValidPercentages = false;
			}
			
		}
		else
		{
			savePercentValues(true)
			if(isValidPercentages)
			{
				var M1 = parseFloat($('#M1Percent').val());
				var M2 = parseFloat($('#M2Percent').val());
				if(M1 != 0 && M2 != 0)
				{
					setTimeout(function()
					{
						loadNextImage();
					}, 1000);
				}			
			}
			isValidPercentages = false;
		}
	}
}

function loadNextImage()
{
	//This if statement is redundent but it will catch
	//if the bounds are not checked earlier.
	if((CurrentIndex + 1) > images.length)
	{
		DialogBox('You cannot go ahead any farther. <br> You have reached the end of the images.');
	}
	else
	{
		CurrentIndex = CurrentIndex +1;
		loadImage(images[CurrentIndex]);
	}
}

function loadPreviousImage()
{
	//This if statement is redundent but it will catch
	//if the bounds are not checked earlier.
	if((CurrentIndex - 1) > images.length)
	{
		DialogBox('You cannot go back any farther. <br> You have reached the beginning of the images.');
	}
	else
	{
		CurrentIndex = CurrentIndex - 1;
		loadImage(images[CurrentIndex]);
	}
}

function setupCBMarker()
{
	//Setup minor tweaks to UI
	if(projectType == 1)
	{
		document.getElementById('forward').value = ">>| (CaIA)";
	}
	
	//Set Offsets
	offSetLeft = document.getElementById("pointer_div").offsetLeft;
	offSetTop = document.getElementById("pointer_div").offsetTop;
	
	//Load First Image and markings
	loadFirstImage();
	
	//Check IE Version
	IEVersion = msieversion();
	
	//Load the drop down Menu
	DropDownCode = document.getElementById('user_review').innerHTML;
	loadDropDown();
	

	//Setup the action for clicking on the image
	if( projectType == 0 || projectType == 1 || projectType == 2 || projectType == 8 || projectType == 9 || projectType == 10)
	{
		$('#pointer_div').bind('click', function(event){point_it(event,$(this));});
	}
	else if(projectType == 3)
	{
		//Do nothing when the user clicks on the image, this project is for viewing only
		$('#pointer_div').bind('click', function(event){point_it(event,$(this));});
	}
	else if(projectType == 4)
	{	
		//Setup a popup to remind the user that they must select the drop down data first
		$("#previewDiv").unbind();
		if(dataSetName == "dataset_2"){
			$("#previewDiv").bind('click', function(event){DialogBox("Please select the Primary and Secondary Grades first",false,function(){},function(){});});
		}
		else{
			$("#previewDiv").bind('click', function(event){DialogBox("Please select the "+dataSetText+" first",false,function(){},function(){});});
		}
		
		//hide the pointer div
		$('#pointer_div').css('display','none');
		
		//$('#pointer_div').bind('click', function(event){DialogBox("Please select the "+dataSetText+" first",false,function(){},function(){});});
	}
	
	
	//Setup IE 6 & 7 Offsets
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
}
function disablePreview()
{
	//Disable the previewDiv
	if(dataSetName != "dataset_2"){
		$('#previewDiv').css('display','none');
	}
	
	//remove bindings
	$('#previewDiv').unbind();
	
	//Remove image from preview
	if(dataSetName != "dataset_2"){
		$("#previewImage").attr("src", "");
	}
	
	//Enable the pointer_div
	if(dataSetName != "dataset_2"){
		$('#pointer_div').css('display','block');
	}
	
	//Disable the select box
	$('#data_selection').attr('disabled', 'disabled');
	
	//Disable the Secondary Grade box if necessary
	if(dataSetName == "dataset_2"){
		$('#data_selection_2').attr('disabled', 'disabled');
	}
	
	//Remove other binds when clicking on the image
	$('#pointer_div').unbind();
	
	//Bind clicking on the image back to normal
	if(dataSetName != "dataset_2"){
		$('#pointer_div').bind('click', function(event){point_it(event,$(this));});
	}
}

function dataSelected(dontPrompt)
{
	//Get Drop Down Data Value
	var selectedValue = $('#data_selection option:selected').val();
	var selectedText = $('#data_selection option:selected').text();
	
	//Get Secondary Grade values if needed
	var selectedValue_2 = $('#data_selection_2 option:selected').val();
	var selectedText_2 = $('#data_selection_2 option:selected').text();
	
	if(dontPrompt == false && selectedValue != 0) //6 means the selectedValue is PASS
	{
		//If both the primary and secondary grade are not selected
		if(selectedValue_2 == 'EMPTY'){
			$('#data_selection_2').removeAttr('disabled');
		}
		//If primary is selected and secondary isn't
		else if(dataSetName == 'dataset_2'){
			DialogBox("The Primary and Secondary Grade can not be changed after you have made your selection.<br><br> Do you want to lock in: " + selectedText + " and " + selectedText_2,true,function(){dataSelected(true);HideDialog();},function(){$('#data_selection').val('').attr('selected',true);$('#data_selection_2').val('EMPTY').attr('selected',true);$('#data_selection_2').attr('disabled', 'disabled');HideDialog();});
		}
		//If primary is not selected
		//else if(dataSetName == 'dataset_2' && document.getElementById('data_selection').disabled == false){
		//	DialogBox("The "+dataSetText+" can not be changed after you have made your selection.<br><br> Do you want to lock in: " + selectedText,true,function(){HideDialog();$('#data_selection').attr('disabled','disabled');$('#data_selection_2').removeAttr('disabled');
//},function(){$('#data_selection').val('').attr('selected',true);HideDialog();});
			//$('#data_selection').attr('disabled','disabled');
			//$('#data_selection_2').removeAttr('disabled');
//		}
		//If project doesn't need Secondary grade	
		// This locks in the dropdown data that is selected.
		else{
			//Make sure user selected the correct Drop Down Data
			DialogBox("The "+dataSetText+" can not be changed after you have made your selection.<br><br> Do you want to lock in: " + selectedText,true,function(){dataSelected(true);HideDialog();},function(){$('#data_selection').val('').attr('selected',true);HideDialog();});
			if(dataSetName == "dataset_17")
			{
				// MOLLYBAUMANN
				loadnextimage();
				
			}
		}		
	}
	else
	{
		disablePreview();

		//Send the selection data to server
		if(selectedValue == 0){ 
			loadJSON(currentServer+"?pic=" + CurrentImage + "&action=setDropDownData"+'&pid='+pid + '&dropdowndataid=' + selectedValue + '&dropdowndataid2=' + 0,"Set the Drop Down Data");
		}
		else{
			loadJSON(currentServer+"?pic=" + CurrentImage + "&action=setDropDownData"+'&pid='+pid + '&dropdowndataid=' + selectedValue + '&dropdowndataid2=' + selectedValue_2,"Set the Drop Down Data");
		}
		// Molly added dataset_17 as well to automatic jump
		//This section will allow for the automatic jumping to the next image after the value has been locked in.
		if(dataSetName == "dataset_2" || dataSetName == "dataset_17" || dataSetName == "dataset_19" || dataSetName == "dataset_21"){
			loadNextImage();
			//Let user know they have finished grading.
			if(CurrentIndex == 0){
				alert("You have finished grading this set.");
			};
		}
	}
}

function resetAllMarkers()
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
}

function loadUserMarkings()
{
	//Show a loading image to the user while waiting for image marks to load.
	document.getElementById('centroblasts').innerHTML = "<img src='loading.gif' border='0'>";
	
	//Load the image marks.
	loadJSON(currentServer+'?action=getImgMarks&pic='+CurrentImage+'&pid='+pid,"Loading Image Marks");
}

function loadFirstImage()
{
	loadJSON(currentServer+'?action=getImgTracking&pid='+pid,"Getting Last Image");
}

function returnImageIndex(image)
{
	for(var i=0;i<images.length;i++)
	{
		if(images[i] == image)
		{
			return i;
		}
	}
	return 0;
}

function loadImage(image)
{
	//Reset All Markers
	resetAllMarkers();
	
	//Set current Image and current Index
	CurrentIndex = returnImageIndex(image);
	CurrentImage = images[CurrentIndex];
	
	//Load the acutal image that is to be viewed
	document.getElementById('pointer_div').style.backgroundImage = "URL('"+roiDirectory+CurrentImage+"')";
	
	//Set the UI Image Number
	document.getElementById('imageNumber').innerHTML = (CurrentIndex+1) + "/" + images.length;
	
	//Set the tracker for this image so we know the user was on last.
	loadJSON(currentServer+'?pic=' + CurrentImage + '&action=setImgTracking&pid='+pid,'Saving Image Tracker');

	//Load all the people who marked that image and reset the color marker array counter.
	loadJSON(currentServer+'?action=getUsersForImage&pid='+pid + '&pic=' + CurrentImage ,"Getting All Users Who Marked This Image");
	
	//Load the users markings
	loadUserMarkings();
	
	if (projectType == 2)
	{
		loadEditableReviewMarkings();
	}
	
	if (projectType == 4)
	{
		//hide pointer Div
		$('#pointer_div').css('display','none');
		
		//display previe Div
		$("#previewDiv").css('display','block');
		
		//set previw Image
		$("#previewImage").attr("src", roiDirectory+CurrentImage);

		$("#previewDiv").unbind();
		
		if(dataSetName == "dataset_2"){
			$("#previewDiv").bind('click', function(event){DialogBox("Please select the Primary and Secondary Grades first",false,function(){},function(){});});
		}
		else{
			//Setup a popup to remind the user that they must select the drop down data first
			$("#previewDiv").bind('click', function(event){DialogBox("Please select the "+dataSetText+" first",false,function(){},function(){});});
		}
	
		//get the selected value for the data to be selected
		loadJSON(currentServer + "?action=getDropDownData" + "&pic=" + CurrentImage + '&pid='+pid,"Get the Drop Down Data");
	}
	
	if(projectType == 6 || projectType == 11){
		currentImage = roiDirectory + images[CurrentIndex];
		
		$('#canvasDiv').css("background", "url(" + currentImage + ")");

		//get image naturalHeight and naturalWidth
		tempImg = document.createElement('img');
		tempImg.setAttribute('src', currentImage);
		tempImg.onload = function()
		{
			$('#canvasDiv').css("width", tempImg.naturalWidth);
			$('#canvasDiv').css("height", tempImg.naturalHeight);
			loadAnnotations(lineCoords, lineColors, rectCoords, rectColors, markerCoords, markerColors, tempImg.naturalWidth, tempImg.naturalHeight);
			loadMarkerCategories(catArray);
		}
		
		//put image name in header
		if (currentServerImage != images[CurrentIndex])
		{
			window.location.href = "index.php?pid=" + pid + "&pic=" + images[CurrentIndex];
		}
	}
	
	if(projectType == 7)
	{
		currentImage = roiDirectory + images[CurrentIndex];
		$('#project7ImageDiv').css("background", "url(" + currentImage + ")");
		
		//put image name in header
		if (currentServerImage != images[CurrentIndex])
		{
			window.location.href = "index.php?pid=" + pid + "&pic=" + images[CurrentIndex];
		}
	}
	
	if(projectType == 8)
	{
		posCount = 0; 
		negCount = 0;
	}
	
	if(projectType == 9)
	{
		for(var i = 0; i < 6; i++)
		{
			RvRCounts[i] = 0;
		}
	}
	
	if(projectType == 10)
	{
		for(var i = 0; i < 6; i++)
		{
			acneCounts[i] = 0;
		}
	}
}

function savePositivePercentage(M1, M2)
{
	//erase previous percentage
	loadJSON(currentServer + '?action=erasePosPercent&pic=' + CurrentImage + '&pid=' + pid);
	
	//update percentage
	loadJSON(currentServer + '?action=setPosPercent&pic=' + CurrentImage + '&pid=' + pid + '&M1=' + M1 + '&M2='+ M2);
}

function loadPositivePercentage(M1, M2)
{
	if(M1 != undefined)
	{
		$('#M1Percent').val(M1);
	}
	
	if(M2 != undefined)
	{
		$('#M2Percent').val(M2);
	}	
}

function loadEditableReviewMarkings()
{
	//alert('test');
	loadJSON(currentServer+'?action=getEditableReviewImgMarks&pic='+CurrentImage+'&pid='+pid,"Loading Editable Review Image Marks");
	//loadJSON(currentServer+'?action=getReviewImgMarks&pic='+CurrentImage+'&pid='+pid,"Loading Review Image Marks");
}

function loadReviewMarks()
{	
	//This disables the user from removing previous marks
	$('div[name='+'Marker_' + userid+']').unbind();

	document.getElementById('forward').value = ">>|";
	loadJSON(currentServer+'?action=getReviewImgMarks&pic='+CurrentImage+'&pid='+pid,"Loading Review Image Marks");
}

function goToImage()
{
	reviewing = false;
	var imageText = document.getElementById('imageText').value;
	if(imageText != "" && Number(imageText) > 0 && Number(imageText) <= images.length)
	{
		loadImage(images[(imageText-1)]);
	}
}


