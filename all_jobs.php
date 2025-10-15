<?php 
session_start();
include('includes/connection.php');
include('includes/functions.php');

// Get filter parameters
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$job_type = isset($_GET['type']) ? sanitize_input($_GET['type']) : '';
$location = isset($_GET['location']) ? sanitize_input($_GET['location']) : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Build query
$query = "SELECT j.*, u.name as employer_name, c.name as category_name 
          FROM jobs j 
          LEFT JOIN users u ON j.employer_id = u.id 
          LEFT JOIN categories c ON j.category_id = c.id 
          WHERE j.status = 'active'";

$params = [];
$types = '';

if ($category_id > 0) {
    $query .= " AND j.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

if ($job_type) {
    $query .= " AND j.job_type = ?";
    $params[] = $job_type;
    $types .= 's';
}

if ($location) {
    $query .= " AND j.location LIKE ?";
    $params[] = "%$location%";
    $types .= 's';
}

if ($search) {
    $query .= " AND (j.title LIKE ? OR j.company_name LIKE ? OR j.description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'sss';
}

$query .= " ORDER BY j.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get all categories for filter
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// Get unique locations
$locations = $conn->query("SELECT DISTINCT location FROM jobs WHERE status = 'active' ORDER BY location ASC")->fetch_all(MYSQLI_ASSOC);

include('includes/header.php');
?>

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

<style>
.jobs-filter {
   padding: 2rem 10%;
   background: #f5f5f5;
}

.jobs-filter .heading {
   text-align: center;
   margin-bottom: 2rem;
   font-size: 3rem;
   color: var(--black);
}

.filter-form .flex {
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(25rem, 1fr));
   gap: 1.5rem;
   margin-bottom: 2rem;
}

.filter-form .box label {
   display: block;
   margin-bottom: 0.5rem;
   font-size: 1.6rem;
   color: var(--black);
}

.filter-form .flex-btn {
   display: flex;
   gap: 1rem;
   justify-content: center;
}

.jobs-container .heading-bar {
   padding: 2rem 10%;
   text-align: center;
}

.jobs-container .heading-bar h2 {
   font-size: 2.5rem;
   color: var(--black);
}

.job-item .category {
   font-size: 1.4rem;
   color: #666;
   margin: 0.5rem 0;
}

.job-item .description {
   font-size: 1.4rem;
   color: #777;
   line-height: 1.6;
   margin: 1rem 0;
}

.job-item .deadline {
   font-size: 1.3rem;
   color: #e74c3c;
   margin-top: 1rem;
}

.empty {
   text-align: center;
   padding: 3rem;
   grid-column: 1 / -1;
}

.empty p {
   font-size: 2rem;
   color: #777;
   margin-bottom: 2rem;
}
</style>

<?php include('includes/footer.php'); ?>
