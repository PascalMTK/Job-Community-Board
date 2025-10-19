
CREATE DATABASE IF NOT EXISTS community_job_board;
USE community_job_board;

DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS saved_jobs;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS contact_messages;

CREATE TABLE users (
    id INT AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255) DEFAULT 'default-avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(50) DEFAULT 'fas fa-briefcase',
    job_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE TABLE jobs (
    id INT AUTO_INCREMENT,
    employer_id INT NOT NULL,
    category_id INT,
    title VARCHAR(200) NOT NULL,
    company_name VARCHAR(150) NOT NULL,
    company_logo VARCHAR(255) DEFAULT 'default-company.png',
    description TEXT NOT NULL,
    requirements TEXT,
    responsibilities TEXT,
    location VARCHAR(150) NOT NULL,
    job_type VARCHAR(20) NOT NULL,
    shift_type VARCHAR(50) DEFAULT 'day shift',
    salary_min DECIMAL(10, 2),
    salary_max DECIMAL(10, 2),
    salary_currency VARCHAR(10) DEFAULT 'N$',
    experience_required VARCHAR(50),
    education_required VARCHAR(100),
    skills_required TEXT,
    status VARCHAR(20) DEFAULT 'active',
    views INT DEFAULT 0,
    application_deadline DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT,
    job_id INT NOT NULL,
    student_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    cv_file VARCHAR(255) NOT NULL,
    cover_letter TEXT,
    years_experience VARCHAR(20),
    status VARCHAR(20) DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    notes TEXT,
    PRIMARY KEY (id),
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (job_id, student_id)
);

CREATE TABLE saved_jobs (
    id INT AUTO_INCREMENT,
    student_id INT NOT NULL,
    job_id INT NOT NULL,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_save (student_id, job_id)
);

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);


INSERT INTO categories (name, icon, job_count) VALUES
('Development', 'fas fa-code', 220);

INSERT INTO categories (name, icon, job_count) VALUES
('Designer', 'fas fa-pen', 500);

INSERT INTO categories (name, icon, job_count) VALUES
('Teacher', 'fas fa-chalkboard-user', 150);

INSERT INTO categories (name, icon, job_count) VALUES
('Marketing', 'fas fa-bullhorn', 120);

INSERT INTO categories (name, icon, job_count) VALUES
('Service', 'fas fa-headset', 310);

INSERT INTO categories (name, icon, job_count) VALUES
('Engineer', 'fas fa-wrench', 400);

INSERT INTO categories (name, icon, job_count) VALUES
('Finance', 'fas fa-hand-holding-dollar', 100);

INSERT INTO categories (name, icon, job_count) VALUES
('Labour', 'fas fa-person-digging', 400);


INSERT INTO users (name, email, password, role, phone) VALUES
('John Employer', 'employer@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employer', '+264811234567');

