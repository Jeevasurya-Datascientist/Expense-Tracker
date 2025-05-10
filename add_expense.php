<?php
require_once 'config.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];
$successMessage = '';
$errorMessage = '';

// Get all categories
$categories = getCategories();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = sanitize($_POST['amount']);
    $category = sanitize($_POST['category']);
    $description = sanitize($_POST['description']);
    $expenseDate = sanitize($_POST['expense_date']);
    
    // Validate input
    if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
        $errorMessage = "Please enter a valid amount";
    } elseif (empty($category)) {
        $errorMessage = "Please select a category";
    } elseif (empty($expenseDate)) {
        $errorMessage = "Please select a date";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO expenses (user_id, amount, category, description, expense_date) 
                                   VALUES (:user_id, :amount, :category, :description, :expense_date)");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':expense_date', $expenseDate);
            $stmt->execute();
            
            $successMessage = "Expense added successfully!";
            
            // Clear form data
            $amount = '';
            $category = '';
            $description = '';
            $expenseDate = '';
            
        } catch(PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense - Personal Expense Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success animate__animated animate__fadeIn">
                        <?php echo $successMessage; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger animate__animated animate__shakeX">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-lg border-0 animate__animated animate__fadeInUp">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Add New Expense</h4>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" value="<?php echo isset($amount) ? $amount : ''; ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="" selected disabled>Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category['name']); ?>"
                                            <?php echo (isset($_POST['category']) && $_POST['category'] == $category['name']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="expense_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="expense_date" name="expense_date" 
                                       value="<?php echo isset($expenseDate) ? $expenseDate : date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg animate__animated animate__pulse">
                                    <i class="bi bi-save"></i> Save Expense
                                </button>
                                <a href="dashboard.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="mt-4 text-center animate__animated animate__fadeIn">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5><i class="bi bi-lightbulb"></i> Quick Tip</h5>
                            <p class="text-muted">Categorizing your expenses helps you to better understand your spending habits and identify areas where you can save.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set default date to today
        document.addEventListener('DOMContentLoaded', function() {
            if (!document.getElementById('expense_date').value) {
                document.getElementById('expense_date').value = new Date().toISOString().split('T')[0];
            }
        });
    </script>
</body>
</html>