<?php 
session_start();
include('includes/connection.php');
include('includes/functions.php');

$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($job_id <= 0) {
    redirect('all_jobs.php');
}

$stmt = $conn->prepare("SELECT j.*, u.name as employer_name, u.email as employer_email, u.phone as employer_phone, c.name as category_name 
                        FROM jobs j 
                        LEFT JOIN users u ON j.employer_id = u.id 
                        LEFT JOIN categories c ON j.category_id = c.id 
                        WHERE j.id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    redirect('all_jobs.php');
}

$job = $result->fetch_assoc();
$stmt->close();

$conn->query("UPDATE jobs SET views = views + 1 WHERE id = $job_id");

//Checking if the user already applied
$already_applied = false;
if (is_student()) {
    $user_id = get_user_id();
    $check = $conn->prepare("SELECT id FROM applications WHERE job_id = ? AND student_id = ?");
    $check->bind_param("ii", $job_id, $user_id);
    $check->execute();
    $already_applied = $check->get_result()->num_rows > 0;
    $check->close();
}

include('includes/header.php');
?>
