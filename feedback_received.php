<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Delete the user's account from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    // Destroy the session (log the user out)
    session_destroy();
    
    // Redirect to the login page after account deletion
    header("Location: login.php");
    exit();
} else {
    // If the user is not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Received</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container text-center my-5">
    <h2 class="text-success">Thank You for Your Feedback!</h2>
    <p class="fs-4">We appreciate your time and effort in helping us improve. If you have any more thoughts, feel free to contact us again.</p>
    <a href="login.php" class="btn btn-primary">Return to Login</a>
</div>

</body>
</html>
