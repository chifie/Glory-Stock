
<?php
session_start();

// 1. SECURITY: Block anyone who isn't an ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not admin
    exit();
}

require_once 'db_connect.php';

// 2. GET MONTHLY REVENUE DATA (Last 6 Months)
$sales_query = $pdo->query("
    SELECT DATE_FORMAT(sale_date, '%b %Y') as month_label, SUM(total_price) as total 
    FROM sales 
    GROUP BY month_label 
    ORDER BY sale_date ASC 
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

// 3. GET EXPENSE DATA (Last 6 Months)
$expense_query = $pdo->query("
    SELECT DATE_FORMAT(expense_date, '%b %Y') as month_label, SUM(amount) as total 
    FROM expenses 
    GROUP BY month_label 
    ORDER BY expense_date ASC 
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

// Re-indexing data
$sales_data = [];
foreach($sales_query as $row) { $sales_data[$row['month_label']] = $row['total']; }

$expense_data = [];
foreach($expense_query as $row) { $expense_data[$row['month_label']] = $row['total']; }

$all_months = array_unique(array_merge(array_keys($sales_data), array_keys($expense_data)));

$labels = [];
$revenue_values = [];
$expense_values = [];

foreach($all_months as $month) {
    $labels[] = $month;
    $revenue_values[] = $sales_data[$month] ?? 0;
    $expense_values[] = $expense_data[$month] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Reports | GloryStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .report-card { border: none; border-radius: 20px; background: white; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .table thead { background: #f1f5f9; }
        .page-logo { height: 50px; width: auto; margin-right: 15px; }
        
        /* Print Styles */
        @media print {
            .btn, nav, .stock-audit-btn { display: none !important; }
            .report-card { box-shadow: none !important; border: 1px solid #ddd !important; }
            body { background: white; }
        }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center">
            <img src="logo.png" alt="GloryStock" class="page-logo">
            <div>
                <h3 class="fw-800 mb-0">Business Analytics</h3>
                <p class="text-muted small mb-0">Financial overview for the last 6 months</p>
            </div>
        </div>
        <button onclick="window.print()" class="btn btn-dark fw-bold rounded-pill px-4">
            <i class="fas fa-print me-2"></i> Print Report
        </button>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="report-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0">Revenue vs Expenses</h5>
                    <div class="small text-muted">Real-time Performance Graph</div>
                </div>
                <div style="height: 350px;">
                    <canvas id="businessChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="report-card p-4 h-100">
                <h6 class="fw-bold mb-3 text-uppercase small text-muted">Monthly Statement</h6>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="small text-muted">
                                <th>Month</th>
                                <th>Revenue</th>
                                <th>Expenses</th>
                                <th>Net Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach($all_months as $m): 
                                $rev = $sales_data[$m] ?? 0;
                                $exp = $expense_data[$m] ?? 0;
                                $prof = $rev - $exp;
                            ?>
                            <tr>
                                <td class="fw-bold text-dark"><?php echo $m; ?></td>
                                <td class="text-success fw-semibold"><?php echo number_format($rev); ?> /=</td>
                                <td class="text-danger"><?php echo number_format($exp); ?> /=</td>
                                <td class="fw-bold <?php echo $prof >= 0 ? 'text-primary' : 'text-danger'; ?>">
                                    <?php echo ($prof < 0 ? "-" : "+") . number_format(abs($prof)); ?> /=
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="report-card p-4 h-100">
                <h6 class="fw-bold mb-3 text-uppercase small text-muted">Best Selling Products</h6>
                <?php
                $top_items = $pdo->query("
                    SELECT p.name, SUM(s.quantity) as total_sold 
                    FROM sales s 
                    JOIN products p ON s.product_id = p.id 
                    GROUP BY p.name 
                    ORDER BY total_sold DESC LIMIT 6
                ")->fetchAll();
                
                foreach($top_items as $item):
                ?>
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($item['name']); ?></span>
                    <span class="badge bg-primary rounded-pill"><?php echo $item['total_sold']; ?> Units Sold</span>
                </div>
                <?php endforeach; ?>
                
                <div class="mt-4 pt-2">
                    <div class="alert alert-light border small text-center">
                        <i class="fas fa-info-circle me-2"></i> These items generate the highest footfall for GloryStock.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('businessChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Total Revenue',
            data: <?php echo json_encode($revenue_values); ?>,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3b82f6'
        }, {
            label: 'Total Expenses',
            data: <?php echo json_encode($expense_values); ?>,
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#ef4444'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { usePointStyle: true, font: { weight: 'bold' } } }
        },
        scales: {
            y: { 
                beginAtZero: true, 
                grid: { color: '#f1f5f9' },
                ticks: { callback: function(value) { return value.toLocaleString() + ' /='; } }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>
</body>
</html>