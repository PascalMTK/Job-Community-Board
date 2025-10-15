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
