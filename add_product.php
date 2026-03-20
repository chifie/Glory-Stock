<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';

// --- FETCH CATEGORIES FOR THE DROPDOWN ---
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

$message = "";

// --- HANDLE FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $sku = trim($_POST['sku']);
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    if (!empty($name) && !empty($sku)) {
        try {
            $sql = "INSERT INTO products (name, sku, category_id, price, stock) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $sku, $category_id, $price, $stock]);
            
            // Redirect back to dashboard with success
            header("Location: dashboard.php?msg=ProductAdded");
            exit();
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Stock Manager Pro</title>
    <style>
        :root { --primary: #1e293b; --accent: #3b82f6; --success: #22c55e; --border: #e2e8f0; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; margin: 0; display: flex; flex-direction: column; min-height: 100vh; }
        
        .navbar { background: var(--primary); padding: 1rem 5%; color: white; display: flex; justify-content: space-between; align-items: center; }
        .nav-links a { color: #94a3b8; text-decoration: none; font-weight: 600; font-size: 14px; margin-left: 20px; }

        .container { max-width: 600px; margin: 40px auto; padding: 20px; width: 90%; }
        
        .form-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid var(--border); }
        h1 { margin-top: 0; font-size: 24px; color: var(--primary); }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 14px; }
        
        input, select { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid var(--border); 
            border-radius: 8px; 
            box-sizing: border-box; 
            font-size: 14px;
            transition: border-color 0.2s;
        }
        input:focus, select:focus { border-color: var(--accent); outline: none; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }

        .btn-row { display: flex; gap: 10px; margin-top: 30px; }
        .btn { flex: 1; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; border: none; font-size: 14px; text-align: center; text-decoration: none; }
        .btn-save { background: var(--accent); color: white; }
        .btn-cancel { background: #f1f5f9; color: #475569; }
        
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
        .alert-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    </style>
</head>
<body>

<nav class="navbar">
    <div style="font-weight: 900;">📦 STOCK MANAGER PRO</div>
    <div class="nav-links">
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</nav>

<div class="container">
    <div class="form-card">
        <h1>➕ Add New Product</h1>
        <p style="color: #64748b; margin-bottom: 25px;">Enter product details to update your inventory.</p>

        <?php if($message): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="add_product.php" method="POST">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" placeholder="e.g. Samsung Galaxy S24" required>
            </div>

            <div class="form-group">
                <label>SKU (Stock Keeping Unit) *</label>
                <input type="text" name="sku" placeholder="e.g. PHN-SAM-001" required>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category_id">
                    <option value="">Select a Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Price (TZS)</label>
                    <input type="number" name="price" placeholder="0" min="0" required>
                </div>
                <div class="form-group">
                    <label>Initial Stock Level</label>
                    <input type="number" name="stock" placeholder="0" min="0" required>
                </div>
            </div>

            <div class="btn-row">
                <a href="dashboard.php" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-save">Save Product</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>