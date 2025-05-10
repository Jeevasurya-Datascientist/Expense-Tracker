<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - Expense Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #fff);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .profile-card {
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            background: white;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .btn-logout, .btn-more {
            transition: all 0.3s ease;
        }
        .btn-logout:hover, .btn-more:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center animate__animated animate__fadeInUp">
        <div class="col-md-6 text-center">
            <div class="profile-card p-4">
                <img src="https://i.pravatar.cc/150?u=<?php echo $_SESSION['user_name']; ?>" alt="Profile Picture" class="profile-avatar mb-3">
                <h4 class="fw-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h4>
                <p class="text-muted">Logged in as: <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'user@example.com'); ?></p>

                <hr>

                <div class="d-grid gap-2 mt-4">
                    <a href="logout.php" class="btn btn-danger btn-logout">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                    <a href="more.php" class="btn btn-outline-primary btn-more">
                        <i class="bi bi-three-dots"></i> More Options
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
