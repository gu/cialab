<?php include("SecurePage.php"); ?>
<?php include("GlobalVariables.php"); ?>
<?php include("GlobalFunctions.php"); ?>
<?php Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab); ?>

<?php
if($_SESSION['Permissions']['view_entries'] != 1)
{
	header("Location:".$MainIndex);
}


//Get Variables & sanitize them
$id = "";
$edit = 0;
$error = "";
$alert = "";
if (isset($_GET['id']) == true)
{
	$id = mysql_prep($_GET['id']);
}
if (isset($_GET['edit']) == true)
{
	$edit = mysql_prep($_GET['edit']);
}

if (isset($_POST["SaveEntry"]))
{
	//print_r($_POST);
	
	if(mysql_prep($_POST["patient_id"]) != "" && mysql_prep($_POST["case"]) != "" && mysql_prep($_POST["slide"]) != "" && mysql_prep($_POST["stain"]) != "")
	{
		$sql = "SELECT * FROM `slides_rep`";
		$result = mysql_query($sql);
		$DataRep = array();
		$SQLArray = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$DataRep[$row["index"]] = $row["edit"];
		}
		//UPDATE `cialab`.`slides_rep` SET `general_display` = '0' WHERE `slides_rep`.`id` =14;

		foreach (array_keys($_POST) as $value)
		{
			if(array_key_exists($value, $DataRep) && $DataRep[$value] == 1)
			{
				array_push($SQLArray, " `".$value."` = '" . mysql_prep($_POST[$value]) . "' "); //filter against mysql injections
			}
		}
		
		//array_push($SQLArray,"`date` = '" . date("Y-m-d G:i:s") . "' ";
		
		$sql = "UPDATE `cialab`.`slides_data` SET " . join(" , ", $SQLArray) . " WHERE `slides_data`.`id` = ".$id;
		//echo $sql;
		
		$result = mysql_query($sql);
		if (!$result) 
		{
			$error = "Error: Could not add to database";
			//echo $sql;
		}
		else
		{
			$alert = "Edits Saved Successfully";
		}
	}
	else
	{
		$error = "Error: Patient ID, Case, Slide and Stain are required Fields";
	}
}

