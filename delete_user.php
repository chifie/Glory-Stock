<?php
session_start();
require_once 'db_connect.php';

// 1. SECURITY CHECK
// Only an Admin can trigger a deletion.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: access_denied.php");
    exit();
}

// 2. VALIDATION
if (isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    $current_admin_id = $_SESSION['user_id'];

    // 3. PREVENT SELF-DELETION
    // You should not be able to delete your own account while logged in.
    if ($id_to_delete === $current_admin_id) {
        header("Location: users.php?error=self_delete");
        exit();
    }

    // 4. EXECUTE DELETION
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$id_to_delete])) {
        header("Location: users.php?success=user_removed");
    } else {
        header("Location: users.php?error=failed");
    }
    exit();
} else {
    // If no ID is provided, go back to the user list
    header("Location: users.php");
    exit();
}