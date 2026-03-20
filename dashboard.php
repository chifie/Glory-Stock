<?php
// --- MARCH 10: SECURITY ---
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';

// --- MARCH 12/14/16: ENHANCED LOGIC ---
$search = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';
$cat_filter = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$low_stock_only = isset($_GET['low_stock']) && $_GET['low_stock'] == '1';

// 1. ANALYTICS CALCULATIONS
// Get Total Inventory Value
$val_stmt = $pdo->query("SELECT SUM(price * stock) FROM products");
$total_inventory_value = $val_stmt->fetchColumn() ?? 0;

// Get Today's Revenue
$rev_stmt = $pdo->prepare("SELECT SUM(total_price) FROM sales WHERE DATE(sale_date) = CURDATE()");
$rev_stmt->execute();
$today_revenue = $rev_stmt->fetchColumn() ?? 0;

// --- NEW IMPROVEMENT: MONTHLY PROFIT & EXPENSES ---
$current_month = date('m');
$current_year = date('Y');

// Get Monthly Expenses
$exp_stmt = $pdo->prepare("SELECT SUM(amount) FROM expenses WHERE MONTH(expense_date) = ? AND YEAR(expense_date) = ?");
$exp_stmt->execute([$current_month, $current_year]);
$monthly_expenses = $exp_stmt->fetchColumn() ?? 0;

// Get Monthly Revenue to calculate Net Profit
$mon_rev_stmt = $pdo->prepare("SELECT SUM(total_price) FROM sales WHERE MONTH(sale_date) = ? AND YEAR(sale_date) = ?");
$mon_rev_stmt->execute([$current_month, $current_year]);
$monthly_revenue = $mon_rev_stmt->fetchColumn() ?? 0;
$net_profit = $monthly_revenue - $monthly_expenses;

// 2. BUILD DYNAMIC PRODUCT QUERY
$params = [];
$where_clauses = ["1=1"]; 

if ($search !== '') {
    $where_clauses[] = "(p.name LIKE ? OR p.sku LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($cat_filter > 0) {
    $where_clauses[] = "p.category_id = ?";
    $params[] = $cat_filter;
}
if ($low_stock_only) {
    $where_clauses[] = "p.stock <= 5";
}

$where_sql = implode(" AND ", $where_clauses);

$sql = "SELECT p.*, c.name AS category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE $where_sql 
        ORDER BY p.stock ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Dashboard | StockPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; }
        .navbar { background: #0f172a !important; }
        .nav-link { font-weight: 500; color: #94a3b8 !important; }
        .nav-link.active { color: white !important; }
        .stat-card { border: none; border-radius: 16px; padding: 20px; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
        .card { border-radius: 12px; border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .badge-low { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container py-4">
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card bg-white border-start border-primary border-5 shadow-sm">
                <h6 class="text-muted small fw-bold text-uppercase">Stock Valuation</h6>
                <h3 class="fw-800 mb-0"><?php echo number_format($total_inventory_value); ?> <small class="fs-6 fw-normal">TZS</small></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-white border-start border-success border-5 shadow-sm">
                <h6 class="text-muted small fw-bold text-uppercase">Today's Revenue</h6>
                <h3 class="fw-800 mb-0 text-success"><?php echo number_format($today_revenue); ?> <small class="fs-6 fw-normal">TZS</small></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-white border-start border-danger border-5 shadow-sm">
                <h6 class="text-muted small fw-bold text-uppercase">Monthly Expenses</h6>
                <h3 class="fw-800 mb-0 text-danger"><?php echo number_format($monthly_expenses); ?> <small class="fs-6 fw-normal text-muted">TZS</small></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-white border-start border-warning border-5 shadow-sm">
                <h6 class="text-muted small fw-bold text-uppercase">Net Profit (MTD)</h6>
                <h3 class="fw-800 mb-0 <?php echo ($net_profit >= 0) ? 'text-dark' : 'text-danger'; ?>">
                    <?php echo number_format($net_profit); ?> <small class="fs-6 fw-normal text-muted">TZS</small>
                </h3>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <form action="dashboard.php" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">Search Products</label>
                    <input type="text" name="search" class="form-control" placeholder="SKU or Name..." value="<?php echo $search; ?>">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="0">All Categories</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($cat_filter == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="lowStock" <?php echo $low_stock_only ? 'checked' : ''; ?>>
                        <label class="form-check-label fw-bold text-danger" for="lowStock">Low Stock Only</label>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark w-100 fw-bold">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Inventory List</h5>
            <div>
                <a href="expenses.php" class="btn btn-outline-danger btn-sm fw-bold me-2">💸 Expenses</a>
                <a href="daily_close.php" class="btn btn-warning btn-sm fw-bold me-2">📉 End of Day Report</a>
                <a href="add_product.php" class="btn btn-primary btn-sm fw-bold">➕ Add New Product</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">SKU</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td class="ps-4 text-muted small fw-bold"><?php echo htmlspecialchars($p['sku']); ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($p['name']); ?></td>
                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?></span></td>
                            <td class="fw-bold"><?php echo number_format($p['price']); ?> /=</td>
                            <td class="fw-bold <?php echo ($p['stock'] <= 5) ? 'text-danger' : ''; ?>"><?php echo $p['stock']; ?></td>
                            <td>
                                <?php if ($p['stock'] <= 5): ?>
                                    <span class="badge badge-low border border-danger">REORDER</span>
                                <?php else: ?>
                                    <span class="badge bg-success">HEALTHY</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="delete.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</a>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted">Read-Only</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No products matching your search criteria.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>