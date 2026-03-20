<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $qty_requested = (int)$_POST['quantity'];

    $stmt = $pdo->prepare("SELECT name, stock, price FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        
        if ($qty_requested > $product['stock']) {
            
            $error = "❌ Not enough stock! You tried to sell $qty_requested, but only {$product['stock']} left.";
            header("Location: pos.php?status=error&msg=" . urlencode($error));
            exit();
        } else {
           
            $total_price = $product['price'] * $qty_requested;
            
            
            header("Location: save_transaction.php?id=$product_id&qty=$qty_requested&total=$total_price");
            exit();
        }
    } else {
        header("Location: pos.php?status=error&msg=Product+not+found");
        exit();
    }
}
?>