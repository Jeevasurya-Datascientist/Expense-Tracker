<?php
require_once 'config.php';

$nameErr = $emailErr = $passwordErr = "";
$name = $email = "";
$registrationSuccess = false;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = sanitize($_POST["name"]);
    }
    
    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = sanitize($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        } else {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $emailErr = "Email already exists";
            }
        }
    }
    
    // Validate password
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = $_POST["password"];
        // Check if password meets requirements
        if (strlen($password) < 8) {
            $passwordErr = "Password must be at least 8 characters";
        } elseif (!preg_match("/[A-Z]/", $password)) {
            $passwordErr = "Password must contain at least one uppercase letter";
        } elseif (!preg_match("/[a-z]/", $password)) {
            $passwordErr = "Password must contain at least one lowercase letter";
        } elseif (!preg_match("/[0-9]/", $password)) {
            $passwordErr = "Password must contain at least one number";
        } elseif (!preg_match("/[^a-zA-Z0-9]/", $password)) {
            $passwordErr = "Password must contain at least one special character";
        }
    }
    
    // If no errors, proceed with registration
    if (empty($nameErr) && empty($emailErr) && empty($passwordErr)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();
            
            $registrationSuccess = true;
            
            // Set session for the newly registered user
            $_SESSION['user_id'] = $conn->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            // Redirect to dashboard after 2 seconds
            header("refresh:2;url=dashboard.php");

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
    <title>Register - Personal Expense Tracker</title>
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
        
        .card-body {
            padding: 3rem;
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
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 30px;
            padding: 12px 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }
        
        .logo-container {
            width: 120px;
            height: 120px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.2);
        }
        
        .logo-icon {
            font-size: 3.5rem;
            color: var(--primary-color);
        }
        
        /* Customized animations */
        .custom-fadeIn {
            animation: fadeIn 1.2s ease;
        }
        
        .custom-fadeInUp {
            animation: fadeInUp 0.8s ease;
        }
        

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 10px 25px rgba(67, 97, 238, 0.6);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
            }
        }
        
        .feature-card {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
        }
        
        .feature-card:hover .feature-icon {
            animation: rubberBand 1s;
        }
        
        /* Password strength styles */
        .password-strength {
            margin-top: 10px;
        }
        
        .progress {
            height: 8px;
            border-radius: 4px;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.25);
            border-color: var(--primary-color);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #495057;
        }
        
        .invalid-feedback {
            font-size: 0.85rem;
            margin-top: 5px;
        }
        
        .card-header {
            background-color: var(--primary-color) !important;
            padding: 1.5rem 3rem;
        }
        
        @media (max-width: 768px) {
            .card-body {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <?php if ($registrationSuccess): ?>
                    <div class="alert alert-success animate__animated animate__fadeIn">
                        Registration successful! Redirecting to dashboard...
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-lg animate__animated animate__fadeIn animate__faster">
                    <div class="card-header text-white">
                        <div class="logo-container animate__animated animate__zoomIn">
                            <i class="fas fa-user-plus logo-icon"></i>
                        </div>
                        <h3 class="text-center display-6 fw-bold text-white mb-0 custom-fadeIn">Create Account</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate class="animate__animated animate__fadeInUp animate__delay-1s">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-user text-primary"></i></span>
                                    <input type="text" class="form-control <?php echo (!empty($nameErr)) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo $name; ?>" required>
                                    <div class="invalid-feedback"><?php echo $nameErr; ?></div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-envelope text-primary"></i></span>
                                    <input type="email" class="form-control <?php echo (!empty($emailErr)) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo $email; ?>" required>
                                    <div class="invalid-feedback"><?php echo $emailErr; ?></div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-lock text-primary"></i></span>
                                    <input type="password" class="form-control <?php echo (!empty($passwordErr)) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                                    <div class="invalid-feedback"><?php echo $passwordErr; ?></div>
                                </div>
                                <div class="password-strength mt-2" id="passwordStrength">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted">Password must be at least 8 characters with uppercase, lowercase, number, and special character</small>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary custom-pulse">
                                    <i class="fas fa-user-plus me-2"></i>Register
                                </button>
                                <a href="login.php" class="btn btn-outline-primary animate__animated animate__fadeInUp animate__delay-2s">
                                    <i class="fas fa-sign-in-alt me-2"></i>Already have an account? Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4 text-muted animate__animated animate__fadeIn animate__delay-3s">
                    <p>Track your expenses securely with Personal Expense Tracker</p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Script -->
    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const progressBar = document.querySelector('#passwordStrength .progress-bar');
            let strength = 0;
            
            // Check length
            if (password.length >= 8) strength += 20;
            
            // Check for uppercase
            if (password.match(/[A-Z]/)) strength += 20;
            
            // Check for lowercase
            if (password.match(/[a-z]/)) strength += 20;
            
            // Check for numbers
            if (password.match(/[0-9]/)) strength += 20;
            
            // Check for special characters
            if (password.match(/[^a-zA-Z0-9]/)) strength += 20;
            
            // Update progress bar
            progressBar.style.width = strength + '%';
            
            // Set progress bar color based on strength
            if (strength <= 40) {
                progressBar.className = 'progress-bar bg-danger';
            } else if (strength <= 80) {
                progressBar.className = 'progress-bar bg-warning';
            } else {
                progressBar.className = 'progress-bar bg-success';
            }
        });
        
        // Add animated entrance effects to form elements
        document.addEventListener('DOMContentLoaded', function() {
            const formElements = document.querySelectorAll('.form-control, .btn');
            formElements.forEach((element, index) => {
                element.classList.add('animate__animated', 'animate__fadeInUp');
                element.style.animationDelay = (index * 0.1 + 0.5) + 's';
            });
        });
    </script>
</body>
</html>