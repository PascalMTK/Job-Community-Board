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
