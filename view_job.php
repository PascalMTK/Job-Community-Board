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

<style>
.job-details {
   padding: 2rem 10%;
   max-width: 1400px;
   margin: 0 auto;
}

.job-header {
   display: flex;
   justify-content: space-between;
   align-items: flex-start;
   padding: 2rem;
   background: #fff;
   border-radius: 1rem;
   box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
   margin-bottom: 2rem;
}

.company-info {
   display: flex;
   gap: 2rem;
}

.company-info img {
   width: 10rem;
   height: 10rem;
   object-fit: contain;
   border-radius: 0.5rem;
}

.company-info h1 {
   font-size: 2.8rem;
   color: var(--black);
   margin-bottom: 0.5rem;
}

.company-info h3 {
   font-size: 2rem;
   color: #666;
   margin-bottom: 1rem;
}

.company-info p {
   font-size: 1.4rem;
   color: #777;
   margin: 0.3rem 0;
}

.job-actions {
   display: flex;
   gap: 1rem;
   align-items: center;
}

.job-content {
   display: grid;
   grid-template-columns: 1fr 35rem;
   gap: 2rem;
}

.main-content {
   background: #fff;
   padding: 2rem;
   border-radius: 1rem;
   box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
}

.section {
   margin-bottom: 3rem;
}

.section h2 {
   font-size: 2.2rem;
   color: var(--black);
   margin-bottom: 1.5rem;
   border-bottom: 2px solid var(--main-color);
   padding-bottom: 0.5rem;
}

.tags {
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(25rem, 1fr));
   gap: 1rem;
}

.tags p {
   font-size: 1.5rem;
   color: #555;
   padding: 1rem;
   background: #f5f5f5;
   border-radius: 0.5rem;
}

.description-text {
   font-size: 1.6rem;
   line-height: 1.8;
   color: #555;
}

.skills {
   display: flex;
   flex-wrap: wrap;
   gap: 1rem;
}

.skill-tag {
   display: inline-block;
   padding: 0.8rem 1.5rem;
   background: var(--main-color);
   color: #fff;
   border-radius: 2rem;
   font-size: 1.4rem;
}

.apply-section {
   text-align: center;
   margin-top: 3rem;
   padding-top: 2rem;
   border-top: 1px solid #ddd;
}

.btn-large {
   padding: 1.5rem 4rem;
   font-size: 1.8rem;
}

.sidebar > div {
   background: #fff;
   padding: 2rem;
   border-radius: 1rem;
   box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
   margin-bottom: 2rem;
}

.sidebar h3 {
   font-size: 2rem;
   color: var(--black);
   margin-bottom: 1.5rem;
}

.sidebar h4 {
   font-size: 1.8rem;
   color: #666;
   margin-bottom: 1rem;
}

.sidebar p {
   font-size: 1.4rem;
   color: #777;
   margin: 0.5rem 0;
}

.share-buttons {
   display: flex;
   gap: 1rem;
}

.share-btn {
   width: 4rem;
   height: 4rem;
   display: flex;
   align-items: center;
   justify-content: center;
   background: var(--main-color);
   color: #fff;
   border-radius: 0.5rem;
   font-size: 1.8rem;
   transition: all 0.3s ease;
}

.share-btn:hover {
   transform: translateY(-3px);
   opacity: 0.9;
}

@media (max-width: 991px) {
   .job-header {
      flex-direction: column;
      gap: 2rem;
   }
   
   .job-content {
      grid-template-columns: 1fr;
   }
}
</style>

<?php include('includes/footer.php'); ?>

