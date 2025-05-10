<?php
require_once 'config.php';

$emailErr = $passwordErr = "";
$email = "";
$loginError = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = sanitize($_POST["email"]);
    }
    
    // Validate password
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    }
    
    // If no validation errors, proceed with login
    if (empty($emailErr) && empty($passwordErr)) {
        $password = $_POST["password"];
        
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    
                    // Redirect to dashboard
                    redirect('dashboard.php');
                } else {
                    $loginError = "Invalid email or password";
                }
            } else {
                $loginError = "Invalid email or password";
            }
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Personal Expense Tracker</title>
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
        
        .btn-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
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
        
        .alert-danger {
            background-color: #fff5f5;
            color: #e53e3e;
            border-left: 4px solid #e53e3e;
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
        
        .forgot-password {
            text-align: right;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
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
                <?php if (!empty($loginError)): ?>
                    <div class="alert alert-danger animate__animated animate__shakeX">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $loginError; ?>
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-lg animate__animated animate__fadeIn">
                    <div class="card-header bg-primary text-white text-center">
                        <div class="logo-container animate__animated animate__zoomIn">
                            <i class="fas fa-lock logo-icon"></i>
                        </div>
                        <h3 class="mb-0">Login to Your Account</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate class="animate__animated animate__fadeInUp animate__delay-1s">
                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2 text-primary"></i>Email Address
                                </label>
                                <input type="email" class="form-control <?php echo (!empty($emailErr)) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo $email; ?>" placeholder="Enter your email" required>
                                <div class="invalid-feedback"><?php echo $emailErr; ?></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-key me-2 text-primary"></i>Password
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control <?php echo (!empty($passwordErr)) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Enter your password" required>
                                    <button type="button" class="password-toggle" id="togglePassword">
                                        <i class="far fa-eye"></i>
                                    </button>
                                    <div class="invalid-feedback"><?php echo $passwordErr; ?></div>
                                </div>
                            </div>
                            
                            <div class="forgot-password">
                                <a href="forgot-password.php" class="text-primary">Forgot Password?</a>
                            </div>
                            
                            <div class="d-grid gap-3 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg custom-pulse">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                                <a href="register.php" class="btn btn-link text-center">
                                    <i class="fas fa-user-plus me-1"></i> Don't have an account? Register
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Home
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
            // Toggle password visibility
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