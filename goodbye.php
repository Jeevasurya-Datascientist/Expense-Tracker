<?php
require 'config.php';

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get user info
$userId = $_SESSION['user_id'];
$user = getUserDetails($userId);

// Check if user info was retrieved
if (!$user) {
    // Redirect to login if no user info is found
    redirect('login.php');
}

$fullName = $user['name'];
$email = $user['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Goodbye | Expense Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Animate.css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet"/>

    <style>
        body {
            background: linear-gradient(135deg, #f0f4ff, #e0eafc);
            font-family: 'Segoe UI', sans-serif;
            padding-top: 60px;
        }
        .goodbye-card {
            max-width: 750px;
            margin: auto;
        }
    </style>
</head>
<body>

<div class="card goodbye-card shadow-lg animate__animated animate__fadeInDown">
    <div class="card-body p-5 text-center">
        <h1 class="text-danger animate__animated animate__heartBeat animate__delay-1s">
            <i class="bi bi-emoji-tear-fill"></i> Goodbye!
        </h1>
        <p class="fs-5 mt-3">We're sad to see you go. Before you leave, please share your feedback.</p>

        <!-- Feedback Form -->
        <form action="submit_feedback.php" method="POST" class="text-start mt-4">
            <div class="mb-3">
                <label for="feedback" class="form-label"><strong>What problems did you face?</strong></label>
                <textarea class="form-control" id="feedback" name="feedback" rows="4" required placeholder="Let us know..."></textarea>
            </div>

            <hr class="my-4">

            <h5 class="text-primary"><i class="bi bi-envelope-at"></i> Contact Us</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Your Name</label>
                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($fullName); ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Your Email</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email); ?>" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label for="message" class="form-label">Additional Message</label>
                <textarea class="form-control" name="message" id="message" rows="3" placeholder="Optional..."></textarea>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success animate__animated animate__fadeInUp">
                    <i class="bi bi-send-fill"></i> Submit Feedback
                </button>
                <a href="index.php" class="btn btn-outline-primary animate__animated animate__fadeInUp">
                    <i class="bi bi-house-door-fill"></i> Return to Home
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
