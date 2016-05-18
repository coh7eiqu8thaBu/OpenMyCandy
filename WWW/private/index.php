<?php
define('MyCandy', 1);
include("../private/config.php");
include("../private/functions.php");
$real_ip = null;

if (!isset($_POST['ip'])) {
    $real_ip = $_SERVER['REMOTE_ADDR'];
} else {
    $real_ip = $_POST['ip'];
}

ConnectMySQL($db_host, $db_login, $db_password, $db_database);
CheckBan($real_ip);
AddBan($real_ip);
