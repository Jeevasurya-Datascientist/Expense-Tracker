<?php
require_once 'config.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];

// Get monthly expenses for the last 6 months
$stmt = $conn->prepare("SELECT 
                        DATE_FORMAT(expense_date, '%b %Y') as month,
                        MONTH(expense_date) as month_num,
                        YEAR(expense_date) as year,
                        SUM(amount) as total 
                      FROM expenses 
                      WHERE user_id = :user_id 
                      AND expense_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                      GROUP BY YEAR(expense_date), MONTH(expense_date)
                      ORDER BY YEAR(expense_date), MONTH(expense_date)");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get category distribution for current month
$stmt = $conn->prepare("SELECT 
                        category,
                        SUM(amount) as total,
                        (SUM(amount) / (SELECT SUM(amount) FROM expenses 
                                        WHERE user_id = :user_id 
                                        AND MONTH(expense_date) = MONTH(CURDATE()) 
                                        AND YEAR(expense_date) = YEAR(CURDATE()))) * 100 as percentage
                      FROM expenses 
                      WHERE user_id = :user_id 
                      AND MONTH(expense_date) = MONTH(CURDATE()) 
                      AND YEAR(expense_date) = YEAR(CURDATE())
                      GROUP BY category
                      ORDER BY total DESC");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$categoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get daily average expense for current month
$stmt = $conn->prepare("SELECT 
                        AVG(daily_total) as average
                      FROM (
                        SELECT 
                          DATE(expense_date) as date,
                          SUM(amount) as daily_total
                        FROM expenses 
                        WHERE user_id = :user_id 
                        AND MONTH(expense_date) = MONTH(CURDATE()) 
                        AND YEAR(expense_date) = YEAR(CURDATE())
                        GROUP BY DATE(expense_date)
                      ) as daily_expenses");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$dailyAverage = $stmt->fetch(PDO::FETCH_ASSOC)['average'] ?? 0;

// Get highest expense day in current month
$stmt = $conn->prepare("SELECT 
                        DATE(expense_date) as date,
                        SUM(amount) as total
                      FROM expenses 
                      WHERE user_id = :user_id 
                      AND MONTH(expense_date) = MONTH(CURDATE()) 
                      AND YEAR(expense_date) = YEAR(CURDATE())
                      GROUP BY DATE(expense_date)
                      ORDER BY total DESC
                      LIMIT 1");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$highestDay = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Personal Expense Tracker</title>
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
            <div class="col-md-8">
                <h2 class="text-primary animate__animated animate__fadeIn">
                    <i class="bi bi-bar-chart"></i> Expense Reports
                </h2>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group animate__animated animate__fadeIn">
                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 animate__animated animate__fadeInLeft">
                    <div class="card-body text-center">
                        <h5 class="card-title text-muted">Current Month Average (Daily)</h5>
                        <h2 class="display-5 text-primary">₹<?php echo number_format($dailyAverage, 2); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 animate__animated animate__fadeInUp">
                    <div class="card-body text-center">
                        <h5 class="card-title text-muted">Highest Spending Day</h5>
                        <?php if ($highestDay): ?>
                            <h2 class="display-5 text-danger">₹<?php echo number_format($highestDay['total'], 2); ?></h2>
                            <p class="text-muted"><?php echo date('d M Y', strtotime($highestDay['date'])); ?></p>
                        <?php else: ?>
                            <h2 class="display-5 text-danger">₹0.00</h2>
                            <p class="text-muted">No data available</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 animate__animated animate__fadeInRight">
                    <div class="card-body text-center">
                        <h5 class="card-title text-muted">Top Expense Category</h5>
                        <?php if (count($categoryData) > 0): ?>
                            <h2 class="display-5 text-success"><?php echo htmlspecialchars($categoryData[0]['category']); ?></h2>
                            <p class="text-muted">₹<?php echo number_format($categoryData[0]['total'], 2); ?></p>
                        <?php else: ?>
                            <h2 class="display-5 text-success">N/A</h2>
                            <p class="text-muted">No data available</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 animate__animated animate__fadeInLeft">
                <div class="card-body">
                    <h5 class="card-title text-muted text-center">Last 6 Months Expenses</h5>
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 animate__animated animate__fadeInRight">
                <div class="card-body">
                    <h5 class="card-title text-muted text-center">Current Month Category Breakdown</h5>
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

   
</div>

<script>
    // Monthly Expense Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($monthlyData, 'month')); ?>,
            datasets: [{
                label: 'Total Spent (₹)',
                data: <?php echo json_encode(array_column($monthlyData, 'total')); ?>,
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 1200,
                easing: 'easeOutBounce'
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Category Distribution Pie Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($categoryData, 'category')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($categoryData, 'percentage')); ?>,
                backgroundColor: [
                    '#f87171', '#fbbf24', '#34d399', '#60a5fa', '#a78bfa', '#fb7185', '#10b981'
                ],
                hoverOffset: 10,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.toFixed(2) + '%';
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                duration: 1500
            }
        }
    });
</script>

<!-- Tailwind CDN (optional if Tailwind is not already setup) -->
<script src="https://cdn.tailwindcss.com"></script>
<?php include 'footer.php'; ?>
</body>
</html>
