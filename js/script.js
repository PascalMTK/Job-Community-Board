// --- HELPER FUNCTION: Translatable Alert Messages (for demonstration) ---
const alertMessages = {
    'en': {
        'login_needed': 'Please login to save jobs',
        'login_apply': 'Please login to apply for jobs',
        'logged_out': 'You have been logged out',
        'already_applied': (jobTitle, company) => `You have already applied for ${jobTitle} at ${company}`,
        'apply_success': (jobTitle, company) => `Successfully applied for ${jobTitle} at ${company}`,
        'simulated_redirect': 'Redirecting to the full jobs listing page (simulated scroll).',
    },
    'af': {
        'login_needed': 'Meld asseblief aan om werke te stoor',
        'login_apply': 'Meld asseblief aan om aansoek te doen vir werke',
        'logged_out': 'Jy is uitgemeld',
        'already_applied': (jobTitle, company) => `Jy het reeds aansoek gedoen vir ${jobTitle} by ${company}`,
        'apply_success': (jobTitle, company) => `Suksesvol aansoek gedoen vir ${jobTitle} by ${company}`,
        'simulated_redirect': 'Herlei na die volledige werklys-bladsy (gesimuleerde blaai).',
    }
};

function getAlertMessage(key, lang, ...args) {
    const message = alertMessages[lang] && alertMessages[lang][key] ? alertMessages[lang][key] : alertMessages['en'][key];
    return typeof message === 'function' ? message(...args) : message;
}


// --- FORM VALIDATION FUNCTIONS (Kept outside for modularity) ---

/**
 * Toggles the company name field based on the user type selection.
 * NOTE: This function requires an HTML select element with id="userType"
 * and a wrapper element with id="companyField" containing the companyName input.
 */
function toggleCompanyField() {
    const userTypeSelect = document.getElementById('userType');
    const companyField = document.getElementById('companyField');
    const companyNameInput = document.getElementById('companyName');

    if (userTypeSelect && companyField && companyNameInput) {
        if (userTypeSelect.value === 'employer') {
            companyField.classList.add('visible');
            companyNameInput.setAttribute('required', 'required');
        } else {
            companyField.classList.remove('visible');
            companyNameInput.removeAttribute('required');
        }
    }
}

/**
 * Shows an error message next to an input element.
 */
function showError(inputElement, message) {
    // Find the closest parent element with class 'form-group'
    const formGroup = inputElement.closest('.form-group');
    if (formGroup) {
        formGroup.classList.add('invalid');
        const errorElement = formGroup.querySelector('.error');
        if (errorElement) {
            errorElement.textContent = message;
        }
    }
}

/**
 * Handles the registration form submission and validation.
 * NOTE: This relies on the form using IDs: 'name', 'email', 'password', 'confirmPassword', 'userType', 'companyName'.
 */
function validateSignup(event) {
    event.preventDefault();

    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach(group => group.classList.remove('invalid'));

    let isValid = true;

    const name = document.getElementById('name');
    if (name && name.value.trim().length < 2) {
        showError(name, 'Please enter your full name');
        isValid = false;
    }

    const email = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailRegex.test(email.value)) {
        showError(email, 'Please enter a valid email address');
        isValid = false;
    }

    const password = document.getElementById('password');
    if (password && password.value.length < 6) {
        showError(password, 'Password must be at least 6 characters');
        isValid = false;
    }

    const confirmPassword = document.getElementById('confirmPassword');
    if (confirmPassword && password && confirmPassword.value !== password.value) {
        showError(confirmPassword, 'Passwords do not match');
        isValid = false;
    }

    const userType = document.getElementById('userType');
    if (userType && !userType.value) {
        showError(userType, 'Please select a user type');
        isValid = false;
    }

    if (userType && userType.value === 'employer') {
        const companyName = document.getElementById('companyName');
        if (companyName && companyName.value.trim().length < 2) {
            showError(companyName, 'Please enter your company name');
            isValid = false;
        }
    }

    if (isValid) {
        // Successful submission simulation
        alert('Form submitted successfully! (Simulated registration)');
        // In a real application: event.target.submit();
    }
}


// --- MAIN APPLICATION LOGIC ---

