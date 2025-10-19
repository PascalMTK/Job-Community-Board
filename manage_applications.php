<?php
session_start();
require_once('includes/connection.php');
require_once('includes/functions.php');

if (!is_logged_in() || !is_employer()) {
    redirect('login.php');
}

$user_id = get_user_id();
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

//verify that the cuureent job belongs to this employer
if ($job_id > 0) {
    $verify = $conn->prepare("SELECT id FROM jobs WHERE id = ? AND employer_id = ?");
    $verify->bind_param("ii", $job_id, $user_id);
    $verify->execute();
    if ($verify->get_result()->num_rows == 0) {
        redirect('employer_home.php');
    }
    $verify->close();
}

//handle application status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $app_id = intval($_POST['application_id']);
    $new_status = sanitize_input($_POST['status']);
    
    //update application status
    $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $app_id);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['success'] = 'Application status updated successfully!';
    header("Location: " . $_SERVER['PHP_SELF'] . "?job_id=" . $job_id);
    exit();
}

//get applications
if ($job_id > 0) {
    //applications for specific job
    $query = "SELECT a.*, j.title as job_title, j.company_name
              FROM applications a
              JOIN jobs j ON a.job_id = j.id
              WHERE a.job_id = ? AND j.employer_id = ?
              ORDER BY a.applied_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $job_id, $user_id);
} else {
    //all applications for employer's jobs
    $query = "SELECT a.*, j.title as job_title, j.company_name
              FROM applications a
              JOIN jobs j ON a.job_id = j.id
              WHERE j.employer_id = ?
              ORDER BY a.applied_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

//get jobs for filter
$jobs_query = "SELECT id, title FROM jobs WHERE employer_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($jobs_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

include('includes/header.php');
?>

<!-- Manage Applications Section -->
<section class="manage-applications-section">
    <div class="applications-container">
        <div class="page-header">
            <h1><i class="fas fa-users"></i> Manage Applications</h1>
            <a href="employer_home.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" class="filter-form">
                <select name="job_id" class="input" onchange="this.form.submit()">
                    <option value="">All Jobs</option>
                    <?php foreach ($jobs as $j): ?>
                        <option value="<?php echo $j['id']; ?>" <?php echo $job_id == $j['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($j['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <!-- Applications List -->
        <?php if (count($applications) > 0): ?>
            <div class="applications-grid">
                <?php foreach ($applications as $app): ?>
                    <div class="application-card <?php echo $app['status']; ?>">
                        <div class="application-header">
                            <div class="applicant-info">
                                <div class="avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div>
                                    <h3><?php echo htmlspecialchars($app['full_name']); ?></h3>
                                    <p class="job-applied"><?php echo htmlspecialchars($app['job_title']); ?></p>
                                </div>
                            </div>
                            <span class="status-badge status-<?php echo $app['status']; ?>">
                                <?php echo ucfirst($app['status']); ?>
                            </span>
                        </div>

                        <div class="application-details">
                            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($app['email']); ?></p>
                            <?php if ($app['phone']): ?>
                                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($app['phone']); ?></p>
                            <?php endif; ?>
                            <p><i class="fas fa-calendar"></i> Applied <?php echo time_ago($app['applied_at']); ?></p>
                        </div>

                        <?php if ($app['cover_letter']): ?>
                            <div class="cover-letter">
                                <h4><i class="fas fa-file-alt"></i> Cover Letter</h4>
                                <p><?php echo nl2br(htmlspecialchars($app['cover_letter'])); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="application-actions">
                            <?php if ($app['cv_file']): ?>
                                <a href="uploads/resumes/<?php echo htmlspecialchars($app['cv_file']); ?>" 
                                   class="btn btn-download" target="_blank" download>
                                    <i class="fas fa-download"></i> Download Resume
                                </a>
                            <?php endif; ?>

                            <?php if ($app['status'] == 'pending'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                    <input type="hidden" name="status" value="accepted">
                                    <button type="submit" name="update_status" class="btn btn-accept">
                                        <i class="fas fa-check"></i> Accept
                                    </button>
                                </form>

                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" name="update_status" class="btn btn-reject">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h2>No Applications Yet</h2>
                <p>You haven't received any applications <?php echo $job_id > 0 ? 'for this job' : 'yet'; ?>.</p>
            </div>
        <?php endif; ?>
    </div>
</section>


<style>
.manage-applications-section {
    padding: 80px 20px;
    background: #f5f7fa;
    min-height: 100vh;
}

.applications-container {
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.page-header h1 {
    color: #333;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 15px;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.filter-section {
    background: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.filter-form {
    display: flex;
    gap: 15px;
}

.input {
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
}

.applications-grid {
    display: grid;
    gap: 25px;
}

.application-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 5px solid #667eea;
}

.application-card.accepted {
    border-left-color: #10b981;
}

.application-card.rejected {
    border-left-color: #ef4444;
}

.application-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 25px rgba(0,0,0,0.12);
}

.application-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.applicant-info {
    display: flex;
    gap: 15px;
    align-items: center;
}

.avatar {
    font-size: 50px;
    color: #667eea;
}

.applicant-info h3 {
    margin: 0;
    color: #333;
    font-size: 22px;
}

.job-applied {
    color: #666;
    margin: 5px 0 0 0;
    font-size: 14px;
}

.status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-accepted {
    background: #d4edda;
    color: #155724;
}

.status-rejected {
    background: #f8d7da;
    color: #721c24;
}

.application-details {
    margin-bottom: 20px;
}

.application-details p {
    margin: 8px 0;
    color: #666;
    display: flex;
    align-items: center;
    gap: 10px;
}

.application-details i {
    color: #667eea;
    width: 20px;
}

.cover-letter {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin: 20px 0;
}

.cover-letter h4 {
    margin: 0 0 15px 0;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

.cover-letter p {
    color: #666;
    line-height: 1.8;
    margin: 0;
}

.application-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #f0f0f0;
}

.btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.btn-secondary {
    background: #f5f5f5;
    color: #666;
}

.btn-download {
    background: #667eea;
    color: white;
}

.btn-accept {
    background: #10b981;
    color: white;
}

.btn-reject {
    background: #ef4444;
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 15px rgba(0,0,0,0.2);
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 15px;
}

.empty-state i {
    font-size: 80px;
    color: #ddd;
    margin-bottom: 20px;
}

.empty-state h2 {
    color: #333;
    margin: 0 0 10px 0;
}

.empty-state p {
    color: #999;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .application-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .application-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php include('includes/footer.php'); ?>

