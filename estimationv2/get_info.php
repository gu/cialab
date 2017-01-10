<?php 
include("../SecurePage.php");
include("../GlobalVariables.php"); 
include("../GlobalFunctions.php");

$id = (isset($_POST["pid"])) ? $_POST["pid"] : "";
Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab);

//Try to load the project they request via the pid value
$sql = "SELECT folder FROM roi_projects where id=".$id.";";
try
{
	$row = mysql_fetch_array(mysql_query($sql));
	$ROIFolder = $row['folder'];
}
catch (Exception $e){}

echo $ROIFolder;
?>