<?php

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_employer() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'employer';
}

function is_student() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

function get_user_id() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

function format_salary($min, $max, $currency = 'N$') {
    if ($min && $max) {
        return $currency . number_format($min, 0) . ' - ' . $currency . number_format($max, 0);
    } elseif ($min) {
        return $currency . number_format($min, 0) . '+';
    }
    return 'Negotiable';
}

function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return $diff . ' seconds ago';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    } else {
        return date('M d, Y', $timestamp);
    }
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function show_message($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

function display_message() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'success';
        $message = $_SESSION['message'];
        echo "<div class='alert alert-{$type}'>{$message}</div>";
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

function truncate_text($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function get_file_extension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function validate_file_upload($file, $allowed_types = ['pdf', 'doc', 'docx'], $max_size = 5242880) {
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error';
        return $errors;
    }
    
    $file_ext = get_file_extension($file['name']);
    if (!in_array($file_ext, $allowed_types)) {
        $errors[] = 'Invalid file type. Allowed: ' . implode(', ', $allowed_types);
    }
    
    if ($file['size'] > $max_size) {
        $errors[] = 'File size exceeds maximum allowed (' . ($max_size / 1048576) . 'MB)';
    }
    
    return $errors;
}

function generate_unique_filename($original_name) {
    $ext = get_file_extension($original_name);
    return uniqid() . '_' . time() . '.' . $ext;
}

function upload_file($file, $destination_folder = 'uploads/') {
    if (!file_exists($destination_folder)) {
        mkdir($destination_folder, 0777, true);
    }
    
    $new_filename = generate_unique_filename($file['name']);
    $destination = $destination_folder . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $new_filename;
    }
    
    return false;
}
