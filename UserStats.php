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
<div id="TotalPage">
	<div id="Content">
		<div id="Header"></div>
		<div id="NavBar">
			<?php
			print_head("");
			?>
			
		</div>
		<div id="CenterPage">
			<div id='RightSide' style='width: 713px;float:left;margin-top:20px;margin-bottom:20px;margin-left:20px;margin-right:20px;'>

				<style type='text/CSS'>
				table
				{
					border-collapse:collapse;
					border: 1px black solid;
					width: 100%;
				}
				tr
				{
					border: 1px black solid;
				}
				td
				{
					border: 1px black solid;
				}
				th
				{
					border: 1px black solid;
				}
				</style>
				
				<H2>User Stats</H2>
				
				<TABLE>
				<tr>
					<th>Name</th>
					<th>Project Name</th>
					<th>Total Images</th>
					<th>Last Image Read</th>
					<th>Highest Image Marked</th>
					<th>Last Login</th>
					<th>Proportion Marked</th>
					<!--<th>SQL</th>-->
				</tr>

				<?php
				
					//This enumerates all the users.
					$sql = "SELECT `id` FROM `cialab`.`users_data`;";
					$result = mysql_query($sql);
					$projectsArray = array();
					$alt = false;
					while($row = mysql_fetch_array($result, MYSQL_ASSOC))
					{
						$trStyle = " style='background-color:rgb(255,121,124);' ";
						if($alt == true){$alt=false;}
						else{$alt=true;}
							
						//Set the current user id
						$userID = $row['id'];
						
						//This pulls all the info about the user and the roi projects he or she is associated with
						$sql2 = "SELECT `cialab`.`users_data`.`first_name`,`cialab`.`users_data`.`last_name`,`cbmarker`.`imgtracking`.`image`, `cbmarker`.`imgtracking`.`date` ,`cialab`.`roi_projects`.`id`,`cialab`.`roi_projects`.`name`,`cialab`.`roi_projects`.`folder`,`cialab`.`roi_projects_members`.`roi_project_id` FROM `cialab`.`users_data`,`cialab`.`roi_projects`,`cialab`.`roi_projects_members`,`cbmarker`.`imgtracking` WHERE `cialab`.`roi_projects`.`id`=`cialab`.`roi_projects_members`.`roi_project_id` AND `user_id`='".$userID."' AND `cbmarker`.`imgtracking`.`userid`=`user_id` AND `cialab`.`roi_projects`.`id`= `cbmarker`.`imgtracking`.`project_id` AND`user_id`=`cialab`.`users_data`.`id` GROUP BY `id` ORDER BY `date` ASC;";
						$result2 = mysql_query($sql2);
						
						while($row2 = mysql_fetch_array($result2, MYSQL_ASSOC))
						{
							//Get current temp table name
							$TempTableName = "ROI_Project_" . preg_replace("/[^a-zA-Z0-9]/", "", $row2['roi_project_id']);
							
							$resultValue = mysql_query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'cialab' AND table_name = '".$TempTableName."';");
							
							if(mysql_num_rows($resultValue) == 0)
							{
								$sql =  "
								CREATE TABLE `".$TempTableName."`
								(
								`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
								`name` VARCHAR(200) NOT NULL
								) ENGINE=MYISAM;";
									
								$result4 = mysql_query($sql);// or die(mysql_error());
								
								//This adds the projects in order to an array. This caches the information 
								//so it only has to do this once per project.
								if($result4 == true)
								{
									//*****One Method of listing files that are in a folder.
									//$dir_path = ".".$row2['folder'];
									//$files = glob($dir_path . "*.jpg");
									//$count = count($files);
									//$projectsArray[$row2['name']] = $files;
									
									//**Create Table to hold array of files, so mysql can query it.
									
									//$sql =  "DROP TABLE IF EXISTS ".$TempTableName.";";
									//$result4 = mysql_query($sql) or die(mysql_error());

									$handler = opendir(".".$row2['folder']);

									//Because I originally implemented the file finding this
									//way I have to use it to check which file the users are on
									//hopefully I can switch to a better method.
									while ($file = readdir($handler)) 
									{
										if($file != ".")
										{
											if($file != "..")
											{
												$sql = "INSERT INTO `".$TempTableName."` (`name`) VALUES ('".$file."')";
												$result4 = mysql_query($sql) or die(mysql_error());
											}
											
										}
									}
								}
							}
							//Begin Printout of Table
							
							if($alt){echo "<tr .$trStyle.>";}
							else{echo "<tr>";}
							
							//The First and Last Name of the user
							echo "	<td>";
							echo $row2['first_name']. " " . $row2['last_name'];
							echo "	</td>";
							
							//The Project Name
							echo "	<td>";
							echo $row2['name'];
							echo "	</td>";
							
							//Total Count of Images
							echo "	<td>";
							$sql =  "SELECT COUNT(`cialab`.`".$TempTableName."`.`id`) as count_value FROM `cialab`.`".$TempTableName."`";
							$rowValues = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
							echo $rowValues["count_value"];
							//echo $sql;
							echo "	</td>";
							
							//Last Image User Was On
							echo "	<td>";
							$sql =  "SELECT * FROM `cialab`.`".$TempTableName."` WHERE `name` = '".$row2['image']."'";
							$rowValues = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
							echo $rowValues["id"];
							//echo $sql;
							echo "	</td>";
							
							//Highest Image User Marked
							echo "	<td>";
							$sql =  "SELECT MAX(`cialab`.`".$TempTableName."`.`id`) as max_id, `cbmarker`.`cbdata`.`review_mark`, `cbmarker`.`cbdata`.`project_id`, `cialab`.`".$TempTableName."`.`name`, `cbmarker`.`cbdata`.`userid`, `cbmarker`.`cbdata`.`image` FROM `cialab`.`".$TempTableName."`, `cbmarker`.`cbdata` WHERE `cbmarker`.`cbdata`.`review_mark`='0' AND `cbmarker`.`cbdata`.`project_id` = '".$row2['roi_project_id']."' AND `cbmarker`.`cbdata`.`userid` = '".$userID."' AND `cialab`.`".$TempTableName."`.`name` = `cbmarker`.`cbdata`.`image` GROUP BY `cbmarker`.`cbdata`.`userid` HAVING MAX(`cialab`.`".$TempTableName."`.`id`)";
							$rowValues = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
							echo $rowValues["max_id"];
							//echo $sql;
							echo "	</td>";
							
							//Last Date User Looked At Image
							echo "	<td>";
							echo $row2['date'];
							echo "	</td>";
							
							//The percentage of images marked
							$sql = "SELECT COUNT(*) AS count FROM (SELECT * FROM `cbmarker`.`cbdata` WHERE `userid`='".$userID."' AND `project_id`='".$row2['roi_project_id']."' AND `review_mark`='0' GROUP BY `image`) AS p2";
							$rowValues = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
							$numberMarked = $rowValues["count"];
							
							$sql = "SELECT COUNT(*) AS count FROM `cialab`.`".$TempTableName."`";
							$rowValues = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
							$totalCount = $rowValues["count"];
							//The total count of the images.
							
							echo " <td>";
							echo "$numberMarked / $totalCount";
							echo " </td>";
							
							//echo " <td>";
							//echo $sql;
							//echo " </td>";
							
							echo "</tr>";
						}

					}
				?>
				</TABLE>
				
			</div>
		</div>
		<div id="Footer"></div>
	</div>
</div>

</HTML>