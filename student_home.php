<?php
session_start();
require_once('includes/connection.php');
require_once('includes/functions.php');

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
