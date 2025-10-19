<?php 
session_start();
include('includes/connection.php');
include('includes/functions.php');

if (is_logged_in()) {
    if (is_employer()) {
        redirect('employer_home.php');
    } else {
        redirect('student_home.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['pass'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                //setting session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                if ($user['role'] == 'employer') {
                    redirect('employer_home.php');
                } else {
                    redirect('student_home.php');
                }
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
        $stmt->close();
    }
}

include('includes/header.php');
?>


<div class="account-form-container">

   <section class="account-form">

      <form action="" method="post">
         <h3>welcome back!</h3>
         
         <?php if ($error): ?>
            <p style="color: red; text-align: center;"><?php echo $error; ?></p>
         <?php endif; ?>
         
         <input type="email" required name="email" maxlength="50" placeholder="enter your email" class="input" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
         <input type="password" required name="pass" maxlength="20" placeholder="enter your password" class="input">
         <p>don't have an account? <a href="register.php">register now</a></p>
         <input type="submit" value="login now" name="submit" class="btn">
      </form>
      
      <!-- <div style="margin-top: 20px; padding: 15px; background: #f0f0f0; border-radius: 5px;">
         <p style="margin: 5px 0;"><strong>Test Accounts:</strong></p>
         <p style="margin: 5px 0;">Employer: employer@test.com</p>
         <p style="margin: 5px 0;">Student: student@test.com</p>
         <p style="margin: 5px 0;">Password: password123</p>
      </div> -->
   
   </section>

</div>


<?php include('includes/footer.php'); ?>
