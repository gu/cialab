<?php

if(!(isset($_SESSION['admin']) and $_SESSION['admin'] == 1))
{
	header('Location: '. $LogOut);
}

?>