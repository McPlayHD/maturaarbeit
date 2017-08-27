<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>Chat - Registration</title>
		<link rel="stylesheet" type="text/css" href="css/style.css" />
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    </head>
    <body>
        <div class="content">
            <h2>Chat - Aktivierung</h2>
<?php
if(isset($_GET['key'])) {
	$key = $_GET['key'];
	if (preg_match('/^[a-z0-9]{8}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{12}$/', $key)) {
		$sql_servername = "localhost";
		$sql_username = "chat";
		$sql_password = "***";
		$sql_dbname = "chat";
		$conn = new mysqli($sql_servername, $sql_username, $sql_password, $sql_dbname);
		if (!($conn->connect_error)) {
			$key = strtr($key, array('_' => '\_', '%' => '\%', ' ' => ''));
            $key= $conn->real_escape_string($key);
			$sql = "UPDATE `users` SET `active`=1, `register_time`=". round(microtime(true) * 1000). " WHERE `uuid`='". $key. "' AND `active`=0;";
			$conn->query($sql);
			echo("Falls dieser Link gültig ist, ist der Account nun aktiv.");
		}
	} else {
		echo ('Dieser Key ist nicht gültig');	
	}
} else {
	echo ('Du musst den Aktivierungslink im Mail anklicken');	
}
?>
        </div>
    </body>
</html>