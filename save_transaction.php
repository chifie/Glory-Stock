<?php
require_once 'db_connect.php';

if (isset($_GET['id']) && isset($_GET['qty']) && isset($_GET['total'])) {
    $product_id = $_GET['id'];
    $quantity = (int)$_GET['qty'];
    $total_price = $_GET['total'];

    try {
        
        $pdo->beginTransaction();

        
        $sql_sale = "INSERT INTO sales (product_id, quantity, total_price) VALUES (?, ?, ?)";
        $stmt_sale = $pdo->prepare($sql_sale);
        $stmt_sale->execute([$product_id, $quantity, $total_price]);

        
        $sql_stock = "UPDATE products SET stock = stock - ? WHERE id = ?";
        $stmt_stock = $pdo->prepare($sql_stock);
        $stmt_stock->execute([$quantity, $product_id]);

        
        $pdo->commit();

        header("Location: pos.php?status=success&amount=" . $total_price);
        exit();

    } catch (Exception $e) {
        
        $pdo->rollBack();
        header("Location: pos.php?status=error&msg=Transaction+Failed");
        exit();
    }
} else {
    header("Location: pos.php");
    exit();
}
?>