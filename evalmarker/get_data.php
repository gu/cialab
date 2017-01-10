<?php
$path = (isset($_POST["dir"])) ? $_POST["dir"] : "";

$data = file_get_contents($path);
echo ($data);
//file_put_contents($path, $data);
?>