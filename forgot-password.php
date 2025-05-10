<?php
require_once 'config.php';

$emailErr = $passwordErr = $confirm_passwordErr = "";
$email = $token = "";
$message = "";
$messageType = "";
$showResetForm = false;

// Check if token is provided in URL (for reset form)
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = sanitize($_GET['token']);
    
    // Verify token exists and is not expired
    try {
        $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW()");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $reset = $stmt->fetch(PDO::FETCH_ASSOC);
            $email = $reset['email'];
            $showResetForm = true;
        } else {
            $message = "Invalid or expired password reset link. Please request a new one.";
            $messageType = "danger";
        }
    } catch(PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "danger";
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Request password reset (email form)
    if (isset($_POST['request_reset'])) {
        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
        } else {
            $email = sanitize($_POST["email"]);
            
            // Verify email exists
            try {
                $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    // Generate token
                    $token = bin2hex(random_bytes(32));
                    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Remove existing tokens for this email
                    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = :email");
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();
                    
                    // Insert new token
                    $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)");
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':token', $token);
                    $stmt->bindParam(':expires_at', $expires_at);
                    $stmt->execute();
                    
                    // In a real application, you would send an email with the reset link
                    // For demo purposes, we'll just display the link
                    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?token=" . $token;
                    
                    $message = "A password reset link has been sent to your email address. Please check your inbox.<br>Demo link: <a href='" . $resetLink . "'>Reset Password</a>";
                    $messageType = "success";
                } else {
                    $message = "If the email exists in our system, a password reset link will be sent.";
                    $messageType = "info";
                }
            } catch(PDOException $e) {
                $message = "Error: " . $e->getMessage();
                $messageType = "danger";
            }
        }
    }
    
    // Process password reset (reset form)
    if (isset($_POST['reset_password'])) {
        $token = sanitize($_POST['token']);
        
        // Validate password
        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
        } elseif (strlen($_POST["password"]) < 8) {
            $passwordErr = "Password must be at least 8 characters";
        }
        
        // Validate confirm password
        if (empty($_POST["confirm_password"])) {
            $confirm_passwordErr = "Please confirm your password";
        } elseif ($_POST["password"] != $_POST["confirm_password"]) {
            $confirm_passwordErr = "Passwords do not match";
        }
        
        // If no errors, update password
        if (empty($passwordErr) && empty($confirm_passwordErr)) {
            try {
                // Get email from token
                $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW()");
                $stmt->bindParam(':token', $token);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $reset = $stmt->fetch(PDO::FETCH_ASSOC);
                    $email = $reset['email'];
                    
                    // Hash password
                    $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    
                    // Update user's password
                    $stmt = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
                    $stmt->bindParam(':password', $password_hash);
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();
                    
                    // Delete the token
                    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
                    $stmt->bindParam(':token', $token);
                    $stmt->execute();
                    
                    $message = "Your password has been reset successfully. You can now <a href='login.php'>login</a> with your new password.";
                    $messageType = "success";
                    $showResetForm = false;
                } else {
                    $message = "Invalid or expired password reset link. Please request a new one.";
                    $messageType = "danger";
                    $showResetForm = false;
                }
            } catch(PDOException $e) {
                $message = "Error: " . $e->getMessage();
                $messageType = "danger";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Personal Expense Tracker</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #2ecc71;
            --info-color: #3498db;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .card {
            border-radius: 16px;
            border: none;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(67, 97, 238, 0.2);
        }
        
        .card-header {
            background: var(--primary-color);
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-bottom: none;
            padding: 1.5rem;
        }
        
        .card-header h3 {
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .card-body {
            padding: 2.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 30px;
            padding: 12px 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        }
        
        .btn-outline-secondary {
            border-radius: 30px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #495057;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .alert-success {
            background-color: #f0fff4;
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
        
        .alert-info {
            background-color: #ebf8ff;
            color: var(--info-color);
            border-left: 4px solid var(--info-color);
        }
        
        .alert-warning {
            background-color: #fffaf0;
            color: var(--warning-color);
            border-left: 4px solid var(--warning-color);
        }
        
        .alert-danger {
            background-color: #fff5f5;
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
        
        /* Custom Animation */
        .custom-pulse {
            animation: pulse 2s ease infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
            }
            50% {
                transform: scale(1.03);
                box-shadow: 0 10px 25px rgba(67, 97, 238, 0.6);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
            }
        }
        
        .input-group {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.2);
        }
        
        .logo-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
        }
        
        .password-strength {
            height: 5px;
            margin-top: 10px;
            border-radius: 3px;
            transition: all 0.3s ease;
        }
        
        .strength-text {
            font-size: 0.8rem;
            margin-top: 5px;
            text-align: right;
        }
        
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?> animate__animated animate__fadeIn">
                        <?php if ($messageType == 'success'): ?>
                            <i class="fas fa-check-circle me-2"></i>
                        <?php elseif ($messageType == 'info'): ?>
                            <i class="fas fa-info-circle me-2"></i>
                        <?php elseif ($messageType == 'warning'): ?>
                            <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php else: ?>
                            <i class="fas fa-exclamation-circle me-2"></i>
                        <?php endif; ?>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-lg animate__animated animate__fadeIn">
                    <div class="card-header bg-primary text-white text-center">
                        <div class="logo-container animate__animated animate__zoomIn">
                            <i class="fas fa-key logo-icon"></i>
                        </div>
                        <h3 class="mb-0"><?php echo $showResetForm ? 'Reset Your Password' : 'Forgot Password'; ?></h3>
                    </div>
                    <div class="card-body">
                        <?php if ($showResetForm): ?>
                            <!-- Password Reset Form -->
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate class="animate__animated animate__fadeInUp animate__delay-1s">
                                <input type="hidden" name="token" value="<?php echo $token; ?>">
                                
                                <div class="mb-4">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2 text-primary"></i>Email Address
                                    </label>
                                    <input type="email" class="form-control" id="email" value="<?php echo $email; ?>" disabled>
                                    <small class="text-muted">This is the email associated with your account.</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-2 text-primary"></i>New Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control <?php echo (!empty($passwordErr)) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Enter new password" required>
                                        <button type="button" class="password-toggle" id="togglePassword">
                                            <i class="far fa-eye"></i>
                                        </button>
                                        <div class="invalid-feedback"><?php echo $passwordErr; ?></div>
                                    </div>
                                    <div class="password-strength" id="passwordStrength"></div>
                                    <div class="strength-text" id="strengthText"></div>
                                    <small class="text-muted">Password must be at least 8 characters long.</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-lock me-2 text-primary"></i>Confirm Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control <?php echo (!empty($confirm_passwordErr)) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                                            <i class="far fa-eye"></i>
                                        </button>
                                        <div class="invalid-feedback"><?php echo $confirm_passwordErr; ?></div>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-3 mt-4">
                                    <button type="submit" name="reset_password" class="btn btn-primary btn-lg custom-pulse">
                                        <i class="fas fa-save me-2"></i>Reset Password
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <!-- Email Request Form -->
                            <p class="mb-4 text-muted animate__animated animate__fadeInUp animate__delay-1s">
                                Enter your email address below and we'll send you a link to reset your password.
                            </p>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate class="animate__animated animate__fadeInUp animate__delay-1s">
                                <div class="mb-4">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2 text-primary"></i>Email Address
                                    </label>
                                    <input type="email" class="form-control <?php echo (!empty($emailErr)) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo $email; ?>" placeholder="Enter your email" required>
                                    <div class="invalid-feedback"><?php echo $emailErr; ?></div>
                                </div>
                                
                                <div class="d-grid gap-3 mt-4">
                                    <button type="submit" name="request_reset" class="btn btn-primary btn-lg custom-pulse">
                                        <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center mt-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <a href="login.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility for new password
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');
            
            if (togglePassword && passwordField) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);
                    
                    // Toggle eye icon
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }
            
            // Toggle password visibility for confirm password
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPasswordField = document.getElementById('confirm_password');
            
            if (toggleConfirmPassword && confirmPasswordField) {
                toggleConfirmPassword.addEventListener('click', function() {
                    const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPasswordField.setAttribute('type', type);
                    
                    // Toggle eye icon
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }
            
            // Password strength checker
            const passwordStrength = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('strengthText');
            
            if (passwordField && passwordStrength && strengthText) {
                passwordField.addEventListener('keyup', function() {
                    const password = this.value;
                    let strength = 0;
                    
                    if (password.length >= 8) strength += 1;
                    if (password.match(/[a-z]+/)) strength += 1;
                    if (password.match(/[A-Z]+/)) strength += 1;
                    if (password.match(/[0-9]+/)) strength += 1;
                    if (password.match(/[!@#$%^&*(),.?":{}|<>]+/)) strength += 1;
                    
                    switch (strength) {
                        case 0:
                            passwordStrength.style.width = '0%';
                            passwordStrength.style.backgroundColor = '';
                            strengthText.textContent = '';
                            break;
                        case 1:
                            passwordStrength.style.width = '20%';
                            passwordStrength.style.backgroundColor = '#e74c3c';
                            strengthText.textContent = 'Very Weak';
                            strengthText.style.color = '#e74c3c';
                            break;
                        case 2:
                            passwordStrength.style.width = '40%';
                            passwordStrength.style.backgroundColor = '#f39c12';
                            strengthText.textContent = 'Weak';
                            strengthText.style.color = '#f39c12';
                            break;
                        case 3:
                            passwordStrength.style.width = '60%';
                            passwordStrength.style.backgroundColor = '#f1c40f';
                            strengthText.textContent = 'Medium';
                            strengthText.style.color = '#f1c40f';
                            break;
                        case 4:
                            passwordStrength.style.width = '80%';
                            passwordStrength.style.backgroundColor = '#3498db';
                            strengthText.textContent = 'Strong';
                            strengthText.style.color = '#3498db';
                            break;
                        case 5:
                            passwordStrength.style.width = '100%';
                            passwordStrength.style.backgroundColor = '#2ecc71';
                            strengthText.textContent = 'Very Strong';
                            strengthText.style.color = '#2ecc71';
                            break;
                    }
                });
            }
            
            // Check if passwords match
            if (passwordField && confirmPasswordField) {
                confirmPasswordField.addEventListener('keyup', function() {
                    if (passwordField.value !== this.value) {
                        this.classList.add('is-invalid');
                        this.nextElementSibling.nextElementSibling.innerHTML = "Passwords do not match";
                    } else {
                        this.classList.remove('is-invalid');
                        this.nextElementSibling.nextElementSibling.innerHTML = "";
                    }
                });
            }
            
            // Add animation to form fields on focus
            const formControls = document.querySelectorAll('.form-control');
            formControls.forEach(control => {
                control.addEventListener('focus', function() {
                    this.closest('.mb-4, .mb-3').classList.add('animate__animated', 'animate__pulse');
                });
                
                control.addEventListener('blur', function() {
                    this.closest('.mb-4, .mb-3').classList.remove('animate__animated', 'animate__pulse');
                });
            });
        });
    </script>
</body>
</html>