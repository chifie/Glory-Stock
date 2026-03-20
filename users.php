<?php
// --- MARCH 16: ACCESS CONTROL & USER MGMT ---
session_start();

/**
 * 1. STRICT ACCESS CONTROL
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Ensure they go back to login if unauthorized
    exit();
}

require_once 'db_connect.php';

$message = "";

// 2. Handle New User Creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $role = $_POST['role'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $role]);
        $message = "<div class='alert alert-success shadow-sm border-0'>✅ Account for '$username' created successfully!</div>";
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger border-0'>❌ Error: Username already exists.</div>";
    }
}

// 3. Fetch All Users
$users = $pdo->query("SELECT id, username, role FROM users ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff | GloryStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); background: white; }
        .role-badge { font-size: 10px; font-weight: 800; padding: 4px 10px; border-radius: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .btn-create { background-color: #0f172a; border: none; border-radius: 12px; transition: 0.3s; }
        .btn-create:hover { background-color: #334155; transform: translateY(-1px); }
        .page-logo { height: 50px; width: auto; margin-right: 15px; }
        .table thead { background: #f1f5f9; }
        .form-control, .form-select { border-radius: 10px; padding: 10px; background: #f8fafc; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4 border-bottom pb-3">
        <img src="logo.png" alt="GloryStock" class="page-logo">
        <div>
            <h3 class="fw-800 mb-0">Staff Management</h3>
            <p class="text-muted small mb-0">Control system access and authorized personnel</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card p-4">
                <h5 class="fw-bold mb-4">Register New Staff</h5>
                <?php echo $message; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="username" class="form-control border-start-0" placeholder="e.g. bakari_m" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Access Level</label>
                        <select name="role" class="form-select">
                            <option value="staff">Staff (Sales Only)</option>
                            <option value="admin">Administrator (Full Access)</option>
                        </select>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-create text-white w-100 fw-bold py-3 shadow-sm">
                        <i class="fas fa-user-plus me-2"></i> Create Account
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card h-100 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Authorized Personnel</h5>
                    <span class="badge bg-light text-dark border px-3 py-2 fw-bold"><?php echo count($users); ?> Users</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small text-muted">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="ps-4 text-muted small">#<?php echo $u['id']; ?></td>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user-circle text-muted"></i>
                                        </div>
                                        <?php echo htmlspecialchars($u['username']); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($u['role'] === 'admin'): ?>
                                        <span class="role-badge bg-primary-subtle text-primary border border-primary-subtle">Admin</span>
                                    <?php else: ?>
                                        <span class="role-badge bg-light text-dark border">Staff</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if($u['id'] != $_SESSION['user_id']): ?>
                                        <a href="delete_user.php?id=<?php echo $u['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold border-0" 
                                           onclick="return confirm('Revoke all access for this staff member? This will kick them out of the system.')">
                                            <i class="fas fa-user-slash me-1"></i> Revoke Access
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success fw-bold px-3 py-2 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> You (Active)
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>