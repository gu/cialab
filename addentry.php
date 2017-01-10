<?php include("SecurePage.php"); ?>
<?php include("GlobalVariables.php"); ?>
<?php include("GlobalFunctions.php"); ?>

<?php
$error = "";
$alert = "";
Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab)
?>

<?php

if (isset($_POST["addentry"]))
{
	//print_r($_POST);
	
	if(mysql_prep($_POST["patient_id"]) != "" && mysql_prep($_POST["case"]) != "" && mysql_prep($_POST["slide"]) != "" && mysql_prep($_POST["stain"]) != "" && mysql_prep($_POST["datafield_1"]) != "" && mysql_prep($_POST["datafield_0"]) != "")
	{
		$sql = "SELECT * FROM `slides_rep`";
		$result = mysql_query($sql);
		$DataRep = array();
		$InsertArray = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$DataRep[$row["index"]] = $row["edit"];
		}
		
		foreach (array_keys($_POST) as $value)
		{
			if(array_key_exists($value, $DataRep) && $DataRep[$value] == 1)
			{
				$InsertArray[("`".$value. "`")] = "'" . mysql_prep($_POST[$value]) . "'"; //filter against mysql injections
			}
		}
		
		if(array_key_exists("`barcode`", $InsertArray) == false)
		{
			$InsertArray["`barcode`"] = "'" . mysql_prep($_POST["patient_id"]) . "|" .mysql_prep($_POST["case"]) . "|" . mysql_prep($_POST["slide"]). "|" . mysql_prep($_POST["stain"]) . "'";
		}
		
		$InsertArray["`date`"] = "'" . date("Y-m-d G:i:s") . "'";
		
		$sql = "INSERT INTO `cialab`.`slides_data` (" . join(", ", array_keys($InsertArray)) . ") VALUES (" . join(", ", $InsertArray). ");";
		$result = mysql_query($sql);
		if (!$result) 
		{
			$error = "Error: Could not add to database";
			//echo $sql;
		}
		else
		{
			if ($_POST["addentry"]=="Add & Print Entry")
			{
				$barcode = mysql_prep($_POST["patient_id"]) . "|" .mysql_prep($_POST["case"]) . "|" . mysql_prep($_POST["slide"]). "|" . mysql_prep($_POST["stain"]);
				exec("\"C:/Users/rschmidt/Documents/Visual Studio 2008/Projects/BarcodeConsoleApp/BarcodeConsoleApp/bin/Release/BarcodeConsoleApp.exe\" \"" . $barcode . "\"",$ReturnArray);

			}
			$alert = "Slide Added Successfully";
			
		}
	}
	else
	{
		$error = "Error: Patient ID, Case, Slide and Stain are required Fields";
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML>
<TITLE>Login To Barcode Database</TITLE>

<style type="text/css">
#PatientInformation
{
border-collapse:collapse;
text-align:center;
}
#PatientInformation td
{
text-align:center;
}
#SlideTable
{
border-width: 1px;
border-color: black;
border-collapse:collapse;
text-align:center;
}
#SlideTable td
{
font-size:1em;
border:1px solid #98bf21;
padding:3px 5px 3px 5px;
}
#BadAlert
{
background-color: #F8F8F8;
border:1px solid red;
margin-top: 10px;
margin-left:10px;
margin-right:10px;
padding:5px;
}
#GoodAlert
{
background-color: #F8F8F8;
border:1px solid #33CC00;
margin-top: 10px;
margin-left:10px;
margin-right:10px;
padding:5px;
}

</style>

<link rel="stylesheet" type="text/css" href="./css/indexstyle.css" />
<script language="Javascript" src="qtip.js"></script>
<div id="TotalPage">
	<div id="Content">
		<div id="Header"></div>
		<div id="NavBar">
			<?php
			print_head("addentry.php");
			?>
		</div>
		<div id="CenterPage">

			<div id="CenterPageLeft" style="width:190px;float:left;">
				<div style="margin-top:10px;margin-bottom:10px;">
				<b>Variable Options</b>
				</div>
				<SELECT NAME="" style="Width: 180px;" SIZE = 12 onchange="AddDataField(this.value);">
					<?php
					$sql = "SELECT * FROM `slides_rep`";
					$result = mysql_query($sql);
					
					while($row = mysql_fetch_array($result, MYSQL_ASSOC))
					{
						if ($row["edit"] == 1)
						{
							echo "<OPTION Value='".$row["index"]."'>".$row["name"]."</OPTION>";
						}
					}
					?>
				</SELECT>
				<div style="height:20px;float:left;width:100%;"></div>
			</div>
			
			<div id="CenterPageRight" style="float:right;width:543px;">

				<?php 
				if ($error != "")
				{
					echo "<div id='BadAlert'>".$error."</div>"; 
				}
				if ($alert != "")
				{
					echo "<div id='GoodAlert'>".$alert."</div>"; 
				}
				?>

				<div id='infocontainer' style="float:right;width:528px;">
					<div style="margin-top:15px;"><b>Information</b></div>
					<div id='infobar'></div>
					<form name="InformationForm" method='POST'>
					<div id='InformationDiv'></div>
					</form>
					<div id='PatientInformationDiv'>
					</div>
				</div>
			</div>
		</div>
		<div id="Footer"></div>
	</div>
</div>

</HTML>

<script type="text/javascript">
	var DataIndexField = new Array();
	function WebRequest(URL,DivName,AddToFront,AddToBack,Tag)
	{
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{	
				if(Tag == "lookupid")
				{
					var elementsList = document.getElementsByName('patient_id');
					if(elementsList.length > 0)
					{
						elementsList[0].value = xmlhttp.responseText;
					}
				}
				else if(Tag == "patientpreview")
				{
					document.getElementById(DivName).innerHTML = AddToFront + xmlhttp.responseText + AddToBack;
					tooltip.init();
				}
				else if(Tag == "indices")
				{
					document.getElementById(DivName).innerHTML = AddToFront + xmlhttp.responseText + AddToBack;
					SaveValues();
					tooltip.init();
				}
			}
		}
		xmlhttp.open("GET",URL,true);
		xmlhttp.send();
	}
	function AddDataField(indices)
	{
		//This code eliminates duplicate data fields
		var SplitIndices = indices.split(",");
		for(var x=0;x<SplitIndices.length;x++)
		{
			for(var i=0;i<DataIndexField.length;i++) 
			{
				if(SplitIndices[x] === DataIndexField[i])
				{
				  SplitIndices.remove(x);
				  break;
				}
			}
			DataIndexField[DataIndexField.length] = SplitIndices[x];
		}
		indices = SplitIndices.join(",");
		TableTopHTML = "<table id='Information' style='float:left;'><tbody>";

		if (document.getElementById('InformationDiv').innerHTML != "")
		{
			var content = document.getElementById('InformationDiv').innerHTML;
			var test = content.split("<TBODY>");
			if (test.length > 1) //For Internet Explore (they always capitalize tbody
			{
				TableBottomHTML = test[1];
			}
			else //for Firefox they always lowercase tbody
			{
				var test = content.split("<tbody>");
				TableBottomHTML = test[1];
			}
		}
		else
		{
			TableBottomHTML = "<tr><td></td><td></td><td><br><input type='submit' name='addentry' value='Add & Print Entry' style='width:144px;'></td></tr><tr><td></td><td></td><td><input type='submit' name='addentry' value='Add Entry' style='width:144px;'></td></tr></tbody></table>";
		}
		WebRequest("<?php echo $URL; ?>/DataServer.php?indices=" + indices,'InformationDiv',TableTopHTML,TableBottomHTML,"indices");
	}
	function UpdatePatientInformation(patientid)
	{
		TableHTML = "<table id='PatientInformation' border='1' style='float:right;text-align:right;'>";
		WebRequest("<?php echo $URL; ?>/DataServer.php?patientpreview=" + patientid,'PatientInformationDiv',TableHTML,"</table>","patientpreview");
	}
	function UpdateInformation(value)
	{
		var elements = document.getElementsByName('datafield_1');
		if(elements != null && elements.length > 0)
		{
			var element = elements[0];
			if(element.value != "")
			{
				WebRequest("<?php echo $URL; ?>/DataServer.php?lookupid=" + value + "&study="+element.value,"","","","lookupid");
			}
		}

	}
	AddDataField('patient_id,case,slide,stain,datafield_0,datafield_1,datafield_3');
	
	function SaveValues()
	{
		
		var previousValuesArray = Array();
		<?php 
		foreach(array_keys($_POST) as $value)
		{
			echo "previousValuesArray['".$value."']='".$_POST[$value]."';\n";
		}
		?>

		for(x in previousValuesArray)
		{
			var elementsList = document.getElementsByName(x);
			if(elementsList.length > 0)
			{
				if(previousValuesArray[x+'_save'] == 'on')
				{
					elementsList[0].value = previousValuesArray[x];
					document.getElementById(x+'_save').checked = true;
				}
			}
		}
		
	
	}
</script>
