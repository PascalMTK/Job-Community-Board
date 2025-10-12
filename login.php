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