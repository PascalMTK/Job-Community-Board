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
