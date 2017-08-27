<?php
if (isset($_GET['uuid'])) {
	$uuid = $_GET['uuid'];
	if (preg_match('/^[a-z0-9]{8}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{4}\-[a-z0-9]{12}$/', $uuid)) {
		$sql_servername = "localhost";
		$sql_username = "chat";
		$sql_password = "***";
		$sql_dbname = "chat";
		$conn = new mysqli($sql_servername, $sql_username, $sql_password, $sql_dbname);
		if (!($conn->connect_error)) {
			$sql = "SELECT `name` FROM `users` WHERE `uuid`='". $uuid. "';";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				if($row = $result->fetch_assoc()) {
					$name = $row['name'];
					echo($name);
				} else {
					echo('Error: Fatal Error. We could not fetch the row of the table');
				}
			} else {
				echo('Error: There is no user with this UUID');
			}
		} else {
			echo('Error: MySQL-Database is not responding.');
		}
	} else {
		echo('Error: UUID format is wrong');	
	}
} else {
	echo('Error: No uuid set');	
}
?>