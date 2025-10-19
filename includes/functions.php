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
