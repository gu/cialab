<?php
$pid = (isset($_POST["pid"])) ? $_POST["pid"] : "";
$uid = (isset($_POST["uid"])) ? $_POST["uid"] : "";
$img = (isset($_POST["img"])) ? $_POST["img"] : "";
$M1 = (isset($_POST["M1"])) ? $_POST["M1"] : "";
$M2 = (isset($_POST["M2"])) ? $_POST["M2"] : "";

include("../SecurePage.php");
include("../GlobalVariables.php");
include("../GlobalFunctions.php");

Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cbmarker);

$sql = "SELECT * FROM pospercentestimation where userid=\"".$uid."\" and project_id=\"".$pid."\" and image=\"".$img."\";";
$row = mysql_fetch_array(mysql_query($sql));

if (!$row) {
	$sql = "INSERT INTO pospercentestimation (userid, project_id, image, date, M1, M2) VALUES (".$uid.", ".$pid.", \"".$img."\", now(), ".$M1.", ".$M2.");";
} else {
	$sql = "UPDATE pospercentestimation SET M1=".$M1.", M2=".$M2." WHERE userid=\"".$uid."\" and project_id=\"".$pid."\" and image=\"".$img."\";";
}

$ret = mysql_query($sql);
if (!$ret) {
	echo "error";
} else {
	echo "success";
}
?>
