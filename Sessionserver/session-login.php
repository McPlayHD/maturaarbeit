<?php
if (isset($_POST['email']) && isset($_POST['passwd']) && strlen($_POST['email']) > 0 && strlen($_POST['passwd']) > 0) {
	$email = $_POST['email'];
	$passwd = $_POST['passwd'];
	if (preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $email)) { /*http://emailregex.com/*/
		if (strlen($passwd) >= 6) {
			$sql_servername = "localhost";
			$sql_username = "chat";
			$sql_password = "***";
			$sql_dbname = "chat";
			$conn = new mysqli($sql_servername, $sql_username, $sql_password, $sql_dbname);
			if (!($conn->connect_error)) {
				$email = strtr($email, array('_' => '\_', '%' => '\%', ' ' => ''));
                $email = $conn->real_escape_string($email);
				$sql = "SELECT `name`, `uuid`, `passwd`, `reactivate_passwd_key`, `wrong_passwd_tryes`, `session_key`, `session_key_time`, `session_ip` FROM `users` WHERE email='". $email. "';";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					if($row = $result->fetch_assoc()) {
						if ($row['reactivate_passwd_key'] == NULL) {
							if (password_verify($passwd, $row['passwd'])) {
								$ip = $_SERVER['REMOTE_ADDR'];
								$uuid = $row['uuid'];
								$uname = $row['name'];
								$key;
								if ($row['session_ip'] != NULL && $ip == $row['session_ip'] && $row['session_key'] != NULL) {
									$key = $row['session_key'];
									$sql = "UPDATE `users` SET `session_key_time`=". round(microtime(true) * 1000). ", `last_passwd_fail`=0, `wrong_passwd_tryes`=0, `reactivate_passwd_key`=NULL WHERE `email`='". $email. "';";
									$conn->query($sql);
								} else {
									$sql = "UPDATE `users` SET `session_key`=UUID(), `session_key_time`=". round(microtime(true) * 1000). ", `session_ip`='". $ip. "', `last_passwd_fail`=0, `wrong_passwd_tryes`=1, `reactivate_passwd_key`=NULL WHERE `email`='". $email. "';";
									$conn->query($sql);
									$sql = "SELECT `session_key` FROM `users` WHERE `email`='". $email. "';";
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
										if($row = $result->fetch_assoc()) {
											$key = $row['session_key'];
										} else {
											echo('Error 212\nMySQL read failure. Please report this to mail@0x3b.ch');	
										}
									} else {
										echo('Error 211\nFatal Error. Please report this to mail@0x3b.ch');	
									}
								}
								echo($key. ",". $uuid. ",". $uname);
							} else {
								$sql = "UPDATE `users` SET `wrong_passwd_tryes` = `wrong_passwd_tryes`+1 WHERE `email`='". $email. "' AND `last_passwd_fail` > ". (round(microtime(true) * 1000) - 24*60*60*1000). ";";
								$conn->query($sql);
								$sql = "UPDATE `users` SET `last_passwd_fail` = ". round(microtime(true) * 1000). " WHERE `email`='". $email. "';";
								$conn->query($sql);
								if ($row['wrong_passwd_tryes'] >= 5) {
									$sql = "UPDATE `users` SET `reactivate_passwd_key`=UUID() WHERE `email`='". $email. "';";
									$conn->query($sql);
									$sql = "SELECT `reactivate_passwd_key` FROM `users` WHERE `email`='". $email. "';";
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
										if($row = $result->fetch_assoc()) {
											$key = $row['reactivate_passwd_key'];
											require '/var/www/html/PHPMailer/PHPMailerAutoload.php';
											$mail             = new PHPMailer();
											$mail->IsSMTP();                           // telling the class to use SMTP
											$mail->SMTPAuth   = true;                  // enable SMTP authentication
											$mail->Host       = "mail.mcplayhd.net"; // set the SMTP server
											$mail->SMTPSecure = 'tls';                  // Enable TLS encryption, `ssl` also accepted
											$mail->Port = 587;                          // TCP port to connect to
											$mail->Username   = "no-reply@0x3b.ch"; // SMTP account username
											$mail->Password   = "***";        // SMTP account password
											$mail->setFrom('no-reply@0x3b.ch', '0x3b.ch - NoReply', 0);
											$mail->addAddress($email);
											$mail->Subject  = 'Chat - Account Entsperren';
											$mail->Body     = "Hi ". $username. "\r\n". "Das Passwort zu deinem Account wurde zu oft falsch eingegeben. Aus diesem Grund haben wir deinen Account gesperrt und du kannst den Account mit einem Klick auf diesen Link wieder entsperren: http://0x3b.ch/chat/unlock-account.php?key=". $key. "\nFalls du dein Passwort vergessen hast, kannst du es auf dieser Seite resetten: http://0x3b.ch/chat/reset-password-form.php";
											if(!$mail->send()) {
												$sql = "UPDATE `users` SET `reactivate_passwd_key`=NULL WHERE `email`='". $email. "';";
												$conn->query($sql);
												echo('Error 210\nFatal Error. Please report this to mail@0x3b.ch');
											}
										} else {
											echo('Error 209\nMySQL read failure. Please report this to mail@0x3b.ch');	
										}
									} else {
										echo('Error 208\nFatal Error. Please report this to mail@0x3b.ch');	
									}
								}
								echo('Error 207\nPassword incorrect');
							}
						} else {
							echo('Error 206\nThis account is locked');	
						}
					} else {
						echo('Error 205\nMySQL read failure. Please report this to mail@0x3b.ch');	
					}
				} else {
					echo('Error 204\nEmail not found');	
				}
			} else {
				echo('Error 203\nDatabase connection failure');	
			}
		} else {
			echo('Error 202\nPassword format is not valid');
		}
	} else {
		echo('Error 201\nEmail format is not valid');
	}
} else {
	echo('Error 200\nPassword or Email is missing');
}
?>