INSERT INTO users (name, email, password, role, phone) VALUES
('Jane Student', 'student@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '+264817654321');

INSERT INTO users (name, email, password, role, phone) VALUES
('NamTech Solutions', 'namtech@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employer', '+264612345678');

INSERT INTO users (name, email, password, role, phone) VALUES
('MediaWorks Namibia', 'mediaworks@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employer', '+264612345679');

INSERT INTO jobs (employer_id, category_id, title, company_name, company_logo, description, requirements, location, job_type, shift_type, salary_min, salary_max, salary_currency, experience_required, status, application_deadline) VALUES
(3, 1, 'Senior Web Developer', 'NamTech Solutions', 'images/icon-1.png', 
'We are looking for an experienced Senior Web Developer to join our dynamic team. You will be responsible for developing and maintaining web applications using modern technologies.',
'5+ years of experience in web development, Proficiency in PHP, JavaScript, HTML, CSS, Experience with MySQL databases, Strong problem-solving skills',
'Windhoek, Namibia', 'full-time', 'day shift', 15000, 25000, 'N$', '5+ years', 'active', '2025-11-15');

INSERT INTO jobs (employer_id, category_id, title, company_name, company_logo, description, requirements, location, job_type, shift_type, salary_min, salary_max, salary_currency, experience_required, status, application_deadline) VALUES
(4, 2, 'Graphic Designer', 'MediaWorks Namibia', 'images/icon-2.png',
'Creative graphic designer needed to create visual concepts and designs for various media platforms.',
'Degree in Graphic Design or related field, Proficiency in Adobe Creative Suite, Strong portfolio, Attention to detail',
'Swakopmund, Namibia', 'part-time', 'flexible hours', 8000, 12000, 'N$', '2-3 years', 'active', '2025-11-20');

INSERT INTO jobs (employer_id, category_id, title, company_name, company_logo, description, requirements, location, job_type, shift_type, salary_min, salary_max, salary_currency, experience_required, status, application_deadline) VALUES
(3, 1, 'Intern Web Developer', 'Innovate Software Hub', 'images/icon-3.png',
'Great opportunity for fresh graduates to gain hands-on experience in web development.',
'Recent graduate or final year student, Basic knowledge of HTML CSS JavaScript, Willingness to learn, Good communication skills',
'Windhoek, Namibia', 'internship', 'day shift', 3000, 5000, 'N$', '0-1 years', 'active', '2025-12-01');

INSERT INTO jobs (employer_id, category_id, title, company_name, company_logo, description, requirements, location, job_type, shift_type, salary_min, salary_max, salary_currency, experience_required, status, application_deadline) VALUES
(3, 1, 'Junior Front-End Developer', 'Namibia IT Hub', 'images/icon-4.png',
'Join our team as a Junior Front-End Developer and work on exciting projects.',
'1-2 years experience with JavaScript frameworks, Knowledge of React or Vue.js, Understanding of responsive design, Team player',
'Walvis Bay, Namibia', 'contract', 'fixed shift', 6000, 10000, 'N$', '1-2 years', 'active', '2025-11-30');

INSERT INTO jobs (employer_id, category_id, title, company_name, company_logo, description, requirements, location, job_type, shift_type, salary_min, salary_max, salary_currency, experience_required, status, application_deadline) VALUES
(4, 7, 'Administrative Assistant', 'Omaheke Finance Group', 'images/icon-5.png',
'We need an organized and detail-oriented administrative assistant.',
'Strong organizational skills, Proficiency in MS Office, Good communication skills, Previous office experience preferred',
'Gobabis, Namibia', 'temporary', 'flexible hours', 5000, 8000, 'N$', '1-3 years', 'active', '2025-11-25');

INSERT INTO jobs (employer_id, category_id, title, company_name, company_logo, description, requirements, location, job_type, shift_type, salary_min, salary_max, salary_currency, experience_required, status, application_deadline) VALUES
(3, 1, 'IT Support Technician', 'Desert Cloud Technologies', 'images/icon-6.png',
'Provide technical support to clients and maintain IT infrastructure.',
'IT certification or degree, Experience with Windows and Linux, Strong troubleshooting skills, Customer service oriented',
'Oshakati, Namibia', 'full-time', 'day shift', 10000, 18000, 'N$', '2-4 years', 'active', '2025-12-10');

INSERT INTO applications (job_id, student_id, full_name, email, phone, cv_file, cover_letter, years_experience, status) VALUES
(1, 2, 'Jane Student', 'student@test.com', '+264817654321', 'uploads/cv_jane_student.pdf', 
'I am very interested in this position and believe my skills align well with your requirements.', 
'1-3 years', 'pending');

INSERT INTO applications (job_id, student_id, full_name, email, phone, cv_file, cover_letter, years_experience, status) VALUES
(3, 2, 'Jane Student', 'student@test.com', '+264817654321', 'uploads/cv_jane_student.pdf', 
'As a recent graduate, I am eager to learn and contribute to your team.', 
'0-1 years', 'accepted');
