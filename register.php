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

<?php include('includes/footer.php'); ?>
