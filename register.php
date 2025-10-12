<?php include('includes/header.php'); ?>

<div class="form-container">
    <h2>Create an Account</h2>
    <form action="register.php" method="POST">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="role">I am a:</label>
        <select id="role" name="role" required>
            <option value="">Select your role</option>
            <option value="student">Student (looking for jobs)</option>
            <option value="employer">Employer (posting jobs)</option>
        </select>

        <button type="submit" name="register">Register</button>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </form>
</div>

<?php
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Please log in.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Error: Could not register user.');</script>";
    }

    $stmt->close();
}
$conn->close();
?>


<?php include('includes/footer.php'); ?>
