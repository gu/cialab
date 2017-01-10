var canvas;
var canvasWidth;
var canvasHeight;
var arrayOfCoords = new Array();
var gl; //instantiated in loadLines
//drawing tool
var lines = new Array();
var lineColor = new Array();
var numOfLines = 0; //can't figure out why it increments extra
var linePaint = false;
//marker tool
var markers = new Array();
var markerColor = new Array();
var numOfMarkers = 0;
var markerPaint = false;
//rect tool
var rects = new Array();
var rectColor = new Array();
var numOfRects = 0;
var initRectX = 0;
var initRectY = 0;
var rectPaint = false;
//needed for saving to database
var coordsBeforeConvert = new Array();
var lineCoordsBeforeConvert = new Array();
var rectCoordsBeforeConvert = new Array();
var markerCoordsBeforeConvert = new Array();
//for undo and redo
var undoActionQueue = new Array();
var redoActionQueue = new Array();
var erasedAnnotationArrays = new Array();

//not currently in use
function sleep(miliseconds) 
{
    var currentTime = new Date().getTime();
    while (currentTime + miliseconds >= new Date().getTime())
	{
    }
}

//mediates interaction with the canvas
function prepareCanvas()
{
	//Click on the canvas
	$('#canvasDiv').mousedown(function(e){
		if(drawingColor != "dragging" && !isSaving)
		{
			if(currTool == "pencil")
			{
				arrayOfCoords.length = 0;
				linePaint = true;
				coordsBeforeConvert.push(e.pageX - this.offsetLeft);
				coordsBeforeConvert.push(e.pageY - this.offsetTop);
				convertCoordsToWebGLFormat(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
			}
			else if(currTool == "rect")
			{
				arrayOfCoords.length = 0;
				rectPaint = true;
				coordsBeforeConvert.push(e.pageX - this.offsetLeft);
				coordsBeforeConvert.push(e.pageY - this.offsetTop);
				convertCoordsToWebGLFormat(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
				initRectX = arrayOfCoords[0];
				initRectY = arrayOfCoords[1];
				
				//draw current rect
				vertices = [initRectX, initRectY,
					initRectX+.01, initRectY,
					initRectX+.01, initRectY-.01,
					initRectX, initRectY-.01,
					initRectX, initRectY]
				webGLDraw(vertices, "LINE_STRIP", drawingColor);
				
				arrayOfCoords = vertices;
			}
			else if(currTool == "marker")
			{
				renderPreviousAnnotations();
				markerPaint = true;
				
				//get marker position
				arrayOfCoords.length = 0;
				coordsBeforeConvert.push(e.pageX - this.offsetLeft);
				coordsBeforeConvert.push(e.pageY - this.offsetTop);
				convertCoordsToWebGLFormat(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
				mouseX = arrayOfCoords[0];
				mouseY = arrayOfCoords[1];
				
				//This will keep marker size the same no matter what size canvas we have
				canvasWidthRatio = canvasWidth/500;
				canvasHeightRatio = canvasHeight/500;
				markerWidth = .02 / canvasWidthRatio;
				markerHeight = .02 / canvasHeightRatio;
				
				//draw current marker
				vertices = [mouseX+markerWidth, mouseY+markerHeight, mouseX-markerWidth, mouseY+markerHeight, mouseX+markerWidth, mouseY-markerHeight,
				mouseX-markerWidth, mouseY-markerHeight, mouseX-markerWidth, mouseY+markerHeight, mouseX+markerWidth, mouseY-markerHeight]
				webGLDraw(vertices, "TRIANGLES", drawingColor);
				
				arrayOfCoords = vertices;
			}
		}
	});
	
	//Drag on the canvas
	$('#canvasDiv').mousemove(function(e){
		if(linePaint && !isSaving)
		{
			renderPreviousAnnotations();
			
			//draw current line
			coordsBeforeConvert.push(e.pageX - this.offsetLeft);
			coordsBeforeConvert.push(e.pageY - this.offsetTop);
			convertCoordsToWebGLFormat(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
			webGLDraw(arrayOfCoords, "LINE_STRIP", drawingColor);
		}
		if(rectPaint && !isSaving)
		{
			renderPreviousAnnotations();
			
			arrayOfCoords.length = 0;
			coordsBeforeConvert[2] = e.pageX - this.offsetLeft
			coordsBeforeConvert[3] = e.pageY - this.offsetTop
			convertCoordsToWebGLFormat(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
			mouseX = arrayOfCoords[0];
			mouseY = arrayOfCoords[1];
			
			//render current rect
			vertices = [initRectX, initRectY,
				mouseX, initRectY,
				mouseX, mouseY,
				initRectX, mouseY,
				initRectX, initRectY]
			webGLDraw(vertices, "LINE_STRIP", drawingColor)
			
			arrayOfCoords = vertices;
		}
		//Highlights annotations to be erased for eraser tool
		if(currTool == "eraser" && !isSaving)
		{		
			var mouseX = e.pageX - this.offsetLeft;
			var mouseY = e.pageY - this.offsetTop;
			
			var isOverLine = false;
			var isOverMarker = false;
			var isOverRect = false;
			
			//MARKER
			for(var i = 0; i < markerCoordsBeforeConvert.length; i++)
			{
				if(mouseX < markerCoordsBeforeConvert[i][0] + 7.5 
				   && mouseX > markerCoordsBeforeConvert[i][0] - 7.5
				   && mouseY < markerCoordsBeforeConvert[i][1] + 7.5
				   && mouseY > markerCoordsBeforeConvert[i][1] - 7.5)
				{
					isOverMarker = true;
					renderPreviousAnnotations();
					webGLDraw(markers[i], "TRIANGLES", "yellow");
					break;
				}
			}
			
			//LINE	
			if(!isOverMarker)
			{
				for(var i = 0; i < lineCoordsBeforeConvert.length; i++)
				{
					for(var j = 0; j < lineCoordsBeforeConvert[i].length/2; j++)
					{
						xCoord = lineCoordsBeforeConvert[i][j*2];
						yCoord = lineCoordsBeforeConvert[i][j*2+1];
						if(mouseX > xCoord - 10
						   && mouseX < xCoord + 10
						   && mouseY > yCoord - 10
						   && mouseY < yCoord + 10)
						{
							isOverLine = true;
							renderPreviousAnnotations();
							webGLDraw(lines[i], "LINE_STRIP", "yellow")
							break;
						}
					}
				}
			}

			//RECTANGLE
			if(!isOverMarker && !isOverLine)
			{
				for(var i = 0; i < rectCoordsBeforeConvert.length; i++)
				{
					if(mouseX > rectCoordsBeforeConvert[i][0] 
					   && mouseX < rectCoordsBeforeConvert[i][2]
					   && mouseY > rectCoordsBeforeConvert[i][1]
					   && mouseY < rectCoordsBeforeConvert[i][3])
					{
						isOverRect = true;
						renderPreviousAnnotations();
						webGLDraw(rects[i], "LINE_STRIP", "yellow")
						break;
					}
				}
			}

			//If not mouse overed an annotation
			if(!isOverMarker && !isOverRect && !isOverLine)
			{
				renderPreviousAnnotations();
			}
		}
	});
	
	$('#canvasDiv').hover(function(e){
		if(currTool == "pencil")
		{
			$('#canvasDiv').css("cursor", "url(js/cbmarker/images/pencil-icon_cursor.png), auto")
		}
		else if(currTool == "rect")
		{
			//$('#canvasDiv').css("cursor", "url(js/cbmarker/images/rect-icon_cursor.png), auto");
			$('#canvasDiv').css("cursor", "nw-resize");
		}
		else if(currTool == "marker")
		{
			//$('#canvasDiv').css("cursor", "url(js/cbmarker/images/marker-icon_cursor.png), auto");
			$('#canvasDiv').css("cursor", "crosshair");
		}
		else if(currTool == "eraser")
		{
			//This one is gif to get rid of the shadow that the png image originally had
			$('#canvasDiv').css("cursor", "url(js/cbmarker/images/eraser-icon_cursor.gif), auto");
		}
		//else
		//{
			//$('#canvasDiv').css("cursor", "auto");
		//}
	});
	
	//click used for eraser
	$('#canvasDiv').click(function(e){
		if(currTool == "eraser" && !isSaving)
		{
			var mouseX = e.pageX - this.offsetLeft;
			var mouseY = e.pageY - this.offsetTop;
			var isOverLine = false;
			var isOverMarker = false;
			
			//MARKER
			for(var i = 0; i < markerCoordsBeforeConvert.length; i++)
			{
				//if mouse is over a marker then highlight it yellow
				if(mouseX < markerCoordsBeforeConvert[i][0] + 7.5 
				   && mouseX > markerCoordsBeforeConvert[i][0] - 7.5
				   && mouseY < markerCoordsBeforeConvert[i][1] + 7.5
				   && mouseY > markerCoordsBeforeConvert[i][1] - 7.5)
				{
					isOverMarker = true;
					erasedAnnotationArrays.push(markers[i]);
					markers[i] = 0;
					//saveAnnotationsToDB("marker");
					undoActionQueue.push(i);
					undoActionQueue.push("eraseMarker");
					redoActionQueue = new Array();
					break;
				}
			}
			
			//LINE
			if(!isOverMarker)
			{
				for(var i = 0; i < lineCoordsBeforeConvert.length; i++)
				{
					for(var j = 0; j < lineCoordsBeforeConvert[i].length/2; j++)
					{
						xCoord = lineCoordsBeforeConvert[i][j*2];
						yCoord = lineCoordsBeforeConvert[i][j*2+1];
						if(mouseX > xCoord - 10
						   && mouseX < xCoord + 10
						   && mouseY > yCoord - 10
						   && mouseY < yCoord + 10)
						{
							isOverLine = true;
							erasedAnnotationArrays.push(lines[i]);
							lines[i] = 0;
							//saveAnnotationsToDB("line");
							undoActionQueue.push(i);
							undoActionQueue.push("eraseLine");
							redoActionQueue = new Array();
							break;
						}
					}
				}
			}
			
			//RECTANGLE
			if(!isOverMarker && !isOverLine)
			{
				for(var i = 0; i < rectCoordsBeforeConvert.length; i++)
				{
					if(mouseX > rectCoordsBeforeConvert[i][0] 
					   && mouseX < rectCoordsBeforeConvert[i][2]
					   && mouseY > rectCoordsBeforeConvert[i][1]
					   && mouseY < rectCoordsBeforeConvert[i][3])
					{
						erasedAnnotationArrays.push(rects[i]);
						rects[i] = 0;
						//saveAnnotationsToDB("rect");
						undoActionQueue.push(i);
						undoActionQueue.push("eraseRect");
						redoActionQueue = new Array();
						break;
					}
				}
			}
		}
	});
	
	//Done drawing
	$('#canvasDiv').mouseup(function(e){
		saveInfoToArrays();
	});
	$('#canvasDiv').mouseleave(function(e){
		saveInfoToArrays();
	});
	
	function saveInfoToArrays()
	{
		if(linePaint && !isSaving)
		{
			lines[numOfLines] = arrayOfCoords;
			lineColor[numOfLines] = drawingColor;
			lineCoordsBeforeConvert[numOfLines] = coordsBeforeConvert;
			numOfLines++;
			arrayOfCoords = new Array();
			coordsBeforeConvert = new Array();
			//saveAnnotationsToDB("line");
			undoActionQueue.push("line");
			redoActionQueue = new Array();
			isSaved = false;
		}
		else if(rectPaint && !isSaving)
		{
			rects[numOfRects] = arrayOfCoords;
			rectColor[numOfRects] = drawingColor;
			rectCoordsBeforeConvert[numOfRects] = coordsBeforeConvert;
			numOfRects++;
			arrayOfCoords = new Array();
			coordsBeforeConvert = new Array();
			//saveAnnotationsToDB("rect");
			undoActionQueue.push("rect");
			redoActionQueue = new Array();
			isSaved = false;
		}
		else if(markerPaint && !isSaving)
		{
			markers[numOfMarkers] = arrayOfCoords;
			markerColor[numOfMarkers] = drawingColor;
			markerCoordsBeforeConvert[numOfMarkers] = coordsBeforeConvert;
			numOfMarkers++;
			arrayOfCoords = new Array();
			coordsBeforeConvert = new Array();
			//saveAnnotationsToDB("marker");
			undoActionQueue.push("marker");
			redoActionQueue = new Array();
			isSaved = false;
		}
		linePaint = false;
		rectPaint = false;
		markerPaint = false;
	}
	
	//will check if user pressed CTRL-Z or CTRL-Y
	document.onkeydown = undoOrRedoAnnotation;
}

//webGL needs float values from 0-1 relative to canvas rather than pixel numbers
function convertCoordsToWebGLFormat(x, y)
{
	width_ratio = canvas.width/2;
	height_ratio = canvas.height/2;
	
	//X Coordinate
	if(x > width_ratio)
	{
		x = (x - width_ratio)/width_ratio;
	}
	else
	{
		x = -1 * (1 - (x/width_ratio));
	}
	
	if(y > height_ratio)
	{
		y = -1 *(y - height_ratio)/height_ratio;
	}
	else
	{
		y = 1 - (y/height_ratio);
	}
	
	arrayOfCoords.push(x);
	arrayOfCoords.push(y);
}
	
//renders previously drawn annotations when screen refreshes for new annotation
function renderPreviousAnnotations()
{
	//clears the canvas(needed for undo)
	gl.clear(gl.COLOR_BUFFER_BIT);
	
	//lines
	for(var i = 0; i < numOfLines; i++)
	{
		webGLDraw(lines[i], "LINE_STRIP", lineColor[i]);
	}
	
	//rectangles
	for(var i = 0; i < numOfRects; i++)
	{
		webGLDraw(rects[i], "LINE_STRIP", rectColor[i]);
	}
	
	//markers
	for(var i = 0; i < numOfMarkers; i++)
	{
		webGLDraw(markers[i], "TRIANGLES", markerColor[i]);
	}
}

//draws annotions on canvas
function webGLDraw(arrayOfVertices, drawArrayType, color)
{
	var vertices = new Float32Array(arrayOfVertices);
	vbuffer = gl.createBuffer();
	gl.bindBuffer(gl.ARRAY_BUFFER, vbuffer);
	gl.bufferData(gl.ARRAY_BUFFER, vertices, gl.STATIC_DRAW);
	itemSize = 2;
	numItems = vertices.length/itemSize;
	
	gl.useProgram(program);
	program.uColor = gl.getUniformLocation(program, "uColor");
	switch(color)
	{
		case "red":						
			gl.uniform4fv(program.uColor, [1.0, 0.0, 0.0, 1.0]);
			break;
		
		//changed green from [0 1 0] to make it darker for Annotation Drawling 2
		case "green":
			gl.uniform4fv(program.uColor, [0.0, .502, 0.0, 1.0]);
			break;
		case "blue":
			gl.uniform4fv(program.uColor, [0.0, 0.0, 1.0, 1.0]);
			break;
		case "black":
			gl.uniform4fv(program.uColor, [0.0, 0.0, 0.0, 1.0]);
			break;
		case "yellow":
			gl.uniform4fv(program.uColor, [1.0, 1.0, 0.0, 1.0]);
			break;
		
		//This orange is very similar to yellow [0.957, .70, 0.019, 1.0]
		case "orange":
			gl.uniform4fv(program.uColor, [1.0, .50, 0.0, 1.0]);
			
	}
	program.aVertexPosition = gl.getAttribLocation(program, "aVertexPosition");
	gl.enableVertexAttribArray(program.aVertexPosition);
	gl.enableVertexAttribArray(program.aVertexPosition);
	gl.vertexAttribPointer(program.aVertexPosition, itemSize, gl.FLOAT, false, 0, 0);
	
	switch(drawArrayType)
	{
		case "LINE_STRIP":
			gl.drawArrays(gl.LINE_STRIP, 0, numItems);
			break;
		case "TRIANGLES":
			gl.drawArrays(gl.TRIANGLES, 0, numItems);
	}
}

//allows user to undo annotation
function undoOrRedoAnnotation(isButtonClick)
{
	var evtobj = window.event? event : e;
	
	//if user presses CTRL-Z or clicks undo button
	if ((evtobj.keyCode == 90 && evtobj.ctrlKey) || isButtonClick == "undo")
	{
		var currAction;
		if(undoActionQueue.length == 0)
		{
			alert("There are no more actions to undo");
		}
		else
		{
			currAction = undoActionQueue.pop();
		}
		
		if(currAction == "line")
		{
			numOfLines--;
			redoActionQueue.push("line");
			//saveAnnotationsToDB("line");
		}
		else if(currAction == "rect")
		{
			numOfRects--;
			redoActionQueue.push("rect");
			//saveAnnotationsToDB("rect");
		}
		else if(currAction == "marker")
		{
			numOfMarkers--;
			redoActionQueue.push("marker");
			//saveAnnotationsToDB("marker");
		}
		else if(currAction == "eraseLine")
		{
			var index = undoActionQueue.pop();
			var restoreArray = erasedAnnotationArrays.pop();
			lines[index] = restoreArray;
			redoActionQueue.push(index);
			redoActionQueue.push("eraseLine");
			//saveAnnotationsToDB("line");
		}
		else if(currAction == "eraseRect")
		{
			var index = undoActionQueue.pop();
			var restoreArray = erasedAnnotationArrays.pop();
			rects[index] = restoreArray;
			redoActionQueue.push(index);
			redoActionQueue.push("eraseRect");
			//saveAnnotationsToDB("rect");
		}
		else if(currAction == "eraseMarker")
		{
			var index = undoActionQueue.pop();
			var restoreArray = erasedAnnotationArrays.pop();
			markers[index] = restoreArray;
			redoActionQueue.push(index);
			redoActionQueue.push("eraseMarker");
			//saveAnnotationsToDB("marker");
		}

		renderPreviousAnnotations();
	}
	
	//if user presses CTRL-Y or clicks redo button
	else if ((evtobj.keyCode == 89 && evtobj.ctrlKey) || isButtonClick == "redo")
	{
		var currAction;
		if(redoActionQueue.length == 0)
		{
			alert("Already at most recent change");
		}
		else
		{
			currAction = redoActionQueue.pop();
		}
		
		if(currAction == "line")
		{
			numOfLines++;
			undoActionQueue.push("line");
			//saveAnnotationsToDB("line");
		}
		else if(currAction == "rect")
		{
			numOfRects++;
			undoActionQueue.push("rect");
			//saveAnnotationsToDB("rect");
		}
		else if(currAction == "marker")
		{
			numOfMarkers++;
			undoActionQueue.push("marker");
			//saveAnnotationsToDB("marker");
		}
		else if(currAction == "eraseLine")
		{
			var index = redoActionQueue.pop();
			erasedAnnotationArrays.push(lines[index]);
			lines[index] = 0;
			undoActionQueue.push(index);
			undoActionQueue.push("eraseLine");
			//saveAnnotationsToDB("line");
		}
		else if(currAction == "eraseRect")
		{
			var index = redoActionQueue.pop();
			erasedAnnotationArrays.push(rects[index]);
			rects[index] = 0;
			undoActionQueue.push(index);
			undoActionQueue.push("eraseRect");
			//saveAnnotationsToDB("rect");
		}
		else if(currAction == "eraseMarker")
		{
			var index = redoActionQueue.pop();
			erasedAnnotationArrays.push(markers[index]);
			markers[index] = 0;
			undoActionQueue.push(index);
			undoActionQueue.push("eraseMarker");
			//saveAnnotationsToDB("marker");
		}
		renderPreviousAnnotations();
	}
}

//save prompt after pressing Save Annotations button
function saveAnnotations(fromLocation)
{
	if(fromLocation == "button")
	{
		var message = "Are you sure you want to save?"
	}
	else if(fromLocation == "fromForward" || fromLocation == "fromBackward")
	{
		var message = "You have not yet saved. Do you want to save now?";
	}
	var yesFunction = function()
	{
		saveAnnotationsToDB("line");
		saveAnnotationsToDB("rect");
		saveAnnotationsToDB("marker");
		isSaved = true;
		isSaving = true;
		HideDialog();

		DialogBox("Saving...please wait");
		setTimeout(function(){HideDialog(); isSaving = false;}, 5000);
		if (fromLocation == "fromForward") {
			loadNextImage();
		}
	};
	var noFunction = function()
	{
		HideDialog();
		if (fromLocation == "fromForward") {
			loadNextImage();
		}
	}
	DialogBox(message,true,yesFunction,noFunction);
}

//save annotation data in the database
function saveAnnotationsToDB(annotType)
{	
	//erase annotations of this type (needed for undo function)
	loadJSON(currentServer + "?action=eraseAnnotCoord&pic=" + CurrentImage + '&pid=' + pid + '&annotType=' + annotType);
	
	if(annotType == "line")
	{	
		for(var i = 0; i < numOfLines; i++)
		{
			if(lines[i] != 0)
			{
				//in coreFunctions		
				loadJSONarray(lineCoordsBeforeConvert[i], lineColor[i], "line");	
			}
		}
	}

	else if(annotType == "rect")
	{
		for(var i = 0; i < numOfRects; i++)
		{
			if(rects[i] != 0)
			{
				//in coreFunctions
				loadJSONarray(rectCoordsBeforeConvert[i], rectColor[i], "rect");
			}
		}		
	}
	
	else if(annotType == "marker")
	{
		for(var i = 0; i < numOfMarkers; i++)
		{
			if(markers[i] != 0)
			{
				//in coreFunctions
				console.info(markers[i]);
				loadJSONarray(markerCoordsBeforeConvert[i], markerColor[i], "marker");	
			}	
		}
	}
}

//save marker category
function saveMarkerCategory(markerColor)
{
	//erase previous category
	loadJSON(currentServer + '?action=eraseAnnotCoord' + '&pid=' + pid + '&drawingColor=' + markerColor + '&annotType=markerCategory');
	
	//update category
	var catId = "#" + markerColor + "MarkerCategory";
	loadJSON(currentServer + '?action=setAnnotCoord' + '&pid=' + pid +
		'&drawingColor=' + markerColor + '&annotType=markerCategory&markerCategory=' + $(catId).val());
}

//Clear Annotations prompt
function clearAllAnnotations()
{
	var message = "Are you sure you want to clear all annotations?(This cannot be undone.)"
	var yesFunction = function()
	{
		clearAllAnnotationsFromDB();
		HideDialog();
	};
	var noFunction = function()
	{
		HideDialog();
	}
	DialogBox(message,true,yesFunction,noFunction);
}

//Clear all annotations on current image
function clearAllAnnotationsFromDB()
{
	loadJSON(currentServer+"?pic=" + CurrentImage + "&action=clearAllAnnotations"+'&pid='+pid,"Clear current line annotations");
	window.location.href = "index.php?pid=" + pid + "&pic=" + images[CurrentIndex];
}

//This will load any previously saved lines as well as needed variables
function loadAnnotations(lineCoords, lineColors, rectCoords, rectColors, markerCoords, markerColors, canvaswidth, canvasheight)
{
	//setup Canvas
	canvasWidth = canvaswidth;
	canvasHeight = canvasheight;
	var canvasDiv = document.getElementById('canvasDiv');
	canvas = document.createElement('canvas');
	canvas.setAttribute('width', canvasWidth);
	canvas.setAttribute('height', canvasHeight);
	canvas.setAttribute('id', 'canvas');
	canvasDiv.appendChild(canvas);
	if(typeof G_vmlCanvasManager != 'undefined') {
		canvas = G_vmlCanvasManager.initElement(canvas);
	}
	
	//setup webGL
	gl = canvas.getContext("experimental-webgl");
	
	gl.viewport(0,0,canvas.width,canvas.height);
	//gl.clearColor(1,1,1,1); //background color
	gl.clear(gl.COLOR_BUFFER_BIT);
	
	var v = document.getElementById("vertex").firstChild.nodeValue;
	var f = document.getElementById("fragment").firstChild.nodeValue;
	
	var vs = gl.createShader(gl.VERTEX_SHADER);
	gl.shaderSource(vs, v);
	gl.compileShader(vs);
	
	var fs = gl.createShader(gl.FRAGMENT_SHADER);
	gl.shaderSource(fs, f);
	gl.compileShader(fs);
	
	program = gl.createProgram();
	gl.attachShader(program, vs);
	gl.attachShader(program, fs);
	gl.linkProgram(program);
	
	if(!gl.getShaderParameter(vs, gl.COMPILE_STATUS))
	{
		console.log(gl.getShaderInfoLog(vs));
	}	
	if(!gl.getShaderParameter(fs, gl.COMPILE_STATUS))
	{
		console.log(gl.getShaderInfoLog(fs));
	}
	if(!gl.getProgramParameter(program, gl.LINK_STATUS))
	{
		console.log(gl.getProgramInfoLog(program));
	}
	
	//LINES
	if(lineCoords.length > 0)
	{
		arrayOfCoords = new Array();
		for(var i = 0; i < lineCoords.length; i++)
		{		
			for(var j = 0; j < lineCoords[i].length; j+=2)
			{
				convertCoordsToWebGLFormat(lineCoords[i][j], lineCoords[i][j+1]);
			}
			lines[i] = arrayOfCoords;
			arrayOfCoords = new Array();
		}
		lineColor = lineColors;
		numOfLines = lineCoords.length;	
		lineCoordsBeforeConvert = lineCoords;
		
		//draw
		for(var i = 0; i < numOfLines; i++)
		{
			webGLDraw(lines[i], "LINE_STRIP", lineColor[i]);
		}
	}
	
	//RECTANGLES
	if(rectCoords.length > 0)
	{
		arrayOfCoords = new Array();
		for(var i = 0; i < rectCoords.length; i++)
		{
			for(var j = 0; j < rectCoords[i].length; j+= 2)
			{
				convertCoordsToWebGLFormat(rectCoords[i][j], rectCoords[i][j+1]);
			}
			var topLeftX = arrayOfCoords[0];
			var topLeftY = arrayOfCoords[1];
			var bottomRightX = arrayOfCoords[2];
			var bottomRightY = arrayOfCoords[3];
			var rectWidth = bottomRightX - topLeftX;
			var rectHeight = topLeftY - bottomRightY;
			rects[i] = [topLeftX, topLeftY,
				topLeftX + rectWidth, topLeftY,
				topLeftX + rectWidth, topLeftY - rectHeight,
				topLeftX, topLeftY - rectHeight,
				topLeftX, topLeftY]
			arrayOfCoords = new Array();
		}
		rectColor = rectColors;
		numOfRects = rectCoords.length;
		rectCoordsBeforeConvert = rectCoords;
		
		//draw
		for(var i = 0; i < numOfRects; i++)
		{
			webGLDraw(rects[i], "LINE_STRIP", rectColor[i]);
		}
	}
	
	//MARKERS
	if(markerCoords.length > 0)
	{
		arrayOfCoords = new Array();
		for(var i = 0; i < markerCoords.length; i++)
		{
			for(var j = 0; j < markerCoords[i].length; j+= 2)
			{
				convertCoordsToWebGLFormat(markerCoords[i][j], markerCoords[i][j+1]);
			}
			var mouseX = arrayOfCoords[0];
			var mouseY = arrayOfCoords[1];
			
			//This will keep marker size the same no matter what size canvas we have
			canvasWidthRatio = canvasWidth/500;
			canvasHeightRatio = canvasHeight/500;
			markerWidth = .02 / canvasWidthRatio;
			markerHeight = .02 / canvasHeightRatio;
			
			markers[i] = [mouseX+markerWidth, mouseY+markerHeight, mouseX-markerWidth, mouseY+markerHeight, mouseX+markerWidth, mouseY-markerHeight,
				mouseX-markerWidth, mouseY-markerHeight, mouseX-markerWidth, mouseY+markerHeight, mouseX+markerWidth, mouseY-markerHeight];
			arrayOfCoords = new Array();
		}
		markerColor = markerColors;
		numOfMarkers = markerCoords.length;
		markerCoordsBeforeConvert = markerCoords;
		
		//draw
		for(var i = 0; i < numOfMarkers; i++)
		{
			webGLDraw(markers[i], "TRIANGLES", markerColor[i]);
		}
	}
}

//load marker categories
function loadMarkerCategories(catArray)
{
	if(catArray['red'] != undefined)
	{
		$('#redMarkerCategory').val(catArray['red']);
	}
	
	if(catArray['green'] != undefined)
	{
		$('#greenMarkerCategory').val(catArray['green']);
	}
	
	if(catArray['blue'] != undefined)
	{
		$('#blueMarkerCategory').val(catArray['blue']);
	}
	
	if(catArray['black'] != undefined)
	{
		$('#blackMarkerCategory').val(catArray['black']);
	}
}

//code for when the XML Data button is pushed
function loadXML()
{
	loadJSON(currentServer + "?action=writeXMLData&pic=" + CurrentImage + '&pid=' + pid);
}

//Changes color if a color button is pressed
function changeColor(color)
{
	if(color == "red")
	{
		$('#red-icon').css("border", "2px solid #FF99CC");
		$('#green-icon').css("border", "none");
		$('#blue-icon').css("border", "none");
		$('#black-icon').css("border", "none");
		$('#pink-icon').css("border", "none");
		$('#purple-icon').css("border", "none");
		$('#orange-icon').css("border", "none");
			
		if(drawingColor != "erase" && drawingColor != "dragging"){
			drawingColor = "red";
		}

		currentColor = "red";
	}
	else if(color == "green")
	{
		$('#red-icon').css("border", "none");
		$('#green-icon').css("border", "2px solid #FF99CC");
		$('#blue-icon').css("border", "none");
		$('#black-icon').css("border", "none");
		$('#pink-icon').css("border", "none");
		$('#purple-icon').css("border", "none");
		$('#orange-icon').css("border", "none");
		
		if(drawingColor != "erase" && drawingColor != "dragging"){
			drawingColor = "green";
		}
		
		currentColor = "green";
	}
	else if(color == "blue")
	{
		$('#red-icon').css("border", "none");
		$('#green-icon').css("border", "none");
		$('#blue-icon').css("border", "2px solid #FF99CC");
		$('#black-icon').css("border", "none");
		$('#pink-icon').css("border", "none");
		$('#purple-icon').css("border", "none");
		$('#orange-icon').css("border", "none");
		
		if(drawingColor != "erase" && drawingColor != "dragging"){
			drawingColor = "blue";
		}
		
		currentColor = "blue";
	}
	else if(color == "black")
	{
		$('#red-icon').css("border", "none");
		$('#green-icon').css("border", "none");
		$('#blue-icon').css("border", "none");
		$('#black-icon').css("border", "2px solid #FF99CC");
		$('#pink-icon').css("border", "none");
		$('#purple-icon').css("border", "none");
		$('#orange-icon').css("border", "none");
		
		if(drawingColor != "erase" && drawingColor != "dragging"){
			drawingColor = "black";
		}
		
		currentColor = "black";
	}
	else if(color == "pink")
	{
		$('#red-icon').css("border", "none");
		$('#green-icon').css("border", "none");
		$('#blue-icon').css("border", "none");
		$('#black-icon').css("border", "none");
		$('#pink-icon').css("border", "2px solid #FF99CC");
		$('#purple-icon').css("border", "none");
		$('#orange-icon').css("border", "none");
		
		if(drawingColor != "erase" && drawingColor != "dragging"){
			drawingColor = "pink";
		}
		
		currentColor = "pink";
	}
	else if(color == "purple")
	{
		$('#red-icon').css("border", "none");
		$('#green-icon').css("border", "none");
		$('#blue-icon').css("border", "none");
		$('#black-icon').css("border", "none");
		$('#pink-icon').css("border", "none");
		$('#purple-icon').css("border", "2px solid #FF99CC");
		$('#orange-icon').css("border", "none");
		
		if(drawingColor != "erase" && drawingColor != "dragging"){
			drawingColor = "purple";
		}
		
		currentColor = "purple";
	}
	//eric added
	else if(color == "orange")
	{
		$('#red-icon').css("border", "none");
		$('#green-icon').css("border", "none");
		$('#blue-icon').css("border", "none");
		$('#black-icon').css("border", "none");
		$('#pink-icon').css("border", "none");
		$('#purple-icon').css("border", "none");
		$('#orange-icon').css("border", "2px solid #FF99CC");
		
		if(drawingColor != "erase" && drawingColor != "dragging"){
			drawingColor = "orange";
		}
		
		currentColor = "orange";
	}
}

//Changes tool if a tool button was pressed
function changeTool(tool)
{
	if(tool == "pencil")
	{
		$('#pencil-icon').css("border", "2px solid #996633");
		$('#rect-icon').css("border", "none");
		$('#marker-icon').css("border", "none");
		$('#eraser-icon').css("border", "none");
		$('#dragging-icon').css("border", "none");
		
		drawingColor = currentColor;
		currTool = "pencil";
	}
	else if(tool == "rect")
	{
		$('#pencil-icon').css("border", "none");
		$('#rect-icon').css("border", "2px solid #996633");
		$('#marker-icon').css("border", "none");
		$('#eraser-icon').css("border", "none");
		$('#dragging-icon').css("border", "none");
		
		drawingColor = currentColor;
		currTool = "rect";
	}
	else if(tool == "marker")
	{
		$('#pencil-icon').css("border", "none");
		$('#rect-icon').css("border", "none");
		$('#marker-icon').css("border", "2px solid #996633");
		$('#eraser-icon').css("border", "none");
		$('#dragging-icon').css("border", "none");
		
		drawingColor = currentColor;
		currTool = "marker";
	}
	else if(tool == "eraser")
	{
		$('#pencil-icon').css("border", "none");
		$('#rect-icon').css("border", "none");
		$('#marker-icon').css("border", "none");
		$('#eraser-icon').css("border", "2px solid #996633");
		$('#dragging-icon').css("border", "none");
		
		drawingColor = "erase";
		currTool = "eraser";
	}
	else if(tool == "dragging")
	{
		$('#pencil-icon').css("border", "none");
		$('#rect-icon').css("border", "none");
		$('#marker-icon').css("border", "none");
		$('#eraser-icon').css("border", "none");
		$('#dragging-icon').css("border", "2px solid #996633");
		
		drawingColor = "dragging";
	}
}



