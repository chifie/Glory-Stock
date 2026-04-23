<<<<<<< HEAD
<?php
require_once 'db_connect.php';

try {
    $query = $pdo->query("SHOW TABLES");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);

    if (count($tables) > 0) {
        echo "<h3>✅ Connection Verified!</h3>";
        echo "Your database has the following tables:<br>";
        foreach ($tables as $table) {
            echo "- " . $table . "<br>";
        }
    } else {
        echo "<h3>⚠️ Connected, but no tables found.</h3>";
        echo "Did you run the SQL code for Products, Sales, and Users?";
    }
} catch (PDOException $e) {
    echo "<h3>❌ Connection Failed!</h3>";
    echo "Error: " . $e->getMessage();
}
=======
<?php
require_once 'db_connect.php';

try {
    $query = $pdo->query("SHOW TABLES");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);

    if (count($tables) > 0) {
        echo "<h3>✅ Connection Verified!</h3>";
        echo "Your database has the following tables:<br>";
        foreach ($tables as $table) {
            echo "- " . $table . "<br>";
        }
    } else {
        echo "<h3>⚠️ Connected, but no tables found.</h3>";
        echo "Did you run the SQL code for Products, Sales, and Users?";
    }
} catch (PDOException $e) {
    echo "<h3>❌ Connection Failed!</h3>";
    echo "Error: " . $e->getMessage();
}
>>>>>>> f1f996b39031b13ecb1c00a432fd157d25e86313
?>