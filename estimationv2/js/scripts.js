/* TODO:
 *  1) Link with main page.
 *    a) Be able to get Project folder and user_id for storage
 *
 *  2) Proper directory creation through PHP
 *
 *  3) Export JSON into meaningful data.
 * 
 *  4) Ensure undo/redo maintains correct ordering with erase.
 *
 *  5) Develop more precise erasing process.
 */

 function parse(val) {
    var result = "Not found",
        tmp = [];
    location.search
    //.replace ( "?", "" ) 
    // this is better, there might be a question mark inside
    .substr(1)
        .split("&")
        .forEach(function (item) {
        tmp = item.split("=");
        if (tmp[0] === val) result = decodeURIComponent(tmp[1]);
    });
    return result;
}

function baseName(str) {
   var base = new String(str).substring(str.lastIndexOf('/') + 1); 
   return base.replace(".","_");
}

var input_args = parse('pid').split(",");
var pid = input_args[0];
var uid = input_args[1];
 

 
var folder;
var project_name;
var user_id;

var images = new Array();
var image_position = 0;

var image_label = document.getElementById("image-pos");
var marker_button = document.getElementById("marker-button");
var rect_button = document.getElementById("rect-button");
var line_button = document.getElementById("line-button");
var erase_button = document.getElementById("erase-button");
var red_button = document.getElementById("red-button");
var orange_button = document.getElementById("orange-button");
var green_button = document.getElementById("green-button");
var next_button = document.getElementById("next-button");
var back_button = document.getElementById("back-button");
var clear_button = document.getElementById("clear-button");
var undo_button = document.getElementById("undo-button");
var redo_button = document.getElementById("redo-button");

//var canvas = new fabric.Canvas('c');
var canvas = new fabric.CanvasEx('c');
var base_image;
var anno_mode = "marker";
marker_button.style.backgroundColor = "#78909C";

var RED = "#FF0000";
var ORANGE = "#FF8000";
var GREEN = "#00FF00";
var RED_C = "rgba(255,0,0,1)";
var ORANGE_C = "rgba(255,128,0,1)";
var GREEN_C = "rgba(0,255,0,1)";
var color_mode = RED;
var canvas_color_mode = RED_C;
red_button.style.backgroundColor = "#78909C";

var color_before_erase_indicator;

var isDown, origX, origY, rect;
var marker;

var undo_stack = new Array();
var redo_stack = new Array();

canvas.freeDrawingBrush.color = "#FF0000";
fabric.Object.prototype.selectable = false;
canvas.freeDrawingBrush.width = 2;
canvas.perPixelTargetFind = true;
canvas.selection = false;
fabric.util.addListener(document.getElementsByClassName('upper-canvas')[0], 'contextmenu', function(e) {
        e.preventDefault();
    });

function setImage(position) {
  canvas.clear();
  fabric.Image.fromURL(images[position], function(oImg) {
    canvas.setHeight(oImg.getHeight());
	canvas.setWidth(oImg.getWidth());
  });
  canvas.setBackgroundImage(images[position], canvas.renderAll.bind(canvas));
  image_label.innerHTML = "Image {0}/{1}".format(position+1, images.length);
  
  importAnnotations(position);
}

function exportAnnotations(position, old_position) {
  var json_out = "";
  canvas.forEachObject(function(obj) {
	json_out = json_out.concat(JSON.stringify(obj));
	json_out = json_out.concat("SPLIT");
  });
console.log(json_out);
console.log("data/storage/{0}/{1}".format(pid, uid));

    $.ajax({
      url: "write_data.php",
	  type: 'POST',
	  data: {
		"path": "data/storage/{0}/{1}".format(pid, uid),
		"file": "{0}.json".format(baseName(images[old_position])),
		"data": json_out
	  },
	  success: function(response) {
console.log(response);
		setImage(position);
	  },
          error: function(e) {
		console.log(e);
          }
    });
}

function importAnnotations(position) {
  $.ajax({
    url: "get_data.php",
	type: "POST",
	data: {
	  "dir": "data/storage/{0}/{1}/{2}.json".format(pid, uid, baseName(images[position]))
	},
	success: function(response) {
	  var annotations = response.split("SPLIT");
	  annotations.splice(-1,1);
	  var json_objects = new Array();
	  for (var i = 0; i < annotations.length; i++) {
	    json_objects[i] = JSON.parse(annotations[i]);
	  }
console.log(json_objects);
	  fabric.util.enlivenObjects(json_objects, function(objects) {
		var origRenderOnAddRemove = canvas.renderOnAddRemove;
		canvas.renderOnAddRemove = false;

		objects.forEach(function(o) {
		  if (o.type != "image") {
		    canvas.add(o);
		  }
		});

		canvas.renderOnAddRemove = origRenderOnAddRemove;
		canvas.renderAll();
  var counter = 0;
  canvas.forEachObject(function(obj) { counter++; });
	  });
	},
          error: function(e) {
		console.log(e);
          }
  });
}

