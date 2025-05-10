<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    $userId = $_SESSION['user_id'];
    
    // Delete the user from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    // Destroy the session to log the user out
    session_destroy();

    // Redirect to goodbye.php
    header("Location: goodbye.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Delete Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet"/>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center animate__animated animate__fadeInDown">
        <div class="col-md-6">
            <div class="card border-danger shadow">
                <div class="card-body text-center">
                    <h4 class="text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Confirm Account Deletion</h4>
                    <p class="mt-3">Are you sure you want to permanently delete your account? This action <strong>cannot be undone.</strong></p>
                    <form method="POST" action="goodbye.php">
    <button name="confirm_delete" type="submit" class="btn btn-danger btn-lg animate__animated animate__pulse animate__infinite">
        <i class="bi bi-trash"></i> Yes, Delete My Account
    </button>
    <a href="more.php" class="btn btn-secondary mt-3 d-block">
        <i class="bi bi-x-circle"></i> Cancel
    </a>
</form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
