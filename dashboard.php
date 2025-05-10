<?php
require_once 'config.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Get total expenses
$stmt = $conn->prepare("SELECT SUM(amount) as total FROM expenses WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$totalExpense = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Get today's expenses
$stmt = $conn->prepare("SELECT SUM(amount) as today FROM expenses WHERE user_id = :user_id AND DATE(expense_date) = CURDATE()");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$todayExpense = $stmt->fetch(PDO::FETCH_ASSOC)['today'] ?? 0;

// Get this month's expenses
$stmt = $conn->prepare("SELECT SUM(amount) as month FROM expenses WHERE user_id = :user_id AND MONTH(expense_date) = MONTH(CURDATE()) AND YEAR(expense_date) = YEAR(CURDATE())");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$monthExpense = $stmt->fetch(PDO::FETCH_ASSOC)['month'] ?? 0;

// Get recent expenses (last 5)
$stmt = $conn->prepare("SELECT * FROM expenses WHERE user_id = :user_id ORDER BY expense_date DESC, created_at DESC LIMIT 5");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$recentExpenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get expense data for chart
$stmt = $conn->prepare("SELECT 
                        DATE(expense_date) as date, 
                        SUM(amount) as total 
                      FROM expenses 
                      WHERE user_id = :user_id 
                      GROUP BY DATE(expense_date) 
                      ORDER BY date DESC 
                      LIMIT 7");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$chartData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$chartData = array_reverse($chartData);

// Get category data for pie chart
$stmt = $conn->prepare("SELECT 
                        category, 
                        SUM(amount) as total 
                      FROM expenses 
                      WHERE user_id = :user_id 
                      GROUP BY category");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$pieChartData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Personal Expense Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-lg border-0 animate__animated animate__fadeIn">
                    <div class="card-body">
                        <h2 class="text-primary">Welcome, <?php echo htmlspecialchars($userName); ?>!</h2>
                        <p class="text-muted">Here's an overview of your expenses</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 animate__animated animate__fadeInLeft">
                    <div class="card-body text-center">
                        <h5 class="card-title text-muted">Total Expenses</h5>
                        <h2 class="display-5 text-primary">₹<?php echo number_format($totalExpense, 2); ?></h2>
                        <div class="mt-3">
                            <a href="add_expense.php" class="btn btn-primary">Add New Expense</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 animate__animated animate__fadeInUp">
                    <div class="card-body text-center">
                        <h5 class="card-title text-muted">Today's Expenses</h5>
                        <h2 class="display-5 text-success">₹<?php echo number_format($todayExpense, 2); ?></h2>
                        <div class="mt-3">
                            <a href="history.php?filter=today" class="btn btn-outline-success">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 animate__animated animate__fadeInRight">
                    <div class="card-body text-center">
                        <h5 class="card-title text-muted">This Month</h5>
                        <h2 class="display-5 text-info">₹<?php echo number_format($monthExpense, 2); ?></h2>
                        <div class="mt-3">
                            <a href="history.php?filter=month" class="btn btn-outline-info">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 animate__animated animate__fadeIn">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Expense Trend (Last 7 days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="expenseChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 animate__animated animate__fadeIn">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Expense by Category</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="pieChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 animate__animated animate__fadeInUp">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Expenses</h5>
                        <a href="history.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (count($recentExpenses) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Category</th>
                                            <th>Description</th>
                                            <th class="text-end">Amount (₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentExpenses as $expense): ?>
                                            <tr>
                                                <td><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></td>
                                                <td>
                                                    <span class="badge rounded-pill bg-light text-dark">
                                                        <?php echo htmlspecialchars($expense['category']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                                <td class="text-end">₹<?php echo number_format($expense['amount'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-receipt fs-1 text-muted"></i>
                                </div>
                                <h5>No expenses yet</h5>
                                <p class="text-muted">Start tracking your expenses by adding your first entry</p>
                                <a href="add_expense.php" class="btn btn-primary">Add Expense</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Bar chart for expense trend
        const ctx = document.getElementById('expenseChart').getContext('2d');
        const expenseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    <?php foreach ($chartData as $data): ?>
                        '<?php echo date('d M', strtotime($data['date'])); ?>',
                    <?php endforeach; ?>
                ],
                datasets: [{
                    label: 'Daily Expenses (₹)',
                    data: [
                        <?php foreach ($chartData as $data): ?>
                            <?php echo $data['total']; ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value;
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeOutBounce'
                }
            }
        });

        // Pie chart for category distribution
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: [
                    <?php foreach ($pieChartData as $data): ?>
                        '<?php echo $data['category']; ?>',
                    <?php endforeach; ?>
                ],
                datasets: [{
                    data: [
                        <?php foreach ($pieChartData as $data): ?>
                            <?php echo $data['total']; ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(199, 199, 199, 0.7)',
                        'rgba(83, 102, 255, 0.7)',
                        'rgba(40, 180, 255, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return label + ': ₹' + value;
                            }
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    animateRotate: true
                }
            }
        });
    </script>
</body>
</html>