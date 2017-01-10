<?php

function mysql_prep($value)
{
	/* This code was found on the intenet somewhere */
	//$value = addslashes($value);
    //return $value;
	
	$magic_quotes_active = get_magic_quotes_gpc();
	$new_enough_php = function_exists( "mysql_real_escape_string" ); // i.e. PHP >= v4.3.0
	if( $new_enough_php ) 
	{ 	
		// PHP v4.3.0 or higher
		// undo any magic quote effects so mysql_real_escape_string can do the work
		if( $magic_quotes_active ) 
		{ 
			$value = stripslashes( $value ); 
		}
		$value = mysql_real_escape_string( $value );
	} 
	else 
	{ 
		// before PHP v4.3.0
		// if magic quotes aren't already on then add slashes manually
		if( !$magic_quotes_active )
		{ 
			$value = addslashes($value); 
		}
		// if magic quotes are active, then the slashes already exist
	}
	return $value;
}

function print_head($value)
{
	if($value == 'index.php'){echo "<a class='tab' href='index.php'><div id='NavBarItemCurrent'>Home</div></a>";}
	else{echo "<a class='tab' href='index.php'><div id='NavBarItem'>Home</div></a>";}

	if(isset($_SESSION['Permissions']['view_entries']) && $_SESSION['Permissions']['view_entries'] == 1)
	{
		if($value == 'entries.php'){echo "<a class='tab' href='entries.php'><div id='NavBarItemCurrent'>View Entries</div></a>";}
		else{echo "<a class='tab' href='entries.php'><div id='NavBarItem'>View Entries</div></a>";}
	}
	
	if(isset($_SESSION['Permissions']['view_add_entries']) && $_SESSION['Permissions']['view_add_entries'] == 1)
	{
		if($value == 'addentry.php'){echo "<a class='tab' href='addentry.php'><div id='NavBarItemCurrent'>Add Entry</div></a>";}
		else{echo "<a class='tab' href='addentry.php'><div id='NavBarItem'>Add Entry</div></a>";}
	}
	
	if(isset($_SESSION['Permissions']['view_controls']) && $_SESSION['Permissions']['view_controls'] == 1)
	{
		if($value == 'controls.php'){echo "<a class='tab' href='controls.php'><div id='NavBarItemCurrent'>Control Panel</div></a>";}
		else{echo "<a class='tab' href='controls.php'><div id='NavBarItem'>Control Panel</div></a>";}
	}
	
	if(isset($_SESSION['Permissions']['view_image_search']) && $_SESSION['Permissions']['view_image_search'] == 1)
	{
		if($value == 'search.php'){echo "<a class='tab' href='search.php'><div id='NavBarItemCurrent'>Image Search</div></a>";}
		else{echo "<a class='tab' href='search.php'><div id='NavBarItem'>Image Search</div></a>";}
	}
	
	if(isset($_SESSION['Permissions']['upload_skindata']) && $_SESSION['Permissions']['upload_skindata'] == 1)
	{
		if($value == 'uploadskin.php'){echo "<a class='tab' href='uploadskin.php'><div id='NavBarItemCurrent'>Upload Imgs</div></a>";}
		else{echo "<a class='tab' href='uploadskin.php'><div id='NavBarItem'>Upload Imgs</div></a>";}
	}
	
	if(isset($_SESSION['Permissions']['view_cbmarker']) && $_SESSION['Permissions']['view_cbmarker'] == 1)
	{
		if($value == 'roimarker.php'){echo "<a class='tab' href='roimarker.php'><div id='NavBarItemCurrent'>Projects</div></a>";}
		else{echo "<a class='tab' href='roimarker.php'><div id='NavBarItem'>Projects</div></a>";}
	}
	
	echo "
	<style type='text/css'>
				#NavBarTextItem
				{
					color: black;
				}
				ul.logout,
				ul.logout li,
				ul.logout ul
				{
					 list-style: none;
					 margin: 0;
					 padding: 0;
				}
				ul.logout 
				{
				 position: relative;
				 z-index: 597;
				 float: left;
				}

				ul.logout li 
				{
				 float: left;
				 line-height: 1.3em;
				 vertical-align: middle;
				 padding-left: 5px;
				 padding-right: 5px;
				 top: -1px;
				 left: 1px;
				 zoom: 1;
				}

				ul.logout li:hover 
				{
				 position: relative;
				 z-index: 599;
				 cursor: default;
				 background-color:#F0F0F0;
				 border: 1px solid #D4D4D4;
				}
				ul.logout li:hover ul 
				{
					background-color:#F0F0F0;
					border: 1px solid #D4D4D4;
					left: -1px;
					visibility: visible;
				}
				ul.logout li ul:hover 
				{
					background-color: #4B4B4B;
					color: #F0F0F0;
					left: -1px;
					visibility: visible;
				}
				ul.logout ul 
				{
				 visibility: hidden;
				 position: absolute;
				 top: 100%;
				 left: 0;
				 z-index: 598;
				 width: 100%;
				}

			</style>
			<div id='NavBarTextItem'>
				<ul class='logout'>
					<li>
				";
				
					if ($_SESSION['IsAuthenticated']==true)
					{
					echo $_SESSION['UserName'];
					}
				echo "
						<a href='./logout.php' style='text-decoration:none;color:black;'>
							<ul class='nothing'>
								Log Out
							</ul>
						</a>
					</li>
				</ul>
			</div>";
}

