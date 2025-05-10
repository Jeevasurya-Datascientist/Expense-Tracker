<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$userId = $_SESSION['user_id'];
$user = getUserDetails($userId);
$fullName = $user['name'];
$email = $user['email'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the feedback and message from the form
    $feedback = $_POST['feedback'];
    $message = $_POST['message'] ?? '';  // Optional message

    // Insert the feedback into the database
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, feedback, message) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $feedback, $message]);

    // Optionally, you can also send a confirmation email
    $subject = "Thank you for your feedback";
    $body = "Dear $fullName,\n\nThank you for sharing your feedback with us. We appreciate your input and will take it into consideration.\n\nBest regards,\nExpense Tracker Team";
    mail($email, $subject, $body);

    // Redirect to a confirmation page
    header("Location: feedback_received.php");  // A page to show a confirmation message
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Submit Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet"/>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center animate__animated animate__fadeInDown">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center">
                    <h4 class="text-primary"><i class="bi bi-pencil-fill"></i> Your Feedback</h4>
                    <p class="mt-3">We value your feedback and want to improve our service. Please take a moment to share your experience.</p>

                    <!-- Feedback Form -->
                    <form method="POST">
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
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send-fill"></i> Submit Feedback
                            </button>
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="bi bi-house-door-fill"></i> Return to Home
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
