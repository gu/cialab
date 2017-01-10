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
 
var classifications;
 
var folder;
var project_name;
var user_id;

var images = new Array();
var image_position = 0;

var image_label = document.getElementById("image-pos");
var next_button = document.getElementById("next-button");
var back_button = document.getElementById("back-button");
var g1_button = document.getElementById("group-one-button");
var g2_button = document.getElementById("group-two-button");
var g3_button = document.getElementById("group-three-button");
var g4_button = document.getElementById("group-four-button");

next_button.style.pointerEvents = 'none';
back_button.style.pointerEvents = 'none';
next_button.style.backgroundColor = '#455A64';
back_button.style.backgroundColor = '#455A64';
	g1_button.style.backgroundColor = '#455A64';
	g2_button.style.backgroundColor = '#455A64';
	g3_button.style.backgroundColor = '#455A64';
	g4_button.style.backgroundColor = '#455A64';

//var canvas = new fabric.Canvas('c');
var canvas = new fabric.CanvasEx('c');
canvas.setWidth(window.innerWidth);
var base_image;

fabric.Object.prototype.selectable = false;

function setImage() {
  canvas.clear();
  fabric.Image.fromURL(images[image_position], function(oImg) {
	if (oImg.getWidth() > canvas.getWidth()) {
		oImg.scaleToWidth(canvas.getWidth());
	}
	canvas.setHeight(oImg.getHeight());
	canvas.add(oImg);
  image_label.innerHTML = "Image {0}/{1}".format(image_position+1, images.length);
	next_button.style.pointerEvents = 'auto';
	back_button.style.pointerEvents = 'auto';
	next_button.style.backgroundColor = '#546E7A';
	back_button.style.backgroundColor = '#546E7A';
  //canvas.setBackgroundImage(images[image_position], function() {

//	canvas.renderAll.bind(canvas);
 // });

  if (classifications[image_position] === 1) {
	toggleG1();
  } else if (classifications[image_position] === 2) {
	toggleG2();
  } else if (classifications[image_position] === 3) {
	toggleG3();
  } else if (classifications[image_position] === 4) {
	toggleG4();
  } else {
	g1_button.style.backgroundColor = '#546E7A';
	g2_button.style.backgroundColor = '#546E7A';
	g3_button.style.backgroundColor = '#546E7A';
	g4_button.style.backgroundColor = '#546E7A';
  }
  });

  
}

function saveClassifications() {
console.log('in save');
    $.ajax({
      url: "write_data.php",
	  type: 'POST',
	  data: {
		"path": "data/storage/{0}/{1}".format(pid, uid),
		"file": "data.json",
		"data": JSON.stringify(classifications) 
	  },
	  success: function(response) {
console.log('response');
		setImage();
	  },
          error: function(e) {
		console.log(e);
          }
    });
}

function loadClassifications() {
  $.ajax({
    url: "get_data.php",
	type: "POST",
	data: {
	  "dir": "data/storage/{0}/{1}/data.json".format(pid, uid)
	},
	success: function(response) {
	  console.log(response);
	if (response) {
		classifications = JSON.parse(response);
	} else {
		classifications = [];
	}
	  //classifications = JSON.;
	  setImage(image_position);
	},
        error: function(e) {
	console.log(e);
        }
  });
}

function loadImage() {
  saveClassifications();
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
  	  loadClassifications();
	}
  });
}

window.onload = function() {
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

function next_click() {
  if (image_position == images.length-1) {
    console.log("Can't go next");
  } else {
	next_button.style.pointerEvents = 'none';
	back_button.style.pointerEvents = 'none';
next_button.style.backgroundColor = '#455A64';
back_button.style.backgroundColor = '#455A64';
	g1_button.style.backgroundColor = '#455A64';
	g2_button.style.backgroundColor = '#455A64';
	g3_button.style.backgroundColor = '#455A64';
	g4_button.style.backgroundColor = '#455A64';
    image_position++;
	loadImage();
  }
}
function back_click() {
  if (image_position == 0) {
    console.log("Can't go back");
  } else {
	next_button.style.pointerEvents = 'none';
	back_button.style.pointerEvents = 'none';
next_button.style.backgroundColor = '#455A64';
back_button.style.backgroundColor = '#455A64';
	g1_button.style.backgroundColor = '#455A64';
	g2_button.style.backgroundColor = '#455A64';
	g3_button.style.backgroundColor = '#455A64';
	g4_button.style.backgroundColor = '#455A64';
    image_position--;
	loadImage();
  }
}
function toggleG1() {
	classifications[image_position] = 1;
	g1_button.style.backgroundColor='#00933B';
	g2_button.style.backgroundColor='#546E7A';
	g3_button.style.backgroundColor='#546E7A';
	g4_button.style.backgroundColor='#546E7A';
}
function toggleG2() {
	classifications[image_position] = 2;
	g2_button.style.backgroundColor='#00933B';
	g1_button.style.backgroundColor='#546E7A';
	g3_button.style.backgroundColor='#546E7A';
	g4_button.style.backgroundColor='#546E7A';
}
function toggleG3() {
	classifications[image_position] = 3;
	g3_button.style.backgroundColor='#00933B';
	g1_button.style.backgroundColor='#546E7A';
	g2_button.style.backgroundColor='#546E7A';
	g4_button.style.backgroundColor='#546E7A';
}
function toggleG4() {
	classifications[image_position] = 4;
	g4_button.style.backgroundColor='#00933B';
	g1_button.style.backgroundColor='#546E7A';
	g2_button.style.backgroundColor='#546E7A';
	g3_button.style.backgroundColor='#546E7A';
}

next_button.addEventListener("click", next_click);
back_button.addEventListener("click", back_click);
g1_button.addEventListener("click", toggleG1);
g2_button.addEventListener("click", toggleG2);
g3_button.addEventListener("click", toggleG3);
g4_button.addEventListener("click", toggleG4);

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
