<?php
// The MySQL server host
$host = getenv('mysql-server');

// Database use name
$user = getenv('MYSQL_USER');

//database user password
$pass = getenv('MYSQL_PASSWORD');

// check the MySQL connection status
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected to MySQL server successfully!";
}
?>
