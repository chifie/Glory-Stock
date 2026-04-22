<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Denied</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center text-center" style="height: 100vh;">
    <div class="mx-auto card p-5 border-0 shadow-sm" style="border-radius: 20px;">
        <h1 class="display-1">🚫</h1>
        <h3 class="fw-bold">Access Denied</h3>
        <p class="text-muted">Hello <?php echo $_SESSION['username']; ?>, you don't have Admin rights.</p>
        <a href="pos.php" class="btn btn-dark px-4 py-2">Back to POS</a>
    </div>
</body>
</html>