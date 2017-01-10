<?php
include("GlobalVariables.php");
include("GlobalFunctions.php");

session_start();

$user_id = $_SESSION['Id'];
$user_ip = $_SESSION['Ip'];
$session_id = session_id();

//Connect to Database
Connect_To_DB($db_server_official,$db_user_official,$db_pwd_official,$db_cbmarker);

//Insert Logout Entry
$sql = "INSERT INTO `cialab`.`users_logout_times` (`user_id`,`ip_address`,`session_id`) VALUES ('".$user_id."','".$user_ip."','".$session_id."');"; 
$result = mysql_query($sql);

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();
header("Location:".$MainIndex);
?>