<?php 
session_start();
include('includes/connection.php');
include('includes/functions.php');

// Check if user is employer
if (!is_employer()) {
    redirect('login.php');
}

$user_id = get_user_id();

// Handle job posting
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_job'])) {
    $title = sanitize_input($_POST['title']);
    $category_id = intval($_POST['category']);
    $company_name = sanitize_input($_POST['company_name']);
    $description = sanitize_input($_POST['description']);
    $requirements = sanitize_input($_POST['requirements']);
    $responsibilities = isset($_POST['responsibilities']) ? sanitize_input($_POST['responsibilities']) : '';
    $location = sanitize_input($_POST['location']);
    $job_type = sanitize_input($_POST['job_type']);
    $shift_type = sanitize_input($_POST['shift_type']);
    
    // Parse salary range
    $salary_range = sanitize_input($_POST['salary_range']);
    $salary_min = 0;
    $salary_max = 0;
    
    // Try to extract numbers from salary range (e.g., "10000 - 20000" or "10000-20000")
    if (preg_match('/(\d+)\s*-\s*(\d+)/', $salary_range, $matches)) {
        $salary_min = floatval($matches[1]);
        $salary_max = floatval($matches[2]);
    } elseif (preg_match('/(\d+)/', $salary_range, $matches)) {
        // Single number, use it as minimum
        $salary_min = floatval($matches[1]);
        $salary_max = 0;
    }
    // If "negotiable" or no numbers, leave as 0
    
    $experience = sanitize_input($_POST['experience']);
    $education = isset($_POST['education']) ? sanitize_input($_POST['education']) : '';
    $skills = isset($_POST['skills']) ? sanitize_input($_POST['skills']) : '';
    $deadline = $_POST['deadline'];
    
    if (empty($title) || empty($company_name) || empty($description) || empty($location)) {
        $error = 'Please fill in all required fields';
    } else {
        $stmt = $conn->prepare("INSERT INTO jobs (employer_id, category_id, title, company_name, description, requirements, responsibilities, location, job_type, shift_type, salary_min, salary_max, experience_required, education_required, skills_required, application_deadline, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->bind_param("iissssssssddssss", $user_id, $category_id, $title, $company_name, $description, $requirements, $responsibilities, $location, $job_type, $shift_type, $salary_min, $salary_max, $experience, $education, $skills, $deadline);
        
        if ($stmt->execute()) {
            $success = 'Job posted successfully!';
        } else {
            $error = 'Failed to post job. Please try again.';
        }
        $stmt->close();
    }
}

// Get employer's jobs
$jobs_query = "SELECT j.*, 
               (SELECT COUNT(*) FROM applications WHERE job_id = j.id) as application_count,
               (SELECT COUNT(*) FROM applications WHERE job_id = j.id AND status = 'pending') as pending_count
               FROM jobs j 
               WHERE j.employer_id = ? 
               ORDER BY j.created_at DESC";
