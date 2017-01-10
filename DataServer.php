<?php include("SecurePage.php"); ?>
<?php include("GlobalVariables.php"); ?>
<?php include("GlobalFunctions.php"); ?>
<?php
Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab);

if(isset($_GET["indices"]) == true)
{
	$indices = explode(",",$_GET["indices"]);
	foreach ($indices as $value)
	{
		//echo filter_var($value, FILTER_SANITIZE_STRING);
		AddToInformationField(mysql_prep($value));
	}
}

if(isset($_GET["patientpreview"]) == true)
{
	CreatePatientPreview(mysql_prep($_GET["patientpreview"]));
}

if(isset($_GET["lookupid"]) == true && isset($_GET["study"]) == true)
{
	
	$sql = "SELECT `patient_id` FROM `slides_data` WHERE `original_slide`='".mysql_prep($_GET["lookupid"])."' AND `datafield_1`='".mysql_prep($_GET["study"])."' LIMIT 1";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	echo $row['patient_id'];
}


function CreatePatientPreview($patientid)
{
	$StainArray = array();

	$sql = "SELECT * FROM `dataset_3`";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$StainArray[$row["value"]] = $row["name"];
	}

	$patientid = filter_var($patientid, FILTER_SANITIZE_NUMBER_INT);
	//echo $patientid;
	$sql = "SELECT * FROM `slides_data` WHERE `patient_id` = '".$patientid."' ORDER BY `case` ASC, `slide` ASC;";
	$result = mysql_query($sql);
	
	$PatientArray = array();
	
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if (array_key_exists($row["case"], $PatientArray) == false)
		{
			$PatientArray[$row["case"]] = array();
		}
		
		$PatientArray[$row["case"]][$row["slide"]] = $StainArray[$row["stain"]];
	}
	if (sizeof($PatientArray) > 0)
	{
		echo "<tr><td>Case</td><td colspan='10'>Slides</td></tr>";
		foreach (array_keys($PatientArray) as $SlideArrayName)
		{
			echo "<tr><td>".$SlideArrayName."</td><td><table id='SlideTable'><tr>";
				foreach (array_keys($PatientArray[$SlideArrayName]) as $value)
				{
					echo "<td><a title='".$PatientArray[$SlideArrayName][$value]."'>".$value."</a></td>";
				}
			echo "</tr></table></td></tr>";
		}
	}
	//print_r($PatientArray);
}

function AddToInformationField($index)
{
	$sql = "SELECT * FROM `slides_rep` WHERE `index` = '".$index."'";
	$result = mysql_query($sql);
	
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		echo "<tr style='padding:2px;'><td style='text-align: right; color:rgb(60,60,60);'>".$row["name"]." :</td><td width='5px'></td><td>";
		if ($row['index'] == 'patient_id')//Custom code run only for patient_id
		{
				$sql = "SELECT MAX(`patient_id`) FROM `slides_data`";
				$result = mysql_query($sql);
				$rowmax = mysql_fetch_array($result, MYSQL_NUM);

				echo ReturnDataField($index,"", ($rowmax[0] + 1),"145px",false,"onchange='UpdatePatientInformation(this.value)'",false,"slides_rep",true);
		}
		elseif($row['index'] == 'original_slide')//Custom JS code to be run on specific inputs
		{
			echo ReturnDataField($index,"", "","145px",false,"onchange='UpdateInformation(this.value);'",false,"slides_rep",true);
		}
		else
		{
			echo ReturnDataField($index,"", "","145px",false,"",false,"slides_rep",true);
		}
		echo "<input type='checkbox' name='".$row['index']."_save' id='".$row['index']."_save'> ";
		echo "</td></tr>";
	}
	
}
?>