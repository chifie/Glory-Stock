<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id    = $_POST['id']; // Hidden input from form
    $name  = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $sql = "UPDATE products SET name = ?, price = ?, stock = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$name, $price, $stock, $id])) {
        header("Location: dashboard.php?status=updated");
    } else {
        echo "Update failed.";
    }
}
?>