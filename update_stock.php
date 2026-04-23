<<<<<<< HEAD
<?php
require_once 'db_connect.php';

if (isset($_GET['id']) && isset($_GET['qty'])) {
    $product_id = $_GET['id'];
    $quantity_sold = (int)$_GET['qty'];

    try {
        
        $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$quantity_sold, $product_id]);

        
        header("Location: pos.php?status=complete&msg=Stock+Updated+Successfully");
        exit();

    } catch (PDOException $e) {
        header("Location: pos.php?status=error&msg=Warehouse+Update+Failed");
        exit();
    }
} else {
    header("Location: pos.php");
    exit();
}
=======
<?php
require_once 'db_connect.php';

if (isset($_GET['id']) && isset($_GET['qty'])) {
    $product_id = $_GET['id'];
    $quantity_sold = (int)$_GET['qty'];

    try {
        
        $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$quantity_sold, $product_id]);

        
        header("Location: pos.php?status=complete&msg=Stock+Updated+Successfully");
        exit();

    } catch (PDOException $e) {
        header("Location: pos.php?status=error&msg=Warehouse+Update+Failed");
        exit();
    }
} else {
    header("Location: pos.php");
    exit();
}
>>>>>>> f1f996b39031b13ecb1c00a432fd157d25e86313
?>