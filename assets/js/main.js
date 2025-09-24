// Main application JavaScript
let currentUser = null;

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    setupValidation();
    setupEventListeners();
    checkSession();
});

function setupEventListeners() {
    // Login form submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    // Registration form submission
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegistration);
    }
}

function showForm(formType) {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const toggleBtns = document.querySelectorAll('.toggle-btn');
    
    toggleBtns.forEach(btn => btn.classList.remove('active'));
    
    if (formType === 'login') {
        loginForm.classList.add('active');
        registerForm.classList.remove('active');
        toggleBtns[0].classList.add('active');
    } else {
        registerForm.classList.add('active');
        loginForm.classList.remove('active');
        toggleBtns[1].classList.add('active');
    }
    
    clearErrors();
    hideMessage();
}

function showMessage(message, type) {
    const messageContainer = document.getElementById('messageContainer');
    messageContainer.textContent = message;
    messageContainer.className = `message ${type}`;
    messageContainer.style.display = 'block';
}

function hideMessage() {
    const messageContainer = document.getElementById('messageContainer');
    messageContainer.style.display = 'none';
}

function showLoading(button) {
    button.disabled = true;
    button.classList.add('loading');
    button.textContent = '';
}

function hideLoading(button, originalText) {
    button.disabled = false;
    button.classList.remove('loading');
    button.textContent = originalText;
}

async function handleLogin(e) {
    e.preventDefault();
    
    if (!validateLoginForm()) {
        return;
    }
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    showLoading(submitBtn);
    
    const formData = new FormData(e.target);
    const loginData = {
        username: formData.get('username'),
        password: formData.get('password')
    };
    
    try {
        const response = await fetch('api/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'login',
                ...loginData
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const responseText = await response.text();
        let result;
        
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            console.error('Response text:', responseText);
            throw new Error('Invalid server response');
        }
        
        if (result.success) {
            currentUser = result.user;
            sessionStorage.setItem('user', JSON.stringify(result.user));
            showDashboard(result.user);
            showMessage(result.message, 'success');
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        console.error('Login error:', error);
        showMessage('An error occurred during login. Please try again. Check console for details.', 'error');
    } finally {
        hideLoading(submitBtn, originalText);
    }
}

async function handleRegistration(e) {
    e.preventDefault();
    
    if (!validateRegistrationForm()) {
        return;
    }
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    showLoading(submitBtn);
    
    const formData = new FormData(e.target);
    
    // Get hobbies array
    const hobbies = [];
    const hobbyInputs = document.querySelectorAll('input[name="hobbies[]"]:checked');
    hobbyInputs.forEach(input => hobbies.push(input.value));
    
    const registrationData = {
        fullname: formData.get('fullname'),
        email: formData.get('email'),
        username: formData.get('username'),
        password: formData.get('password'),
        confirm_password: formData.get('confirm_password'),
        gender: formData.get('gender'),
        hobbies: hobbies,
        country: formData.get('country') || ''
    };
    
    try {
        const response = await fetch('api/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'register',
                ...registrationData
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const responseText = await response.text();
        let result;
        
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            console.error('Response text:', responseText);
            throw new Error('Invalid server response');
        }
        
        if (result.success) {
            showMessage(result.message, 'success');
            // Clear form and switch to login
            e.target.reset();
            setTimeout(() => {
                showForm('login');
            }, 2000);
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        console.error('Registration error:', error);
        showMessage('An error occurred during registration. Please try again. Check console for details.', 'error');
    } finally {
        hideLoading(submitBtn, originalText);
    }
}

function showDashboard(user) {
    const dashboard = document.getElementById('dashboard');
    const formsContainer = document.getElementById('formsContainer');
    const welcomeMessage = document.getElementById('welcomeMessage');
    const userDetails = document.getElementById('userDetails');
    
    // Hide forms and show dashboard
    formsContainer.style.display = 'none';
    dashboard.style.display = 'block';
    
    // Update welcome message
    welcomeMessage.textContent = `Welcome, ${user.fullname}!`;
    
    // Build user details HTML
    let detailsHTML = `
        <div class="user-detail">
            <span><strong>Full Name:</strong></span>
            <span>${escapeHtml(user.fullname)}</span>
        </div>
        <div class="user-detail">
            <span><strong>Email:</strong></span>
            <span>${escapeHtml(user.email)}</span>
        </div>
        <div class="user-detail">
            <span><strong>Username:</strong></span>
            <span>${escapeHtml(user.username)}</span>
        </div>
        <div class="user-detail">
            <span><strong>Gender:</strong></span>
            <span>${escapeHtml(user.gender)}</span>
        </div>
    `;
    
    if (user.hobbies && user.hobbies.length > 0) {
        detailsHTML += `
            <div class="user-detail">
                <span><strong>Hobbies:</strong></span>
                <span>${escapeHtml(user.hobbies.join(', '))}</span>
            </div>
        `;
    }
    
    if (user.country) {
        detailsHTML += `
            <div class="user-detail">
                <span><strong>Country:</strong></span>
                <span>${escapeHtml(user.country)}</span>
            </div>
        `;
    }
    
    detailsHTML += `
        <div class="user-detail">
            <span><strong>Member Since:</strong></span>
            <span>${formatDate(user.created_at)}</span>
        </div>
    `;
    
    userDetails.innerHTML = detailsHTML;
}

function logout() {
    currentUser = null;
    sessionStorage.removeItem('user');
    
    // Hide dashboard and show forms
    const dashboard = document.getElementById('dashboard');
    const formsContainer = document.getElementById('formsContainer');
    
    dashboard.style.display = 'none';
    formsContainer.style.display = 'block';
    
    // Reset forms
    document.getElementById('loginForm').reset();
    document.getElementById('registerForm').reset();
    
    // Show login form by default
    showForm('login');
    
    // Clear any messages
    hideMessage();
    clearErrors();
    
    showMessage('You have been logged out successfully.', 'success');
    setTimeout(hideMessage, 2000);
}

function checkSession() {
    const storedUser = sessionStorage.getItem('user');
    if (storedUser) {
        try {
            currentUser = JSON.parse(storedUser);
            showDashboard(currentUser);
        } catch (error) {
            console.error('Error parsing stored user data:', error);
            sessionStorage.removeItem('user');
        }
    }
}

// Utility functions
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Export functions for global access
window.showForm = showForm;
window.logout = logout;