function Connect_To_DB($db_server_value, $db_user_value, $db_pwd_value, $db_name_value)
{
	$conn = mysql_connect($db_server_value, $db_user_value, $db_pwd_value) or die('Error connecting to mysql');
	// $dbname = 'cialab';
	mysql_select_db($db_name_value);
}

function CreateFilterField($index,$SelectedValue,$Operator)
{
		//Filter Out Malicious Code
		$index = mysql_prep($index);
		$SelectedValue = mysql_prep($SelectedValue);
		$Operator = mysql_prep($Operator);
		
		//SQL Request for Name of DataField
		$sql = "SELECT * FROM `slides_rep` WHERE `index` = '".$index."'";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
	
		$HasOperator = $row["operators"];
		$AllowAny = $row['allow_any'];
	
		//Detect if field comes with operators
		if($HasOperator == true)
		{
			echo "<tr><td class='clean'>".$row["name"].":</td><td class='clean'>";
			echo ReturnDataField("operator",$index."_operator", $Operator,"46px",$AllowAny,"",false,"slides_rep",false);
			echo ReturnDataField($index,"", $SelectedValue,"24px",$AllowAny,"",false,"slides_rep",false); 
			echo "</td></tr>";
		}
		else
		{
			echo "<tr><td class='clean'>".$row["name"].":</td><td class='clean'>";
			echo ReturnDataField($index,"", $SelectedValue,"72px",$AllowAny,"",false,"slides_rep",false); 
			echo "</td></tr>";
		}
}

function ReturnDataValue($index,$SelectedValue)
{
	//Filter Out Malicious Code
	$index = mysql_prep($index);
	//echo "<script language='JavaScript'>alert('" . $index . "');</script>";
	$ReturnValue = $SelectedValue;
	
	$sql = "SELECT * FROM `slides_rep` WHERE `index` = '$index'";
	$DataArray = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
	
	if($DataArray["type"] != 0)
	{
		$sql = "SELECT * FROM `".$DataArray["type_id"]."` WHERE `value` = '".mysql_prep($SelectedValue)."' ORDER BY `name` ASC";
		$DataFieldArray = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
		if($DataFieldArray['value'] == mysql_prep($SelectedValue))
		{
			$ReturnValue = $DataFieldArray['name'];
		}
	}

	return $ReturnValue;
}

