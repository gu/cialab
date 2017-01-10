<?php 
include("GlobalVariables.php");
session_start();

//Create needed session variables. 
$_SESSION['IP'] = "";
$_SESSION['UserName'] = "";
$_SESSION['Password'] = "";
$_SESSION['id'] = "";
$_SESSION['Active'] = true;
$_SESSION['admin'] = 0;
$_SESSION['IsAuthenticated'] = False;

Connect_To_DB($db_server_official,$db_user_official,$db_pwd_official,$db_name_official);
function Connect_To_DB($db_server_value, $db_user_value, $db_pwd_value, $db_name_value)
{
	$conn = mysql_connect($db_server_value, $db_user_value, $db_pwd_value) or die('Error connecting to mysql');
	mysql_select_db($db_name_value);
}

//echo "<script language='Javascript'>alert('password: ";if(isset($_POST["p"])){echo $_POST["p"];} echo "  username:"; if(isset($_POST["u"])){echo $_POST["u"];} echo "');</script>";
if (isset($_SESSION['IsAuthenticated']) == false || $_SESSION['IsAuthenticated'] == false)
{
	//echo "<div id='test' style='height:100px;width:100px;color:red;background-color:blue;'>".$_POST["p"]."  ".$_POST["u"]."</div>";
	if (isset($_POST["u"]) == true && isset($_POST["p"]) == true)
	{
		if (IsAuthenticated($_POST["u"],$_POST["p"]) == true)
		{
			$_SESSION['UserName'] = mysql_prep($_POST["u"]);
			$_SESSION['Password'] = mysql_prep($_POST["p"]);
			$_SESSION['IsAuthenticated'] = true;
		}
	}
	else
	{
		$_SESSION['IsAuthenticated'] = false;
	}
}

if ($_SESSION['IsAuthenticated']==true)
{
	//echo "<script language='Javascript'>alert('test2');</script>";
	header('Location: '.$MainIndex);
}


function IsAuthenticated($username, $password)
{
	$username = mysql_prep($username);
	$password = mysql_prep($password);
	
	$sql = "SELECT `userid`,`admin` FROM `users_data` WHERE `email` = '$username' AND `password` = '$password'"; 
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	if (mysql_num_rows($result) == 1)
	{
		$_SESSION['id'] = $row['userid'];
		$_SESSION['admin'] = $row['admin'];
		return true;
	}
	return false;
}

function mysql_prep($value)
{
	$value = preg_replace("/[^a-z0-9_.@\\-| ]/i", "", $value);
    return $value;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<HTML>
<TITLE>Login To Centroblast Marker</TITLE>
<link rel="stylesheet" type="text/css" href="./css/indexstyle.css" />
<script language="javascript" src="./MD5.js"></script>
<script language="javascript">
function ConvertPassword()
{
	document.getElementById('p').value = MD5(document.getElementById('p_unencrypted').value);
}
</script>
<div id="TotalPage">
	<div id="Content">
		<div id="Header"></div>
		<div id="CenterPage">
				<FORM id='LoginForm' name='LoginForm' action='./login.php' method='POST' onSubmit='javascript:ConvertPassword();'>
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



