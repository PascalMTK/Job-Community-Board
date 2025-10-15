<?php 
session_start();
include('includes/connection.php');
include('includes/functions.php');

<!-- jobs filter section starts -->
<section class="jobs-filter">
   <h1 class="heading">All Available Jobs</h1>
   
   <form action="" method="get" class="filter-form">
      <div class="flex">
         <div class="box">
            <label>Search</label>
            <input type="text" name="search" placeholder="Job title, company or keyword..." value="<?php echo htmlspecialchars($search); ?>" class="input">
         </div>
         
         <div class="box">
            <label>Category</label>
            <select name="category" class="input">
               <option value="">All Categories</option>
               <?php foreach ($categories as $cat): ?>
               <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cat['name']); ?> (<?php echo $cat['job_count']; ?>)
               </option>
               <?php endforeach; ?>
            </select>
         </div>
         
         <div class="box">
            <label>Job Type</label>
            <select name="type" class="input">
               <option value="">All Types</option>
               <option value="full-time" <?php echo ($job_type == 'full-time') ? 'selected' : ''; ?>>Full-Time</option>
               <option value="part-time" <?php echo ($job_type == 'part-time') ? 'selected' : ''; ?>>Part-Time</option>
               <option value="contract" <?php echo ($job_type == 'contract') ? 'selected' : ''; ?>>Contract</option>
               <option value="internship" <?php echo ($job_type == 'internship') ? 'selected' : ''; ?>>Internship</option>
               <option value="temporary" <?php echo ($job_type == 'temporary') ? 'selected' : ''; ?>>Temporary</option>
            </select>
         </div>
         
         <div class="box">
            <label>Location</label>
            <select name="location" class="input">
               <option value="">All Locations</option>
               <?php foreach ($locations as $loc): ?>
               <option value="<?php echo htmlspecialchars($loc['location']); ?>" <?php echo ($location == $loc['location']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($loc['location']); ?>
               </option>
               <?php endforeach; ?>
            </select>
         </div>
      </div>
      
      <div class="flex-btn">
         <button type="submit" class="btn">Apply Filters</button>
         <a href="all_jobs.php" class="btn" style="background: #777;">Clear Filters</a>
      </div>
   </form>
</section>
<!-- jobs filter section ends -->

<!-- jobs section starts -->
<section class="jobs-container">
   <div class="heading-bar">
      <h2><?php echo count($jobs); ?> Jobs Found</h2>
   </div>
   
   <div class="box-container" id="jobs-list">
      <?php if (!empty($jobs)): ?>
         <?php 
         $i = 1;
         foreach ($jobs as $job): 
         ?>
         <div class="box job-item" style="--i: <?php echo $i++; ?>;" 
              data-category="<?php echo $job['category_id']; ?>" 
              data-type="<?php echo $job['job_type']; ?>"
              data-location="<?php echo htmlspecialchars($job['location']); ?>">
            <div class="company">
               <img src="<?php echo htmlspecialchars($job['company_logo']); ?>" alt="<?php echo htmlspecialchars($job['company_name']); ?>">
               <div>
                  <h3><?php echo htmlspecialchars($job['company_name']); ?></h3>
                  <p><?php echo time_ago($job['created_at']); ?></p>
               </div>
            </div>
            
            <h3 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h3>
            
            <?php if ($job['category_name']): ?>
            <p class="category"><i class="fas fa-tag"></i> <span><?php echo htmlspecialchars($job['category_name']); ?></span></p>
            <?php endif; ?>
            
            <p class="location"><i class="fas fa-map-marker-alt"></i> <span><?php echo htmlspecialchars($job['location']); ?></span></p>
            
            <div class="description">
               <?php echo truncate_text($job['description'], 120); ?>
            </div>
            
            <div class="tags">
               <p><i class="fas fa-money-bill-wave"></i> <span><?php echo format_salary($job['salary_min'], $job['salary_max'], $job['salary_currency']); ?></span></p>
               <p><i class="fas fa-briefcase"></i> <span><?php echo htmlspecialchars($job['job_type']); ?></span></p>
               <p><i class="fas fa-clock"></i> <span><?php echo htmlspecialchars($job['shift_type']); ?></span></p>
            </div>
            
            <?php if ($job['application_deadline']): ?>
            <p class="deadline"><i class="fas fa-calendar-alt"></i> Apply by: <?php echo date('M d, Y', strtotime($job['application_deadline'])); ?></p>
            <?php endif; ?>
            
            <div class="flex-btn">
               <a href="view_job.php?id=<?php echo $job['id']; ?>" class="btn">View Details</a>
               <?php if (is_student()): ?>
               <button type="button" class="far fa-heart save-job-btn" data-job-id="<?php echo $job['id']; ?>"></button>
               <?php endif; ?>
            </div>
         </div>
         <?php endforeach; ?>
      <?php else: ?>
         <div class="empty">
            <p>No jobs found matching your criteria.</p>
            <a href="all_jobs.php" class="btn">View All Jobs</a>
         </div>
      <?php endif; ?>
   </div>
</section>
<!-- jobs section ends -->

