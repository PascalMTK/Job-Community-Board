<?php
session_start();
require_once('includes/connection.php');
require_once('includes/functions.php');

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = get_user_id();

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch statistics based on role
if (is_student()) {
    // Student statistics
    $stats_query = "
        SELECT 
            COUNT(*) as total_applications,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_applications,
            SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_applications,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_applications
        FROM applications 
        WHERE id = ?
    ";
    $stmt = $conn->prepare($stats_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    
    // Fetch saved jobs count
    $saved_query = "SELECT COUNT(*) as saved_count FROM saved_jobs WHERE id = ?";
    $stmt = $conn->prepare($saved_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $saved_result = $stmt->get_result()->fetch_assoc();
} else {
    // Employer statistics
    $stats_query = "
        SELECT 
            COUNT(DISTINCT j.id) as total_jobs,
            SUM(CASE WHEN j.status = 'active' THEN 1 ELSE 0 END) as active_jobs,
            COUNT(DISTINCT a.id) as total_applications,
            SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending_applications
        FROM jobs j
        LEFT JOIN applications a ON j.id = a.job_id
        WHERE j.employer_id = ?
    ";
    $stmt = $conn->prepare($stats_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
}

include('includes/header.php');
?>