function loadImage(position, old_position) {
  exportAnnotations(position, old_position);
}

function loadImagesFromServer(dir) {
  $.ajax({
	url: "server.php",
	type: 'POST',
	data: { 
	  "dir": ".."+dir
	},
	success: function(response) {
	  images = response.split("BREAK");
	  images.splice(-1,1);
	  for (var i = 0; i < images.length; i++) {
		images[i] = images[i].replace("\\", "/");
		images[i] = images[i].replace("//", "/");
	  }
	  setImage(image_position);
	}
  });
}

window.onload = function() {
//	loadImagesFromServer(folder);
	$.ajax({
	   url: "get_info.php",
	   type: 'POST',
	   data: {
		 "pid": pid
	   },
	   success: function(response) {
		 folder = response;
		 var buff = folder.split("/");
		 project_name = buff[buff.length-2];
		
		 loadImagesFromServer(folder);
	   },
	   error: function(xhr, status, error) {
		 var err = eval("(" + xhr.responseText +")");
	   }
	});
};

canvas.on('mouse:down', function(o) {
if (o.e.which === 1) {
  if (anno_mode == "rect") {
	  isDown = true;
	  var pointer = canvas.getPointer(o.e);
	  origX = pointer.x;
	  origY = pointer.y;
	  rect = new fabric.Rect({
		left: origX,
		top: origY,
		width: 0,
		height: 0,
		stroke: canvas_color_mode,
		strokeWidth: 5,
		fill: "rgba(0,0,0,0)"
	  });
	  rect.type="rect";
	  rect.selectable = false;
	  canvas.add(rect);
	  
	  canvas.on('mouse:move', function (option) {
	  	  var e = option.e;
		  rect.set('width', e.offsetX - origX);
		  rect.set('height', e.offsetY - origY);
		  rect.setCoords();
		  canvas.renderAll();
	  });
	  
	  undo_stack.push(rect);
	  var entry = new Array(2);
	  entry[0] = "add";
	  entry[1] = rect;
	  undo_stack.push(entry);
  } else if (anno_mode == "marker") {
    var pointer = canvas.getPointer(o.e);
	marker = new fabric.Rect({
	  left: pointer.x - 5,
	  top: pointer.y - 5,
	  originX: 'left',
	  originY: 'top',
	  width: 10,
	  height: 10,
	  angle: 0,
	  stroke: canvas_color_mode,
          strokeWidth: 5,
	  fill: canvas_color_mode
	});
	canvas.add(marker);
	canvas.renderAll();
	
	var entry = new Array(2);
	entry[0] = "add";
	entry[1] = marker;
	undo_stack.push(entry);
	
  } else if (anno_mode == "erase") {
	var entry = new Array(2);
	entry[0] = "remove";
	entry[1] = o.target;
	undo_stack.push(entry);
	
	canvas.remove(o.target);
	canvas.renderAll();
  }
} else if (o.e.which === 3) {
  if ( o.target && o.target.type != "image") {
    if (o.target.stroke === '#FF0000') {
      o.target.stroke = '#FF8000';
    } else if (o.target.stroke === 'rgba(255,0,0,1)') {
      o.target.stroke = 'rgba(255,128,0,1)';
    } else if (o.target.stroke === '#FF8000') {
      o.target.stroke = '#00FF00';
    } else if (o.target.stroke === 'rgba(255,128,0,1)') {
      o.target.stroke = 'rgba(0,255,0,1)';
    } else if (o.target.stroke === '#00FF00') {
      o.target.stroke = '#FF0000';
    } else if (o.target.stroke === 'rgba(0,255,0,1)') {
      o.target.stroke = 'rgba(255,0,0,1)';
    }
    canvas.renderAll();
  }  
}
});

canvas.on('mouse:up', function(o) {
  isDown = false;
  if (anno_mode == "line") {
	var counter = 0;
	canvas.forEachObject(function(obj) { counter++; });
	var line = canvas.item(counter-1);
	
	var entry = new Array(2);
	entry[0] = "add";
	entry[1] = line;
	undo_stack.push(entry);
  }
  if (anno_mode == "rect") {
    canvas.off('mouse:move');
  }
});

canvas.on('mouse:over', function(e) {
  if (anno_mode == "erase" && e.target.type != "image") {
    color_before_erase_indicator = e.target.stroke;
	if (e.target.width == 10 ){
		e.target.setFill('yellow');
	}
    e.target.setStroke('yellow');
	canvas.renderAll();
  } else if (e.target.type != "image") {
    e.target.strokeWidth = 7;
    canvas.renderAll();
  }
});

canvas.on('mouse:out', function(e) {
  if (anno_mode == "erase" && e.target.type != "image") {
    e.target.setStroke(color_before_erase_indicator);
	if (e.target.width == 10 ){
		e.target.setFill(color_before_erase_indicator);
	}
	canvas.renderAll();
  } else if (e.target.type != "image") {
    e.target.strokeWidth = 5;
    canvas.renderAll();
  } 
});

