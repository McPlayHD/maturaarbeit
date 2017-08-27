<?php
if (isset($_POST['session-id']) && isset($_POST['uuid'])) {
	$key = $_POST['session-id'];
	$uuid = $_POST['uuid'];
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_POST['ip'])) {
		$ip = $_POST['ip'];
	}
	if (preg_match('/^[a-z0-9]{8}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{12}$/', $key)) {
		if (preg_match('/^[a-z0-9]{8}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{12}$/', $uuid)) {
			$sql_servername = "localhost";
			$sql_username = "chat";
			$sql_password = "***";
			$sql_dbname = "chat";
			$conn = new mysqli($sql_servername, $sql_username, $sql_password, $sql_dbname);
			if (!($conn->connect_error)) {
				$sql = "SELECT `session_key`, `session_key_time` FROM `users` WHERE `uuid`='". $uuid. "';";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					if($row = $result->fetch_assoc()) {
						if ($row['session_key'] == $key) {
							if ($row['session_key_time'] > round(microtime(true) * 1000) - 7*24*60*60*1000) {
								echo('true');
							} else {
								$sql = "UPDATE `users` SET `session_key`=NULL, `session_key_time`=0, `session_ip`=NULL WHERE `uuid`='". $uuid. "';";
								$conn->query($sql);
								echo('false');
							}
						} else {
							echo('false');
						}
					} else {
						echo('Error 304\nMySQL read failure. Please report this to mail@0x3b.ch');
					}
				} else {
					echo('Error 303\nUUID ungültig');	
				}
			} else {
				echo('Error 302\nDatabase connection failure');	
			}
		} else {
			echo('Error 302\nUUID is in wrong fromat');	
		}
	} else {
		echo('Error 301\nSession-Id is in wrong format');	
	}
} else {
	echo('Error 300\nSession-Id or UUID is missing');	
}
?>