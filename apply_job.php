<?php
session_start();
require_once('includes/connection.php');
require_once('includes/functions.php');

if (!is_logged_in() || !is_student()) {
    redirect('login.php');
}

$user_id = get_user_id();

$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($job_id <= 0) {
    redirect('all_jobs.php');
}

//Get job details from user
$stmt = $conn->prepare("SELECT j.*, u.name as employer_name, u.email as employer_email 
                        FROM jobs j 
                        JOIN users u ON j.employer_id = u.id 
                        WHERE j.id = ? AND j.status = 'active'");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    redirect('all_jobs.php');
}

$job = $result->fetch_assoc();
$stmt->close();

// Check if already applied
$check = $conn->prepare("SELECT id FROM applications WHERE job_id = ? AND student_id = ?");
$check->bind_param("ii", $job_id, $user_id);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    $_SESSION['error'] = 'You have already applied for this job.';
    redirect('view_job.php?id=' . $job_id);
}
$check->close();

// Get student details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply'])) {
    $cover_letter = sanitize_input($_POST['cover_letter']);
    $resume_file = '';
    
    // Handle resume upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['resume']['type'], $allowed_types)) {
            $error = 'Only PDF and DOC/DOCX files are allowed for resume.';
        } elseif ($_FILES['resume']['size'] > $max_size) {
            $error = 'Resume file size must be less than 5MB.';
        } else {
            $upload_dir = 'uploads/resumes/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
            $resume_file = 'resume_' . $user_id . '_' . $job_id . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $resume_file;
            
            if (!move_uploaded_file($_FILES['resume']['tmp_name'], $upload_path)) {
                $error = 'Failed to upload resume. Please try again.';
            }
        }
    } else {
        $error = 'Please upload your resume.';
    }
    
    if (empty($error)) {
        // Insert application with all required fields from database schema
        $stmt = $conn->prepare("INSERT INTO applications (job_id, student_id, full_name, email, phone, cv_file, cover_letter, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $phone = $student['phone'] ? $student['phone'] : '';
        $stmt->bind_param("iisssss", $job_id, $user_id, $student['name'], $student['email'], $phone, $resume_file, $cover_letter);
        
        if ($stmt->execute()) {
            $success = 'Application submitted successfully! The employer has been notified.';
            
            // Send email notification to employer (optional - requires mail configuration)
            $subject = "New Job Application for: " . $job['title'];
            $message = "Hello " . $job['employer_name'] . ",\n\n";
            $message .= "You have received a new application for your job posting: " . $job['title'] . "\n\n";
            $message .= "Applicant: " . $student['name'] . "\n";
            $message .= "Email: " . $student['email'] . "\n\n";
            $message .= "Please log in to your dashboard to review the application.\n\n";
            $message .= "Best regards,\nJobHunt Team";
            
            // Uncomment to send email (requires mail server configuration)
            // mail($job['employer_email'], $subject, $message, "From: noreply@jobhunt.com");
            
            $_SESSION['success'] = $success;
            redirect('dashboard.php');
        } else {
            $error = 'Failed to submit application. Please try again.';
        }
        $stmt->close();
    }
}

include('includes/header.php');
?>

