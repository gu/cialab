<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Classification</title>
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
	<div id="group-one-button" class="control-button" title="Group 1">
	 Benign 
	</div>
	<div id="group-two-button" class="control-button" title="Group 2">
	 Dysplasia 
	</div>
	<div id="group-three-button" class="control-button" title="Group 3">
	 CIS
	</div>
	<div id="group-four-button" class="control-button" title="Group 3">
  	 Other	
	</div>
  </div>
  <br>
  <canvas id="c" class="canvas"></canvas>

  <script src="js/fabric.js"></script>
  <script src="js/fabric.canvasex.js"></script>
  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="js/scripts.js"></script>
</body>
</html>
