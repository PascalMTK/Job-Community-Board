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

// Format salary range
function format_salary($min, $max, $currency = 'N$') {
    if ($min && $max) {
        return $currency . number_format($min, 0) . ' - ' . $currency . number_format($max, 0);
    } elseif ($min) {
        return $currency . number_format($min, 0) . '+';
    }
    return 'Negotiable';
}

// Time ago function
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



