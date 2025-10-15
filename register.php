<?php 
session_start();
include('includes/connection.php');
include('includes/functions.php');

// Redirect if already logged in
if (is_logged_in()) {
    if (is_employer()) {
        redirect('employer_home.php');
    } else {
        redirect('student_home.php');
    }
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['pass'];
    $confirm_password = $_POST['c_pass'];
    $role = isset($_POST['role']) ? sanitize_input($_POST['role']) : 'student';
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
        $stmt->close();
    }
}

include('includes/header.php');
?>

<div class="account-form-container">

   <section class="account-form">

      <form action="" method="post">
         <h3>create new account!</h3>
         
         <?php if ($error): ?>
            <p style="color: red; text-align: center;"><?php echo $error; ?></p>
         <?php endif; ?>
         
         <?php if ($success): ?>
            <p style="color: green; text-align: center;"><?php echo $success; ?></p>
         <?php endif; ?>
         
         <input type="text" required name="name" maxlength="100" placeholder="enter your name" class="input" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
         <input type="email" required name="email" maxlength="50" placeholder="enter your email" class="input" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
         <input type="password" required name="pass" maxlength="20" placeholder="enter your password" class="input">
         <input type="password" required name="c_pass" maxlength="20" placeholder="confirm your password" class="input">
         
         <select name="role" required class="input" style="margin: 1rem 0;">
            <option value="">Select Role</option>
            <option value="student" <?php echo (isset($_POST['role']) && $_POST['role'] == 'student') ? 'selected' : ''; ?>>Student (Job Seeker)</option>
            <option value="employer" <?php echo (isset($_POST['role']) && $_POST['role'] == 'employer') ? 'selected' : ''; ?>>Employer (Job Provider)</option>
         </select>
         
         <p>already have an account? <a href="login.php">login now</a></p>
         <input type="submit" value="register now" name="submit" class="btn">
      </form>
   
   </section>

</div>

<?php include('includes/footer.php'); ?>

