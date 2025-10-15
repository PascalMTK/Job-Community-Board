<?php
// Session Management File
// Note: This file is kept for compatibility but session_start() 
// should be called directly in each page that needs it

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session timeout (30 minutes of inactivity)
$inactive_timeout = 1800; // 30 minutes in seconds

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive_timeout)) {
    // Last request was more than 30 minutes ago
    session_unset();
    session_destroy();
    session_start();
}

$_SESSION['last_activity'] = time(); // Update last activity timestamp
?>
