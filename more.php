<?php
session_start();

// Mock default values if not logged in (for demo)
if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = 'John Doe';
    $_SESSION['user_email'] = 'john@example.com';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $newUserName = trim(htmlspecialchars($_POST['user_name']));
    
    // Make sure the name isn't empty
    if (!empty($newUserName)) {
        // Update the session data
        $_SESSION['user_name'] = $newUserName;
        
        // Set a success message
        $successMessage = "Profile updated successfully!";
    } else {
        $errorMessage = "Name cannot be empty!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>More Settings - Expense Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body {
            background: linear-gradient(to bottom right, #f0f8ff, #ffffff);
            font-family: 'Segoe UI', sans-serif;
        }
        .settings-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 2rem;
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .profile-pic:hover {
            transform: scale(1.05);
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25);
        }
        .form-label {
            font-weight: 500;
        }
        .alert {
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center animate__animated animate__fadeInUp">
        <div class="col-md-6">
            <div class="settings-card animate__animated animate__zoomIn">
                <h4 class="text-center mb-4"><i class="bi bi-gear-fill text-primary"></i> Profile Settings</h4>

                <!-- Success/Error Messages -->
                <?php if (isset($successMessage)): ?>
                <div class="alert alert-success animate__animated animate__fadeIn" role="alert">
                    <i class="bi bi-check-circle-fill"></i> <?php echo $successMessage; ?>
                </div>
                <?php endif; ?>

                <?php if (isset($errorMessage)): ?>
                <div class="alert alert-danger animate__animated animate__fadeIn" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $errorMessage; ?>
                </div>
                <?php endif; ?>

                <!-- Profile Photo -->
                <div class="text-center mb-4">
                    <img src="https://i.pravatar.cc/150?u=<?php echo urlencode($_SESSION['user_name']); ?>" id="profilePreview" class="profile-pic mb-2" alt="Profile">
                    <input type="file" class="form-control form-control-sm mt-2" id="profileInput" accept="image/*">
                    <small class="text-muted">Note: Image upload functionality requires additional server-side code</small>
                </div>

                <!-- Edit Name -->
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="mb-3">
                        <label for="userName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="userName" name="user_name" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email (read-only)</label>
                        <input type="email" class="form-control" id="userEmail" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" readonly>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save2"></i> Save Changes
                        </button>
                        <a href="profile.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left-circle"></i> Back to Profile
                        </a>
                    </div>
                </form>

                <!-- More Options -->
                <hr class="my-4">
                <div class="text-center">
                    <a href="change_password.php" class="btn btn-link">
                        <i class="bi bi-key"></i> Change Password
                    </a>
                    <br>
                    <a href="delete_account.php" class="btn btn-link text-danger">
                        <i class="bi bi-trash"></i> Delete My Account
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Bootstrap & JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Preview uploaded profile picture
    document.getElementById('profileInput').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function () {
                document.getElementById('profilePreview').src = reader.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.classList.add('animate__fadeOut');
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
    });
</script>

</body>
</html>