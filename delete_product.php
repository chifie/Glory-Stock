<?php
// --- MARCH 08: SESSION START ---
session_start();
require_once 'db_connect.php';

// --- MARCH 11: USER ROLE PROTECTION ---
// Improvement: Check if the user is logged in AND if they are an ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // If not an admin, stop immediately. 
    // This prevents a 'Staff' member from typing the URL manually to delete stock.
    header("Location: dashboard.php?error=unauthorized");
    exit();
}

// 1. Check if the ID exists in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // --- MARCH 12: DATA INTEGRITY (PREVENT ORPHAN SALES) ---
        // Check if this product has sales records before deleting
        $checkSales = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE product_id = ?");
        $checkSales->execute([$id]);
        
        if ($checkSales->fetchColumn() > 0) {
            // If it has sales, we shouldn't delete it (or we archive it instead)
            header("Location: dashboard.php?error=has_sales");
            exit();
        }

        // 2. Prepare the DELETE statement
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        // 3. Execute the deletion
        $stmt->execute([$id]);

        // 4. Redirect back to dashboard with a success message
        header("Location: dashboard.php?status=deleted");
        exit();

    } catch (PDOException $e) {
        die("Error deleting product: " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>