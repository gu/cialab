<?php
if(isset($_POST['file_name'])){
	$file = $_POST['file_name'];
	header('Content-type: application/csv');
	header('Content-Disposition: attachment; filename="'.$file.'"');
	readfile($file);
	exit();
}
?>


<form action="forceDownload.php" method="post">
	<input name="file_name" value="DebraData.csv" type="hidden" />
	<input type="submit" value="Download Debra's Data" onclick="forceDownload()" />
</form>

<form action="forceDownload.php" method="post">
	<input name="file_name" value="AjmalData.csv" type="hidden" />
	<input type="submit" value="Download Ajmal's Data" onclick="forceDownload()" />
</form>

<form action="forceDownload.php" method="post">
	<input name="file_name" value="KeluoData.csv" type="hidden" />
	<input type="submit" value="Download Keluo's Data" onclick="forceDownload()" />
</form>