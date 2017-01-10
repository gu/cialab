<?php 
include("./SecurePage.php");
include("./GlobalVariables.php");
include("./GlobalFunctions.php"); 
?>
<?php
if(isset($_FILES["SearchFile"]) == true)
{
	
	$ServerMode = false;
	$target_path = "./uploads/";
	$RemoteUploadPath = "./uploads/";
	
	$UniServerPath = "C:\Users\\rschmidt\Desktop\UniServer\\";
	$LocalUploadPath = $UniServerPath . "www\uploads\\";
	$DatabaseApp = "C:\Users\\rschmidt\Documents\Visual Studio 2008\Projects\DatabaseApp2\DatabaseApp2\bin\\Release\DatabaseApp2.exe";
	
	if($ServerMode == true)
	{
		$UniServerPath = "\\\\LANGLEY.BMI.OHIO-STATE.EDU\Data\misc_stuff\amie\UniServer\\";
		$DatabaseApp = $UniServerPath . "database_app\DatabaseApp2.exe";
		$LocalUploadPath = $UniServerPath . "www\\uploads\\";
	}
	
	//$RemoteUploadPath = $RemoteUploadPath . basename( $_FILES['SearchFile']['name']); 
	$FileName = explode(".",$_FILES["SearchFile"]["name"]);
	$UniqueFileId = md5($_FILES["SearchFile"]["name"]);
	$FileExtension = $FileName[count($FileName)-1];
	//echo $UniqueFileId;
	$RemoteUploadPath = $RemoteUploadPath . $UniqueFileId . "." . $FileExtension; 

	if(move_uploaded_file($_FILES['SearchFile']['tmp_name'], $RemoteUploadPath)) 
	{
		//echo "The file ".  basename( $_FILES['SearchFile']['name']). " has been uploaded";
	} 
	else
	{
		//echo "There was an error uploading the file, please try again!";
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML>
<TITLE>Search For Images</TITLE>
<script language='Javascript'>
function loaded(id) 
{
	var i = document.getElementById(id);
	
	if (i.contentDocument) {
		var d = i.contentDocument;
	} else if (i.contentWindow) {
		var d = i.contentWindow.document;
	} else {
		var d = window.frames[id].document;
	}
	if (d.location.href == "about:blank") {
		return;
	}
	document.getElementById('FileNameObj').value = "";
	document.getElementById('loadingimg').style.visibility = "collapse";
	document.getElementById('loadingimg').style.height = "0px";
	document.getElementById('Results').innerHTML = d.body.innerHTML;
}
function loading()
{
	document.getElementById('loadingimg').style.visibility = "visible";
	document.getElementById('loadingimg').style.height = "100px";
	//document.getElementById('Results').innerHTML="<div id='loadingimg' style='width:100px;height:100px;margin-left:100px;margin-right:100px;'><img src='../images/loading.gif'></div>"; 
	document.forms[0].submit();
}

</script>
<link rel="stylesheet" type="text/css" href="./css/indexstyle.css" />
<script language="Javascript" src="../qtip.js"></script>
<BODY style="background-color: rgb(255,255,255);background-attachment: fixed;background-repeat: repeat-x;">
<div id="TotalPage">
	<div id="Content">
		<div id="Header"></div>
		<div id="NavBar">
			<?php
			print_head("search.php");
			?>
		</div>
		<script language='JavaScript'>
		function ShowHide()
		{
			
			if(document.getElementById('OptionsText').innerHTML == 'Show Options')
			{
				document.getElementById('OptionsText').innerHTML = 'Hide Options';
				document.getElementById('options').style.display = 'block';
			}
			else
			{
				document.getElementById('OptionsText').innerHTML = 'Show Options';
				document.getElementById('options').style.display = 'none';
			}
		}
		</script>
		<style type='text/css'>
		.nodec A:link
		{
			text-decoration: none;
			color:black;
		}
		.nodec A:visited
		{
			text-decoration: none;
			color:black;
		}
		.nodec A:active
		{
			text-decoration: none;
			color:black;
		}
		.nodec A:hover
		{
			text-decoration: none;
			color:black;
		}
		</style>
		<div id="SearchBar" >
			<span class="nodec">
			<div id='DataFields' style='width:753px; float:left; text-align:left;border-bottom:1px solid #D4D4D4;border-left:1px solid #D4D4D4;border-right:1px solid #D4D4D4;overflow:hidden;'>
				<form enctype="multipart/form-data" name='form1' action="./ImageSearchServer.php" target="HiddenSearch" method="POST">
					<div id='TopSearchBar' style='width:748px;float:left;'><div id='UploadDiv' style='float:left;text-decorations:none;color:black;margin-left:10px;margin-top:5px;'>Upload File: <input type="file" name="SearchFile" style='' onChange="loading();"></div><div id='optionsDiv' style='float:right;font-size: 16px;margin-top:4px;margin-right:10px;'><a class='nodec' id='OptionsText' href='#' onClick="ShowHide()">Show Options</a></div></div>
					<div id='options' style="margin-top:10px;float:left;display:none;width:753px;background-color:#4B4B4B;color:#F0F0F0;">
					
					<div id='settings_1' style='float:left;border-right: solid 1px rgb(212, 212, 212);border-left: solid 1px rgb(212, 212, 212);padding-right:10px;padding-left:10px;margin-left:-1px;'>
						<table>
							<tr rowspan='2'><u>Search Details</u></tr>
							<tr>
								<td>Matlab:</td>
								<td>
									<select name="matlab_version">
										<option value="matlabr2009b">Matlab R2009b</option>
										<option value="matlabr2010a">Matlab R2010a</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>Disease:</td>
								<td>							
									<select name="disease_selection">
										<option value="-1">Any</option>
										<?php
										Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_imgfeatures);
										$sql = "SELECT `id`,`disease` FROM `disease`;";
										$result = mysql_query($sql);
										while($row = mysql_fetch_array($result, MYSQL_ASSOC))
										{
											echo "<option value='".$row['id']."'>" . $row['disease'] . "</option>";
										}
										?>
									</select>
								</td>
							</tr>
						</table>
					</div>				
					<div id='settings_1' style='float:left;border-right: solid 1px rgb(212, 212, 212);border-left: solid 1px rgb(212, 212, 212);padding-right:10px;padding-left:10px;margin-left:-1px;'>
						<table>
							<tr rowspan='2'><u>Results</u></tr>
							<tr>	
								<td>Returned:</td>
								<td>
									<select name="result_number">
										<option value="10">10</option>
										<option value="20">20</option>
										<option value="30">30</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>Based On:</td> 
								<td>
									<select name="decision_number">
										<option value="1">1</option>
										<option value="3">3</option>
										<option value="5">5</option>
										<option value="10">10</option>
										<option value="15">15</option>
										<option value="20">20</option>
									</select>
								</td>
							</tr>
						</table>
					</div>
					
					</div>
					<input type='hidden' value='SearchFile' name='SearchFile'>
					<input type='hidden' id='FileNameObj' value='<?php if(isset($UniqueFileId) && $UniqueFileId != ""){echo $UniqueFileId . "." . $FileExtension;} ?>' name='FileName'>
				</form>
				<iframe style="display:none" src="about:blank" id="HiddenSearch" name="HiddenSearch" onload="loaded('HiddenSearch')"></iframe>
			</div>
			</span>
		</div>
		<div id="CenterPage" style=''>
			<div id='loadingimg' style='visibility:collapse;text-align:center;width:100px;height:0px;margin-left:auto;margin-right:auto;'>
				<img src='../images/loading.gif'>
			</div>
			<div id='Results' style='float:left;width:100%;'></div>
		</div>
		<div id="Footer"></div>
	</div>
</div>
<?php if(isset($UniqueFileId) && $UniqueFileId != ""){echo "<script language='Javascript'>loading();</script>";} ?>
<script language='Javascript'>
document.getElementById('Footer').style.top = (window.innerHeight - 47) + "px";
document.getElementById('CenterPage').style.minHeight = (window.innerHeight - 169 - 50) + "px";
//alert(document.getElementById('Header').style.height);
window.onresize = function()
{
	//alert(document.getElementById('Header').style.height + document.getElementById('NavBar').style.height);
	document.getElementById('CenterPage').style.minHeight = (window.innerHeight - 169 - 50) + "px";
	document.getElementById('Footer').style.top = (window.innerHeight - 47) + "px";
}
</script>
</BODY>
</HTML>