<?php 
include("GlobalVariables.php");
include("GlobalFunctions.php");

session_start();

Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab);

//If the user has not been authenticated then attemp to authenticate the user
if (!isset($_SESSION['IsAuthenticated']) || $_SESSION['IsAuthenticated'] != true)
{
	if (isset($_POST["u"]) == true && isset($_POST["p"]) == true)
	{
		AttemptAuthentication($_POST["u"],$_POST["p"]);
	}
	else
	{
		$_SESSION['IsAuthenticated'] = false;
	}
}

//If authenticated then send user to main page
if ($_SESSION['IsAuthenticated']==true)
{
	$user_id = $_SESSION['Id'];
	$user_ip = $_SESSION['Ip'];
	$session_id = session_id();
	
	$sql = "INSERT INTO `cialab`.`users_login_times` (`user_id`,`ip_address`,`session_id`) VALUES ('".$user_id."','".$user_ip."','".$session_id."');"; 
	$result = mysql_query($sql);
	if($result)
	{
		$sql = "SELECT * FROM `users_data` WHERE `Id` = '$user_id'"; 
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		
		if ($row['Numofprojects'] == 1)
		{
			$quer=mysql_query("SELECT `roi_projects`.`id`,`roi_projects`.`name`,`roi_projects`.`folder`,`roi_projects_members`.`roi_project_id` FROM `roi_projects`,`roi_projects_members` WHERE `roi_projects`.`id`=`roi_projects_members`.`roi_project_id` AND `roi_projects_members`.`project_viewable`='1' AND `user_id`='".$_SESSION['Id']."';"); 
			$rowvar = mysql_fetch_array($quer);
			header("Location:".$URL . "/cbmarker/index.php?" . "pid=" . $rowvar['id']);
		}
		elseif (isset($_SESSION['LastPage']) && $_SESSION['LastPage'] != "")
		{
			header("Location:".$_SESSION['LastPage']);
		}
		else
		{
			header("Location:".$MainIndex);
		}
	}
}

function AttemptAuthentication($username, $password)
{
	$username = mysql_prep($username);
	$password = mysql_prep($password);
	
	$sql = "SELECT * FROM `users_data` WHERE `email` = '$username' AND `password` = '$password'"; 
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	if (mysql_num_rows($result) == 1)
	{
		//Grab some other user info
		$user_rank = $row['rank'];
		$_SESSION['Id'] = $row['id'];
		$_SESSION['Ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['UserName'] = mysql_prep($_POST["u"]);
		$_SESSION['Password'] = mysql_prep($_POST["p"]);
		$_SESSION['FirstName'] = $row['first_name'];
		$_SESSION['LastName'] = $row['last_name'];
		$_SESSION['IsAuthenticated'] = true;
		$_SESSION['Active'] = true;
		$_SESSION['Permissions'] = mysql_fetch_array(mysql_query("SELECT * FROM `users_perm` WHERE `user_rank`='$user_rank';"));
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<HTML>
<TITLE>Login To Barcode Database</TITLE>
<link rel="stylesheet" type="text/css" href="./css/indexstyle.css" />
<script language="javascript" src="MD5.js"></script>
<script language="javascript">
//break out of a frame, if stuck in one.
if (top.location != location)
{
    top.location.href = document.location.href ;
}
  
function ConvertPassword()
{
	document.getElementById('p').value = MD5(document.getElementById('p_unencrypted').value);
}
</script>
<div id="TotalPage">
	<div id="Content">
		<div id="Header"></div>
		<div id="CenterPage">
				<FORM id='LoginForm' name='LoginForm' action='login.php' method='post' onSubmit='javascript:ConvertPassword();'>
				<input type='hidden' id='p' name='p' value=''>
				<table style="margin-right: auto; margin-left: auto; margin-top: 50px; margin-bottom: 50px; text-align:right;";>
					<tr>
						<td width="100px">
							Email :
						</td>
						<td>
							<INPUT type='text' id='u' name='u'>
						</td>
					</tr>
					<tr>
						<td width="100px">
							Password :
						</td>
						<td>
							<INPUT type='password' id='p_unencrypted' name='p_unencrypted'>
						</td>
					</tr>
					<tr>
						<td>
							<INPUT type='submit' name='login' value='Login'>
						</td>
					</tr>
				</table>
				</FORM>
		</div>
		<div id="Footer"></div>
	</div>
</div>

</HTML>



