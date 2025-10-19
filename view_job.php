<?php 
session_start();
include('includes/connection.php');
include('includes/functions.php');

$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($job_id <= 0) {
    redirect('all_jobs.php');
}

$stmt = $conn->prepare("SELECT j.*, u.name as employer_name, u.email as employer_email, u.phone as employer_phone, c.name as category_name 
                        FROM jobs j 
                        LEFT JOIN users u ON j.employer_id = u.id 
                        LEFT JOIN categories c ON j.category_id = c.id 
                        WHERE j.id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    redirect('all_jobs.php');
}

$job = $result->fetch_assoc();
$stmt->close();

$conn->query("UPDATE jobs SET views = views + 1 WHERE id = $job_id");

//Checking if the user already applied
$already_applied = false;
if (is_student()) {
    $user_id = get_user_id();
    $check = $conn->prepare("SELECT id FROM applications WHERE job_id = ? AND student_id = ?");
    $check->bind_param("ii", $job_id, $user_id);
    $check->execute();
    $already_applied = $check->get_result()->num_rows > 0;
    $check->close();
}

include('includes/header.php');
?>

<section class="job-details">
   <div class="job-header">
      <div class="company-info">
         <img src="<?php echo htmlspecialchars($job['company_logo']); ?>" alt="<?php echo htmlspecialchars($job['company_name']); ?>">
         <div>
            <h1><?php echo htmlspecialchars($job['title']); ?></h1>
            <h3><?php echo htmlspecialchars($job['company_name']); ?></h3>
            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></p>
            <p><i class="fas fa-clock"></i> Posted <?php echo time_ago($job['created_at']); ?> • <?php echo $job['views']; ?> views</p>
         </div>
      </div>
      
      <div class="job-actions">
         <?php if (is_student()): ?>
            <?php if ($already_applied): ?>
               <button class="btn" disabled style="background: #95a5a6;">Already Applied</button>
            <?php else: ?>
               <a href="apply_job.php?id=<?php echo $job['id']; ?>" class="btn">Apply Now</a>
            <?php endif; ?>
            <button class="far fa-heart save-job-btn" data-job-id="<?php echo $job['id']; ?>"></button>
         <?php elseif (!is_logged_in()): ?>
            <a href="login.php" class="btn">Login to Apply</a>
         <?php endif; ?>
      </div>
   </div>
   
   <div class="job-content">
      <div class="main-content">
         <div class="section">
            <h2>Job Overview</h2>
            <div class="tags">
               <p><i class="fas fa-money-bill-wave"></i> <strong>Salary:</strong> <?php echo format_salary($job['salary_min'], $job['salary_max'], $job['salary_currency']); ?></p>
               <p><i class="fas fa-briefcase"></i> <strong>Job Type:</strong> <?php echo ucfirst($job['job_type']); ?></p>
               <p><i class="fas fa-clock"></i> <strong>Shift:</strong> <?php echo htmlspecialchars($job['shift_type']); ?></p>
               <?php if ($job['experience_required']): ?>
               <p><i class="fas fa-user-tie"></i> <strong>Experience:</strong> <?php echo htmlspecialchars($job['experience_required']); ?></p>
               <?php endif; ?>
               <?php if ($job['category_name']): ?>
               <p><i class="fas fa-tag"></i> <strong>Category:</strong> <?php echo htmlspecialchars($job['category_name']); ?></p>
               <?php endif; ?>
               <?php if ($job['application_deadline']): ?>
               <p><i class="fas fa-calendar-alt"></i> <strong>Application Deadline:</strong> <?php echo date('M d, Y', strtotime($job['application_deadline'])); ?></p>
               <?php endif; ?>
            </div>
         </div>
         
         <div class="section">
            <h2>Job Description</h2>
            <div class="description-text">
               <?php echo nl2br(htmlspecialchars($job['description'])); ?>
            </div>
         </div>
         
         <?php if ($job['responsibilities']): ?>
         <div class="section">
            <h2>Responsibilities</h2>
            <div class="description-text">
               <?php echo nl2br(htmlspecialchars($job['responsibilities'])); ?>
            </div>
         </div>
         <?php endif; ?>
         
         <?php if ($job['requirements']): ?>
         <div class="section">
            <h2>Requirements</h2>
            <div class="description-text">
               <?php echo nl2br(htmlspecialchars($job['requirements'])); ?>
            </div>
         </div>
         <?php endif; ?>
         
         <?php if ($job['skills_required']): ?>
         <div class="section">
            <h2>Required Skills</h2>
            <div class="skills">
               <?php 
               $skills = explode(',', $job['skills_required']);
               foreach ($skills as $skill): 
               ?>
               <span class="skill-tag"><?php echo trim(htmlspecialchars($skill)); ?></span>
               <?php endforeach; ?>
            </div>
         </div>
         <?php endif; ?>
         
         <?php if ($job['education_required']): ?>
         <div class="section">
            <h2>Education Required</h2>
            <p><?php echo htmlspecialchars($job['education_required']); ?></p>
         </div>
         <?php endif; ?>
         
         <div class="apply-section">
            <?php if (is_student() && !$already_applied): ?>
               <a href="apply_job.php?id=<?php echo $job['id']; ?>" class="btn btn-large">Apply for This Position</a>
            <?php elseif (is_student() && $already_applied): ?>
               <button class="btn btn-large" disabled style="background: #95a5a6;">You Already Applied</button>
            <?php elseif (!is_logged_in()): ?>
               <a href="login.php" class="btn btn-large">Login to Apply</a>
            <?php endif; ?>
         </div>
      </div>
      
      <div class="sidebar">
         <div class="employer-card">
            <h3>About Employer</h3>
            <h4><?php echo htmlspecialchars($job['employer_name']); ?></h4>
            <?php if ($job['employer_email']): ?>
            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($job['employer_email']); ?></p>
            <?php endif; ?>
            <?php if ($job['employer_phone']): ?>
            <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($job['employer_phone']); ?></p>
            <?php endif; ?>
         </div>
         
         <div class="share-card">
            <h3>Share This Job</h3>
            <div class="share-buttons">
               <a href="#" class="share-btn"><i class="fab fa-facebook-f"></i></a>
               <a href="#" class="share-btn"><i class="fab fa-twitter"></i></a>
               <a href="#" class="share-btn"><i class="fab fa-linkedin"></i></a>
               <a href="#" class="share-btn"><i class="fas fa-envelope"></i></a>
            </div>
         </div>
      </div>
   </div>
</section>
