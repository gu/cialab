<?php 
include("GlobalVariables.php");
session_start();
if (isset($_SESSION['Active_DEMO']))
{
	//SESSION HAS STARTED AGAIN
	if(isset($REMOTE_ADDR) == true)
	{
		$_SESSION['IP'] = $REMOTE_ADDR;
	}
	
	if ($_SESSION['IsAuthenticated_DEMO']==False)
	{
		header('Location: '.$LoginPage);
		die();
	}
}
else
{
	//session created for first time
	$_SESSION['IP_DEMO'] = $REMOTE_ADDR;
	$_SESSION['UserName_DEMO'] = "";
	$_SESSION['Password_DEMO'] = "";
	$_SESSION['id_DEMO'] = "";
	$_SESSION['IsAuthenticated_DEMO'] = False;
	$_SESSION['admin_DEMO'] = 0;
	
	header('Location: '.$LoginPage);
}
?>
