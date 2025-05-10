<?php
require_once 'config.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];

// Handle filter and search
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$fromDate = isset($_GET['from_date']) ? sanitize($_GET['from_date']) : '';
$toDate = isset($_GET['to_date']) ? sanitize($_GET['to_date']) : '';

// Prepare SQL query based on filters
$sql = "SELECT * FROM expenses WHERE user_id = :user_id";
$params = [':user_id' => $userId];

// Apply date filters
if ($filter === 'today') {
    $sql .= " AND DATE(expense_date) = CURDATE()";
} elseif ($filter === 'yesterday') {
    $sql .= " AND DATE(expense_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
} elseif ($filter === 'week') {
    $sql .= " AND expense_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($filter === 'month') {
    $sql .= " AND MONTH(expense_date) = MONTH(CURDATE()) AND YEAR(expense_date) = YEAR(CURDATE())";
} elseif ($filter === 'custom' && !empty($fromDate) && !empty($toDate)) {
    $sql .= " AND expense_date BETWEEN :from_date AND :to_date";
    $params[':from_date'] = $fromDate;
    $params[':to_date'] = $toDate;
}

// Apply search filter
if (!empty($search)) {
    $sql .= " AND (description LIKE :search OR category LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

// Apply category filter
if (!empty($category)) {
    $sql .= " AND category = :category";
    $params[':category'] = $category;
}

// Order by date
$sql .= " ORDER BY expense_date DESC, created_at DESC";

// Execute query
$stmt = $conn->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total of filtered expenses
$totalFiltered = 0;
foreach ($expenses as $expense) {
    $totalFiltered += $expense['amount'];
}

// Get all categories for filter dropdown
$categories = getCategories();

// Delete expense
if (isset($_POST['delete_expense'])) {
    $expenseId = sanitize($_POST['expense_id']);
    
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $expenseId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    // Redirect to refresh the page
    redirect("history.php?filter=$filter" . 
             (!empty($search) ? "&search=$search" : "") . 
             (!empty($category) ? "&category=$category" : "") .
             (!empty($fromDate) ? "&from_date=$fromDate" : "") .
             (!empty($toDate) ? "&to_date=$toDate" : ""));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense History - Personal Expense Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="text-primary animate__animated animate__fadeIn">
                    <i class="bi bi-clock-history"></i> Expense History
                </h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="add_expense.php" class="btn btn-primary animate__animated animate__fadeIn">
                    <i class="bi bi-plus-circle"></i> Add New Expense
                </a>
            </div>
        </div>
        
        <div class="card shadow-sm border-0 mb-4 animate__animated animate__fadeIn">
            <div class="card-body">
                <form action="history.php" method="get" class="row g-3">
                    <div class="col-md-3">
                        <label for="filter" class="form-label">Time Period</label>
                        <select class="form-select" id="filter" name="filter" onchange="toggleCustomDate()">
                            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Time</option>
                            <option value="today" <?php echo $filter === 'today' ? 'selected' : ''; ?>>Today</option>
                            <option value="yesterday" <?php echo $filter === 'yesterday' ? 'selected' : ''; ?>>Yesterday</option>
                            <option value="week" <?php echo $filter === 'week' ? 'selected' : ''; ?>>Last 7 Days</option>
                            <option value="month" <?php echo $filter === 'month' ? 'selected' : ''; ?>>This Month</option>
                            <option value="custom" <?php echo $filter === 'custom' ? 'selected' : ''; ?>>Custom Range</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 custom-date <?php echo $filter !== 'custom' ? 'd-none' : ''; ?>">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="from_date" name="from_date" value="<?php echo $fromDate; ?>">
                    </div>
                    
                    <div class="col-md-3 custom-date <?php echo $filter !== 'custom' ? 'd-none' : ''; ?>">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="to_date" name="to_date" value="<?php echo $toDate; ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['name']); ?>" 
                                        <?php echo $category === $cat['name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="history.php" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow-sm border-0 animate__animated animate__fadeIn">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Expenses</h5>
                <span class="badge bg-primary">Total: ₹<?php echo number_format($totalFiltered, 2); ?></span>
            </div>
            <div class="card-body">
                <?php if (count($expenses) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount (₹)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expenses as $expense): ?>
                                    <tr>
                                        <td><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></td>
                                        <td>
                                            <span class="badge rounded-pill bg-light text-dark">
                                                <?php echo htmlspecialchars($expense['category']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                        <td class="text-end">₹<?php echo number_format($expense['amount'], 2); ?></td>
                                        <td>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                                <input type="hidden" name="expense_id" value="<?php echo $expense['id']; ?>">
                                                <button type="submit" name="delete_expense" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-search fs-1 text-muted"></i>
                        </div>
                        <h5>No expenses found</h5>
                        <p class="text-muted">Try changing your search criteria or add new expenses</p>
                        <a href="add_expense.php" class="btn btn-primary">Add Expense</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleCustomDate() {
            const filter = document.getElementById('filter').value;
            const customDateFields = document.querySelectorAll('.custom-date');
            
            if (filter === 'custom') {
                customDateFields.forEach(field => field.classList.remove('d-none'));
            } else {
                customDateFields.forEach(field => field.classList.add('d-none'));
            }
        }
    </script>

<?php include 'footer.php'; ?>
</body>
</html>