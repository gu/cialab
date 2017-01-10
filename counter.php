<?php
if (file_exists($filename))
{
$CounterFile = "counter.txt";
$fh = fopen($CounterFile, 'r');
$theData = fgets($fh);
fclose($fh);
echo $theData;

}
else
{

$fh = fopen($CounterFile, 'w') or die("can't open file");
$stringData = "Floppy Jalopy\n";
fwrite($fh, $stringData);
$stringData = "Pointy Pinto\n";
fwrite($fh, $stringData);
fclose($fh);
}
?>