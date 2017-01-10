<?php 
include("SecurePage.php");
include("GlobalVariables.php"); 
include("GlobalFunctions.php"); 

Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab);

 ?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML>
<TITLE>Login To Barcode Database</TITLE>
<script language='javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>
<link rel="stylesheet" type="text/css" href="./css/indexstyle.css" />
<div id="TotalPage">
	<div id="Content">
		<div id="Header"></div>
		<div id="NavBar">
			<?php
			print_head("uploadskin.php");
			?>
			
		</div>
		
		 <script language='Javascript'>
			function uploadComplete(returnString)
			{
				updateResults(returnString,true);
				//$('#Results').css('display','block');
				//$('#Results').html(returnString);
			}
			function updateResults(HTMLtext,fade)
			{
				//Display the data
				$('#Results').stop();
				$('#Results').css('display','none');
				$('#Results').html(HTMLtext);
				
				//If fade then fade the data in and out.
				if(fade==true)
				{
					$('#Results').fadeIn(3000);
					$('#Results').fadeOut(3000);
				}
				else
				{
					$('#Results').css('display','block');
				}
			}
			function uploadStart()
			{
				$('#Results').css('display','block');
				$('#Results').html("<img src='./images/loading.gif'>");
			}
		</script>
		
		<style type="text/css">
		#NewsBlockHeader
		{
			width:340px;
			float:left;
			border: 2px solid #D4D4D4;
			padding:3px;
			border-bottom-width:4px;
			background-color:rgb(245,245,245);
			color:#4B4B4B;
			font-family:Arial;
			font-size: 25px;
			text-align: left;
		}
		#NewsBlockBody
		{
			float:left;
			width:340px;
			border: 2px solid rgb(212,84,11); 
			padding:3px;
			background-color:white;
			color:#4B4B4B;
			font-family:Arial;
			font-size: 14px;
			text-align: left;
		}
		#NewBlockTitle
		{
			color:black;
			font-weight:bold;
			float:left;
			width:100%;
			margin-top:5px;
			margin-bottom:3px;
			margin-left: 3px;
		}
		#NewBlockLinkObj
		{
			float:left;
			padding: 3px;
			border: 1px solid #D4D4D4;
			background-color: rgb(245,245,245);
			color: #4B4B4B;
			text-decoration: none;
			margin:3px;
		}
		#NewBlockLinkObj:hover
		{
			cursor: pointer;
			color: rgb(245,245,245);
			background-color: #4B4B4B;
		}
		#NewsBlock
		{
			float:left;
			width:350px;
			margin:12px;
		}
		</style>
		<div id="CenterPage">

			<div id='PanelTop' style ='margin-left:auto; margin-right:auto;padding-top:20px;font-size:200%'>
					Upload Images
			</div>
			<div id='PanelBottom' style='text-align:center; margin-left:auto; margin-right:auto;padding-top:20px;'>
				<form action='uploadskinfunct.php' method='POST' enctype='multipart/form-data' target='upload_target' name='UploadReviewSetForm' id='UploadReviewSetForm'>
				<div id='DataFields'>
				<INPUT type='HIDDEN' value='UploadImages' name='UPLOAD_NAME' id='UPLOAD_NAME'>
				<TABLE style='margin-left:auto; margin-right:auto;'>
					<tr>
						<td>
						Project
						</td>
						<td>
						File
						</td>
						<td>
						</td>
					</tr>
					<tr>
						<td>
							<SELECT NAME='projects' id='projects' style='Width: 180px;' SIZE=1>";
							<?php 
							$sql = "SELECT `name`,`id` FROM `roi_projects`";
							$result = mysql_query($sql);
							while($row = mysql_fetch_array($result, MYSQL_ASSOC))
							{
									echo "<OPTION Value='".$row['id']."'>".$row["name"]."</OPTION>";
							}

							echo "</SELECT>";
							 ?>
						</td>
						<td>
							<input type='file' value='UploadedFile' name='UPLOAD' id='UPLOAD'>
						</td>

						<td>
							<input type='submit' name='submit' value='Upload Image' onClick='uploadStart();'>
						</td>
					<tr>
					<tr>
						<td></td>
						<td>
							<input type='checkbox' id='overwrite' name='overwrite' value='true'> Overwrite Files
						</td>
						<td></td>
					</tr>
				</TABLE>
				<div id='Results'></div>
				<TABLE>
					<tr>
						<td><h3>Note: Only Image Data in a Web-based format</h2></td>
					</tr>
					<tr>
						<td>
						This upload feature currently accepts only JPG or GIF files.<b></div>
						</td>
					</tr>
						<tr>
						<td>
						Please have unique names for each image that is uploaded in its respective project. Uploading two images with the same name (e.g. temp.jpg and temp.jpg) into the same project will result in the first one being overwritten.</div>
						</td>
					</tr>
				</TABLE>
				
				</div>
				</form>
				
				<iframe id='upload_target' name='upload_target' src='#' style='width:0px;height:0px;border:0px solid #fff;'></iframe>
			</div>
		</div>
		<div id="Footer"></div>
	</div>
</div>

</HTML>