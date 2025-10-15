<?php 
session_start();
include('includes/connection.php');
include('includes/functions.php');
include('includes/header.php');

$success = '';
$error = '';


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send'])) {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['number']);
    $role = sanitize_input($_POST['role']);
    $message = sanitize_input($_POST['message']);
    
    if (empty($name) || empty($email) || empty($phone) || empty($role) || empty($message)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, role, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $role, $message);
        
        if ($stmt->execute()) {
            $success = 'Message sent successfully! We will get back to you soon.';
        } else {
            $error = 'Failed to send message. Please try again.';
        }
        $stmt->close();
    }
}
?>
<?php session_start(); ?>

<main role="main">
   <!-- contact us section starts  -->
   <section aria-labelledby="contact-title">
      <h1 id="contact-title" class="section-title">contact us</h1>

      <section class="contact">
         <div class="box-container">
            <div class="box">
               <i class="fas fa-phone" aria-hidden="true"></i>
               <a href="tel:+264812345678">+264 81 234 5678</a>
               <a href="tel:+264612345678">+264 61 234 5678</a>
            </div>

            <div class="box">
               <i class="fas fa-envelope" aria-hidden="true"></i>
               <a href="mailto:info@jobhuntnamibia.com">info@jobhuntnamibia.com</a>
               <a href="mailto:support@jobhuntnamibia.com">support@jobhuntnamibia.com</a>
            </div>

            <div class="box">
               <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
               <a href="https://maps.google.com/?q=Office+12,+Independence+Avenue,+Windhoek,+Namibia" target="_blank">
                  Office 12, Independence Avenue, Windhoek, Namibia
               </a>
            </div>
         </div>

         <form action="" method="post" aria-labelledby="form-title">
            <h2 id="form-title">drop your message</h2>
            
            <?php if ($error): ?>
               <p style="color: red; text-align: center; margin-bottom: 1rem;"><?php echo $error; ?></p>
            <?php endif; ?>
            
            <?php if ($success): ?>
               <p style="color: green; text-align: center; margin-bottom: 1rem;"><?php echo $success; ?></p>
            <?php endif; ?>
            
            <div class="flex">
               <div class="box">
                  <label for="name">name <span>*</span></label>
                  <input type="text" id="name" name="name" required maxlength="100" placeholder="enter your name" class="input" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
               </div>
               <div class="box">
                  <label for="email">email <span>*</span></label>
                  <input type="email" id="email" name="email" required maxlength="50" placeholder="enter your email" class="input" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
               </div>
               <div class="box">
                  <label for="number">number <span>*</span></label>
                  <input type="tel" id="number" name="number" required placeholder="enter your phone number" class="input" value="<?php echo isset($_POST['number']) ? htmlspecialchars($_POST['number']) : ''; ?>">
               </div>
               <div class="box">
                  <label for="role">role <span>*</span></label>
                  <select id="role" name="role" required class="input">
                     <option value="">select your role</option>
                     <option value="employee" <?php echo (isset($_POST['role']) && $_POST['role'] == 'employee') ? 'selected' : ''; ?>>job seeker (employee)</option>
                     <option value="employer" <?php echo (isset($_POST['role']) && $_POST['role'] == 'employer') ? 'selected' : ''; ?>>job provider (employer)</option>
                  </select>
               </div>
            </div>
            <div class="box">
               <label for="message">message <span>*</span></label>
               <textarea id="message" name="message" class="input" required maxlength="1000" placeholder="enter your message" cols="30" rows="10"><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
            </div>
            <input type="submit" value="send message" name="send" class="btn">
         </form>
      </section>
   </section>
   <!-- contact us section ends -->
</main>

