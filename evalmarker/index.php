<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>CBMarker Version 2</title>
  <meta name="description" content="CBMarker Version 2">
  <meta name="author" content="SitePoint">

  <link rel="stylesheet" href="css/styles.css?v=1.0">

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>

<body>
  <div class="control-panel">
	<div id="back-button" class="control-button" style="font-size:200%;" title="Move to previous image.">&larr;</div>
	<div id="next-button" class="control-button" style="font-size:200%;" title="Move to next image.">&rarr;</div>
	<div id="image-pos" class="control-label">Image: 0/0</div>
	<div class="divider"></div>
	<div id="marker-button" class="control-button" title="Click to draw a marker.">
	  <img src="images/marker-icon.png" alt="Marker" style="height:40px">
	</div>
	<div id="line-button" class="control-button" title="Hold and drag to draw a line.">
	  <img src="images/pencil-icon.png" alt="Pencil" style="height:40px">
	</div>
	<div id="rect-button" class="control-button" title="Hold and drag to draw a rectangle.">
	  <img src="images/rect-icon.png" alt="Rectangle" style="height:40px">
	</div>
	<div id="erase-button" class="control-button" title="Click over an annotation to remove it.">
	  <img src="images/eraser-icon.png" alt="Eraser" style="height:40px">
	</div>
	<div class="divider"></div>
	<div id="red-button" class="control-button" title="Tumor Cells">
	  <img src="images/red-icon.png" alt="Red" style="height:40px"></div>
	<div id="orange-button" class="control-button" title="Stromal Cells">
	  <img src="images/orange-icon.png" alt="Orange" style="height:40px"></div>
	<div id="green-button" class="control-button" title="Lymphocytes">
	  <img src="images/green-icon.png" alt="Green" style="height:40px"></div>
	<div class="divider"></div>
	<div id="clear-button" class="control-button" title="Clear all annotations.">CLEAR</div>
	<!-- Temporarily disabled
	<div id="undo-button" class="control-button" style="font-size:200%;">&#8630;</div>
	<div id="redo-button" class="control-button" style="font-size:200%;">&#8631;</div> -->
  </div>
  <br>
  <canvas id="c" class="canvas"></canvas>

  <script src="js/fabric.js"></script>
  <script src="js/fabric.canvasex.js"></script>
  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="js/scripts.js"></script>
</body>
</html>
