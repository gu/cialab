<?php include("SecurePage.php"); ?>
<?php include("GlobalVariables.php"); ?>
<?php include("GlobalFunctions.php"); ?>
<?php 
Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab); 

if($_SESSION['Permissions']['view_entries'] != 1)
{
	header("Location:".$MainIndex);
}

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
			print_head("entries.php");
			?>
		</div>
		<div id="CenterPage">
			<div id="CenterPageLeft">
				<!--
				<div id="FloatBox" style="float:left; width:133px; margin-left: 4px; margin-right: 4px; margin-top:20px; border: 1px solid rgb(180,180,180); padding: 3px;background-color: rgb(250,250,250);">
					Filters:
				</div>
				-->
				<div id='FilterContainer'>
					<b>Filters</b>
					<div id='infobar' style="margin"></div>
					
					<table style="border:0; margin:0px; padding-1:px;">
					<FORM method='GET'>
					
					<?php
					$sql = "SELECT * FROM `slides_rep` WHERE `filter_display` = TRUE";
					$result = mysql_query($sql);
					while($row = mysql_fetch_array($result, MYSQL_ASSOC))
					{
						$index = "";
						$index_op = "";
						if(isset($_GET[$row['index']]) == true)
						{
							$index = $_GET[$row['index']];
						}
						if(isset($_GET[$row['index'].'_operator']) == true)
						{
							$index_op = $_GET[$row['index'].'_operator'];
						}
						CreateFilterField($row['index'],$index ,$index_op);
					}
					?>
					
					<tr>
						<td class='clean' style='text-align:center;'>
						<input type='Submit' name='submit' value='Filter'>
						</td>
						<td class='clean' style='text-align:center;'>
						<input type='Submit' name='submit' value='Clear'>
						</td>
					</tr>
					
					</FORM>
					</table>

				</div>
			</div>
			
			<div id="CenterPageRight">

			<script type="text/javascript">
			function DoNav(theUrl)
			{
			
			document.location.href = theUrl;
			}
			
			</script>

			
			<style type="text/css">
				table 
				{
				margin-left: auto;
				margin-right: auto;
				margin-top: 20px;
				margin-bottom: 20px;
				border-collapse: collapse;
				}

				td
				{
				border: 1px solid #2e2d2d;
				padding-left:0px;
				padding-right: 2px;
				padding-top:5px;
				padding-bottom: 5px;
				cursor:pointer;
				}
				
				td.clean
				{
				border: 0px;
				cursor:pointer;
				}
				#PageSelected a
				{
					margin-bottom: 10px;
					font-weight:bold;
					color:red;
					float:left;
					margin-left:4px;
					margin-right:4px;
					text-decoration:none;
				}
				#NextBack a
				{
					color:blue;
					margin-bottom: 10px;
					float:left;
					margin-left:4px;
					margin-right:4px;
					text-decoration:none;
				}
				#NextBack a:hover
				{
					text-decoration:underline;
				}
				#Pages a
				{
					color: #306EFF;
					margin-bottom: 10px;
					float:left;
					margin-left:4px;
					margin-right:4px;
					text-decoration:none;
				}
				#Pages a:hover
				{
					text-decoration:underline;
				}
			</style>
		
			<table style="width: 96%;">	
				
				<tr style="background-color: rgb(230,230,230)">
				
				<?php
				//This has a total of at least 4 SQL requests all to different tables and possibly more
				//depending what data is being displayed
				
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
					if($row['real_data'] == true) //detects if the data is actually represented in `slides_data`
					{
						if ($row['general_display'] == true) //determines if variable is to be displayed on the entries screen
						{
							echo "<td><!--<a href='/viewentry.php?SortBy=561'>--><b>".$row['name']."</b><!--</a>--></td>";
							
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
				//Add two fields at the end that allow for editing and deleting of a slide
				echo"<td><b>Edit</b></td><td><b>Delete</b></td></tr>";
				
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
				//Create the SQL request
				$sql = "SELECT `id`,`" . join("`,`", $SelectIndexArray) . "` FROM `slides_data`"; 	//join the selected index array for variables to grab and grab id 
				if(sizeof($SQLArray) > 0)															//so that it cab be passed on if user decieds to click a specific slide
				{
					$sql = $sql . " WHERE " . join(" AND ", $SQLArray) . " ";
				}
				$result = mysql_query($sql);
				$num_rows = mysql_num_rows($result);
				//echo $sql;
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
	
				//echo $sql;
				
				//Display the Table with the data in it. This can be modified to create an XML output if that is required later
				$result = mysql_query($sql);
				$line = false;
				while($row = mysql_fetch_array($result))
				{
					if ($line == true)
					{
					echo "<tr style='background-color: rgb(240,240,240)' onclick=DoNav('"."/viewentry.php?id=".$row['id']."')>";
					$line = false;
					}
					else
					{
					echo "<tr onclick=DoNav('"."/viewentry.php?id=".$row['id']."')>";
					$line = true;
					}
					
					foreach(array_keys($SelectIndexArray) as $value)
					{
						echo "<td>";
						//check array to see if this data requires a lookup via a datafield array created earlier
						if(array_key_exists($value, $DataFields))
						{
							echo $DataFields[$value][$row[$value]];
						}
						else
						{
							echo $row[$value];
						}
						echo "</td>";
					}
					echo "<td style='padding:0;'>";
					echo "<a href='./viewentry.php?id=".$row['id']."&edit=1'><img src='../images/edit.gif' border=0; width=22px; height=22px;></a>";
					echo "</td>";
					echo "<td style='padding:0;'>";
					echo "<a href='./viewentry.php?id=".$row['id']."'><img src='../images/delete.gif' border=0; width=22px; height=22px;></a>";
					echo "</td>";
					echo "</tr>";
				}

				?>
				</table>
			<!--</div>-->
			<div id='OtherPages' style='text-align:center;margin-bottom: 10px; margin-top:5px; margin-left: 10px;'>
			<?php
			//This is overly complicated but was hacked together and works
			function CreateLink($id)
			{
				$link = "";
				if (strlen($_SERVER['REQUEST_URI']) > strlen('/entries.php'))
				{
					if(sizeof(explode("&PageNumber=", $_SERVER['REQUEST_URI'])) > 1)
					{
						//echo "qwer";
						$temp = array();
						$temp = explode("&PageNumber=", $_SERVER['REQUEST_URI']);
						$temp2 = array();
						$temp2 = explode("&",$temp[1]);
						$temp2[0] = $id;
						@$newstring = $temp[0] ."&PageNumber=" . $temp2[0] . $temp2[1];
						$link = $newstring;
					}
					else if(sizeof(explode("?PageNumber=", $_SERVER['REQUEST_URI'])) > 1)
					{
						//echo "qwer";
						$temp = array();
						$temp = explode("?PageNumber=", $_SERVER['REQUEST_URI']);
						$newstring = $temp[0] . "?PageNumber=" . $id;
						$link = $newstring;
					}
					else
					{
						$link = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "&PageNumber=" . $id;
					}
				}
				else
				{
					$link = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "?PageNumber=" . $id;
				}
				return $link;
			}
			
			//All this code prints out the bottom page numbers so the user can 
			//navigate through the various pages of enteries
			$ResultPageBuffer = 2; 	//This variable determines how many pages are on the left and right of the
									//current page selected.
			$PageNumber = 1;
			if(isset($_GET['PageNumber']) == true)
			{
				if (mysql_prep($_GET['PageNumber']) != "")
				{
					$PageNumber = mysql_prep($_GET['PageNumber']);
				}
			}
			
			
			$ResultPages = ceil($num_rows / $Results_Per_Page);
			if (strlen($_SERVER['REQUEST_URI']) > strlen('/entries.php'))
			{
				$_GET['PageNumber'] = 2;
				$AnchorLink="<a href='http://" . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI']. "&PageNumber=";
			}
			else
			{
				$AnchorLink="<a href='http://" . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI']. "?PageNumber=";
			}
			if ($PageNumber!=1) 
			{
				echo "<div id='NextBack'><a href='" . CreateLink($PageNumber - 1) . "'>< Back</a>" . "</div>";
			}
			for($x=($PageNumber-$ResultPageBuffer);$x<=($PageNumber+$ResultPageBuffer);$x++)
			{
				if(($x>0) && ($x<=$ResultPages) && ($ResultPages>1))
				{
					if($x == $PageNumber)
					{
					echo "<div id='PageSelected'><a href='". CreateLink($x) . "'>".$x."</a></div>";
					}
					else
					{
					echo "<div id='Pages'><a href='" . CreateLink($x) . "'>".$x."</a></div>";
					}
				}
			}
			if ($PageNumber!=$ResultPages) 
			{
				echo "<div id='NextBack'><a href='" . CreateLink($PageNumber + 1) . "'>Next ></a>" . "</div>";
			}
			?>
			
			</div>
			<div id='excel' style='float:right; text-align:right; padding-right: 10px; text-decoration:none;'>
				<?php
				$addr = $_SERVER['REQUEST_URI'];
				$addr = str_replace("entries.php","excelsheet.php",$addr);
				$excelAddress = "http://" . $_SERVER['HTTP_HOST'] . $addr;
				
				$pos = strpos($excelAddress, '?');
				
				echo "<a style='text-decoration:none; color:black;' href='" . $excelAddress . "'>Export To Excel</a>";
				echo "<br>";
				if($pos == false)
				{
					echo "<a style='text-decoration:none; color:black;' href='" . $excelAddress . "?return_all_fields=true'>Export To Excel With Details</a>";
				}
				else
				{
					echo "<a style='text-decoration:none; color:black;' href='" . $excelAddress . "&return_all_fields=true'>Export To Excel With Details</a>";
				}
				
				?>
			</div>
		</div>
		</div>
		<div id="Footer"></div>
	</div>
</div>

</HTML>