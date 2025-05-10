<?php
require_once 'config.php';

// Redirect to dashboard if logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Expense Tracker</title>
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
        
        .benefits {
            background-color: white;
            border-radius: 12px;
            padding: 2rem;
            margin-top: 3rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .benefits-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
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
        
        .custom-pulse {
            animation: pulse 2s ease infinite;
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
                <div class="card shadow-lg animate__animated animate__fadeIn animate__faster">
                    <div class="card-body text-center">
                        <div class="logo-container animate__animated animate__zoomIn">
                            <i class="fas fa-wallet logo-icon"></i>
                        </div>
                        <h1 class="display-5 fw-bold text-primary mb-3 custom-fadeIn">Personal Expense Tracker</h1>
                        <p class="lead mb-4 text-muted animate__animated animate__fadeInUp animate__delay-1s">Take control of your finances with our powerful and easy-to-use expense tracking tool.</p>
                        
                        <div class="d-grid gap-3 col-lg-8 mx-auto">
                            <button class="btn btn-primary btn-lg custom-pulse" onclick="window.location.href='login.php'">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                            <button class="btn btn-outline-primary btn-lg animate__animated animate__fadeInUp animate__delay-2s" onclick="window.location.href='register.php'">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="benefits animate__animated animate__fadeInUp animate__delay-3s">
                    <h2 class="benefits-title text-center mb-4">Why Use Our Tracker?</h2>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="feature-card text-center">
                                <i class="fas fa-chart-pie feature-icon animate__animated"></i>
                                <h5>Visual Analytics</h5>
                                <p class="text-muted">See where your money goes with intuitive charts</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card text-center">
                                <i class="fas fa-mobile-alt feature-icon animate__animated"></i>
                                <h5>Mobile Friendly</h5>
                                <p class="text-muted">Track expenses on the go from any device</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card text-center">
                                <i class="fas fa-bell feature-icon animate__animated"></i>
                                <h5>Budget Alerts</h5>
                                <p class="text-muted">Get notified when approaching limits</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card text-center">
                                <i class="fas fa-lock feature-icon animate__animated"></i>
                                <h5>Secure & Private</h5>
                                <p class="text-muted">Your financial data stays private</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Script -->
    <script>
        // Add interaction to feature cards
        document.addEventListener('DOMContentLoaded', function() {
            const featureCards = document.querySelectorAll('.feature-card');
            
            featureCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.querySelector('.feature-icon').classList.add('animate__rubberBand');
                });
                
                card.addEventListener('mouseleave', function() {
                    this.querySelector('.feature-icon').classList.remove('animate__rubberBand');
                });
            });
            
            // Staggered animation for features
            setTimeout(() => {
                const features = document.querySelectorAll('.feature-card');
                features.forEach((feature, index) => {
                    setTimeout(() => {
                        feature.classList.add('animate__animated', 'animate__fadeInUp');
                    }, 200 * index);
                });
            }, 1000);
        });
    </script>
</body>
</html>