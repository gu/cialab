<?php 
include("SecurePage.php");
include("GlobalVariables.php"); 
include("GlobalFunctions.php"); 
 ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML>
<TITLE>Login To Barcode Database</TITLE>
<link rel="stylesheet" type="text/css" href="./css/indexstyle.css" />
<div id="TotalPage">
	<div id="Content">
		<div id="Header"></div>
		<div id="NavBar">
			<?php
			print_head("index.php");
			?>
			
		</div>
		<style type="text/css">
		#NewsBlockHeader
		{
			width:338px;
			float:left;
			border: 2px solid #D4D4D4;
			padding:3px;
			border-bottom-width:4px;
			background-color:rgb(245,245,245);
			color:#4B4B4B;
			font-family:Arial;
			font-size: 25px;
			text-align: left;
			overflow:hidden;
		}
		#NewsBlockBody
		{
			float:left;
			width:338px;
			border: 2px solid rgb(212,84,11); 
			padding:3px;
			background-color:white;
			color:#4B4B4B;
			font-family:Arial;
			font-size: 14px;
			text-align: left;
			overflow:hidden;
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
			overflow:hidden;
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
			width: 346px;
			margin: 12px;
			display: inline;
		}
		</style>
		<div id="CenterPage">
			<div id='LeftSide' style='width: 372px; float:left;'>

				<div id='NewsBlock'>
					<div id='NewsBlockHeader' style='border-bottom-color:#4646b1;'>
					CIA Lab News
					</div>
					<div id='NewsBlockBody' style='border-color: #4646b1'>
					The Clinical Image Analysis Laboratory has updated the layout for CellMarker&#0153;, formally known as CBMarker. There have also been additional options created to allow for additional participants to fully utilize the system. Additional options include the ability to create multiple markings corresponding to different tissues and cell types. HTML5 features are on their way which will include the ability to annotate regions within the specific images!
					</div>
				</div>

			</div>
			<div id='RightSide' style='width:372px;float:right;'>

				<div id='NewsBlock'>
					<div id='NewsBlockHeader' style='border-bottom-color:#09F;'>
					CellMarker&#0153; Additions
					</div>
					<div id='NewsBlockBody' style='border-color:#09F'>
					The Full Slide viewer is fully functional with the newly added ability for marking differentiation! Other additions are soon to come.
					</div>
				</div>
					<div id='NewsBlock'>
					<div id='NewsBlockHeader' style='border-bottom-color:rgb(155,144,142);'>
					Full Slide Images are viewable!
					</div>
					<div id='NewsBlockBody' style='border-color: rgb(155,144,142)'>
						The demo for the full slide annotator is also being released today,
						accessible <a target='other' href='http://140.254.126.245/fullslide/index.php?pid=59/'>here</a>.
					</div>

				</div>
			</div>
				<?php
				/*
				<div id='NewsBlock'>
					<div id='NewsBlockHeader' style='border-bottom-color:rgb(212,84,11);'>
					Barcode Lookup
					</div>
					<div id='NewsBlockBody' style='border-color: rgb(212,84,11)'>
					
					<FORM method='GET' action='viewentry.php'>
						Barcode:
						<input type='TEXT' name='barcode' style='width: 170px;padding:3px;margin:3px;'>
						<input type='SUBMIT' style='width:75px;padding:3px;margin:3px;' value='Find'>
					</FORM>
					</div>
				</div>
				<div id='NewsBlock'>
					<div id='NewsBlockHeader' style='border-bottom-color:rgb(95,120,3);'>
					Control Panel
					</div>
					<div id='NewsBlockBody' style='border-color: rgb(95,120,3);'>
					
					<div id='NewBlockTitle'>User Controls:</div>
					
						<div id='NewBlockLinkObj'>Add Users</div>
						<div id='NewBlockLinkObj'>Remove Users</div>
						<div id='NewBlockLinkObj'>Edit Users</div>
					
					<div id='NewBlockTitle'>DataField Controls:</div>
					
						<div id='NewBlockLinkObj'>Add Fields</div>
						<div id='NewBlockLinkObj'>Remove Fields</div>
						<div id='NewBlockLinkObj'>Edit Fields</div>
						
					<div id='NewBlockTitle'>DataSet Controls:</div>
					
						<div id='NewBlockLinkObj'>Add Data Set</div>
						<div id='NewBlockLinkObj'>Remove Data Set</div>
						<div id='NewBlockLinkObj'>Edit Data Set</div>

					</div>
				</div>
			</div>
			<div id='RightSide' style='width:372px;float:right;'>
				<div id='NewsBlock'>
					<div id='NewsBlockHeader' style='border-bottom-color:rgb(0,96,141);'>
					Control Panel
					</div>
					<div id='NewsBlockBody' style='border-color: rgb(0,96,141);'>
					
					<div id='NewBlockTitle'>User Controls:</div>
					
						<div id='NewBlockLinkObj'>Add Users</div>
						<div id='NewBlockLinkObj'>Remove Users</div>
						<div id='NewBlockLinkObj'>Edit Users</div>
					
					<div id='NewBlockTitle'>DataField Controls:</div>
					
						<div id='NewBlockLinkObj'>Add Fields</div>
						<div id='NewBlockLinkObj'>Remove Fields</div>
						<div id='NewBlockLinkObj'>Edit Fields</div>
						
					<div id='NewBlockTitle'>DataSet Controls:</div>
					
						<div id='NewBlockLinkObj'>Add Data Set</div>
						<div id='NewBlockLinkObj'>Remove Data Set</div>
						<div id='NewBlockLinkObj'>Edit Data Set</div>

					</div>
				</div>
				<div id='NewsBlock'>
					<div id='NewsBlockHeader' style='border-bottom-color:rgb(155,144,142);'>
					Image Search
					</div>
					<div id='NewsBlockBody' style='border-color: rgb(155,144,142)'>
						<div id='wrap' style='position:relative;width:330px; height:30px;'>
						<FORM name='ImageSearch' enctype="multipart/form-data" method='POST' action='search.php'>
							<div id='name' style='float:left; padding-top:5px;'>Image:</div>
							<style type="text/css">
								#test1
								{
									position:absolute; 
									top:2px; 
									left:260px; 
									width:73px; 
									height: 28px; 
									background-image:url("search.png");
									background-repeat:no-repeat;
								}
								#test1:hover
								{
									background-image:url("searchover.png");
								}
							</style>
							
							
							<!--[if IE]>
							<input type='FILE' name='SearchFile' style='width:286px;height:30px;position:absolute;right:0px;top:0px;margin:0px;opacity:100;z-index:200;' onChange='document.forms["ImageSearch"].submit();'>
							<![endif]-->
							
							<!--[if !IE]>-->
							<div id='test1' style='position:absolute; top:0px; left:260px; width:73px; height: 24px;padding-top:5px;text-align:center;'>Browse</div>
							<input type='TEXT' style='height:21px;width:206px;position:absolute;margin-left:5px;z-index:400'>
							<input type='FILE' name='SearchFile' style='height:30px;position:absolute;right:-4px;top:-1px;margin:0px;opacity:100;z-index:200;' onChange='document.forms["ImageSearch"].submit();'>
							<!--<![endif]-->
							
							
						</FORM>
						</div>
					</div>
				</div>
				*/
				?>
		</div>
		<div id="Footer"></div>
	</div>
</div>

</HTML>