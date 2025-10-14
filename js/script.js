// --- Additional Functions for Forms ---
function toggleCompanyField() {
    const userType = document.getElementById('userType');
    const companyField = document.getElementById('companyField');
    
    if (userType.value === 'employer') {
        companyField.classList.add('visible');
        document.getElementById('companyName').setAttribute('required', 'required');
    } else {
        companyField.classList.remove('visible');
        document.getElementById('companyName').removeAttribute('required');
    }
}

function validateSignup(event) {
    event.preventDefault();
    
    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach(group => group.classList.remove('invalid'));
    
    let isValid = true;
    
    const name = document.getElementById('name');
    if (name.value.trim().length < 2) {
        showError(name, 'Please enter your full name');
        isValid = false;
    }
    
    const email = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value)) {
        showError(email, 'Please enter a valid email address');
        isValid = false;
    }
    
    const password = document.getElementById('password');
    if (password.value.length < 6) {
        showError(password, 'Password must be at least 6 characters');
        isValid = false;
    }
    
    const confirmPassword = document.getElementById('confirmPassword');
    if (confirmPassword.value !== password.value) {
        showError(confirmPassword, 'Passwords do not match');
        isValid = false;
    }
    
    const userType = document.getElementById('userType');
    if (!userType.value) {
        showError(userType, 'Please select a user type');
        isValid = false;
    }
    
    if (userType.value === 'employer') {
        const companyName = document.getElementById('companyName');
        if (companyName.value.trim().length < 2) {
            showError(companyName, 'Please enter your company name');
            isValid = false;
        }
    }
    
    if (isValid) {
        alert('Form submitted successfully!');
    }
}

function showError(inputElement, message) {
    const formGroup = inputElement.closest('.form-group');
    formGroup.classList.add('invalid');
    const errorElement = formGroup.querySelector('.error');
    errorElement.textContent = message;
}

