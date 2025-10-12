<?php
$host = "localhost";
$user = "root";
$pass = "muddysituation";
$dbname = "community_job_board";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>