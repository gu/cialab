<?php include("GlobalVariables.php"); ?>
<?php 
session_start();
if (isset($_SESSION['Active']))
{
	//SESSION HAS STARTED AGAIN
	if(isset($REMOTE_ADDR) == true)
	{
		$_SESSION['IP'] = $REMOTE_ADDR;
	}
	
	if ($_SESSION['IsAuthenticated']==False)
	{
		$_SESSION['LastPage'] = $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		header('Location:'.$LoginPage);
		die();
	}
}
else
{
	//session created for first time
	$_SESSION['IP'] = $REMOTE_ADDR;
	$_SESSION['Active'] = true;
	$_SESSION['UserName'] = "";
	$_SESSION['Password'] = "";
	$_SESSION['Id'] = "";
	$_SESSION['FirstName'] = "";
	$_SESSION['LastName'] = "";
	$_SESSION['IsAuthenticated'] = False;
	$_SESSION['LastPage'] = $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	
	header('Location:'.$LoginPage);
}

?>