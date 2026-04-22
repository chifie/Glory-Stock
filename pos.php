<?php
// --- MARCH 15: POS TERMINAL WITH AUDIT LOGGING ---
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';

$message = "";

// --- PROCESS SALE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = (int)$_POST['product_id'];
    $qty_to_sell = (int)$_POST['quantity'];
    $cashier_name = $_SESSION['username'] ?? 'Staff'; 

    $stmt = $pdo->prepare("SELECT name, price, stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        if ($qty_to_sell > $product['stock']) {
            $message = "<div class='alert alert-danger shadow-sm'>⚠️ <strong>Stock Alert:</strong> Only {$product['stock']} available.</div>";
        } else {
            $total_cost = $product['price'] * $qty_to_sell;

            $pdo->beginTransaction();
            try {
                // 1. RECORD THE SALE
                $insert = $pdo->prepare("INSERT INTO sales (product_id, quantity, total_price, sale_date) VALUES (?, ?, ?, NOW())");
                $insert->execute([$product_id, $qty_to_sell, $total_cost]);
                $new_receipt_id = $pdo->lastInsertId();

                // 2. UPDATE PRODUCT STOCK
                $update = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $update->execute([$qty_to_sell, $product_id]);

                // 3. THE AUDIT LOG
                $log = $pdo->prepare("INSERT INTO stock_log (product_id, change_qty, reason) VALUES (?, ?, ?)");
                $log->execute([$product_id, -$qty_to_sell, "Sale (Staff: {$_SESSION['username']})"]);

                $pdo->commit();
                header("Location: pos.php?success=1&amount=$total_cost&receipt_id=$new_receipt_id");
                exit();
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = "<div class='alert alert-danger'>❌ System Error: " . $e->getMessage() . "</div>";
            }
        }
    }
}

// Fetch active products
$products = $pdo->query("SELECT id, name, price, stock FROM products WHERE stock > 0 ORDER BY name ASC")->fetchAll();

// Fetch recent sales
$recent_sales = $pdo->query("SELECT s.*, p.name FROM sales s 
                             JOIN products p ON s.product_id = p.id 
                             ORDER BY s.sale_date DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Terminal | GloryStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; }
        .pos-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .history-card { border: none; border-radius: 12px; }
        .btn-sale { padding: 14px; font-weight: 700; border-radius: 10px; background: #0f172a; border: none; }
        .btn-sale:hover { background: #1e293b; transform: translateY(-1px); }
        /* Logo adjustment for page header */
        .page-logo { height: 45px; width: auto; margin-right: 15px; }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container">
    <div class="d-flex align-items-center mb-4 border-bottom pb-3">
        <img src="logo.png" alt="GloryStock" class="page-logo">
        <div>
            <h3 class="fw-800 mb-0">POS Terminal</h3>
            <p class="text-muted small mb-0">Process customer sales quickly and securely.</p>
        </div>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-md-5">
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success border-0 shadow-sm text-center py-4 mb-4" style="border-radius: 16px;">
                    <div class="display-6 mb-2">✅</div>
                    <h4 class="fw-bold">Transaction Complete</h4>
                    <p class="text-muted">Total Paid: <span class="fw-bold text-dark"><?php echo number_format($_GET['amount']); ?> TZS</span></p>
                    <a href="receipt.php?id=<?php echo $_GET['receipt_id']; ?>" target="_blank" class="btn btn-dark fw-bold px-4">🖨️ Print Receipt</a>
                </div>
            <?php endif; ?>

            <?php echo $message; ?>

            <div class="card pos-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0 text-uppercase" style="letter-spacing: 1px;">Checkout</h5>
                        <span class="badge bg-light text-dark border p-2 small">Cashier: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Select Item</label>
                            <select name="product_id" class="form-select form-select-lg border-0 bg-light" required>
                                <option value="">-- Choose Product --</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?php echo $p['id']; ?>">
                                        <?php echo htmlspecialchars($p['name']); ?> — <?php echo number_format($p['price']); ?> /= (Stock: <?php echo $p['stock']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Quantity</label>
                            <input type="number" name="quantity" class="form-control form-control-lg border-0 bg-light" min="1" value="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sale w-100 shadow-sm text-uppercase">Process Sale</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0">Recent Sales</h5>
                <a href="sales_history.php" class="small text-decoration-none">View All</a>
            </div>
            <div class="card history-card shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-uppercase text-muted">
                                <th class="ps-3">Time</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th class="text-end pe-3">Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_sales as $rs): ?>
                            <tr>
                                <td class="ps-3 small"><?php echo date('H:i A', strtotime($rs['sale_date'])); ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($rs['name']); ?></td>
                                <td><span class="badge bg-light text-dark">x<?php echo $rs['quantity']; ?></span></td>
                                <td class="fw-bold"><?php echo number_format($rs['total_price']); ?> /=</td>
                                <td class="text-end pe-3">
                                    <a href="receipt.php?id=<?php echo $rs['id']; ?>" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill">🖨️ View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="stock_log.php" class="btn btn-light btn-sm fw-bold text-muted">🛡️ View Stock Audit Trail</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>