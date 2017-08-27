<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>Chat - Installation</title>
		<link rel="stylesheet" type="text/css" href="css/style.css" />
    </head>
    <body>
    	<div class="content">
        	<h2>Chat - Installation</h2>
<?php
$servername = "localhost";
$username = "chat";
$password = "***";
$dbname = "chat";
$conn = new mysqli($servername, $username, $password, $dbname);
if (!($conn->connect_error)) {
	if ($conn->query("SHOW TABLES LIKE 'users'")->num_rows==0) {
		$sql = "CREATE TABLE `users` (".
		"`uuid` text, ".
		"`name` text, ".
		"`email` text, ".
		"`passwd` text, ".
		"`register_time` bigint(20) NOT NULL DEFAULT '-1', ".
		"`active` tinyint(1) NOT NULL DEFAULT '0', ".
		"`reset_passwd_key` text DEFAULT NULL, ".
		"`reset_passwd_time` bigint(20) NOT NULL DEFAULT '0', ".
		"`wrong_passwd_tryes` int NOT NULL DEFAULT '0', ".
		"`last_passwd_fail` bigint(20) NOT NULL DEFAULT '0', ".
		"`reactivate_passwd_key` text DEFAULT NULL, ".
		"`session_key` text DEFAULT NULL, ".
		"`session_ip` text DEFAULT NULL, ".
		"`session_key_time` bigint(20) NOT NULL DEFAULT '0'".
		") ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$conn->query($sql);
		$sql = "ALTER TABLE `users` ADD UNIQUE (`uuid`(767));";
		$conn->query($sql);	
		echo ('<h3>Die Installation wurde abgeschlossen.</h3>');
	} else {
		echo('<h3>Die Installation wurde bereits durchgeführt.</h3>');
	}
} else {
	echo ('<h3>Die MySQL-Datenbank ist momentan nicht erreichbar. Versuche es später noch einmal.</h3>');
}
?>
        </div>
    </body>
</html>