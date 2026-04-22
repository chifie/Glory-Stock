<?php
// --- MARCH 15: SECURE EDIT WITH AUDIT LOGGING ---
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';

// 1. GET THE PRODUCT ID
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = (int)$_GET['id'];

// 2. FETCH CURRENT DATA
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found!");
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// 3. HANDLE THE UPDATE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name        = htmlspecialchars($_POST['product_name']);
    $sku         = htmlspecialchars($_POST['sku']);
    $category_id = (int)$_POST['category_id'];
    $price       = (float)$_POST['price'];
    $new_stock   = (int)$_POST['stock'];
    $old_stock   = (int)$product['stock']; // Store the old value to calculate difference

    try {
        $pdo->beginTransaction();

        // Update the product details
        $sql = "UPDATE products SET name = ?, sku = ?, category_id = ?, price = ?, stock = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $sku, $category_id, $price, $new_stock, $id]);

        // --- IMPROVEMENT: LOG THE CHANGE IF STOCK CHANGED ---
        if ($new_stock != $old_stock) {
            $difference = $new_stock - $old_stock;
            $reason = ($difference > 0) ? "Manual Restock" : "Manual Reduction/Correction";
            
            $log_stmt = $pdo->prepare("INSERT INTO stock_log (product_id, change_qty, reason) VALUES (?, ?, ?)");
            $log_stmt->execute([$id, $difference, $reason]);
        }

        $pdo->commit();
        header("Location: dashboard.php?status=updated");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Update failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item | StockPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; padding-top: 50px; }
        .edit-card { 
            background: white; 
            padding: 30px; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            border: none;
            max-width: 500px;
            margin: auto;
        }
        .form-label { font-weight: 700; font-size: 0.8rem; text-uppercase; color: #64748b; margin-top: 15px; }
        .form-control, .form-select { 
            border: 1px solid #e2e8f0; 
            padding: 12px; 
            border-radius: 10px;
            font-weight: 500;
        }
        .btn-update { 
            background: #0f172a; 
            color: white; 
            border: none; 
            padding: 15px; 
            border-radius: 12px; 
            font-weight: 800; 
            width: 100%; 
            margin-top: 30px;
            transition: 0.3s;
        }
        .btn-update:hover { background: #1e293b; transform: translateY(-2px); }
        .stock-indicator {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            border-left: 5px solid #007bff;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="edit-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h3 class="fw-800 m-0">Edit Product</h3>
            <span class="badge bg-dark">ID: #<?php echo $id; ?></span>
        </div>
        <p class="text-muted small mb-4">Modify product details and stock levels.</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label class="form-label">Product Name</label>
            <input type="text" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>

            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">SKU / Code</label>
                    <input type="text" name="sku" class="form-control" value="<?php echo htmlspecialchars($product['sku']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <label class="form-label">Selling Price (TZS)</label>
            <input type="number" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>

            <div class="stock-indicator">
                <label class="form-label mt-0">Stock Quantity</label>
                <input type="number" name="stock" class="form-control bg-white" value="<?php echo $product['stock']; ?>" required>
                <p class="text-muted mb-0 mt-2" style="font-size: 0.75rem;">
                    * Changing this value will create an entry in the <strong>Stock Audit Trail</strong>.
                </p>
            </div>

            <button type="submit" class="btn-update">SAVE CHANGES</button>
            <a href="dashboard.php" class="btn d-block text-center mt-3 text-muted fw-bold small text-decoration-none">← Cancel and Exit</a>
        </form>
    </div>
</div>

</body>
</html>