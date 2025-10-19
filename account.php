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


<style>
.account-section {
    padding: 80px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.account-container {
    max-width: 1200px;
    margin: 0 auto;
}

/* Profile Header */
.profile-header {
    background: white;
    border-radius: 15px;
    padding: 40px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    position: relative;
}

.profile-avatar {
    font-size: 100px;
    color: #667eea;
}

.profile-info h1 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 32px;
}

.user-role {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #667eea;
    font-weight: 600;
    margin: 5px 0;
    font-size: 18px;
}

.member-since {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #666;
    margin: 5px 0;
}

.logout-btn {
    position: absolute;
    top: 30px;
    right: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 25px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.logout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

/* Account Statistics */
.account-stats, .account-details, .quick-actions {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #333;
    margin-bottom: 25px;
    font-size: 24px;
}

.section-title i {
    color: #667eea;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.stat-box {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 25px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.3s ease;
}

.stat-box:hover {
    transform: translateY(-5px);
}

.stat-icon {
    font-size: 40px;
    color: #667eea;
    background: white;
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.stat-icon.success {
    color: #10b981;
}

.stat-icon.pending {
    color: #f59e0b;
}

.stat-icon.danger {
    color: #ef4444;
}

.stat-content h3 {
    margin: 0;
    font-size: 32px;
    color: #333;
}

.stat-content p {
    margin: 5px 0 0 0;
    color: #666;
    font-size: 14px;
}

/* Details Grid */
.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.detail-item {
    padding: 20px;
    background: #f8fafc;
    border-radius: 10px;
    border-left: 4px solid #667eea;
}

.detail-item label {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #667eea;
    font-weight: 600;
    margin-bottom: 10px;
}

.detail-item p {
    margin: 0;
    color: #333;
    font-size: 16px;
}

/* Actions Grid */
.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.action-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    font-weight: 600;
}

.action-btn i {
    font-size: 32px;
}

.action-btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
        padding: 30px 20px;
    }
    
    .logout-btn {
        position: static;
        margin-top: 20px;
    }
    
    .stats-grid, .details-grid, .actions-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include('includes/footer.php'); ?>


