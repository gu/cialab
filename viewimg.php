<?php include("./SecurePage.php"); ?>
<?php include("./GlobalVariables.php"); ?>
<?php include("./GlobalFunctions.php"); ?>
<?php


if(isset($_GET['id']) == true && isset($_GET['db']) == true)
{
	Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_imgfeatures);
	
	$sql = "SELECT `PngFileName` FROM `".mysql_prep($_GET['db'])."` WHERE `id` = '" . mysql_prep($_GET['id']) . "'";
	
	$row = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
	
	$out = array();
	exec('cd', $out);
	$wwwPath = $out[0];
	$uniserverPath = rtrim($wwwPath,"\\www");
	$diseaseImagePath = $uniserverPath . "\\disease_images\\";
	//echo $diseaseImagePath;
	
	$imagepath = $diseaseImagePath . $row['PngFileName'];
	//echo $imagepath;

	if(isset($_GET['width']) == true && isset($_GET['height']) == true)
	{
		$im = resizeImage($imagepath,$_GET['width'],$_GET['height']);
	}
	else
	{
		$im=imagecreatefrompng($imagepath);
	}
	
	
	if(!$im)
	{
		//Create a black image 
        $im  = imagecreatetruecolor(150, 30);
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);

        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

        //Output an error message 
        imagestring($im, 1, 5, 5, 'Error loading ' . "Image", $tc);
	}

	header('Content-Type: image/jpeg');
	imagejpeg($im);
	imagedestroy($im);
}

if(isset($_GET['filename']) == true)
{
	$imagepath = "./uploads/" . $_GET['filename'];
	if(isset($_GET['width']) == true && isset($_GET['height']) == true)
	{
		$im = resizeImage($imagepath,$_GET['width'],$_GET['height']);
	}
	else
	{
		$im=imagecreatefrompng($imagepath);
	}
	header('Content-Type: image/jpeg');
	imagejpeg($im);
	imagedestroy($im);
}

function resizeImage($originalImage,$toWidth,$toHeight){
    
    // Get the original geometry and calculate scales
    list($width, $height) = getimagesize($originalImage);
    $xscale=$width/$toWidth;
    $yscale=$height/$toHeight;
    
    // Recalculate new size with default ratio
    if ($yscale>$xscale){
        $new_width = round($width * (1/$yscale));
        $new_height = round($height * (1/$yscale));
    }
    else {
        $new_width = round($width * (1/$xscale));
        $new_height = round($height * (1/$xscale));
    }

    // Resize the original image
    $imageResized = imagecreatetruecolor($new_width, $new_height);
    $imageTmp     = imagecreatefrompng ($originalImage);
    imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    return $imageResized;
}



?>
