<?php 
include("../GlobalVariables.php");
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
		header('Location: '.$LoginPage);
		die();
	}
}
else
{
	//session created for first time
	$_SESSION['IP'] = $REMOTE_ADDR;
	$_SESSION['UserName'] = "";
	$_SESSION['Password'] = "";
	$_SESSION['Id'] = "";
	$_SESSION['IsAuthenticated'] = False;
	$_SESSION['admin'] = 0;
	
	header('Location: '.$LoginPage);
}
?>
