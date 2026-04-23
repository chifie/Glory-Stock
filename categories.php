<<<<<<< HEAD
<?php
// --- MARCH 15: CATEGORY MANAGEMENT ---
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';

$message = "";

// 1. Handle New Category Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $cat_name = htmlspecialchars(trim($_POST['category_name']));
    
    if (!empty($cat_name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        try {
            $stmt->execute([$cat_name]);
            $message = "<div class='alert alert-success'>✅ Category '$cat_name' added successfully!</div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>❌ Error: Category might already exist.</div>";
        }
    }
}

// 2. Fetch All Categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories | StockPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .navbar-brand { font-weight: 800; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">📦 STOCKPRO</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link active" href="categories.php">Categories</a>
            <a class="nav-link text-danger" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <h3 class="fw-bold mb-4">New Category</h3>
            <?php echo $message; ?>
            <div class="card p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Category Name</label>
                        <input type="text" name="category_name" class="form-control form-control-lg" placeholder="e.g. Beverages" required>
                    </div>
                    <button type="submit" name="add_category" class="btn btn-primary w-100 fw-bold py-2">Create Category</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <h3 class="fw-bold mb-4">Existing Categories</h3>
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Category Name</th>
                                <th class="text-end pe-4">Total Products</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <?php
                                    // Sub-query to count products per category
                                    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
                                    $count_stmt->execute([$cat['id']]);
                                    $p_count = $count_stmt->fetchColumn();
                                ?>
                                <tr>
                                    <td class="ps-4 text-muted small"><?php echo $cat['id']; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($cat['name']); ?></td>
                                    <td class="text-end pe-4">
                                        <span class="badge bg-light text-dark border px-3"><?php echo $p_count; ?> Items</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
=======
<?php
// --- MARCH 15: CATEGORY MANAGEMENT ---
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';

$message = "";

// 1. Handle New Category Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $cat_name = htmlspecialchars(trim($_POST['category_name']));
    
    if (!empty($cat_name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        try {
            $stmt->execute([$cat_name]);
            $message = "<div class='alert alert-success'>✅ Category '$cat_name' added successfully!</div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>❌ Error: Category might already exist.</div>";
        }
    }
}

// 2. Fetch All Categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories | StockPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .navbar-brand { font-weight: 800; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">📦 STOCKPRO</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link active" href="categories.php">Categories</a>
            <a class="nav-link text-danger" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <h3 class="fw-bold mb-4">New Category</h3>
            <?php echo $message; ?>
            <div class="card p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Category Name</label>
                        <input type="text" name="category_name" class="form-control form-control-lg" placeholder="e.g. Beverages" required>
                    </div>
                    <button type="submit" name="add_category" class="btn btn-primary w-100 fw-bold py-2">Create Category</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <h3 class="fw-bold mb-4">Existing Categories</h3>
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Category Name</th>
                                <th class="text-end pe-4">Total Products</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <?php
                                    // Sub-query to count products per category
                                    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
                                    $count_stmt->execute([$cat['id']]);
                                    $p_count = $count_stmt->fetchColumn();
                                ?>
                                <tr>
                                    <td class="ps-4 text-muted small"><?php echo $cat['id']; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($cat['name']); ?></td>
                                    <td class="text-end pe-4">
                                        <span class="badge bg-light text-dark border px-3"><?php echo $p_count; ?> Items</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
>>>>>>> f1f996b39031b13ecb1c00a432fd157d25e86313
</html>