<?php
	// MySQL database connection
	$user = "root"; // MySQL username
	$pass = "root@123"; // MySQL password
	$host = "localhost"; // Server name or IP address
	$dbname = "ptw"; // Database name
	
	$conn = mysqli_connect($host, $user, $pass, $dbname);
	
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
?>
