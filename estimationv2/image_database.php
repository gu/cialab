<?php
$root = (isset($_POST["dir"])) ? $_POST["dir"] : "data";

function getDirContents($dir, &$results = array()) {
  $files = scandir($dir);
  foreach ($files as $key => $value) {
    $path = $dir.DIRECTORY_SEPARATOR.$value;
    if ($value != "." && $value != "..") {
      $results[] = $path;
      #getDirContents($path, $results);
      #$results[] = $path;
    }
  }
  return $results;
}

$ret = getDirContents($root);

foreach($ret as $blah) {
  echo $blah . "BREAK";
}
?>
