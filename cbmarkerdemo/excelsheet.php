<?php 
include("SecurePage.php");
include("GlobalVariables.php");
?>
<?php

Connect_To_DB($db_server_official,$db_user_official,$db_pwd_official,$db_name_official);

function Connect_To_DB($db_server_value, $db_user_value, $db_pwd_value, $db_name_value)
{
	$conn = mysql_connect($db_server_value, $db_user_value, $db_pwd_value) or die('Error connecting to mysql');
	mysql_select_db($db_name_value);
}

if(isset($_SESSION['admin']) and $_SESSION['admin'] == 1)
{
	$sql = "SELECT * FROM `cbdata`";

	$result = mysql_query($sql);
	$contents = "";
	$printColumnNames = true;
	while($row = mysql_fetch_array($result))
	{
		if($printColumnNames == true)
		{
			foreach(array_keys($row) as $value)
			{
				$contents = $contents . $value;
				$contents = $contents . ",";
			}
			$contents = $contents . "\n";
			$printColumnNames = false;
		}
		
		foreach(array_keys($row) as $value)
		{
			$contents = $contents . $row[$value];

			$contents = $contents . ",";
			//$contents = $contents . " \t ";
		}
		$contents = $contents . "\n";
		//$contents = $contents . " \n";
	}

	$filename ="Database.csv";
	header('Content-type: application/vnd.ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);
	echo $contents;
}
else
{
	echo "<html><h1>Access Denied</h1></html>";
}
?>