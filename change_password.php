<?php
session_start();
// Assume the user is logged in and session holds `user_id`
// In production, always check if user is authenticated

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        $error = "New passwords do not match.";
    } else {
        // Connect DB
        include 'config.php';
        $id = $_SESSION['user_id'];

        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (password_verify($current, $user['password'])) {
            $new_hashed = password_hash($new, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$new_hashed, $id]);
            $success = "Password updated successfully.";
        } else {
            $error = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet"/>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center animate__animated animate__fadeIn">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4"><i class="bi bi-shield-lock-fill"></i> Change Password</h4>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger animate__animated animate__shakeX"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success animate__animated animate__fadeInDown"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="current" class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" id="current" required>
                        </div>
                        <div class="mb-3">
                            <label for="new" class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" id="new" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm" class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" id="confirm" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle"></i> Update Password
                            </button>
                        </div>
                        <div class="text-center mt-3">
                            <a href="more.php" class="btn btn-link"><i class="bi bi-arrow-left-circle"></i> Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