$stmt = $conn->prepare($jobs_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$my_jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get statistics using regular queries
$stats_query = "
    SELECT 
        COUNT(DISTINCT j.id) as total_jobs,
        SUM(CASE WHEN j.status = 'active' THEN 1 ELSE 0 END) as active_jobs,
        COUNT(DISTINCT a.id) as total_applications,
        SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending_applications
    FROM jobs j
    LEFT JOIN applications a ON j.id = a.job_id
    WHERE j.employer_id = ?
";
$stmt = $conn->prepare($stats_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get categories for form
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

include('includes/header.php');
?>
    
<!-- Employer Dashboard -->
<section class="employer-dashboard">
   <div class="dashboard-header">
      <h1>Employer Dashboard</h1>
      <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
   </div>
   
   <!-- Statistics -->
   <div class="stats-grid">
      <div class="stat-box" style="--i: 1;">
         <i class="fas fa-briefcase"></i>
         <h3><?php echo $stats['total_jobs']; ?></h3>
         <p>Total Jobs Posted</p>
      </div>
      <div class="stat-box" style="--i: 2;">
         <i class="fas fa-check-circle"></i>
         <h3><?php echo $stats['active_jobs']; ?></h3>
         <p>Active Jobs</p>
      </div>
      <div class="stat-box" style="--i: 3;">
         <i class="fas fa-file-alt"></i>
         <h3><?php echo $stats['total_applications']; ?></h3>
         <p>Total Applications</p>
      </div>
      <div class="stat-box" style="--i: 4;">
         <i class="fas fa-clock"></i>
         <h3><?php echo $stats['pending_applications']; ?></h3>
         <p>Pending Reviews</p>
      </div>
   </div>
   
   <!-- Post New Job Button -->
   <div class="action-bar">
      <button class="btn btn-large" onclick="document.getElementById('postJobModal').style.display='flex'">
         <i class="fas fa-plus"></i> Post New Job
      </button>
   </div>
   
   <!-- My Jobs List -->
   <div class="my-jobs">
      <h2>My Posted Jobs</h2>
      <?php if (!empty($my_jobs)): ?>
         <div class="jobs-grid">
            <?php foreach ($my_jobs as $job): ?>
            <div class="job-card">
               <div class="job-card-header">
                  <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                  <span class="status-badge status-<?php echo $job['status']; ?>">
                     <?php echo ucfirst($job['status']); ?>
                  </span>
               </div>
               <p class="company"><i class="fas fa-building"></i> <?php echo htmlspecialchars($job['company_name']); ?></p>
               <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></p>
               <p class="date"><i class="fas fa-calendar"></i> Posted <?php echo time_ago($job['created_at']); ?></p>
               
               <div class="job-stats">
                  <span><i class="fas fa-eye"></i> <?php echo $job['views']; ?> views</span>
                  <span><i class="fas fa-file-alt"></i> <?php echo $job['application_count']; ?> applications</span>
                  <span><i class="fas fa-clock"></i> <?php echo $job['pending_count']; ?> pending</span>
               </div>
               
               <div class="job-actions">
                  <a href="view_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm">View</a>
                  <a href="manage_applications.php?job_id=<?php echo $job['id']; ?>" class="btn btn-sm">Applications</a>
                  <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm">Edit</a>
               </div>
            </div>
            <?php endforeach; ?>
         </div>
      <?php else: ?>
         <p class="empty-message">You haven't posted any jobs yet. Click "Post New Job" to get started!</p>
      <?php endif; ?>
   </div>
</section>

<!-- Post Job Modal -->
<div id="postJobModal" class="modal">
   <div class="modal-content">
      <span class="close" onclick="document.getElementById('postJobModal').style.display='none'">&times;</span>
      <h2>Post a New Job</h2>
      
      <?php if ($error): ?>
         <p style="color: red; text-align: center;"><?php echo $error; ?></p>
      <?php endif; ?>
      
      <?php if ($success): ?>
         <p style="color: green; text-align: center;"><?php echo $success; ?></p>
      <?php endif; ?>
      
      <form action="" method="post" class="post-job-form">
         <div class="form-grid">
            <div class="form-group">
               <label>Job Title <span>*</span></label>
               <input type="text" name="title" required class="input" placeholder="e.g. Senior Web Developer">
            </div>
            
            <div class="form-group">
               <label>Company Name <span>*</span></label>
               <input type="text" name="company_name" required class="input" placeholder="Your company name">
            </div>
            
            <div class="form-group">
               <label>Category</label>
               <select name="category" class="input">
                  <option value="0">Select Category</option>
                  <?php foreach ($categories as $cat): ?>
                  <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
            
            <div class="form-group">
               <label>Location <span>*</span></label>
               <input type="text" name="location" required class="input" placeholder="e.g. Windhoek, Namibia">
            </div>
            
            <div class="form-group">
               <label>Job Type</label>
               <select name="job_type" class="input" required>
                  <option value="full-time">Full-Time</option>
                  <option value="part-time">Part-Time</option>
                  <option value="contract">Contract</option>
                  <option value="internship">Internship</option>
                  <option value="temporary">Temporary</option>
               </select>
            </div>
            
            <div class="form-group">
               <label>Shift Type</label>
               <input type="text" name="shift_type" class="input" placeholder="e.g. day shift" value="day shift">
            </div>
            
            <div class="form-group">
               <label>Salary Range (N$)</label>
               <input type="text" name="salary_range" class="input" placeholder="e.g. 10000 - 20000 or Negotiable" required>
               <small style="color: #999; font-size: 13px;">Enter salary range or type "Negotiable"</small>
            </div>
            
            <div class="form-group">
               <label>Experience Required</label>
               <input type="text" name="experience" class="input" placeholder="e.g. 2-3 years">
            </div>
            
            <div class="form-group">
               <label>Education Required</label>
               <input type="text" name="education" class="input" placeholder="e.g. Bachelor's Degree">
            </div>
            
            <div class="form-group">
               <label>Skills (comma-separated)</label>
               <input type="text" name="skills" class="input" placeholder="e.g. PHP, JavaScript, MySQL">
            </div>
            
            <div class="form-group">
               <label>Application Deadline</label>
               <input type="date" name="deadline" class="input">
            </div>
         </div>
         
         <div class="form-group">
            <label>Job Description <span>*</span></label>
            <textarea name="description" required class="input" rows="5" placeholder="Describe the job position..."></textarea>
         </div>
         
         <div class="form-group">
            <label>Requirements <span>*</span></label>
            <textarea name="requirements" required class="input" rows="4" placeholder="List the job requirements..."></textarea>
         </div>
         
         <div class="form-group">
            <label>Responsibilities</label>
            <textarea name="responsibilities" class="input" rows="4" placeholder="List the job responsibilities..."></textarea>
         </div>
         
         <button type="submit" name="post_job" class="btn btn-large">Post Job</button>
      </form>
   </div>
</div>

<style>
.employer-dashboard {
   padding: 2rem 10%;
}

.dashboard-header {
   text-align: center;
   margin-bottom: 3rem;
}

.dashboard-header h1 {
   font-size: 3.5rem;
   color: var(--black);
   margin-bottom: 0.5rem;
}

.stats-grid {
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(25rem, 1fr));
   gap: 2rem;
   margin-bottom: 3rem;
}

.stat-box {
   background: #fff;
   padding: 2rem;
   border-radius: 1rem;
   box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
   text-align: center;
   animation: fadeInUp 0.5s ease forwards;
   animation-delay: calc(var(--i) * 0.1s);
   opacity: 0;
}

@keyframes fadeInUp {
   to {
      opacity: 1;
      transform: translateY(0);
   }
}

.stat-box i {
   font-size: 4rem;
   color: var(--main-color);
   margin-bottom: 1rem;
}

.stat-box h3 {
   font-size: 3rem;
   color: var(--black);
   margin-bottom: 0.5rem;
}

.stat-box p {
   font-size: 1.6rem;
   color: #777;
}

.action-bar {
   text-align: center;
   margin-bottom: 3rem;
}

.btn-large {
   padding: 1.5rem 3rem;
   font-size: 1.8rem;
}

.my-jobs h2 {
   font-size: 2.8rem;
   color: var(--black);
   margin-bottom: 2rem;
}

.jobs-grid {
   display: grid;
   grid-template-columns: repeat(auto-fill, minmax(35rem, 1fr));
   gap: 2rem;
}

.job-card {
   background: #fff;
   padding: 2rem;
   border-radius: 1rem;
   box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
}

.job-card-header {
   display: flex;
   justify-content: space-between;
   align-items: flex-start;
   margin-bottom: 1rem;
}

.job-card h3 {
   font-size: 2rem;
   color: var(--black);
}

.status-badge {
   padding: 0.5rem 1rem;
   border-radius: 2rem;
   font-size: 1.2rem;
   font-weight: bold;
}

.status-active {
   background: #27ae60;
   color: #fff;
}

.status-closed {
   background: #e74c3c;
   color: #fff;
}

.status-draft {
   background: #95a5a6;
   color: #fff;
}

.job-card p {
   font-size: 1.4rem;
   color: #777;
   margin: 0.5rem 0;
}

.job-stats {
   display: flex;
   gap: 1.5rem;
   margin: 1.5rem 0;
   padding: 1rem 0;
   border-top: 1px solid #ddd;
   border-bottom: 1px solid #ddd;
}

.job-stats span {
   font-size: 1.3rem;
   color: #666;
}

.job-actions {
   display: flex;
   gap: 1rem;
   margin-top: 1.5rem;
}

.btn-sm {
   padding: 0.8rem 1.5rem;
   font-size: 1.4rem;
}

.modal {
   display: none;
   position: fixed;
   z-index: 1000;
   left: 0;
   top: 0;
   width: 100%;
   height: 100%;
   background: rgba(0,0,0,0.5);
   align-items: center;
   justify-content: center;
   overflow-y: auto;
}

.modal-content {
   background: #fff;
   padding: 3rem;
   border-radius: 1rem;
   max-width: 90rem;
   width: 90%;
   max-height: 90vh;
   overflow-y: auto;
   position: relative;
}

.close {
   position: absolute;
   right: 2rem;
   top: 2rem;
   font-size: 3rem;
   cursor: pointer;
   color: #777;
}

.close:hover {
   color: var(--black);
}

.post-job-form h2 {
   font-size: 2.5rem;
   color: var(--black);
   margin-bottom: 2rem;
}

.form-grid {
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(30rem, 1fr));
   gap: 1.5rem;
   margin-bottom: 1.5rem;
}

.form-group {
   margin-bottom: 1.5rem;
}

.form-group label {
   display: block;
   margin-bottom: 0.5rem;
   font-size: 1.6rem;
   color: var(--black);
}

.form-group label span {
   color: red;
}

.empty-message {
   text-align: center;
   font-size: 1.8rem;
   color: #777;
   padding: 3rem;
}
</style>

<script>
// Close modal when clicking outside
window.onclick = function(event) {
   const modal = document.getElementById('postJobModal');
   if (event.target == modal) {
      modal.style.display = "none";
   }
}
</script>

<?php include('includes/footer.php'); ?>
