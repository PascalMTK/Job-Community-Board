<?php
session_start();
require_once('includes/connection.php');
require_once('includes/functions.php');

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = get_user_id();

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

//Fetch statistics based on role
if (is_student()) {
    $stats_query = "
        SELECT 
            COUNT(*) as total_applications,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_applications,
            SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_applications,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_applications
        FROM applications 
        WHERE id = ?
    ";
    $stmt = $conn->prepare($stats_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    
    //Fetch saved jobs count
    $saved_query = "SELECT COUNT(*) as saved_count FROM saved_jobs WHERE id = ?";
    $stmt = $conn->prepare($saved_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $saved_result = $stmt->get_result()->fetch_assoc();
} else {
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
}

include('includes/header.php');
?>


<section class="account-section">
    <div class="account-container">
        
        //profile header
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                <p class="user-role">
                    <i class="fas <?php echo is_employer() ? 'fa-briefcase' : 'fa-user-graduate'; ?>"></i>
                    <?php echo ucfirst($user['role']); ?>
                </p>
                <p class="member-since">
                    <i class="fas fa-calendar"></i>
                    Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
                </p>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>

        //account statistics
        <div class="account-stats">
            <h2 class="section-title">
                <i class="fas fa-chart-line"></i>
                Account Summary
            </h2>
            
            <div class="stats-grid">
                <?php if (is_student()): ?>
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['total_applications'] ?? 0; ?></h3>
                            <p>Total Applications</p>
                        </div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['pending_applications'] ?? 0; ?></h3>
                            <p>Pending</p>
                        </div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['accepted_applications'] ?? 0; ?></h3>
                            <p>Accepted</p>
                        </div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-icon danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['rejected_applications'] ?? 0; ?></h3>
                            <p>Rejected</p>
                        </div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-bookmark"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $saved_result['saved_count'] ?? 0; ?></h3>
                            <p>Saved Jobs</p>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['total_jobs'] ?? 0; ?></h3>
                            <p>Total Jobs Posted</p>
                        </div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['active_jobs'] ?? 0; ?></h3>
                            <p>Active Jobs</p>
                        </div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['total_applications'] ?? 0; ?></h3>
                            <p>Total Applications</p>
                        </div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['pending_applications'] ?? 0; ?></h3>
                            <p>Pending Review</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        //account detatils
        <div class="account-details">
            <h2 class="section-title">
                <i class="fas fa-user"></i>
                Personal Information
            </h2>
            
            <div class="details-grid">
                <div class="detail-item">
                    <label><i class="fas fa-user"></i> Full Name</label>
                    <p><?php echo htmlspecialchars($user['name']); ?></p>
                </div>
                
                <div class="detail-item">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                
                <div class="detail-item">
                    <label><i class="fas fa-id-badge"></i> Account Type</label>
                    <p><?php echo ucfirst($user['role']); ?></p>
                </div>
                
                <div class="detail-item">
                    <label><i class="fas fa-calendar-check"></i> Account Created</label>
                    <p><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>
        </div>

        //quick actions
        <div class="quick-actions">
            <h2 class="section-title">
                <i class="fas fa-bolt"></i>
                Quick Actions
            </h2>
            
            <div class="actions-grid">
                <?php if (is_student()): ?>
                    <a href="all_jobs.php" class="action-btn">
                        <i class="fas fa-search"></i>
                        Browse Jobs
                    </a>
                    <a href="dashboard.php" class="action-btn">
                        <i class="fas fa-file-alt"></i>
                        My Applications
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Saved Jobs feature coming soon!'); return false;">
                        <i class="fas fa-bookmark"></i>
                        Saved Jobs
                    </a>
                    <a href="contact.php" class="action-btn">
                        <i class="fas fa-envelope"></i>
                        Contact Support
                    </a>
                <?php else: ?>
                    <a href="employer_home.php" class="action-btn">
                        <i class="fas fa-plus-circle"></i>
                        Post New Job
                    </a>
                    <a href="employer_home.php#my-jobs" class="action-btn">
                        <i class="fas fa-briefcase"></i>
                        My Jobs
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Manage Applications feature coming soon!'); return false;">
                        <i class="fas fa-users"></i>
                        Manage Applications
                    </a>
                    <a href="contact.php" class="action-btn">
                        <i class="fas fa-envelope"></i>
                        Contact Support
                    </a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>
