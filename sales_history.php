<?php
// --- MARCH 15: ANALYTICS & REPORTING ---
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';

// 1. Capture Date Filters (Default to last 30 days if empty)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// 2. Fetch Detailed Sales Records
$query = "SELECT s.*, p.name as product_name, p.sku 
          FROM sales s 
          JOIN products p ON s.product_id = p.id 
          WHERE DATE(s.sale_date) BETWEEN ? AND ?
          ORDER BY s.sale_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$start_date, $end_date]);
$sales = $stmt->fetchAll();

// 3. Calculate Total Revenue for the Period
$total_revenue = 0;
foreach ($sales as $sale) {
    $total_revenue += $sale['total_price'];
}

// 4. Identify Top Selling Product for the Period
$top_stmt = $pdo->prepare("SELECT p.name, SUM(s.quantity) as total_qty 
                           FROM sales s JOIN products p ON s.product_id = p.id 
                           WHERE DATE(s.sale_date) BETWEEN ? AND ?
                           GROUP BY s.product_id ORDER BY total_qty DESC LIMIT 1");
$top_stmt->execute([$start_date, $end_date]);
$top_product = $top_stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales History | StockPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .report-card { border: none; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .stat-box { border-radius: 12px; padding: 20px; color: white; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm no-print">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">📦 STOCKPRO</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="pos.php">POS Terminal</a>
            <a class="nav-link active" href="sales_history.php">History</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-800 m-0">Sales Analytics</h2>
        <button onclick="window.print()" class="btn btn-outline-dark no-print">🖨️ Export PDF/Print</button>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stat-box bg-primary shadow-sm">
                <h6 class="text-uppercase small fw-bold opacity-75">Total Period Revenue</h6>
                <h2 class="fw-800 mb-0"><?php echo number_format($total_revenue); ?> <small class="fs-6">TZS</small></h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-box bg-success shadow-sm">
                <h6 class="text-uppercase small fw-bold opacity-75">Top Moving Product</h6>
                <h2 class="fw-800 mb-0"><?php echo $top_product['name'] ?? 'N/A'; ?></h2>
                <small>Units Sold: <?php echo $top_product['total_qty'] ?? 0; ?></small>
            </div>
        </div>
    </div>

    <div class="card report-card mb-4 no-print">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">From Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">To Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-dark w-100 fw-bold">Update Report</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card report-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Timestamp</th>
                        <th>Product Details</th>
                        <th>Quantity</th>
                        <th class="text-end pe-4">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($sales) > 0): ?>
                        <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td class="ps-4 small text-muted"><?php echo date('d M Y, H:i', strtotime($sale['sale_date'])); ?></td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($sale['product_name']); ?></div>
                                <div class="small text-muted">SKU: <?php echo htmlspecialchars($sale['sku']); ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border px-3"><?php echo $sale['quantity']; ?></span></td>
                            <td class="text-end pe-4 fw-800"><?php echo number_format($sale['total_price']); ?> /=</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No sales recorded for this period.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>