<!-- Application Form Section -->
<section class="application-form-section">
    <div class="application-container">
        <div class="back-link">
            <a href="view_job.php?id=<?php echo $job_id; ?>">
                <i class="fas fa-arrow-left"></i> Back to Job Details
            </a>
        </div>
        
        <div class="application-header">
            <h1>Apply for Position</h1>
            <div class="job-info">
                <h2><?php echo htmlspecialchars($job['title']); ?></h2>
                <p><i class="fas fa-building"></i> <?php echo htmlspecialchars($job['company_name']); ?></p>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="application-form">
            <div class="form-section">
                <h3>Your Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="input" value="<?php echo htmlspecialchars($student['name']); ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="input" value="<?php echo htmlspecialchars($student['email']); ?>" disabled>
                    </div>
                    
                    <?php if ($student['phone']): ?>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" class="input" value="<?php echo htmlspecialchars($student['phone']); ?>" disabled>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-section">
                <h3>Application Details</h3>
                
                <div class="form-group">
                    <label>Upload Resume/CV <span class="required">*</span></label>
                    <p class="help-text">Accepted formats: PDF, DOC, DOCX (Max 5MB)</p>
                    <input type="file" name="resume" class="input-file" accept=".pdf,.doc,.docx" required>
                    <div class="file-preview" id="filePreview"></div>
                </div>
                
                <div class="form-group">
                    <label>Cover Letter <span class="required">*</span></label>
                    <p class="help-text">Tell the employer why you're a great fit for this position</p>
                    <textarea name="cover_letter" class="input" rows="8" placeholder="Dear Hiring Manager,&#10;&#10;I am writing to express my interest in the <?php echo htmlspecialchars($job['title']); ?> position at <?php echo htmlspecialchars($job['company_name']); ?>...&#10;&#10;I believe my skills and experience make me an excellent candidate because..." required></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="view_job.php?id=<?php echo $job_id; ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" name="apply" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Submit Application
                </button>
            </div>
        </form>
    </div>
</section>

<style>
.application-form-section {
    padding: 80px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.application-container {
    max-width: 900px;
    margin: 0 auto;
}

.back-link {
    margin-bottom: 20px;
}

.back-link a {
    color: white;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: transform 0.3s ease;
}

.back-link a:hover {
    transform: translateX(-5px);
}

.application-header {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.application-header h1 {
    color: #333;
    margin: 0 0 20px 0;
    font-size: 32px;
}

.job-info h2 {
    color: #667eea;
    margin: 0 0 10px 0;
    font-size: 24px;
}

.job-info p {
    color: #666;
    margin: 5px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.job-info i {
    color: #667eea;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-error {
    background: #fee;
    color: #c33;
    border: 1px solid #fcc;
}

.alert-success {
    background: #efe;
    color: #3c3;
    border: 1px solid #cfc;
}

.application-form {
    background: white;
    border-radius: 15px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.form-section {
    margin-bottom: 40px;
    padding-bottom: 40px;
    border-bottom: 2px solid #f0f0f0;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h3 {
    color: #333;
    margin: 0 0 25px 0;
    font-size: 22px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-section h3::before {
    content: '';
    width: 4px;
    height: 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 2px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    color: #333;
    font-weight: 600;
    margin-bottom: 8px;
}

.required {
    color: #e74c3c;
}

.help-text {
    font-size: 13px;
    color: #999;
    margin-top: -5px;
    margin-bottom: 8px;
}

.input, .input-file {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    transition: border-color 0.3s ease;
}

.input:focus {
    outline: none;
    border-color: #667eea;
}

.input:disabled {
    background: #f5f5f5;
    color: #666;
}

textarea.input {
    resize: vertical;
    min-height: 150px;
    font-family: inherit;
}

.input-file {
    padding: 10px;
    cursor: pointer;
}

.file-preview {
    margin-top: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
    display: none;
}

.file-preview.active {
    display: block;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
}

.btn {
    padding: 15px 40px;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 16px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #f5f5f5;
    color: #666;
}

.btn-secondary:hover {
    background: #e0e0e0;
}

@media (max-width: 768px) {
    .application-form {
        padding: 20px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// File preview
document.querySelector('.input-file').addEventListener('change', function(e) {
    const preview = document.getElementById('filePreview');
    if (this.files && this.files[0]) {
        const file = this.files[0];
        preview.innerHTML = `
            <i class="fas fa-file-pdf"></i>
            <strong>${file.name}</strong>
            <span>(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
        `;
        preview.classList.add('active');
    } else {
        preview.classList.remove('active');
    }
});
</script>

<?php include('includes/footer.php'); ?>
