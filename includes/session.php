<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$inactive_timeout = 1800; 

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive_timeout)) {
    session_unset();
    session_destroy();
    session_start();
}

$_SESSION['last_activity'] = time(); 
?>
