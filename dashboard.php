<?php 
session_start();
include('includes/connection.php');
include('includes/functions.php');

if (!is_logged_in() || !is_student()) {
    redirect('login.php');
}

$user_id = get_user_id();

$applications_query = "
    SELECT a.*, j.title, j.company_name, j.location, j.job_type, 
           u.name as employer_name, u.email as employer_email
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN users u ON j.employer_id = u.id
    WHERE a.student_id = ?
    ORDER BY a.applied_at DESC
";
$stmt = $conn->prepare($applications_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calculate statistics
$total = count($applications);
$pending = count(array_filter($applications, fn($a) => $a['status'] == 'pending'));
$accepted = count(array_filter($applications, fn($a) => $a['status'] == 'accepted'));
$rejected = count(array_filter($applications, fn($a) => $a['status'] == 'rejected'));

include('includes/header.php');
?>

<!-- Dashboard Section -->
<section class="dashboard" id="dashboard">
   <h1 class="heading">Your Dashboard</h1>
   
   <?php if (isset($_SESSION['success'])): ?>
      <div class="alert-success">
         <i class="fas fa-check-circle"></i>
         <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
      </div>
   <?php endif; ?>
   
   <div class="stats">
      <div class="box" style="--i: 1;">
         <i class="fas fa-file-alt"></i>
         <h3><?php echo $total; ?></h3>
         <p>Total Applications</p>
      </div>
      <div class="box" style="--i: 2;">
         <i class="fas fa-clock"></i>
         <h3><?php echo $pending; ?></h3>
         <p>Pending Applications</p>
      </div>
      <div class="box" style="--i: 3;">
         <i class="fas fa-check-circle"></i>
         <h3><?php echo $accepted; ?></h3>
         <p>Accepted Applications</p>
      </div>
      <div class="box" style="--i: 4;">
         <i class="fas fa-times-circle"></i>
         <h3><?php echo $rejected; ?></h3>
         <p>Rejected Applications</p>
      </div>
   </div>
   
   <div class="applications">
      <div class="section-header">
         <h2 class="heading" style="margin: 0;">Your Job Applications</h2>
      </div>

      <div class="box-container" id="applicationsList">
         <?php if (count($applications) > 0): ?>
            <?php foreach ($applications as $app): ?>
               <div class="application-box status-<?php echo $app['status']; ?>" data-status="<?php echo $app['status']; ?>">
                  <div class="app-header">
                     <h3><?php echo htmlspecialchars($app['title']); ?></h3>
                     <span class="status-badge badge-<?php echo $app['status']; ?>">
                        <?php echo ucfirst($app['status']); ?>
                     </span>
                  </div>
                  
                  <div class="app-details">
                     <p><i class="fas fa-building"></i> <strong>Company:</strong> <?php echo htmlspecialchars($app['company_name']); ?></p>
                     <p><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> <?php echo htmlspecialchars($app['location']); ?></p>
                     <p><i class="fas fa-briefcase"></i> <strong>Type:</strong> <?php echo ucfirst($app['job_type']); ?></p>
                     <p><i class="fas fa-calendar"></i> <strong>Applied:</strong> <?php echo time_ago($app['applied_at']); ?></p>
                  </div>
                  
                  <div class="app-actions">
                     <a href="view_job.php?id=<?php echo $app['job_id']; ?>" class="btn-view">
                        <i class="fas fa-eye"></i> View Job
                     </a>
                     <?php if ($app['status'] == 'accepted'): ?>
                        <p class="contact-info">
                           <i class="fas fa-envelope"></i> Contact: <?php echo htmlspecialchars($app['employer_email']); ?>
                        </p>
                     <?php endif; ?>
                  </div>
               </div>
            <?php endforeach; ?>
         <?php else: ?>
            <div class="empty-applications">
               <i class="fas fa-briefcase"></i>
               <h3>No Applications Yet</h3>
               <p>You haven't applied for any jobs yet. Start exploring opportunities!</p>
               <a href="all_jobs.php" class="btn">Browse Jobs</a>
            </div>
         <?php endif; ?>
      </div>
   </div>
</section>

<style>
.alert-success {
   background: #d4edda;
   color: #155724;
   padding: 15px 20px;
   border-radius: 8px;
   margin-bottom: 20px;
   display: flex;
   align-items: center;
   gap: 10px;
}

.application-box {
   background: white;
   border-radius: 12px;
   padding: 25px;
   box-shadow: 0 2px 10px rgba(0,0,0,0.08);
   transition: transform 0.3s ease;
   border-left: 4px solid #667eea;
   margin-bottom: 20px;
}

.application-box:hover {
   transform: translateY(-3px);
   box-shadow: 0 5px 20px rgba(0,0,0,0.12);
}

.application-box.status-accepted {
   border-left-color: #10b981;
}

.application-box.status-rejected {
   border-left-color: #ef4444;
}

.application-box.status-pending {
   border-left-color: #f59e0b;
}

.app-header {
   display: flex;
   justify-content: space-between;
   align-items: flex-start;
   margin-bottom: 15px;
   padding-bottom: 15px;
   border-bottom: 2px solid #f0f0f0;
}

.app-header h3 {
   margin: 0;
   color: #333;
   font-size: 20px;
   flex: 1;
}

.status-badge {
   padding: 6px 16px;
   border-radius: 20px;
   font-size: 12px;
   font-weight: 600;
   text-transform: uppercase;
}

.badge-pending {
   background: #fff3cd;
   color: #856404;
}

.badge-accepted {
   background: #d4edda;
   color: #155724;
}

.badge-rejected {
   background: #f8d7da;
   color: #721c24;
}

.app-details p {
   margin: 10px 0;
   color: #666;
   display: flex;
   align-items: center;
   gap: 10px;
}

.app-details i {
   color: #667eea;
   width: 20px;
}

.app-actions {
   margin-top: 15px;
   padding-top: 15px;
   border-top: 2px solid #f0f0f0;
   display: flex;
   justify-content: space-between;
   align-items: center;
   gap: 10px;
}

.btn-view {
   background: #667eea;
   color: white;
   padding: 10px 20px;
   border-radius: 8px;
   text-decoration: none;
   display: inline-flex;
   align-items: center;
   gap: 8px;
   font-weight: 600;
   transition: background 0.3s ease;
}

.btn-view:hover {
   background: #5568d3;
}

.contact-info {
   color: #10b981;
   font-size: 14px;
   margin: 0;
}

.empty-applications {
   text-align: center;
   padding: 60px 20px;
   background: white;
   border-radius: 12px;
}

.empty-applications i {
   font-size: 64px;
   color: #ddd;
   margin-bottom: 20px;
}

.empty-applications h3 {
   color: #333;
   margin: 0 0 10px 0;
}

.empty-applications p {
   color: #999;
   margin-bottom: 20px;
}
</style>

<footer class="footer">
   <section class="grid">
      <div class="box" style="--i: 1;">
         <h3 data-en="quick links" data-af="vinnige skakels">quick links</h3>
         <a href="home.html" data-en="home" data-af="tuis"><i class="fas fa-angle-right"></i> home</a>
         <a href="about.html" data-en="about" data-af="oor ons"><i class="fas fa-angle-right"></i> about</a>
         <a href="jobs.html" data-en="all jobs" data-af="alle werke"><i class="fas fa-angle-right"></i> all jobs</a>
         <a href="contact.html" data-en="contact us" data-af="kontak ons"><i class="fas fa-angle-right"></i> contact us</a>
         <a href="dashboard.html" data-en="dashboard" data-af="kontroleskerm"><i class="fas fa-angle-right"></i> dashboard</a>
      </div>
      <div class="box" style="--i: 2;">
         <h3 data-en="extra links" data-af="ekstra skakels">extra links</h3>
         <a href="#" data-en="account" data-af="rekening"><i class="fas fa-angle-right"></i> account</a>
         <a href="login.html" data-en="login" data-af="aanmeld"><i class="fas fa-angle-right"></i> login</a>
         <a href="register.html" data-en="register" data-af="registreer"><i class="fas fa-angle-right"></i> register</a>
         <a href="#" data-en="post job" data-af="plaas werk"><i class="fas fa-angle-right"></i> post job</a>
         <a href="dashboard.html" data-en="dashboard" data-af="kontroleskerm"><i class="fas fa-angle-right"></i> dashboard</a>
      </div>
      <div class="box" style="--i: 3;">
         <h3 data-en="follow us" data-af="volg ons">follow us</h3>
         <a href="#"><i class="fab fa-facebook-f"></i> facebook</a>
         <a href="#"><i class="fab fa-twitter"></i> twitter</a>
         <a href="#"><i class="fab fa-instagram"></i> instagram</a>
         <a href="#"><i class="fab fa-linkedin"></i> linkedin</a>
         <a href="#"><i class="fab fa-youtube"></i> youtube</a>
      </div>
   </section>
   <div class="credit" data-en="&copy; copyright @ 2025 by Group 83 | all rights reserved!" data-af="&copy; kopiereg @ 2025 deur Groep 83 | alle regte voorbehou!">&copy; copyright @ 2025 by <span>Group 83</span> | all rights reserved!</div>
</footer>

<button class="settings-btn fas fa-cog" data-en="Settings" data-af="Instellings"></button>
<div class="settings-panel">
   <h3 data-en="Settings" data-af="Instellings">Settings</h3>
   <div class="option">
      <label for="dark-mode" data-en="Dark Mode" data-af="Donker Modus">Dark Mode</label>
      <input type="checkbox" id="dark-mode">
   </div>
   <div class="option">
      <label for="language" data-en="Language" data-af="Taal">Language</label>
      <select id="language">
         <option value="en" data-en="English" data-af="Engels">English</option>
         <option value="af" data-en="Afrikaans" data-af="Afrikaans">Afrikaans</option>
      </select>
   </div>
</div>

<div class="application-modal" id="applicationModal">
   <div class="modal-content">
      <button class="close-modal" onclick="closeApplicationModal()">
         <i class="fas fa-times"></i>
      </button>
      
      <h2 data-en="Apply for This Job" data-af="Doen Aansoek vir Hierdie Werk">Apply for This Job</h2>
      
      <form id="applicationForm">
         <div class="form-group">
            <label data-en="Full Name" data-af="Volle Naam">Full Name <span class="required">*</span></label>
            <input type="text" class="input" data-placeholder-en="Enter your full name" data-placeholder-af="Voer jou volle naam in" placeholder="Enter your full name" required>
         </div>

         <div class="form-group">
            <label data-en="Email Address" data-af="E-pos Adres">Email Address <span class="required">*</span></label>
            <input type="email" class="input" data-placeholder-en="Enter your email" data-placeholder-af="Voer jou e-pos in" placeholder="Enter your email" required>
         </div>

         <div class="form-group">
            <label data-en="Phone Number" data-af="Telefoonnommer">Phone Number <span class="required">*</span></label>
            <input type="tel" class="input" data-placeholder-en="Enter your phone number" data-placeholder-af="Voer jou telefoonnommer in" placeholder="Enter your phone number" required>
         </div>

         <div class="form-group">
            <label data-en="Upload CV/Resume" data-af="Laai CV/Curriculum Vitae Op">Upload CV/Resume <span class="required">*</span></label>
            <label class="file-upload" for="cvUpload">
               <i class="fas fa-cloud-upload-alt"></i>
               <p data-en="Click to upload your CV" data-af="Klik om jou CV op te laai">Click to upload your CV</p>
               <small data-en="PDF, DOC, DOCX (Max 5MB)" data-af="PDF, DOC, DOCX (Maks 5MB)">PDF, DOC, DOCX (Max 5MB)</small>
               <input type="file" id="cvUpload" accept=".pdf,.doc,.docx" onchange="handleFileUpload(event)" required>
            </label>
            <div class="file-info" id="fileInfo">
               <i class="fas fa-file"></i>
               <span id="fileName"></span>
            </div>
         </div>

         <div class="form-group">
            <label data-en="Cover Letter" data-af="Motiveringsbrief">Cover Letter</label>
            <textarea class="input" data-placeholder-en="Tell us why you're a great fit for this position..." data-placeholder-af="Vertel ons hoekom jy perfek is vir hierdie posisie..." placeholder="Tell us why you're a great fit for this position..."></textarea>
         </div>

         <div class="form-group">
            <label data-en="Years of Experience" data-af="Jare Ondervinding">Years of Experience</label>
            <select class="input">
               <option data-en="Select experience" data-af="Kies ondervinding">Select experience</option>
               <option data-en="0-1 years" data-af="0-1 jaar">0-1 years</option>
               <option data-en="1-3 years" data-af="1-3 jaar">1-3 years</option>
               <option data-en="3-5 years" data-af="3-5 jaar">3-5 years</option>
               <option data-en="5+ years" data-af="5+ jaar">5+ years</option>
            </select>
         </div>

         <button type="submit" class="btn" data-en="Submit Application" data-af="Dien Aansoek In" style="width: 100%;">Submit Application</button>
      </form>
   </div>
</div>

<div class="notification" id="notification">
   <i class="fas fa-check-circle"></i>
   <span id="notificationText">Success!</span>
</div>

<script src="js/script.js"></script>
<script src="js/dashboard.js"></script>

