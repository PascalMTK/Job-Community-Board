<?php 
session_start();
include('includes/connection.php');
include('includes/functions.php');

// Handle search
$search_results = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $title = sanitize_input($_POST['title']);
    $location = sanitize_input($_POST['location']);
    
    $query = "SELECT j.*, u.name as employer_name FROM jobs j 
              LEFT JOIN users u ON j.employer_id = u.id 
              WHERE j.status = 'active' AND (j.title LIKE ? OR j.company_name LIKE ?) AND j.location LIKE ?
              ORDER BY j.created_at DESC LIMIT 20";
    
    $search_term = "%$title%";
    $location_term = "%$location%";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $search_term, $search_term, $location_term);
    $stmt->execute();
    $search_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Get categories with job counts
$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories = $conn->query($categories_query)->fetch_all(MYSQLI_ASSOC);

// Get latest jobs
$jobs_query = "SELECT j.*, u.name as employer_name FROM jobs j 
               LEFT JOIN users u ON j.employer_id = u.id 
               WHERE j.status = 'active' 
               ORDER BY j.created_at DESC LIMIT 6";
$latest_jobs = $conn->query($jobs_query)->fetch_all(MYSQLI_ASSOC);

include('includes/header.php');
?>

<!-- home section starts -->
<div class="home-container">
   <section class="home">
      <form action="" method="post">
         <h3 data-en="find your dream job" data-af="vind jou droomwerk">find your dream job</h3>
         <p data-en="job title" data-af="werkstitel">job title <span>*</span></p>
         <input type="text" name="title" placeholder="keyword, category or company" 
         data-placeholder-en="keyword, category or company" data-placeholder-af="sleutelwoord, kategorie of maatskappy" required maxlength="20" class="input">
         <p data-en="job location" data-af="werksligging">job location</p>
         <input type="text" name="location" placeholder="city, state or country" data-placeholder-en="city, state or country" data-placeholder-af="stad, staat of land" required maxlength="50" class="input">
         <input type="submit" value="search job" name="search" class="btn" data-value-en="search job" data-value-af="soek werk">
      </form>
   </section>
</div>
<!-- home section ends -->

<!-- category section starts -->
<section class="category">
   <h1 class="heading" data-en="job categories" data-af="werkskategorieë">job categories</h1>
   <div class="box-container">
      <?php 
      $i = 1;
      foreach ($categories as $category): 
      ?>
      <a href="all_jobs.php?category=<?php echo $category['id']; ?>" class="box" style="--i: <?php echo $i++; ?>;">
         <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
         <div>
            <h3><?php echo htmlspecialchars($category['name']); ?></h3>
            <span><?php echo $category['job_count']; ?> jobs</span>
         </div>
      </a>
      <?php endforeach; ?>
   </div>
</section>
<!-- category section ends -->

<!-- jobs section starts -->
<section class="jobs-container">
   <h1 class="heading" data-en="latest jobs" data-af="nuutste werke">latest jobs</h1>
   <div class="box-container">
      <?php 
      $i = 1;
      foreach ($latest_jobs as $job): 
      ?>
      <div class="box" style="--i: <?php echo $i++; ?>;">
         <div class="company">
            <img src="<?php echo htmlspecialchars($job['company_logo']); ?>" alt="<?php echo htmlspecialchars($job['company_name']); ?>">
            <div>
               <h3><?php echo htmlspecialchars($job['company_name']); ?></h3>
               <p><?php echo time_ago($job['created_at']); ?></p>
            </div>
         </div>
         <h3 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h3>
         <p class="location"><i class="fas fa-map-marker-alt"></i> <span><?php echo htmlspecialchars($job['location']); ?></span></p>
         <div class="tags">
            <p><i class="fas fa-money-bill-wave"></i> <span><?php echo format_salary($job['salary_min'], $job['salary_max'], $job['salary_currency']); ?></span></p>
            <p><i class="fas fa-briefcase"></i> <span><?php echo htmlspecialchars($job['job_type']); ?></span></p>
            <p><i class="fas fa-clock"></i> <span><?php echo htmlspecialchars($job['shift_type']); ?></span></p>
         </div>
         <div class="flex-btn">
            <a href="view_job.php?id=<?php echo $job['id']; ?>" class="btn" data-en="view details" data-af="bekyk besonderhede">view details</a>
            <?php if (is_student()): ?>
            <button type="button" class="far fa-heart save-job-btn" data-job-id="<?php echo $job['id']; ?>"></button>
            <?php endif; ?>
         </div>
      </div>
      <?php endforeach; ?>
      
      <?php if (empty($latest_jobs)): ?>
      <p style="text-align: center; padding: 2rem;">No jobs available at the moment.</p>
      <?php endif; ?>
   </div>
   <div style="text-align: center; margin-top: 2rem;">
      <a href="all_jobs.php" class="btn" data-en="view all" data-af="bekyk alles">view all</a>
   </div>
</section>
<!-- jobs section ends -->

<?php include('includes/footer.php')?>