document.addEventListener('DOMContentLoaded', () => {
    // --- DOM Elements (ADDED missing elements for context) ---
    const menuBtn = document.querySelector('#menu-btn');
    const navbar = document.querySelector('.header .flex .navbar');
    const settingsBtn = document.querySelector('.settings-btn');
    const settingsPanel = document.querySelector('.settings-panel');
    // const closeSettingsBtn = document.getElementById('closeSettingsBtn'); // Removed: not in the provided HTML
    const darkModeToggle = document.getElementById('dark-mode');
    const body = document.body;
    
    // Elements from the header/dashboard that were missing in the original selection:
    const loginBtn = document.querySelector('a[href="login.html"]'); // Assuming login link is the 'account' link
    const registerBtn = document.querySelector('a[href="register.html"]'); // Assuming register link is often near login
    const userInfo = document.querySelector('.header .flex .logo'); // Using logo as a proxy for a user info dropdown/link (Needs a dedicated HTML element for a clean solution)
    const userNameSpan = document.querySelector('.header .flex .logo span'); // Adjusting to the logo span for a simulated name display
    const jobsList = document.querySelector('.jobs-container .box-container'); // Selector for the list of jobs on the homepage
    const viewAllJobsBtn = document.querySelector('.jobs-container a.btn[href="jobs.html"]');
    
    // Dashboard Stats (IDs are assumed/simulated from the original code)
    const totalApplications = document.getElementById('totalApplications') || { textContent: '0' };
    const pendingApplications = document.getElementById('pendingApplications') || { textContent: '0' };
    const acceptedApplications = document.getElementById('acceptedApplications') || { textContent: '0' };
    const rejectedApplications = document.getElementById('rejectedApplications') || { textContent: '0' };
    const applicationsList = document.getElementById('applicationsList') || { innerHTML: '' };
    
    const languageSelect = document.getElementById('language');

    // Current user state and data
    let currentUser = null;
    let isLoggedIn = false;
    let applications = [];


    // --- 1. Navbar Toggle and Scroll Behavior ---
    if (menuBtn && navbar) {
        menuBtn.onclick = () => {
            navbar.classList.toggle('active');
        };

        window.onscroll = () => {
            navbar.classList.remove('active');
        };
    }

    // --- 2. Limit Input Number Length (Original functionality) ---
    document.querySelectorAll('input[type="number"]').forEach(inputNumber => {
        inputNumber.oninput = () => {
            if (inputNumber.value.length > inputNumber.maxLength) {
                inputNumber.value = inputNumber.value.slice(0, inputNumber.maxLength);
            }
        };
    });

    // --- 3. Settings Panel Toggle ---
    if (settingsBtn && settingsPanel) {
        settingsBtn.addEventListener('click', () => {
            settingsPanel.classList.toggle('active');
            settingsBtn.setAttribute('aria-expanded', settingsPanel.classList.contains('active'));
        });
        
        // Add a click handler to close when clicking outside the panel (optional)
        document.addEventListener('click', (e) => {
            if (!settingsPanel.contains(e.target) && !settingsBtn.contains(e.target) && settingsPanel.classList.contains('active')) {
                settingsPanel.classList.remove('active');
                settingsBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }


    // --- 4. Dark Mode Toggle ---
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        if (darkModeToggle) darkModeToggle.checked = true;
    }

    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', () => {
            body.classList.toggle('dark-mode');
            localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
        });
    }


    // --- 5. Language Switcher ---
    let currentLang = localStorage.getItem('language') || 'en';

    if (languageSelect) {
        languageSelect.value = currentLang;
        document.documentElement.lang = currentLang;
        translatePage(currentLang);

        languageSelect.addEventListener('change', () => {
            currentLang = languageSelect.value;
            localStorage.setItem('language', currentLang);
            document.documentElement.lang = currentLang;
            translatePage(currentLang);
        });
    }

    function translatePage(lang) {
        // Text content translation
        document.querySelectorAll('[data-en][data-af]').forEach(el => {
            const translation = el.getAttribute(`data-${lang}`);
            if (translation) {
                // Ensure text is only updated if the attribute exists and is not empty
                el.textContent = translation;
            }
        });

        // Input placeholder translation
        document.querySelectorAll('input[data-placeholder-en][data-placeholder-af]').forEach(input => {
            input.placeholder = input.getAttribute(`data-placeholder-${lang}`) || input.getAttribute('placeholder');
        });

        // Input value translation
        document.querySelectorAll('input[data-value-en][data-value-af]').forEach(input => {
            input.value = input.getAttribute(`data-value-${lang}`) || input.getAttribute('value');
        });

        // Option text translation
        document.querySelectorAll('option[data-en][data-af]').forEach(option => {
            option.textContent = option.getAttribute(`data-${lang}`) || option.textContent;
        });

        // Title translation
        const title = document.querySelector('title');
        if (title) {
            title.textContent = title.getAttribute(`data-${lang}`) || title.textContent;
        }
    }

    // --- 6. Login State and Modals ---

    const toggleLoginState = (loggedIn, username = 'JobSeeker') => {
        isLoggedIn = loggedIn;
        currentUser = loggedIn ? { name: username } : null;
        
        // Find the 'account' link
        const accountLink = document.querySelector('a[data-en="account"]'); 
        const postJobLink = document.querySelector('a.btn[data-en="post job"]');

        if (accountLink) {
            accountLink.href = loggedIn ? 'dashboard.html' : 'login.html';
            accountLink.querySelector('span') 
            // In the original HTML, the 'account' link text is changed to 'account' or 'rekening', 
            // but for dynamic display, we'll simulate a greeting next to the logo.
        }

        if (loggedIn) {
             // Simulate user name display near logo, for example
             if (userNameSpan) {
                userNameSpan.textContent = username; // Displaying the username in the logo span (simulation)
                userNameSpan.setAttribute('data-en', username);
                userNameSpan.setAttribute('data-af', username);
             }
             if (postJobLink) postJobLink.style.display = 'block'; // Show post job
        } else {
             // Reset logo text
             if (userNameSpan) {
                userNameSpan.setAttribute('data-en', 'JobHunt');
                userNameSpan.setAttribute('data-af', 'WerkJag');
                translatePage(currentLang); // Re-apply translation
             }
             if (postJobLink) postJobLink.style.display = 'none'; // Hide post job on logout
        }

        renderApplications();
        updateApplicationStats();
    };

    toggleLoginState(false); // Initial state

    // Simulate logout when clicking the user info/logo link (if logged in)
    if (userInfo) {
        userInfo.onclick = (e) => {
            if (isLoggedIn) {
                e.preventDefault();
                toggleLoginState(false);
                alert(getAlertMessage('logged_out', currentLang));
            }
        };
    }

    // Modal creation function
    const createModal = (title, content, onConfirm = null) => {
        let existingModal = document.querySelector('.modal');
        if (existingModal) existingModal.remove();

        const modal = document.createElement('div');
        modal.classList.add('modal', 'active');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>${title}</h3>
                    <button class="close-modal" title="Close" aria-label="Close Modal"><i class="fas fa-times"></i></button>
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

    // Simulate Login/Register links (using the dashboard/account links for interaction)
    const loginLink = document.querySelector('nav.navbar a[href="login.html"]'); 
    const registerLink = document.querySelector('nav.navbar a[href="register.html"]'); 

    if (loginLink) {
        loginLink.onclick = (e) => {
            e.preventDefault();
            const loginFormContent = `
                <form id="loginForm">
                    <p>email <span>*</span></p>
                    <input type="email" placeholder="Email" class="input" required>
                    <p>password <span>*</span></p>
                    <input type="password" placeholder="Password" class="input" required>
                </form>
            `;
            // Simulate successful login
            createModal('User Login', loginFormContent, () => toggleLoginState(true, 'John Doe'));
        };
    }

    if (registerLink) {
        registerLink.onclick = (e) => {
            e.preventDefault();
            const registerFormContent = `
                <form id="registerForm">
                    <div class="form-group">
                        <p>name <span>*</span></p>
                        <input type="text" id="name" placeholder="Full Name" class="input" required>
                        <small class="error"></small>
                    </div>
                    <div class="form-group">
                        <p>email <span>*</span></p>
                        <input type="email" id="email" placeholder="Email" class="input" required>
                        <small class="error"></small>
                    </div>
                    <div class="form-group">
                        <p>password <span>*</span></p>
                        <input type="password" id="password" placeholder="Password" class="input" required>
                        <small class="error"></small>
                    </div>
                    <div class="form-group">
                        <p>confirm password <span>*</span></p>
                        <input type="password" id="confirmPassword" placeholder="Confirm Password" class="input" required>
                        <small class="error"></small>
                    </div>
                    <div class="form-group">
                        <p>user type <span>*</span></p>
                        <select id="userType" class="input" onchange="toggleCompanyField()" required>
                             <option value="" disabled selected>Select user type</option>
                             <option value="jobseeker">Job Seeker</option>
                             <option value="employer">Employer</option>
                        </select>
                        <small class="error"></small>
                    </div>
                    <div class="form-group" id="companyField">
                        <p>company name <span>*</span></p>
                        <input type="text" id="companyName" placeholder="Company Name" class="input">
                        <small class="error"></small>
                    </div>
                </form>
            `;
            // Call validateSignup on Confirm button click
            createModal('New User Registration', registerFormContent, () => {
                const form = document.getElementById('registerForm');
                if (form) {
                    // Temporarily attach handler for validation before confirming
                    form.addEventListener('submit', validateSignup);
                    const submitEvent = new Event('submit');
                    form.dispatchEvent(submitEvent);
                    form.removeEventListener('submit', validateSignup);

                    // If validation passes (this part needs integration with validateSignup return value)
                    // For now, we simulate success on confirm click
                    toggleLoginState(true, 'New User');
                }
            });

            // Need to set up the onchange listener for toggleCompanyField immediately after the modal is created
            setTimeout(() => {
                const userTypeSelect = document.getElementById('userType');
                if (userTypeSelect) {
                    userTypeSelect.addEventListener('change', toggleCompanyField);
                    toggleCompanyField(); // Initial check
                }
            }, 0);
        };
    }
    
    // --- 7. Job Listing & Data (Simulated) ---
    const jobData = [
        { id: 1, title: 'Senior Web Developer', company: 'NamTech Solutions', date: '2 days ago', location: 'Windhoek, Namibia', salary: 'N$15,000 - N$25,000', contract: 'full-time', tags: ['Development', 'PHP', 'Laravel'], logo: 'images/icon-1.png' },
        { id: 2, title: 'Graphic Designer', company: 'MediaWorks Namibia', date: '4 days ago', location: 'Swakopmund, Namibia', salary: 'N$8,000 - N$12,000', contract: 'part-time', tags: ['Designer', 'Figma', 'Prototyping'], logo: 'images/icon-2.png' },
        { id: 3, title: 'Intern Web Developer', company: 'Innovate Software Hub', date: 'posted today', location: 'Windhoek, Namibia', salary: 'N$3,000 - N$5,000', contract: 'internship', tags: ['Development', 'Internship', 'HTML'], logo: 'images/icon-3.png' },
        { id: 4, title: 'Junior Front-End Developer', company: 'Namibia IT Hub', date: '1 week ago', location: 'Walvis Bay, Namibia', salary: 'N$6,000 - N$10,000', contract: 'contract', tags: ['Development', 'React', 'CSS'], logo: 'images/icon-4.png' },
        { id: 5, title: 'Administrative Assistant', company: 'Omaheke Finance Group', date: '3 days ago', location: 'Gobabis, Namibia', salary: 'N$5,000 - N$8,000', contract: 'temporary', tags: ['Service', 'Admin', 'Finance'], logo: 'images/icon-5.png' },
        { id: 6, title: 'IT Support Technician', company: 'Desert Cloud Technologies', date: '5 days ago', location: 'Oshakati, Namibia', salary: 'N$10,000 - N$18,000', contract: 'full-time', tags: ['Engineer', 'Support', 'IT'], logo: 'images/icon-6.png' },
    ];
    
    // Initialize saved jobs from local storage
    let savedJobs = new Set(JSON.parse(localStorage.getItem('savedJobs') || '[]'));

    const renderJobs = (jobs) => {
        if (!jobsList) return;
        jobsList.innerHTML = '';
        
        jobs.forEach(job => {
            const isSaved = savedJobs.has(job.id);
            const jobElement = document.createElement('div');
            jobElement.classList.add('box');
            jobElement.setAttribute('style', `--i: ${job.id};`); // Maintain the style from original HTML
            
            const tagsHtml = job.tags.map(tag => `<p><i class="fas fa-tag"></i><span>${tag}</span></p>`).join('');
            
            // Replicating the structure from your original HTML file
            jobElement.innerHTML = `
                <div class="company">
                    <img src="${job.logo}" alt="${job.company} logo">
                    <div>
                        <h3>${job.company}</h3>
                        <p>${job.date}</p>
                    </div>
                </div>
                <h3 class="job-title">${job.title}</h3>
                <p class="location"><i class="fas fa-map-marker-alt"></i> <span>${job.location}</span></p>
                <div class="tags">
                    <p><i class="fas fa-money-bill-wave"></i> <span>${job.salary}</span></p>
                    <p><i class="fas fa-briefcase"></i> <span>${job.contract}</span></p>
                    <p><i class="fas fa-clock"></i> <span>${job.tags[0]}</span></p>
                </div>
                <div class="flex-btn">
                    <a href="view_job.html" class="btn view-job-btn" data-job-id="${job.id}" data-en="view details" data-af="bekyk besonderhede">view details</a>
                    <button type="button" class="save-btn ${isSaved ? 'active' : ''}" data-job-id="${job.id}" aria-label="Save Job">
                        <i class="${isSaved ? 'fas' : 'far'} fa-heart"></i>
                    </button>
                </div>
            `;
            jobsList.appendChild(jobElement);
        });
        addJobEventListeners();
        translatePage(currentLang); // Re-apply translations after rendering
    };

    const addJobEventListeners = () => {
        document.querySelectorAll('.save-btn').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                if (!isLoggedIn) {
                    alert(getAlertMessage('login_needed', currentLang));
                    return;
                }
                const jobId = parseInt(btn.getAttribute('data-job-id'));
                const icon = btn.querySelector('i');
                if (btn.classList.contains('active')) {
                    btn.classList.remove('active');
                    icon.classList.replace('fas', 'far');
                    savedJobs.delete(jobId);
                } else {
                    btn.classList.add('active');
                    icon.classList.replace('far', 'fas');
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
                    
                    // Attach apply logic after modal is fully rendered
                    setTimeout(() => {
                        const applyBtn = document.querySelector('.modal .apply-btn');
                        if (applyBtn) {
                            applyBtn.onclick = (e) => {
                                e.preventDefault();
                                if (!isLoggedIn) {
                                    alert(getAlertMessage('login_apply', currentLang));
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
        if (!job) return;

        const existingApplication = applications.find(app => app.jobId === jobId);
        if (existingApplication) {
            alert(getAlertMessage('already_applied', currentLang, job.title, job.company));
            return;
        }
        
        // Randomly set status for demonstration
        const statuses = ['pending', 'accepted', 'rejected'];
        const randomStatus = statuses[Math.floor(Math.random() * statuses.length)];

        const newApplication = {
            id: applications.length + 1,
            jobId: jobId,
            jobTitle: job.title,
            company: job.company,
            appliedDate: new Date().toISOString().split('T')[0],
            status: randomStatus
        };
        applications.push(newApplication);
        renderApplications();
        updateApplicationStats();
        alert(getAlertMessage('apply_success', currentLang, job.title, job.company));
    }

    function renderApplications() {
        if (!applicationsList) return;
        
        applicationsList.innerHTML = '';
        if (!isLoggedIn || applications.length === 0) {
            // Placeholder/Empty State (Using simplified HTML structure)
            applicationsList.innerHTML = `
                <div class="box no-applications">
                    <h3>No Applications Yet</h3>
                    <p>You haven't applied for any jobs yet. Browse our job listings and apply to get started!</p>
                    <a href="jobs.html" class="btn">Browse Jobs</a>
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
        
        if(totalApplications) totalApplications.textContent = total;
        if(pendingApplications) pendingApplications.textContent = pending;
        if(acceptedApplications) acceptedApplications.textContent = accepted;
        if(rejectedApplications) rejectedApplications.textContent = rejected;
    }

    // --- 8. View All Jobs Button (Simulated Redirect) ---
    if (viewAllJobsBtn) {
        viewAllJobsBtn.onclick = (e) => {
            e.preventDefault();
            const targetElement = document.getElementById('jobs'); // Assuming there's a section with ID 'jobs'
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' });
                alert(getAlertMessage('simulated_redirect', currentLang));
            } else {
                window.location.href = 'jobs.html';
            }
        };
    }

    // --- 9. Smooth Scrolling for Anchor Links (Original functionality) ---
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                // Offset of 70 pixels for a fixed header
                window.scrollTo({
                    top: targetElement.offsetTop - 70, 
                    behavior: 'smooth'
                });
                if (navbar && navbar.classList.contains('active')) {
                    navbar.classList.remove('active');
                }
            }
        });
    });

    // Initial renders
    renderJobs(jobData);
    // Initial calls for applications are inside toggleLoginState(false);
});