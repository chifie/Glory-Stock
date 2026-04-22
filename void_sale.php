<?php
session_start();
require_once 'db_connect.php';

// SECURITY: Only an Admin can void a sale
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied: You do not have permission to void sales.");
}

if (isset($_GET['id'])) {
    $sale_id = $_GET['id'];

    try {
        $pdo->beginTransaction();

        // 1. Find out WHICH product was sold and HOW MANY
        $stmt = $pdo->prepare("SELECT product_id, quantity FROM sales WHERE id = ?");
        $stmt->execute([$sale_id]);
        $sale = $stmt->fetch();

        if ($sale) {
            // 2. RESTORE STOCK: Add the sold quantity back to the product
            $updateStock = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
            $updateStock->execute([$sale['quantity'], $sale['product_id']]);

            // 3. REMOVE SALE: Delete the record from history
            $deleteSale = $pdo->prepare("DELETE FROM sales WHERE id = ?");
            $deleteSale->execute([$sale_id]);

            $pdo->commit();
            // Redirect back with a success message
            header("Location: sales_history.php?msg=Sale+Voided+Successfully");
            exit();
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Fatal Error: " . $e->getMessage());
    }
}
?>