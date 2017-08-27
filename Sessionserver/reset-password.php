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
            <h2>Chat - Passwort Reset</h2>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['key']) && isset($_POST['passwd']) && isset($_POST['passwd_confirm'])) {
		$key = $_POST['key'];
		if (preg_match('/^[a-z0-9]{8}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{12}$/', $key)) {
			$passwd = $_POST['passwd'];
			$passwd_confirm = $_POST['passwd_confirm'];
			if ($passwd == $passwd_confirm) {
				if (strlen($passwd) >= 6) {
					$sql_servername = "localhost";
					$sql_username = "chat";
					$sql_password = "***";
					$sql_dbname = "chat";
					$conn = new mysqli($sql_servername, $sql_username, $sql_password, $sql_dbname);
					if (!($conn->connect_error)) {
						$key = strtr($key, array('_' => '\_', '%' => '\%', ' ' => ''));
						$key = $conn->real_escape_string($key);
						$passwd = password_hash($passwd, PASSWORD_DEFAULT);
						$sql = "UPDATE `users` SET `passwd`='". $passwd. "', `reset_passwd_key`=NULL, `reset_passwd_time`=0, `last_passwd_fail`=0, `wrong_passwd_tryes`=0, `reactivate_passwd_key`=NULL, `session_key`=NULL, `session_key_time`=0, `session_ip`=NULL WHERE `reset_passwd_key`='". $key. "' AND `reset_passwd_time` > ". (round(microtime(true) * 1000) - 24*60*60*1000). ";";
						$conn->query($sql);
						if ($conn->affected_rows != 0) {
							echo('<h3>Das Passwort wurde neu gesetzt.</h3>');
						} else {
							error('Dieser Key ist nicht gültig oder abgelaufen.');
						}
					}
				} else {
					error('Das Passwort ist zu kurz.');
				}
			} else {
				error('Die beiden Passwörter stimmen nicht überein.');
			}
		} else {
			error ('Dieser Key ist nicht gültig');	
		}
	} else {
		error ('Einige Daten wurden nicht übermittelt. Versuche es erneut');
	}
} else {
	if(isset($_GET['key'])) {
		$key = $_GET['key'];
		sendForm($key);
	} else {
		echo ('<h3>Du musst den Resetlink im Mail anklicken</h3>');	
	}
}
function error($msg)
{
	echo ('<h3>'. $msg. '</h3>');
	sendForm();
}
function sendForm($key)
{
	$result = '<p><b>Passwort neu setzen:</b><br />'
		. '<form action="" method="post" accept-charset="utf-8">'
		. '<input type="hidden" name="key" value="'. $key. '" />'
		. '<table align="center" style="text-align:left;"><tr>'
		. '<td>Neues Passowort: </td><td><input type="password" name="passwd" size="30" pattern=".{6,20}" required /></td>'
		. '</tr><tr>'
		. '<td>Neues Passowort erneut eingeben: </td><td><input type="password" name="passwd_confirm" size="30" pattern=".{6,20}" required />'
		. '</td>'
		. '</tr><tr>'
		. '<td></td><td><input type="submit" name="submit" id="" value="Abschicken" /></td>'
		. '</tr></table>'
		. '</form>';
	echo($result);
}
?>
        </div>
    </body>
</html>