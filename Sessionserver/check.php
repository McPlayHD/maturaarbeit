<?php
if (isset($_GET['username'])) {
	$username = $_GET['username'];
	if (strlen($username) >= 3) {
		if (strlen($username) <= 16) {
			if (preg_match('/^\w+$/', $username)) {
				$sql_servername = "localhost";
				$sql_username = "chat";
				$sql_password = "***";
				$sql_dbname = "chat";
				$conn = new mysqli($sql_servername, $sql_username, $sql_password, $sql_dbname);
				if (!($conn->connect_error)) {
					$username = strtr($username, array('_' => '\_', '%' => '\%', ' ' => ''));
					$username = $conn->real_escape_string($username);
					$sql = "SELECT `uuid` FROM `users` WHERE `name`='". $username. "'";
					$result = $conn->query($sql);
					if ($result->num_rows > 0) {
						error('Dieser Name ist bereits vergeben.');
					} else {
						echo("<font color=\"green\">Dieser Name ist verfügbar</font>");
					}
				} else {
					error('Die MySQL-Datenbank ist momentan nicht erreichbar. Versuche es später noch einmal.');
				}
			} else {
				error("Dieser Name enthält ungültige Zeichen");
			}
		} else {
			error("Dieser Name ist zu lang");
		}
	} else {
		error("Dieser Name ist zu kurz");
	}
} else {
	error("Du musst einen Namen eingeben");
}
function error($error)
{
	echo("<font color=\"red\">". $error. "</font>");
}
?>