?>
<?php
	$NoBarcodeFound = false;
	$DataArray = array();
	if (isset($_GET['barcode']) == true && $id=="")
	{
		$sql = "SELECT * FROM `slides_data` WHERE `barcode` = '".mysql_prep($_GET['barcode'])."'";
		$result = mysql_query($sql);
		$DataArray = mysql_fetch_array($result, MYSQL_ASSOC);
		if(mysql_num_rows($result) == 0)
		{
		$NoBarcodeFound = true;
		}
	}
	else
	{
		//Get Data for ID
		$sql = "SELECT * FROM `slides_data` WHERE `id` = '".$id."'";
		$result = mysql_query($sql);
		$DataArray = mysql_fetch_array($result, MYSQL_ASSOC);
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML>
<TITLE>Login To Barcode Database</TITLE>
<link rel="stylesheet" type="text/css" href="./css/indexstyle.css" />
<script language="Javascript" src="qtip.js"></script>
<style type="text/css">
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
<div id="TotalPage">
	<div id="Content">
		<div id="Header"></div>
		<div id="NavBar">
			<?php
			print_head("entries.php");
			?>
		</div>
		<div id="CenterPage">
			<div id="CenterPageLeft">
				<div id='Barcode' style='float: left; text-align:center;display:inline-block;margin-top: 10px; margin-bottom: 0px;'>

				</div>
				<div id="FloatBox">
					<b>Tools</b>
					<a class="leftlinks" href="
					<?php 					
					echo "viewentry.php?id=" . $id . "&edit=0";
					?>
					"><div id="FloatBoxItem">View Fields</div></a>
					<a class="leftlinks" href="
					<?php 
					echo "viewentry.php?id=" . $id . "&edit=1";
					?>
					"><div id="FloatBoxItem">Edit Fields</div></a>
					<a class="leftlinks" href="
					<?php 
					echo "viewentry.php?id=" . $id . "&edit=2";
					?>
					"><div id="FloatBoxItem">Delete Entry</div></a>
					<?php
					if($NoBarcodeFound == false)
					{
						//$UniServerPath = "C:\Users\\rschmidt\Desktop\UniServer\\";
						try
						{
							$UniServerPath = "";
							if(file_exists("./images/barcodes/" . str_replace("|","-",$DataArray['barcode']) . ".png") == true)
							{
								echo "<img src='./images/barcodes/" . str_replace("|","-",$DataArray['barcode']) . ".png' style='width:100%;height100%'>";
							}
							/*
							$out = array();
							exec('cd', $out);
							if($out != null)
							{
								$wwwPath = $out[0];
								$UniServerPath = rtrim($wwwPath,"\\www") . "\\";
								
								$LocalUploadPath = $UniServerPath . "www\imgsearch\uploads\\";
								if(file_exists("./images/barcodes/" . str_replace("|","-",$DataArray['barcode']) . ".png") == false)
								{
									$myFile = $UniServerPath. "www\images\barcodes\\" . str_replace("|","-",$DataArray['barcode']) . ".txt";
									$fh = fopen($myFile, 'w') or die("can't open file");
									$stringData = $DataArray['barcode'];
									fwrite($fh, $stringData);
									fclose($fh);

									exec("\"" .$UniServerPath. "ImageMagick-6.6.3-Q8\dmtxwrite.exe\" " . "-o \"".$UniServerPath. "www\images\barcodes\\" . str_replace("|","-",$DataArray['barcode']) . ".png\" -s 24x24 \"" .$UniServerPath. "www\images\barcodes\\" . str_replace("|","-",$DataArray['barcode']) . ".txt\"", $result);
									exec("del \"" .$UniServerPath. "www\images\barcodes\\" . str_replace("|","-",$DataArray['barcode']) . ".txt\"", $result);
								}
								echo "<img src='./images/barcodes/" . str_replace("|","-",$DataArray['barcode']) . ".png' style='width:100%;height100%'>";
							}
							*/
						}
						catch(Exception $e)
						{
						}
					}
					?>
				</div>
			</div>
			<div id="CenterPageRight">
			
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

			
			<?php
			if($edit == 2)
			{
				$sql = "DELETE FROM `slides_data` WHERE `id` = " . $id . ";";
				$result = mysql_query($sql);
			}
			
			if($NoBarcodeFound == false)
			{
				echo "
					<div id='infocontainer'>";
					
				echo "
					<form name='Information' method='POST'>
					<b>General Information</b>
					<div id='infobar'></div>
					<table>";
				

				//Get Representation
				$PrivateArray = array();
				
				$sql = "SELECT `index`,`name`,`edit`,`private_information` FROM `slides_rep` WHERE `real_data` = TRUE";
				$result = mysql_query($sql);
				
				while($row = mysql_fetch_array($result, MYSQL_ASSOC))
				{
					if ($row['private_information'] == false)
					{
						echo "<tr style='padding:2px;'><td style='text-align: right; color:rgb(60,60,60);' width='25%'>".$row["name"]." :</td><td width='2%'></td><td width='73%'>";
						if($edit==0)
						{
							echo ReturnDataValue($row['index'],$DataArray[$row['index']]);
						}
						elseif($edit==1)
						{
							if($row['edit'] == true)
							{
								echo ReturnDataField($row['index'],"", $DataArray[$row['index']],"145px",false,"",false,"slides_rep",true);
							}
							else
							{
								echo ReturnDataValue($row['index'],$DataArray[$row['index']]);
							}
						}
						echo "</td></tr>";
					}
					else
					{
						$PrivateArray[] = $row;
						//$PrivateArray[$row['index']] = $row['name'];
					}
				}
				echo "</table>";
				echo "<br><b>Private Information</b><div id='infobar'></div><table>";
				foreach($PrivateArray as $row)
				{
					echo "<tr style='padding:2px;'><td style='text-align: right; color:rgb(60,60,60);'>".$row['name']." :</td><td width='5px'></td><td>";
					if($edit==0)
					{
						echo ReturnDataValue($row['index'],$DataArray[$row['index']]);
					}
					elseif($edit==1)
					{
						if($row['edit'] == true)
						{
							echo ReturnDataField($row['index'],"", $DataArray[$row['index']],"145px",false,"",false,"slides_rep",true);
						}
						else
						{
							echo ReturnDataValue($row['index'],$DataArray[$row['index']]);
						}
					}
					echo "</td></tr>";
				}
				echo "</table>";
				if($edit==1)
				{
					echo "<br><input type='submit' name='SaveEntry' value='Save Edits'>";
				}
				
				if(file_exists("./images/thumbnails/" . str_replace("|","-",$DataArray['barcode']) . ".png") == true && $edit != 1)
				{
				echo "<br><b>Thumbnail</b><div id='infobar'></div><table style='margin-left:auto;margin-right:auto;'>";
				echo "<tr><td><img src='" . "./images/thumbnails/" . str_replace("|","-",$DataArray['barcode']) . ".png" . "'></td></tr>";
				echo "</table>";
				}
				
				echo "</div></form>";
			
				
				/*
				//if the user asks to delete verify
				else if($edit == 2 && $barcode != "")
				{
					
					echo "<br><h3>Are you sure you want to remove entry?</h3>";
					echo "<a class='tab' href='viewentry.php?barcode=" . $barcode . "&edit=3'><div id='Button'>Yes</div></a>";
					echo "<a class='tab' href='viewentry.php?barcode=" . $barcode . "&edit=0'><div id='Button'>No</div></a>";
				}
				//After verification delete the entry
				else if($edit == 3 && $barcode != "")
				{
					Connect_To_DB($db_server, $db_user, $db_pwd, $db_name);
					echo "<h3>Entry Deleted</h3><br>";
					$sql = "DELETE FROM `slides_data` WHERE `barcode` = " . $barcode;
					$result = mysql_query($sql);
				}
				*/
			}
			else
			{
			echo "<div id='myoutercontainer' style='position:relative;width:600px; height:200px'>
					<div id='myinnercontainer' style='position:absolute; top:50%;width:100%; margin-top:-2em; text-align:center;'>
						No Entry Found in Database
					</div>
				</div>";
			}
			
			?>
			</div>
		</div>
		<div id="Footer"></div>
	</div>
</div>

</HTML>