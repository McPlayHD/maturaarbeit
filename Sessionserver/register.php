<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>Chat - Registration</title>
		<link rel="stylesheet" type="text/css" href="css/style.css" />
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
		<script>
        $(document).ready(function() {
            $("#check").click(function() {
				$("#f1").load('check.php?username=' + document.getElementById('un').value, function() {
					if (document.getElementById('f1').innerHTML.startsWith("<font color=\"red\">")) {
						//document.getElementById('un').value = "";
						document.getElementById('check').style.backgroundColor = "tomato";
					} else {
						document.getElementById('check').style.backgroundColor = "lime";
					}
					document.getElementById('f1').innerHTML = "";
				});
            });
        });
        </script>
    </head>
    <body>
    	<div class="content">
        	<h2>Chat - Registration</h2>
<?php
if (isset($_POST['submit'])) {
	if (isset($_POST['email'])
		&& isset($_POST['username'])
		&& isset($_POST['passwd'])
		&& isset($_POST['passwd_confirm'])) {
		if (isset($_POST['g-recaptcha-response'])) {
			$captcha = $_POST['g-recaptcha-response'];
			if ($captcha) {
				$privateKey = "***";
				$ip = $_SERVER['REMOTE_ADDR'];
				$verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret="
					.$privateKey."&response=".$captcha."&remoteip=".$ip);
				$responseData = json_decode($verifyResponse, true);
				if ($responseData['success'] == true) {
					$email = $_POST['email'];
					$username = $_POST['username'];
					$passwd = $_POST['passwd'];
					$passwd_confirm = $_POST['passwd_confirm'];
					if ($passwd == $passwd_confirm) {
						if (strlen($passwd) >= 6) {
							if (strlen($username) >= 3 && strlen($username) <= 16) {
								if (preg_match('/^\w+$/', $username)) {
									if (preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $email)) { /*http://emailregex.com/*/
										$sql_servername = "localhost";
										$sql_username = "chat";
										$sql_password = "***";
										$sql_dbname = "chat";
										$conn = new mysqli($sql_servername, $sql_username, $sql_password, $sql_dbname);
										if (!($conn->connect_error)) {
											$email = strtr($email, array('_' => '\_', '%' => '\%', ' ' => ''));
                            				$email = $conn->real_escape_string($email);
											$username = strtr($username, array('_' => '\_', '%' => '\%', ' ' => ''));
                            				$username = $conn->real_escape_string($username);
											$sql = "SELECT `uuid` FROM `users` WHERE `name`='". $username. "';";
											$result = $conn->query($sql);
											if ($result->num_rows > 0) {
												if($row = $result->fetch_assoc()) {
													error('Dieser Name ist bereits vergeben.');
												} else {
													error('Die Datenbank hat ein Problem bei einer SQL-Abfrage');
												}
											} else {
												$sql = "SELECT `uuid` FROM `users` WHERE `email`='". $email. "';";
												$result = $conn->query($sql);
												if ($result->num_rows > 0) {
													if($row = $result->fetch_assoc()) {
														error('Es existiert bereits ein Account mit dieser Email-Adresse.');
													} else {
														error('Die Datenbank hat ein Problem bei einer SQL-Abfrage');
													}
												} else {
													$passwd = password_hash($passwd, PASSWORD_DEFAULT);
													$sql = "INSERT INTO `users` (`uuid`,`name`,`email`,`passwd`) VALUES (UUID(), '". $username. "', '". $email. "', '". $passwd. "');";
													$conn->query($sql);
													$sql = "SELECT `uuid` FROM `users` WHERE `name`='". $username. "'";
													$result = $conn->query($sql);
													if ($result->num_rows > 0) {
														if($row = $result->fetch_assoc()) {
															$uuid = $row['uuid'];
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
															$mail->Subject  = 'Chat - Aktivierung';
															$mail->Body     = "Hi ". $username. "\r\n". "Um deinen Account zu aktivieren, klicke auf diesen Link oder kopiere ihn in einen Browser: http://0x3b.ch/chat/activate.php?key=". $uuid;
															if(!$mail->send()) {
																echo 'Message was not sent.';
																echo 'Mailer error: ' . $mail->ErrorInfo;
															} else {
																echo ('<h3>Registration erfolgreich!</h3>');
																echo ('<p>Überprüfe nun deine Mails um deinen Account zu aktivieren.</p>');
															}
														} else {
															error('Die Datenbank hat ein Problem bei einer SQL-Abfrage');
														}
													} else {
														error('Die MySQL-Datenbank konnte deine Daten nicht abspeichern. Versuche es später noch einmal.');
													}
												}
											}
										} else {
											error('Die MySQL-Datenbank ist momentan nicht erreichbar. Versuche es später noch einmal.');
										}
									} else {
										error('Diese Email-Adresse wird von uns nicht unterstützt.');
									}
								} else {
									error('Dieser Name enthält ungültige Zeichen.');
								}
							} else {
								error('Der Benutzername muss zwischen 3 und 16 Zeichen lang sein.');
							}
						} else {
							error('Das Passwort ist zu kurz.');
						}
					} else {
						error('Die beiden Passwörter stimmen nicht überein.');
					}
				} else {
					error('Ihr reCAPTCHA war fehlerhaft!');
				}
			} else {
				error('Überprüfen Sie die Eingabe im reCAPTCHA-Feld.');
			}
		} else {
			error('Überprüfen Sie die Eingabe im reCAPTCHA-Feld.');
		}
	} else {
		error('Füllen Sie alle Felder aus.');
	}
} else {
	sendForm();
}
function error($msg)
{
	echo ('<h3>'. $msg. '</h3>');
	sendForm();
}
function sendForm()
{
	$result = '<p><b>Daten eingeben:</b><br />'
		. '<form action="" method="post" accept-charset="utf-8">'
		. '<table align="center" style="text-align:left;"><tr>'
		. '<td>Email-Adresse: </td><td><input type="email" name="email" size="30" pattern=".{3,63}" required /></td>'
		. '</tr><tr>'
		. '<td>Username: </td><td><input id="un" type="text" name="username" size="30" pattern=".{3,16}" required /></td><td><button type="button" id="check">Check</button></td>'
		. '</tr><tr>'
		. '<td>Passwort: </td><td><input type="password" name="passwd" size="30" pattern=".{6,20}" required /></td>'
		. '</tr><tr>'
		. '<td>Passwort erneut eingeben: </td><td><input type="password" name="passwd_confirm" size="30" pattern=".{6,20}" required />'
		. '</td>'
		. '</tr><tr>'
		. '<td></td><td><input type="submit" name="submit" id="" value="Abschicken" /></td>'
		. '</tr></table>'
		. '<br />'
		. '<div align="center" class="g-recaptcha" data-sitekey="6LfXVg0UAAAAANm9wQh63se_V9o1A42Sl0Pghmys"></div>'
		. '</form>';
	echo($result);
}
        </div>
        <p id="f1"></p>
    </body>
</html>