<<<<<<< HEAD
<?php
// 1. Database Connection
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Capture and trim data from the form
    $name        = trim($_POST['product_name']);
    $sku         = trim($_POST['sku']);
    $category_id = $_POST['category_id']; 
    $price       = $_POST['price'];
    $stock       = $_POST['stock'];

    // 3. Validation Rules
    $errors = [];

    if (empty($name))        { $errors[] = "Product name is required."; }
    if (empty($sku))         { $errors[] = "SKU code is required."; }
    if (empty($category_id)) { $errors[] = "Please select a category."; }
    if ($price <= 0)         { $errors[] = "Price must be greater than 0 TZS."; }
    if ($stock < 0)          { $errors[] = "Stock cannot be negative."; }

    // 4. If there are errors, stop and show them to the user
    if (!empty($errors)) {
        echo "<div style='color: red; font-family: sans-serif; padding: 20px;'>";
        echo "<h3>⚠️ Validation Errors:</h3><ul>";
        foreach ($errors as $error) { echo "<li>$error</li>"; }
        echo "</ul><a href='add_product.php'>Go Back</a></div>";
        exit();
    }

    // 5. Database Save Logic
    try {
        $sql = "INSERT INTO products (name, sku, category_id, price, stock) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([$name, $sku, $category_id, $price, $stock]);

        // --- SUCCESS: IMPROVED REDIRECT ---
        // We now send them to dashboard.php so they see the result immediately
        header("Location: dashboard.php?status=success");
        exit();

    } catch (PDOException $e) {
        // Handle Duplicate SKU Error
        if ($e->getCode() == 23000) {
            header("Location: add_product.php?status=error&type=duplicate");
        } else {
            echo "Database Error: " . $e->getMessage();
        }
        exit();
    }
} else {
    header("Location: add_product.php");
    exit();
=======
<?php
// 1. Database Connection
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Capture and trim data from the form
    $name        = trim($_POST['product_name']);
    $sku         = trim($_POST['sku']);
    $category_id = $_POST['category_id']; 
    $price       = $_POST['price'];
    $stock       = $_POST['stock'];

    // 3. Validation Rules
    $errors = [];

    if (empty($name))        { $errors[] = "Product name is required."; }
    if (empty($sku))         { $errors[] = "SKU code is required."; }
    if (empty($category_id)) { $errors[] = "Please select a category."; }
    if ($price <= 0)         { $errors[] = "Price must be greater than 0 TZS."; }
    if ($stock < 0)          { $errors[] = "Stock cannot be negative."; }

    // 4. If there are errors, stop and show them to the user
    if (!empty($errors)) {
        echo "<div style='color: red; font-family: sans-serif; padding: 20px;'>";
        echo "<h3>⚠️ Validation Errors:</h3><ul>";
        foreach ($errors as $error) { echo "<li>$error</li>"; }
        echo "</ul><a href='add_product.php'>Go Back</a></div>";
        exit();
    }

    // 5. Database Save Logic
    try {
        $sql = "INSERT INTO products (name, sku, category_id, price, stock) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([$name, $sku, $category_id, $price, $stock]);

        // --- SUCCESS: IMPROVED REDIRECT ---
        // We now send them to dashboard.php so they see the result immediately
        header("Location: dashboard.php?status=success");
        exit();

    } catch (PDOException $e) {
        // Handle Duplicate SKU Error
        if ($e->getCode() == 23000) {
            header("Location: add_product.php?status=error&type=duplicate");
        } else {
            echo "Database Error: " . $e->getMessage();
        }
        exit();
    }
} else {
    header("Location: add_product.php");
    exit();
>>>>>>> f1f996b39031b13ecb1c00a432fd157d25e86313
}