document.addEventListener('DOMContentLoaded', () => {
    // --- DOM Elements ---
    const menuBtn = document.querySelector('#menu-btn');
    const navbar = document.querySelector('.header .flex .navbar');
    const settingsBtn = document.querySelector('.settings-btn');
    const settingsPanel = document.querySelector('.settings-panel');
    const closeSettingsBtn = document.getElementById('closeSettingsBtn');
    const darkModeToggle = document.getElementById('dark-mode');
    const body = document.body;
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    const userInfo = document.getElementById('userInfo');
    const userNameSpan = document.getElementById('userName');
    const jobsList = document.getElementById('jobsList');
    const viewAllJobsBtn = document.getElementById('viewAllJobs');
    const totalApplications = document.getElementById('totalApplications');
    const pendingApplications = document.getElementById('pendingApplications');
    const acceptedApplications = document.getElementById('acceptedApplications');
    const rejectedApplications = document.getElementById('rejectedApplications');
    const applicationsList = document.getElementById('applicationsList');
    const languageSelect = document.getElementById('language');

    // Current user state
    let currentUser = null;
    let isLoggedIn = false;
    let applications = [];

    // --- 1. Navbar Toggle and Scroll Behavior ---
    menuBtn.onclick = () => {
        navbar.classList.toggle('active');
    };

    window.onscroll = () => {
        navbar.classList.remove('active');
    };

    // --- 2. Limit Input Number Length ---
    document.querySelectorAll('input[type="number"]').forEach(inputNumber => {
        inputNumber.oninput = () => {
            if (inputNumber.value.length > inputNumber.maxLength) {
                inputNumber.value = inputNumber.value.slice(0, inputNumber.maxLength);
            }
        };
    });

    // --- 3. Settings Panel Toggle ---
    settingsBtn.addEventListener('click', () => {
        settingsPanel.classList.toggle('active');
    });

    closeSettingsBtn.addEventListener('click', () => {
        settingsPanel.classList.remove('active');
    });

    // --- 4. Dark Mode Toggle ---
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        darkModeToggle.checked = true;
    }
    darkModeToggle.addEventListener('change', () => {
        body.classList.toggle('dark-mode');
        localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
    });

    // --- 5. Language Switcher ---
    const savedLang = localStorage.getItem('language') || 'en';
    languageSelect.value = savedLang;
    document.documentElement.lang = savedLang;
    translatePage(savedLang);

    languageSelect.addEventListener('change', () => {
        const lang = languageSelect.value;
        localStorage.setItem('language', lang);
        document.documentElement.lang = lang;
        translatePage(lang);
    });

    function translatePage(lang) {
        document.querySelectorAll('[data-en][data-af]').forEach(el => {
            el.textContent = el.getAttribute(`data-${lang}`);
        });

        document.querySelectorAll('input[data-placeholder-en][data-placeholder-af]').forEach(input => {
            input.placeholder = input.getAttribute(`data-placeholder-${lang}`);
        });

        document.querySelectorAll('input[data-value-en][data-value-af]').forEach(input => {
            input.value = input.getAttribute(`data-value-${lang}`);
        });

        document.querySelectorAll('option[data-en][data-af]').forEach(option => {
            option.textContent = option.getAttribute(`data-${lang}`);
        });

        const title = document.querySelector('title');
        if (title) {
            title.textContent = title.getAttribute(`data-${lang}`);
        }
    }

    // --- 6. Login State and Modals ---
    const toggleLoginState = (loggedIn, username = 'JobSeeker') => {
        isLoggedIn = loggedIn;
        currentUser = loggedIn ? { name: username } : null;
        if (loggedIn) {
            loginBtn.classList.add('hidden');
            registerBtn.classList.add('hidden');
            userInfo.classList.add('visible');
            userNameSpan.textContent = username;
        } else {
            loginBtn.classList.remove('hidden');
            registerBtn.classList.remove('hidden');
            userInfo.classList.remove('visible');
        }
        renderApplications();
        updateApplicationStats();
    };

    toggleLoginState(false);

    userInfo.onclick = () => {
        if (isLoggedIn) {
            toggleLoginState(false);
            alert('You have been logged out');
        }
    };

    const createModal = (title, content, onConfirm = null) => {
        let existingModal = document.querySelector('.modal');
        if (existingModal) existingModal.remove();

        const modal = document.createElement('div');
        modal.classList.add('modal', 'active');
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>${title}</h3>
                    <button class="close-modal" title="Close"><i class="fas fa-times"></i></button>
                </div>
                ${content}
                <div class="flex-btn">
                    ${onConfirm ? '<button class="btn modal-confirm-btn">Confirm</button>' : ''}
                    <button class="btn btn-secondary close-modal">Close</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);

        modal.querySelectorAll('.close-modal').forEach(btn => {
            btn.onclick = () => modal.remove();
        });

        if (onConfirm) {
            modal.querySelector('.modal-confirm-btn').onclick = () => {
                onConfirm();
                modal.remove();
            };
        }

        modal.onclick = (e) => {
            if (e.target === modal) modal.remove();
        };
    };

    loginBtn.onclick = (e) => {
        e.preventDefault();
        const loginFormContent = `
            <form>
                <p>email <span>*</span></p>
                <input type="email" placeholder="Email" class="input" required>
                <p>password <span>*</span></p>
                <input type="password" placeholder="Password" class="input" required>
            </form>
        `;
        createModal('User Login', loginFormContent, () => toggleLoginState(true, 'John Doe'));
    };

    registerBtn.onclick = (e) => {
        e.preventDefault();
        const registerFormContent = `
            <form>
                <p>name <span>*</span></p>
                <input type="text" placeholder="Full Name" class="input" required>
                <p>email <span>*</span></p>
                <input type="email" placeholder="Email" class="input" required>
                <p>password <span>*</span></p>
                <input type="password" placeholder="Password" class="input" required>
                <p>confirm password <span>*</span></p>
                <input type="password" placeholder="Confirm Password" class="input" required>
                <p>user type <span>*</span></p>
                <div class="user-type">
                    <input type="radio" name="userType" id="employee" value="employee" checked>
                    <label for="employee">Employee</label>
                    <input type="radio" name="userType" id="student" value="student">
                    <label for="student">Student</label>
                </div>
            </form>
        `;
        createModal('New User Registration', registerFormContent, () => toggleLoginState(true, 'New User'));
    };

    // --- 7. Job Listing ---
    const jobData = [
        { id: 1, title: 'Senior Web Developer', company: 'Tech Innovators', date: '2 days ago', location: 'Remote', salary: '$80k - $120k', contract: 'Full-time', tags: ['PHP', 'Laravel', 'React'], logo: 'https://cdn.iconscout.com/icon/free/png-256/google-160-252187.png' },
        { id: 2, title: 'UX/UI Designer', company: 'Creative Solutions', date: '4 hours ago', location: 'New York, USA', salary: '$60k - $90k', contract: 'Permanent', tags: ['Figma', 'Sketch', 'Prototyping'], logo: 'https://cdn.iconscout.com/icon/free/png-256/netflix-2296092-1941400.png' },
        { id: 3, title: 'Marketing Manager', company: 'Global Brands', date: '1 week ago', location: 'London, UK', salary: '$70k - $110k', contract: 'Full-time', tags: ['SEO', 'Content', 'Analytics'], logo: 'https://cdn.iconscout.com/icon/free/png-256/amazon-1869032-1579471.png' },
        { id: 4, title: 'Data Scientist', company: 'Data Insights Co.', date: '3 days ago', location: 'San Francisco, USA', salary: '$100k - $150k', contract: 'Contract', tags: ['Python', 'Machine Learning', 'SQL'], logo: 'https://cdn.iconscout.com/icon/free/png-256/spotify-5756770-4828135.png' },
        { id: 5, title: 'Customer Support Rep', company: 'Service First', date: '1 day ago', location: 'Anywhere', salary: '$40k - $55k', contract: 'Part-time', tags: ['Communication', 'Help Desk'], logo: 'https://cdn.iconscout.com/icon/free/png-256/facebook-2045233-1725514.png' },
    ];
    
    let savedJobs = new Set(JSON.parse(localStorage.getItem('savedJobs') || '[]'));

    const renderJobs = (jobs) => {
        jobsList.innerHTML = '';
        jobs.forEach(job => {
            const isSaved = savedJobs.has(job.id);
            const jobElement = document.createElement('div');
            jobElement.classList.add('box');
            const tagsHtml = job.tags.map(tag => `<p><i class="fas fa-tag"></i><span>${tag}</span></p>`).join('');
            jobElement.innerHTML = `
                <div class="company">
                    <img src="${job.logo}" alt="${job.company} logo">
                    <div>
                        <h3>${job.company}</h3>
                        <p>${job.date}</p>
                    </div>
                </div>
                <h3 class="job-title">${job.title}</h3>
                <p class="location"><i class="fas fa-map-marker-alt"></i> ${job.location}</p>
                <div class="tags">
                    <p><i class="fas fa-money-bill-wave"></i> ${job.salary}</p>
                    <p><i class="fas fa-clock"></i> ${job.contract}</p>
                    ${tagsHtml}
                </div>
                <div class="flex-btn">
                    <a href="#" class="btn view-job-btn" data-job-id="${job.id}">View Details</a>
                    <button class="save-btn ${isSaved ? 'active' : ''}" data-job-id="${job.id}"><i class="${isSaved ? 'fas' : 'far'} fa-heart"></i></button>
                </div>
            `;
            jobsList.appendChild(jobElement);
        });
        addJobEventListeners();
    };

    const addJobEventListeners = () => {
        document.querySelectorAll('.save-btn').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                if (!isLoggedIn) {
                    alert('Please login to save jobs');
                    return;
                }
                const jobId = parseInt(btn.getAttribute('data-job-id'));
                if (btn.classList.contains('active')) {
                    btn.classList.remove('active');
                    btn.innerHTML = '<i class="far fa-heart"></i>';
                    savedJobs.delete(jobId);
                } else {
                    btn.classList.add('active');
                    btn.innerHTML = '<i class="fas fa-heart"></i>';
                    savedJobs.add(jobId);
                }
                localStorage.setItem('savedJobs', JSON.stringify(Array.from(savedJobs)));
            };
        });

        document.querySelectorAll('.view-job-btn').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                const jobId = parseInt(btn.getAttribute('data-job-id'));
                const job = jobData.find(j => j.id === jobId);
                if (job) {
                    const jobDetailContent = `
                        <p class="job-description">
                            We are looking for a highly skilled <strong>${job.title}</strong> to join our dynamic team at <strong>${job.company}</strong>.
                            This is a <strong>${job.contract}</strong> role located in <strong>${job.location}</strong> with a competitive salary of <strong>${job.salary}</strong>.
                            Key skills include: ${job.tags.join(', ')}.
                            Apply now to build the future with us!
                        </p>
                        <a href="#" class="btn apply-btn" data-job-id="${job.id}">Apply Now</a>
                    `;
                    createModal(`${job.title} - ${job.company}`, jobDetailContent);
                    setTimeout(() => {
                        const applyBtn = document.querySelector('.modal .apply-btn');
                        if (applyBtn) {
                            applyBtn.onclick = (e) => {
                                e.preventDefault();
                                if (!isLoggedIn) {
                                    alert('Please login to apply for jobs');
                                    document.querySelector('.modal').remove();
                                    return;
                                }
                                applyForJob(jobId);
                                document.querySelector('.modal').remove();
                            };
                        }
                    }, 0);
                }
            };
        });
    };

    function applyForJob(jobId) {
        const job = jobData.find(j => j.id === jobId);
        const existingApplication = applications.find(app => app.jobId === jobId);
        if (existingApplication) {
            alert(`You have already applied for ${job.title} at ${job.company}`);
            return;
        }
        const newApplication = {
            id: applications.length + 1,
            jobId: jobId,
            jobTitle: job.title,
            company: job.company,
            appliedDate: new Date().toISOString().split('T')[0],
            status: 'pending'
        };
        applications.push(newApplication);
        renderApplications();
        updateApplicationStats();
        alert(`Successfully applied for ${job.title} at ${job.company}`);
    }

    function renderApplications() {
        applicationsList.innerHTML = '';
        if (!isLoggedIn || applications.length === 0) {
            applicationsList.innerHTML = `
                <div class="box no-applications">
                    <h3>No Applications Yet</h3>
                    <p>You haven't applied for any jobs yet. Browse our job listings and apply to get started!</p>
                    <a href="#jobs" class="btn">Browse Jobs</a>
                </div>
            `;
            return;
        }
        applications.forEach(app => {
            const appElement = document.createElement('div');
            appElement.classList.add('box');
            appElement.innerHTML = `
                <h3>${app.jobTitle}</h3>
                <p><strong>Company:</strong> ${app.company}</p>
                <p><strong>Applied:</strong> ${new Date(app.appliedDate).toLocaleDateString()}</p>
                <p><strong>Status:</strong> <span class="status ${app.status}">${app.status.charAt(0).toUpperCase() + app.status.slice(1)}</span></p>
            `;
            applicationsList.appendChild(appElement);
        });
    }

    function updateApplicationStats() {
        const total = applications.length;
        const pending = applications.filter(app => app.status === 'pending').length;
        const accepted = applications.filter(app => app.status === 'accepted').length;
        const rejected = applications.filter(app => app.status === 'rejected').length;
        totalApplications.textContent = total;
        pendingApplications.textContent = pending;
        acceptedApplications.textContent = accepted;
        rejectedApplications.textContent = rejected;
    }

    // --- 8. View All Jobs Button ---
    viewAllJobsBtn.onclick = (e) => {
        e.preventDefault();
        document.getElementById('jobs').scrollIntoView({ behavior: 'smooth' });
        alert('Redirecting to the full jobs listing page (simulated scroll).');
    };

    // --- 9. Smooth Scrolling for Anchor Links ---
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 70,
                    behavior: 'smooth'
                });
                if (navbar.classList.contains('active')) {
                    navbar.classList.remove('active');
                }
            }
        });
    });

    // Initial renders
    renderJobs(jobData);
    renderApplications();
    updateApplicationStats();
});