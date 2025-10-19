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
