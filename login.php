<?php include('includes/header.php'); ?>
<?php include('includes/connection.php'); ?>
<?php session_start(); ?>

<div class="form-container">
    <h2>Login</h2>
    <form method="POST" action="">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" name="login">Login</button>

        <p>Don’t have an account? <a href="register.php">Register here</a></p>
    </form>
</div>

<?php
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            echo "<script>alert('Welcome back, " . $user['name'] . "!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Incorrect password');</script>";
        }
    } else {
        echo "<script>alert('User not found');</script>";
    }

    $stmt->close();
}
$conn->close();
?>

<?php include('includes/footer.php'); ?>