// script.js - for index.php

document.addEventListener('DOMContentLoaded', function() {
    // Form submission handlers
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    // Password strength validation
    const passwordInput = document.getElementById('registerPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const strengthIndicator = document.getElementById('password-strength-indicator');
    const passwordLoading = document.getElementById('password-loading');
    
    // Handle login form submission
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const loadingIndicator = loginForm.querySelector('.loading-indicator');
            
            // Basic validation
            if (!email || !password) {
                showAlert('loginAlerts', 'danger', 'Please fill in all fields');
                return;
            }
            
            // Show loading indicator
            loadingIndicator.classList.remove('d-none');
            
            // Prepare form data
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);
            
            // Send AJAX request
            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loadingIndicator.classList.add('d-none');
                
                if (data.success) {
                    showAlert('loginAlerts', 'success', 'Login successful! Redirecting...');
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1000);
                } else {
                    showAlert('loginAlerts', 'danger', data.message || 'Login failed. Please check your credentials.');
                }
            })
            .catch(error => {
                loadingIndicator.classList.add('d-none');
                showAlert('loginAlerts', 'danger', 'An error occurred. Please try again later.');
                console.error('Error:', error);
            });
        });
    }
    
    // Handle register form submission
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const loadingIndicator = registerForm.querySelector('.loading-indicator');
            
            // Basic validation
            if (!email || !password || !confirmPassword) {
                showAlert('registerAlerts', 'danger', 'Please fill in all fields');
                return;
            }
            
            if (password !== confirmPassword) {
                showAlert('registerAlerts', 'danger', 'Passwords do not match');
                return;
            }
            
            // Validate password strength
            const strengthResult = checkPasswordStrength(password);
            if (strengthResult.strength === 'weak') {
                showAlert('registerAlerts', 'danger', 'Password is too weak. ' + strengthResult.message);
                return;
            }
            
            // Show loading indicator
            loadingIndicator.classList.remove('d-none');
            
            // Prepare form data
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);
            formData.append('confirm_password', confirmPassword);
            
            // Send AJAX request
            fetch('register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loadingIndicator.classList.add('d-none');
                
                if (data.success) {
                    showAlert('registerAlerts', 'success', 'Registration successful! You can now log in.');
                    // Clear form
                    registerForm.reset();
                    // Switch to login tab after successful registration
                    setTimeout(() => {
                        document.getElementById('login-tab').click();
                    }, 1500);
                } else {
                    showAlert('registerAlerts', 'danger', data.message || 'Registration failed. Please try again.');
                }
            })
            .catch(error => {
                loadingIndicator.classList.add('d-none');
                showAlert('registerAlerts', 'danger', 'An error occurred. Please try again later.');
                console.error('Error:', error);
            });
        });
    }
    
    // Real-time password strength check
    if (passwordInput) {
        let debounceTimer;
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            // Clear previous timer
            clearTimeout(debounceTimer);
            
            if (password.length === 0) {
                strengthIndicator.className = 'password-strength';
                strengthIndicator.style.width = '0';
                return;
            }
            
            // Show loading indicator while "processing"
            passwordLoading.classList.remove('d-none');
            
            // Debounce the strength check to avoid excessive processing
            debounceTimer = setTimeout(() => {
                const result = checkPasswordStrength(password);
                
                // Update strength indicator
                strengthIndicator.className = 'password-strength ' + result.strength;
                passwordLoading.classList.add('d-none');
            }, 300);
        });
    }
    
    // Real-time password match validation
    if (confirmPasswordInput && passwordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            if (passwordInput.value !== this.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Function to check password strength
    function checkPasswordStrength(password) {
        // Check length
        if (password.length < 8) {
            return {
                strength: 'weak',
                message: 'Password must be at least 8 characters long.'
            };
        }
        
        // Check for variety of characters
        const hasNumbers = /\d/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasUppercase = /[A-Z]/.test(password);
        const hasSpecialChars = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        
        const varietyScore = [hasNumbers, hasLowercase, hasUppercase, hasSpecialChars].filter(Boolean).length;
        
        if (varietyScore <= 1) {
            return {
                strength: 'weak',
                message: 'Password should include a mix of letters, numbers, and special characters.'
            };
        } else if (varietyScore === 2) {
            return {
                strength: 'medium',
                message: 'Add more variety of characters for a stronger password.'
            };
        } else {
            return {
                strength: 'strong',
                message: 'Strong password!'
            };
        }
    }
    
    // Function to show alerts
    function showAlert(containerId, type, message) {
        const alertContainer = document.getElementById(containerId);
        if (!alertContainer) return;
        
        // Clear previous alerts
        alertContainer.innerHTML = '';
        
        // Create new alert
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.role = 'alert';
        
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        alertContainer.appendChild(alert);
        
        // Auto-dismiss after 5 seconds for success messages
        if (type === 'success') {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    }
});