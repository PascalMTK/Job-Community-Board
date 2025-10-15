<?php
$host = "127.0.0.1";
$port = 3306;
$user = "root";
$pass = ""; // Empty password - default for XAMPP
$dbname = "community_job_board";

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
