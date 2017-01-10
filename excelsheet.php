<?php include("SecurePage.php"); ?>
<?php include("GlobalVariables.php"); ?>
<?php include("GlobalFunctions.php"); ?>
<?php Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab); ?>
<?php
//This has a total of at least 4 SQL requests all to different tables and possibly more
//depending what data is being displayed
$contents = "";
$sql = "SELECT * FROM `slides_rep`";
$result = mysql_query($sql);
$IndexArray = array();
$SelectIndexArray = array();
$DataFields = array();
//This finds and displays the rows that are to set true for "general_display" 
//This code also creates arrays for data that is dependent on a "datafield_*" table
//This allows for data to be looked up in via an array later which is fater than doing another SQL request
while($row = mysql_fetch_array($result, MYSQL_ASSOC))
{
	if(isset($_GET["return_all_fields"]) == false)
	{
		if($row['real_data'] == true) //detects if the data is actually represented in `slides_data`
		{
			if ($row['general_display'] == true) //determines if variable is to be displayed on the entries screen
			{
				$contents = $contents . $row['name'] . " \t ";
				$SelectIndexArray[$row['index']] = $row['index'];
				
				if($row['type'] > 0) //determines if variable has a data field and creates a data array
				{
					$DataFields[$row['index']] = array();//creates an array to hold the values of the datafields
					
					$DataSQL = "SELECT * FROM `".$row['type_id']."`";
					$DataResult = mysql_query($DataSQL);
					while($item = mysql_fetch_array($DataResult, MYSQL_ASSOC))
					{
						$DataFields[$row['index']][$item['value']] = $item['name'];
					}
				}
			}
			
			$IndexArray[$row['index']] = $row['index'];
		}
	}
	else if(isset($_GET["return_all_fields"]) == true)
	{
		if($row['real_data'] == true) //detects if the data is actually represented in `slides_data`
		{
			$contents = $contents . $row['name'] . " \t ";
			$SelectIndexArray[$row['index']] = $row['index'];
			
			if($row['type'] > 0) //determines if variable has a data field and creates a data array
			{
				$DataFields[$row['index']] = array();//creates an array to hold the values of the datafields
				
				$DataSQL = "SELECT * FROM `".$row['type_id']."`";
				$DataResult = mysql_query($DataSQL);
				while($item = mysql_fetch_array($DataResult, MYSQL_ASSOC))
				{
					$DataFields[$row['index']][$item['value']] = $item['name'];
				}
			}
			
			$IndexArray[$row['index']] = $row['index'];
		}
	}
}

$contents = $contents . " \n ";

$sql = "SELECT * FROM `dataset_5`";//get Operator array aka ">","<","="
$result = mysql_query($sql);
$OperatorArray = array();
$SQLArray = array();
while($row = mysql_fetch_array($result, MYSQL_ASSOC))
{
	$OperatorArray[$row['value']] = $row['name'];
}

$sql = "SELECT * FROM `dataset_6`";//Get display results per page array
$result = mysql_query($sql);
$DisplayArray = array();
while($row = mysql_fetch_array($result, MYSQL_ASSOC))
{
	$DisplayArray[$row['value']] = $row['name'];
}

//print_r($IndexArray);
foreach(array_keys($_GET) as $value)
{
	//determine if the value in the get array is empty
	if ($_GET[$value] != "" && $_GET[$value] != "*")
	{
		//determine if the value in the get array is an actual parameter to be checked
		if(array_key_exists($value, $IndexArray))
		{
			//check to see if the parameter has an operator parameter as well
			if(array_key_exists($value."_operator",$_GET))
			{
				//make sure the operator parameter is specified and not equal to nothing aka "any"
				if($_GET[$value."_operator"] != "")
				{
				//add the SQL command to the SQLarray, later this array will be joined to form the SQL command
				array_push($SQLArray, " `".mysql_prep($value)."` ". $OperatorArray[mysql_prep($_GET[$value."_operator"])]." '".mysql_prep($_GET[$value])."' ");
				}	
			}
			else
			{
				//add the SQL command to the SQLarray, later this array will be joined to form the SQL command
				array_push($SQLArray, " `".mysql_prep($value)."` = '".mysql_prep($_GET[$value])."' ");
			}
		}
	}
}

if(isset($_GET["return_all_fields"]))
{
//Create the SQL request with all fields
$sql = "SELECT * FROM `slides_data`"; 	//join the selected index array for variables to grab and grab id 
}
else
{
//Create the SQL request with only shown fields
$sql = "SELECT `id`,`" . join("`,`", $SelectIndexArray) . "` FROM `slides_data`"; 	//join the selected index array for variables to grab and grab id 

}

if(sizeof($SQLArray) > 0)															//so that it cab be passed on if user decieds to click a specific slide
{
	$sql = $sql . " WHERE " . join(" AND ", $SQLArray) . " ";
}

$result = mysql_query($sql);
$num_rows = mysql_num_rows($result);
$Results_Per_Page = 20;
$PageNumber = 1;

if (isset($_GET["results_per_page"]) == true)
{
	if($DisplayArray[$_GET["results_per_page"]] != "")
	{
		$Results_Per_Page = mysql_prep($DisplayArray[$_GET["results_per_page"]]);
	}
}

if (isset($_GET["PageNumber"]) == true)
{
	if(mysql_prep($_GET['PageNumber']) != "")
	{
		if (($PageNumber > 0) && ($PageNumber <= ceil($num_rows/$Results_Per_Page)))
		{
		$PageNumber = mysql_prep($_GET['PageNumber']);
		}
	}
}
$sql = $sql . " ORDER BY `patient_id`,`barcode` ASC ";
$sql = $sql . " LIMIT ".(($PageNumber-1)*$Results_Per_Page).",".$Results_Per_Page.";";

//$contents = $contents . $sql;

//Display the Table with the data in it. This can be modified to create an XML output if that is required later
$result = mysql_query($sql);

while($row = mysql_fetch_array($result))
{
	foreach(array_keys($SelectIndexArray) as $value)
	{
		if(array_key_exists($value,$row))
		{
			if(array_key_exists($value, $DataFields))
			{
				$contents = $contents . $DataFields[$value][$row[$value]];
			}
			else
			{
				$contents = $contents . $row[$value];
			}
			$contents = $contents . " \t ";
		}
	}
	$contents = $contents . " \n";
}


$filename ="excelreport.xls";
header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
echo $contents;

?>