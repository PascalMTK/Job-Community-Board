<?php
session_start();
require_once('includes/connection.php');
require_once('includes/functions.php');

// Redirect if not logged in or not a student
if (!is_logged_in() || !is_student()) {
    redirect('login.php');
}

$user_id = get_user_id();

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch recommended jobs (latest 6 jobs)
$jobs_query = "
    SELECT j.*, u.name as employer_name, u.email as employer_email, c.name as category_name
    FROM jobs j
    JOIN users u ON j.employer_id = u.id
    JOIN categories c ON j.category_id = c.id
    WHERE j.status = 'active'
    ORDER BY j.created_at DESC
    LIMIT 6
";
$jobs_result = $conn->query($jobs_query);

include('includes/header.php');
?>

<link rel="stylesheet" href="assets/student_home.css">

<section class="hero">
  <div class="hero-content">
    <h1>Welcome back, <?php echo htmlspecialchars(explode(' ', $user['name'])[0]); ?>!</h1>
    <p>Find Your Dream Internship or Job - Connect with top employers and kickstart your career journey today.</p>
    <a href="all_jobs.php" class="btn-primary">Explore All Jobs</a>
  </div>
</section>

<section id="jobs" class="jobs-section">
  <h2 style="text-align: center; margin-bottom: 30px; color: #333;">Recommended Jobs for You</h2>
  
  <div class="search-filter">
    <form action="all_jobs.php" method="GET" style="display: flex; gap: 10px; width: 100%;">
      <input type="text" name="search" placeholder="Search for jobs or companies..." style="flex: 1;" />
      <button type="submit" class="btn">Search</button>
    </form>
  </div>

  <div class="job-cards">
    <?php if ($jobs_result->num_rows > 0): ?>
      <?php while ($job = $jobs_result->fetch_assoc()): ?>
        <div class="job-card fade-up">
          <div class="job-header">
            <h3><?php echo htmlspecialchars($job['title']); ?></h3>
            <span class="job-type <?php echo strtolower($job['type']); ?>">
              <?php echo htmlspecialchars($job['type']); ?>
            </span>
          </div>
          <p><strong><i class="fas fa-building"></i> Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
          <p><strong><i class="fas fa-map-marker-alt"></i> Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
          <p><strong><i class="fas fa-tag"></i> Category:</strong> <?php echo htmlspecialchars($job['category_name']); ?></p>
          <p><strong><i class="fas fa-money-bill-wave"></i> Salary:</strong> <?php echo format_salary($job['salary_min'], $job['salary_max']); ?></p>
          <p class="job-description"><?php echo truncate_text($job['description'], 100); ?></p>
          <div class="job-footer">
            <span class="job-posted"><i class="fas fa-clock"></i> <?php echo time_ago($job['created_at']); ?></span>
            <a href="view_job.php?id=<?php echo $job['id']; ?>" class="apply-btn">View Details</a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div style="text-align: center; padding: 40px; grid-column: 1/-1;">
        <i class="fas fa-briefcase" style="font-size: 64px; color: #ddd; margin-bottom: 20px;"></i>
        <p style="color: #666; font-size: 18px;">No jobs available at the moment. Check back later!</p>
      </div>
    <?php endif; ?>
  </div>
  
  <?php if ($jobs_result->num_rows > 0): ?>
    <div style="text-align: center; margin-top: 30px;">
      <a href="all_jobs.php" class="btn" style="display: inline-block;">View All Jobs</a>
    </div>
  <?php endif; ?>
</section>

<section class="reviews-section">
  <h2>What Students Say</h2>
  <div class="review-cards">
    <div class="review-card slide-in">
      <img src="assets/profile1.jpg" alt="Reviewer 1">
      <p>“JobConnect helped me land my first internship within weeks!”</p>
      <span>- Maria K.</span>
    </div>

    <div class="review-card slide-in">
      <img src="assets/profile2.jpg" alt="Reviewer 2">
      <p>“The platform is so easy to use and connects you with real employers.”</p>
      <span>- Peter L.</span>
    </div>

    <div class="review-card slide-in">
      <img src="assets/profile3.jpg" alt="Reviewer 3">
      <p>“I love how everything is organized and simple to navigate.”</p>
      <span>- Anna N.</span>
    </div>
  </div>
</section>

<style>
.job-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 25px;
  margin-top: 30px;
}

.job-card {
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.job-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.job-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 15px;
  gap: 10px;
}

.job-header h3 {
  margin: 0;
  color: #333;
  font-size: 20px;
  flex: 1;
}

.job-type {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}

.job-type.full-time {
  background: #d4edda;
  color: #155724;
}

.job-type.part-time {
  background: #fff3cd;
  color: #856404;
}

.job-type.contract {
  background: #d1ecf1;
  color: #0c5460;
}

.job-type.internship {
  background: #e2e3e5;
  color: #383d41;
}

.job-card p {
  margin: 10px 0;
  color: #666;
  font-size: 14px;
}

.job-card p i {
  width: 20px;
  color: #667eea;
}

.job-description {
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #eee;
  line-height: 1.6;
}

.job-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
  padding-top: 15px;
  border-top: 1px solid #eee;
}

.job-posted {
  font-size: 12px;
  color: #999;
}

.apply-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 10px 20px;
  border-radius: 25px;
  text-decoration: none;
  font-weight: 600;
  transition: transform 0.3s ease;
}

.apply-btn:hover {
  transform: scale(1.05);
}

.review-card img {
  display: none;
}

.review-card {
  text-align: center;
}

.review-card::before {
  content: '\f007';
  font-family: 'Font Awesome 6 Free';
  font-weight: 900;
  font-size: 60px;
  color: #667eea;
  display: block;
  margin-bottom: 15px;
}
</style>

<script>
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) entry.target.classList.add('visible');
    });
  });

  document.querySelectorAll('.fade-up, .slide-in').forEach(el => observer.observe(el));
</script>

<?php include('includes/footer.php'); ?>
