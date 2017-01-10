<?php 
include("SecurePage.php");
include("GlobalVariables.php"); 
include("GlobalFunctions.php"); 

Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab);

 ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML>
<TITLE>Login To Barcode Database</TITLE>
<link rel="stylesheet" type="text/css" href="./css/indexstyle.css" />
<script>
	function getProjectUrl(idx) {
		var arr = idx.split(',');
		var pid = arr[0];
		var uid = arr[1];
		var ret;

		if (pid === '169' || pid ==='170') {
			ret = '<?php echo $URL ?>'+'/classification/index.php?pid='+idx;
		} else {
			ret = '<?php echo $URL ?>'+'/cbmarkerv2/index.php?pid='+idx;
		}
		window.location = ret;
	}
</script>
<div id="TotalPage">
	<div id="Content">
		<div id="Header"></div>
		<div id="NavBar">
			<?php
			print_head("roimarker.php");
			?>
		</div>
		<div id="CenterPage">
			<div id='LeftSide' style='width: 153px; float:left;'>
			</div>
			<div id='RightSide' style='width: 600px;float:right;'>
				<br><br>
				Hello <b><?php echo $_SESSION['FirstName']." ".$_SESSION['LastName']; ?></b>,<br>
				Select a project to work on from the slection box below.
				<br><br>
				<SELECT id='project_selection' style='Width: 250px;' onChange='getProjectUrl(document.getElementById("project_selection").options[document.getElementById("project_selection").selectedIndex].value)'>
					<OPTION Value=''>Select Project</OPTION>
					<?php
					//This selects the porjects that the user is apart of.
					$sql = "SELECT `roi_projects`.`id`,`roi_projects`.`name`,`roi_projects`.`folder`,`roi_projects_members`.`roi_project_id` FROM `roi_projects`,`roi_projects_members` WHERE `roi_projects`.`id`=`roi_projects_members`.`roi_project_id` AND `roi_projects_members`.`project_viewable`='1' AND `user_id`='".$_SESSION['Id']."';";
					$result = mysql_query($sql);
					while($row = mysql_fetch_array($result, MYSQL_ASSOC))
					{
							//echo "<OPTION Value='".$row['id']."SPLIT".$_SESSION['Id']."'>".$row["name"]."</OPTION>";
							echo "<OPTION Value='".$row['id'].",".$_SESSION['Id']."'>".$row["name"]."</OPTION>";
					}
					
					?>
				</SELECT>
				<br><br>
			</div>
		</div>
		<div id="Footer"></div>
	</div>
</div>

</HTML>
