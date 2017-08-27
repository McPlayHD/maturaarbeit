<?php
if (isset($_POST['email']) && isset($_POST['session_key'])) {
	$email = $_POST['email'];
	$key = $_POST['session_key'];
	$ip = $_SERVER['REMOTE_ADDR'];
	if (preg_match('/^[a-z0-9]{8}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{12}$/', $key)) {
		if (preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $email)) { /*http://emailregex.com/*/
			$sql_servername = "localhost";
			$sql_username = "chat";
			$sql_password = "***";
			$sql_dbname = "chat";
			$conn = new mysqli($sql_servername, $sql_username, $sql_password, $sql_dbname);
			if (!($conn->connect_error)) {
				$email = strtr($email, array('_' => '\_', '%' => '\%', ' ' => ''));
                $email = $conn->real_escape_string($email);
				$sql = "UPDATE `users` SET `session_key`=NULL, `session_key_time`=0, `session_ip`=NULL WHERE `email`='". $email. "' AND `session_key`='". $key. "' AND `session_ip`='". $ip. "';";
				$conn->query($sql);
				if ($conn->affected_rows != 0) {
					echo('true');
				} else {
					echo('false');
				}
			} else {
				echo('Error 302\nDatabase connection failure');	
			}
		} else {
			echo('Error 402\nEmail is in wrong format');
		}
	} else {
		echo('Error 401\nSession-Id is in wrong format');
	}
} else {
	echo('Error 400\nSession-Id or Email is missing');	
}
?>