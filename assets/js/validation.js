// Validation functions
function validateRequired(value, errorId, message) {
    if (!value || value.trim() === '') {
        showError(errorId, message);
        return false;
    }
    hideError(errorId);
    return true;
}

function validateEmail(email, errorId) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!email || email.trim() === '') {
        showError(errorId, 'Email is required');
        return false;
    }
    
    if (!emailRegex.test(email)) {
        showError(errorId, 'Please enter a valid email address');
        return false;
    }
    
    hideError(errorId);
    return true;
}

function validatePassword(password, errorId) {
    if (!password || password.length < 6) {
        showError(errorId, 'Password must be at least 6 characters long');
        return false;
    }
    
    hideError(errorId);
    return true;
}

function validatePasswordMatch(password, confirmPassword, errorId) {
    if (!confirmPassword) {
        showError(errorId, 'Please confirm your password');
        return false;
    }
    
    if (password !== confirmPassword) {
        showError(errorId, 'Passwords do not match');
        return false;
    }
    
    hideError(errorId);
    return true;
}

function validateUsername(username, errorId) {
    if (!username || username.trim() === '') {
        showError(errorId, 'Username is required');
        return false;
    }
    
    if (username.length < 3) {
        showError(errorId, 'Username must be at least 3 characters long');
        return false;
    }
    
    if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        showError(errorId, 'Username can only contain letters, numbers, and underscores');
        return false;
    }
    
    hideError(errorId);
    return true;
}

function validateFullName(fullname, errorId) {
    if (!fullname || fullname.trim() === '') {
        showError(errorId, 'Full name is required');
        return false;
    }
    
    if (fullname.trim().length < 2) {
        showError(errorId, 'Full name must be at least 2 characters long');
        return false;
    }
    
    hideError(errorId);
    return true;
}

function showError(errorId, message) {
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        const inputElement = errorElement.previousElementSibling;
        
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        
        if (inputElement) {
            inputElement.classList.add('error');
        }
    }
}

function hideError(errorId) {
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        const inputElement = errorElement.previousElementSibling;
        
        errorElement.style.display = 'none';
        
        if (inputElement) {
            inputElement.classList.remove('error');
        }
    }
}

function clearErrors() {
    const errorElements = document.querySelectorAll('.error-text');
    const inputElements = document.querySelectorAll('.form-control');
    
    errorElements.forEach(element => {
        element.style.display = 'none';
    });
    
    inputElements.forEach(element => {
        element.classList.remove('error');
    });
}

function validateLoginForm() {
    let isValid = true;
    
    const username = document.getElementById('login_username').value;
    const password = document.getElementById('login_password').value;
    
    if (!validateRequired(username, 'login_username_error', 'Username is required')) {
        isValid = false;
    }
    
    if (!validateRequired(password, 'login_password_error', 'Password is required')) {
        isValid = false;
    }
    
    return isValid;
}

function validateRegistrationForm() {
    let isValid = true;
    
    // Get form values
    const fullname = document.getElementById('fullname').value;
    const email = document.getElementById('email').value;
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const gender = document.querySelector('input[name="gender"]:checked');
    
    // Validate all fields
    if (!validateFullName(fullname, 'fullname_error')) {
        isValid = false;
    }
    
    if (!validateEmail(email, 'email_error')) {
        isValid = false;
    }
    
    if (!validateUsername(username, 'username_error')) {
        isValid = false;
    }
    
    if (!validatePassword(password, 'password_error')) {
        isValid = false;
    }
    
    if (!validatePasswordMatch(password, confirmPassword, 'confirm_password_error')) {
        isValid = false;
    }
    
    if (!gender) {
        showError('gender_error', 'Please select your gender');
        isValid = false;
    } else {
        hideError('gender_error');
    }
    
    return isValid;
}

// Setup real-time validation
function setupValidation() {
    // Email validation on blur
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            validateEmail(this.value, 'email_error');
        });
    }

    // Password validation on input
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            validatePassword(this.value, 'password_error');
            
            // Also validate confirm password if it has a value
            const confirmPassword = document.getElementById('confirm_password');
            if (confirmPassword && confirmPassword.value) {
                validatePasswordMatch(this.value, confirmPassword.value, 'confirm_password_error');
            }
        });
    }

    // Confirm password validation on input
    const confirmPasswordInput = document.getElementById('confirm_password');
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            validatePasswordMatch(password, this.value, 'confirm_password_error');
        });
    }

    // Username validation on blur
    const usernameInput = document.getElementById('username');
    if (usernameInput) {
        usernameInput.addEventListener('blur', function() {
            validateUsername(this.value, 'username_error');
        });
    }

    // Full name validation on blur
    const fullnameInput = document.getElementById('fullname');
    if (fullnameInput) {
        fullnameInput.addEventListener('blur', function() {
            validateFullName(this.value, 'fullname_error');
        });
    }

    // Add smooth focus effects
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        input.addEventListener('blur', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}