function marker_click() {
  if (anno_mode != "marker") {
    anno_mode = "marker";
	marker_button.style.backgroundColor = "#78909C";
	rect_button.style.backgroundColor = "#546E7A";
	line_button.style.backgroundColor = "#546E7A";
	erase_button.style.backgroundColor = "#546E7A";
	canvas.isDrawingMode = false;
  }
}
function rect_click() {
  if (anno_mode != "rect") {
    anno_mode = "rect";
	marker_button.style.backgroundColor = "#546E7A";
	rect_button.style.backgroundColor = "#78909C";
	line_button.style.backgroundColor = "#546E7A";
	erase_button.style.backgroundColor = "#546E7A";
	canvas.isDrawingMode = false;
  }
}
function line_click() {
  if (anno_mode != "line") {
    anno_mode = "line";
	marker_button.style.backgroundColor = "#546E7A";
	rect_button.style.backgroundColor = "#546E7A";
	line_button.style.backgroundColor = "#78909C";
	erase_button.style.backgroundColor = "#546E7A";
	canvas.isDrawingMode = true;
  }
}
function erase_click() {
  if (anno_mode != "erase") {
	//fabric.Object.prototype.selectable = true;
    anno_mode = "erase";
	marker_button.style.backgroundColor = "#546E7A";
	rect_button.style.backgroundColor = "#546E7A";
	line_button.style.backgroundColor = "#546E7A";
	erase_button.style.backgroundColor = "#78909C";
	canvas.isDrawingMode = false;
  }
}
function red_click() {
  if (color_mode != RED) {
	color_mode = RED;
	canvas_color_mode = RED_C;
	canvas.freeDrawingBrush.color = RED;
	red_button.style.backgroundColor = "#78909C";
	orange_button.style.backgroundColor = "#546E7A";
	green_button.style.backgroundColor = "#546E7A";
  }
}
function orange_click() {
  if (color_mode != ORANGE) {
	color_mode = ORANGE;
	canvas_color_mode = ORANGE_C;
	canvas.freeDrawingBrush.color = ORANGE;
	red_button.style.backgroundColor = "#546E7A";
	orange_button.style.backgroundColor = "#78909C";
	green_button.style.backgroundColor = "#546E7A";
  }
}
function green_click() {
  if (color_mode != GREEN) {
	color_mode = GREEN;
	canvas_color_mode = GREEN_C;
	canvas.freeDrawingBrush.color = GREEN;
	red_button.style.backgroundColor = "#546E7A";
	orange_button.style.backgroundColor = "#546E7A";
	green_button.style.backgroundColor = "#78909C";
  }
}
function next_click() {
  if (image_position == images.length-1) {
    console.log("Can't go next");
  } else {
    image_position++;
	undo_stack = new Array();
	redo_stack = new Array();
	loadImage(image_position, image_position - 1);
  }
}
function back_click() {
  if (image_position == 0) {
    console.log("Can't go back");
  } else {
    image_position--;
	undo_stack = new Array();
	redo_stack = new Array();
	loadImage(image_position, image_position + 1);
  }
}
function clear_click() {
  /*var counter = 0;
  canvas.forEachObject(function(obj) { counter++; });
  
  for (var i = 0; i < counter; i++) {
    canvas.remove(canvas.item(1));
  }*/
  var r = confirm("Are you sure?");
  if (r)
	canvas.clear().renderAll();
}
function undo_click() {
  if (undo_stack.length > 0) {
	console.log(undo_stack);
    var recent_annotation = undo_stack.pop();
    redo_stack.push(recent_annotation);
	if (recent_annotation[0] == "add") {
	  canvas.remove(recent_annotation[1]);
	} else if (recent_annotation[0] == "remove") {
	  canvas.add(recent_annotation[1]);
	  canvas.renderAll();
	}
  }
}
function redo_click() {
  if (redo_stack.length > 0) {
	console.log(redo_stack);
    var undid_annotation = redo_stack.pop();
    undo_stack.push(undid_annotation);
	if (undid_annotation[0] == "add") {
      canvas.add(undid_annotation[1]);
      canvas.renderAll();
	} else if (undid_annotation[0] == "remove") {
	  canvas.remove(undid_annotation[1]);
	}
  }
}

marker_button.addEventListener("click", marker_click);
rect_button.addEventListener("click", rect_click);
line_button.addEventListener("click", line_click);
erase_button.addEventListener("click", erase_click);
red_button.addEventListener("click", red_click);
orange_button.addEventListener("click", orange_click);
green_button.addEventListener("click", green_click);
next_button.addEventListener("click", next_click);
back_button.addEventListener("click", back_click);
clear_button.addEventListener("click", clear_click);

if (!String.prototype.format) {
  String.prototype.format = function() {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function(match, number) { 
      return typeof args[number] != 'undefined'
        ? args[number]
        : match
      ;
    });
  };
}
