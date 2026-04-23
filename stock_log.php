<<<<<<< HEAD
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';

// Fetch the logs with product names
$query = "SELECT sl.*, p.name as product_name, p.sku 
          FROM stock_log sl 
          JOIN products p ON sl.product_id = p.id 
          ORDER BY sl.created_at DESC";
$logs = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Audit Trail | StockPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; }
        .navbar { background: #0f172a !important; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .badge-incoming { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-outgoing { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .log-date { font-size: 0.8rem; color: #64748b; }
        .table thead { background-color: #f8fafc; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark sticky-top shadow mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">📦 STOCKPRO AUDIT</a>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">Return to Dashboard</a>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-800 text-dark">Stock Movement History</h4>
            <p class="text-muted">Every manual change and sale recorded for accountability.</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Timestamp</th>
                        <th>Product Details</th>
                        <th>Type</th>
                        <th class="text-center">Quantity</th>
                        <th>Reason / Reference</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($logs) > 0): ?>
                        <?php foreach ($logs as $log): 
                            $is_positive = $log['change_qty'] > 0;
                        ?>
                        <tr>
                            <td class="ps-4 log-date">
                                <?php echo date('M d, Y', strtotime($log['created_at'])); ?><br>
                                <span class="fw-bold"><?php echo date('H:i A', strtotime($log['created_at'])); ?></span>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($log['product_name']); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($log['sku']); ?></div>
                            </td>
                            <td>
                                <?php if ($is_positive): ?>
                                    <span class="badge badge-incoming px-3 py-2">INCOMING</span>
                                <?php else: ?>
                                    <span class="badge badge-outgoing px-3 py-2">OUTGOING</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center fw-800 fs-5 <?php echo $is_positive ? 'text-success' : 'text-danger'; ?>">
                                <?php echo ($is_positive ? '+' : '') . $log['change_qty']; ?>
                            </td>
                            <td>
                                <span class="text-dark small fw-bold text-uppercase">
                                    <?php echo htmlspecialchars($log['reason']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No stock movements found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
=======
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';

// Fetch the logs with product names
$query = "SELECT sl.*, p.name as product_name, p.sku 
          FROM stock_log sl 
          JOIN products p ON sl.product_id = p.id 
          ORDER BY sl.created_at DESC";
$logs = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Audit Trail | StockPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; }
        .navbar { background: #0f172a !important; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .badge-incoming { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-outgoing { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .log-date { font-size: 0.8rem; color: #64748b; }
        .table thead { background-color: #f8fafc; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark sticky-top shadow mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">📦 STOCKPRO AUDIT</a>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">Return to Dashboard</a>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-800 text-dark">Stock Movement History</h4>
            <p class="text-muted">Every manual change and sale recorded for accountability.</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Timestamp</th>
                        <th>Product Details</th>
                        <th>Type</th>
                        <th class="text-center">Quantity</th>
                        <th>Reason / Reference</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($logs) > 0): ?>
                        <?php foreach ($logs as $log): 
                            $is_positive = $log['change_qty'] > 0;
                        ?>
                        <tr>
                            <td class="ps-4 log-date">
                                <?php echo date('M d, Y', strtotime($log['created_at'])); ?><br>
                                <span class="fw-bold"><?php echo date('H:i A', strtotime($log['created_at'])); ?></span>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($log['product_name']); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($log['sku']); ?></div>
                            </td>
                            <td>
                                <?php if ($is_positive): ?>
                                    <span class="badge badge-incoming px-3 py-2">INCOMING</span>
                                <?php else: ?>
                                    <span class="badge badge-outgoing px-3 py-2">OUTGOING</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center fw-800 fs-5 <?php echo $is_positive ? 'text-success' : 'text-danger'; ?>">
                                <?php echo ($is_positive ? '+' : '') . $log['change_qty']; ?>
                            </td>
                            <td>
                                <span class="text-dark small fw-bold text-uppercase">
                                    <?php echo htmlspecialchars($log['reason']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No stock movements found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
>>>>>>> f1f996b39031b13ecb1c00a432fd157d25e86313
</html>