function ReturnDataField($index,$NameOverride,$SelectedValue,$FieldWidth,$AnyOption,$JSEvent,$returnInTableFormat,$tableName,$turnOnTips)
{
	//Filter Out Malicious Code
	$index = mysql_prep($index);
	//$SelectedValue = mysql_prep($SelectedValue);
	$FieldWidth = mysql_prep($FieldWidth);
	$AnyOption = mysql_prep($AnyOption);
	
	$ReturnValue = "";
	$sql = "SELECT * FROM `".$tableName."` WHERE `index` = '$index'";
	$result = mysql_query($sql);
	$title = "";
	
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if($turnOnTips == true)
		{
			$title = " title=\"".$row["description"]."\" ";
		}
		if ($NameOverride != "")
		{
			$FieldName = $NameOverride;
		}
		else
		{
			$FieldName = $row["index"];
		}
		
		if ($returnInTableFormat == true)
		{
			$ReturnValue = $ReturnValue ."<tr><td style='text-align: right;'>".$row["name"].": </td><td></td><td>";
		}
		//Three types of data can be stored textboxes, comboboxes, and radiobuttons, 0,1,2 respectivley
		if ($row["type"] == 0)
		{
			$ReturnValue = $ReturnValue . "<input type='text' value='".$SelectedValue."' name='".$FieldName."' style='width:" . $FieldWidth . ";' ".$title." ".$JSEvent." >";
			if ($returnInTableFormat == true)
			{
				$ReturnValue = $ReturnValue ."</td><td>";
			}
		}
		else if ($row["type"] == 1)
		{
			$ReturnValue = $ReturnValue . "<select name='".$FieldName."' style='width:" . $FieldWidth . ";' ".$title." ".$JSEvent." >";
			//echo "<script language='JavaScript'>alert('".$row["type_id"]."')</script>";
			$sql = "SELECT * FROM `".$row["type_id"]."` ORDER BY `order` ASC";
			$combobox_result = mysql_query($sql);
			if ($AnyOption == true)
			{
				$ReturnValue = $ReturnValue . "<option ";
				if($SelectedValue == "")
				{
					$ReturnValue = $ReturnValue . "SELECTED";
				}
				$ReturnValue = $ReturnValue . " value=''>Any</option>";
			}
			while($items = mysql_fetch_array($combobox_result, MYSQL_ASSOC))
			{
				$ReturnValue = $ReturnValue . "<option ";
				if($SelectedValue == $items["value"])
				{
					$ReturnValue = $ReturnValue . "SELECTED";
				}
				$ReturnValue = $ReturnValue . " value=".$items["value"].">".$items["name"]."</option>";
			}
			$ReturnValue = $ReturnValue . "</select>";
			if ($returnInTableFormat == true)
			{
				$ReturnValue = $ReturnValue ."</td><td>";
			}
		}
		else if ($row["type"] == 2)
		{
			//echo "<script language='JavaScript'>alert('".$row["type_id"]."')</script>";
			$sql = "SELECT * FROM `".$row["type_id"]."` ORDER BY `order` ASC";
			$combobox_result = mysql_query($sql);
			if ($AnyOption == true)
			{
				$ReturnValue = $ReturnValue . "<input";
				if($SelectedValue == "*")
				{
					$ReturnValue = $ReturnValue . "CHECKED";
				}
				$ReturnValue = $ReturnValue . " type='radio' name='".$FieldName."' value='*' ".$title." ".$JSEvent." >Any<br>";
			}
			while($items = mysql_fetch_array($combobox_result, MYSQL_ASSOC))
			{
				$ReturnValue = $ReturnValue . "<input";
				if($SelectedValue == $items["value"])
				{
					$ReturnValue = $ReturnValue . " CHECKED ";
				}
				$ReturnValue = $ReturnValue . " type='radio' name='".$FieldName."' value='".$items["value"]."' ".$title." ".$JSEvent." >".$items["name"]."<br>";
			}
			if ($returnInTableFormat == true)
			{
				$ReturnValue = $ReturnValue ."</td><td>";
			}
		}
		else if ($row["type"] == 3)
		{
			//$ReturnValue = "<tr style='height:0px;'><td></td><td></td><td>";
			$ReturnValue = "";
			$ReturnValue = $ReturnValue . "<input type='hidden' value='".$SelectedValue."' name='".$FieldName."' ".$title." ".$JSEvent.">";
		}
		else if ($row["type"] == 4)
		{
			//If type 4 Do Not display at all
			$ReturnValue = "";
		}
		else if ($row["type"] == 5)
		{
			//Display in password field
			$ReturnValue = $ReturnValue . "<input type='password' value='".$SelectedValue."' name='".$FieldName."' style='width:" . $FieldWidth . ";' ".$title." ".$JSEvent." >";
			if ($returnInTableFormat == true)
			{
				$ReturnValue = $ReturnValue ."</td><td>";
			}
		}

	}
	return $ReturnValue;
}

function TrimFloat($float) 
{
	return sprintf("%01.2f", $float);
}
?>