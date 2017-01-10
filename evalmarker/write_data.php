<?php
$path = (isset($_POST["path"])) ? $_POST["path"] : "";
$file = (isset($_POST["file"])) ? $_POST["file"] : "";
$data = (isset($_POST["data"])) ? $_POST["data"] : "";

echo $data;

if (!file_exists($path)) {
	mkdir($path, 0777, true);
}

file_put_contents($path."/".$file, $data);
?>
