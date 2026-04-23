<<<<<<< HEAD
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold" href="dashboard.php">
            <img src="logo.png" alt="GloryStock" style="height: 38px; width: auto; margin-right: 12px; object-fit: contain;">
            <span style="letter-spacing: 0.5px;">GLORYSTOCK</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto align-items-center">
                <a class="nav-link px-3" href="pos.php">Point of Sale</a>
                <a class="nav-link px-3" href="dashboard.php">Inventory</a>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a class="nav-link px-3 text-info" href="reports.php">Reports</a>
                    <a class="nav-link px-3 text-info" href="expenses.php">Expenses</a>
                    <a class="nav-link px-3 text-warning fw-bold" href="users.php">Manage Staff</a>
                <?php endif; ?>

                <a class="nav-link text-danger ms-lg-3 fw-bold" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)
                </a>
            </div>
        </div>
    </div>
=======
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold" href="dashboard.php">
            <img src="logo.png" alt="GloryStock" style="height: 38px; width: auto; margin-right: 12px; object-fit: contain;">
            <span style="letter-spacing: 0.5px;">GLORYSTOCK</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto align-items-center">
                <a class="nav-link px-3" href="pos.php">Point of Sale</a>
                <a class="nav-link px-3" href="dashboard.php">Inventory</a>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a class="nav-link px-3 text-info" href="reports.php">Reports</a>
                    <a class="nav-link px-3 text-info" href="expenses.php">Expenses</a>
                    <a class="nav-link px-3 text-warning fw-bold" href="users.php">Manage Staff</a>
                <?php endif; ?>

                <a class="nav-link text-danger ms-lg-3 fw-bold" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)
                </a>
            </div>
        </div>
    </div>
>>>>>>> f1f996b39031b13ecb1c00a432fd157d25e86313
</nav>