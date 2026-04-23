<<<<<<< HEAD
<?php
require_once 'db_connect.php';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $key  = $_POST['admin_key'];

    // Security: Only allow Admin if key is PRO-99-SECURE
    if ($role === 'admin' && $key !== 'PRO-99-SECURE') {
        $msg = "<div class='alert alert-danger py-2 small'>❌ Invalid Admin Security Key!</div>";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$user, $pass, $role]);
            header("Location: login.php?registered=true");
            exit();
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger py-2 small'>❌ Username is already taken!</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register | StockPro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        .auth-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .btn-dark { padding: 12px; border-radius: 10px; font-weight: bold; background: #2c3e50; }
        #adminSec { display: none; }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Create Account</h2>
        <p class="text-muted">Join the StockPro team</p>
    </div>

    <?php echo $msg; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="small fw-bold">Choose Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="small fw-bold">Set Password</label>
            <div class="input-group">
                <input type="password" name="password" id="passReg" class="form-control" required>
                <span class="input-group-text" onclick="togglePass('passReg', 'eyeReg')">
                    <i class="fa-solid fa-eye" id="eyeReg"></i>
                </span>
            </div>
        </div>

        <div class="mb-3">
            <label class="small fw-bold">Your Role</label>
            <select name="role" class="form-select" id="roleSelect" onchange="checkRole()">
                <option value="staff">Staff (Sales Only)</option>
                <option value="admin">Administrator (Full Access)</option>
            </select>
        </div>

        <div class="mb-4" id="adminSec">
            <label class="small fw-bold text-danger">Admin Secret Key</label>
            <input type="password" name="admin_key" class="form-control" placeholder="Enter security code">
        </div>

        <button type="submit" class="btn btn-dark w-100 mb-3 text-white">Register Account</button>
        
        <p class="text-center small text-muted">
            Already have an account? <a href="login.php" class="fw-bold text-dark text-decoration-none">Login</a>
        </p>
    </form>
</div>

<script>
    function checkRole() {
        const role = document.getElementById('roleSelect').value;
        document.getElementById('adminSec').style.display = (role === 'admin') ? 'block' : 'none';
    }

    function togglePass(inputId, eyeId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(eyeId);
        if (input.type === "password") {
            input.type = "text";
            eye.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input.type = "password";
            eye.classList.replace("fa-eye-slash", "fa-eye");
        }
    }
</script>
</body>
=======
<?php
require_once 'db_connect.php';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $key  = $_POST['admin_key'];

    // Security: Only allow Admin if key is PRO-99-SECURE
    if ($role === 'admin' && $key !== 'PRO-99-SECURE') {
        $msg = "<div class='alert alert-danger py-2 small'>❌ Invalid Admin Security Key!</div>";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$user, $pass, $role]);
            header("Location: login.php?registered=true");
            exit();
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger py-2 small'>❌ Username is already taken!</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register | StockPro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        .auth-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .btn-dark { padding: 12px; border-radius: 10px; font-weight: bold; background: #2c3e50; }
        #adminSec { display: none; }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Create Account</h2>
        <p class="text-muted">Join the StockPro team</p>
    </div>

    <?php echo $msg; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="small fw-bold">Choose Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="small fw-bold">Set Password</label>
            <div class="input-group">
                <input type="password" name="password" id="passReg" class="form-control" required>
                <span class="input-group-text" onclick="togglePass('passReg', 'eyeReg')">
                    <i class="fa-solid fa-eye" id="eyeReg"></i>
                </span>
            </div>
        </div>

        <div class="mb-3">
            <label class="small fw-bold">Your Role</label>
            <select name="role" class="form-select" id="roleSelect" onchange="checkRole()">
                <option value="staff">Staff (Sales Only)</option>
                <option value="admin">Administrator (Full Access)</option>
            </select>
        </div>

        <div class="mb-4" id="adminSec">
            <label class="small fw-bold text-danger">Admin Secret Key</label>
            <input type="password" name="admin_key" class="form-control" placeholder="Enter security code">
        </div>

        <button type="submit" class="btn btn-dark w-100 mb-3 text-white">Register Account</button>
        
        <p class="text-center small text-muted">
            Already have an account? <a href="login.php" class="fw-bold text-dark text-decoration-none">Login</a>
        </p>
    </form>
</div>

<script>
    function checkRole() {
        const role = document.getElementById('roleSelect').value;
        document.getElementById('adminSec').style.display = (role === 'admin') ? 'block' : 'none';
    }

    function togglePass(inputId, eyeId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(eyeId);
        if (input.type === "password") {
            input.type = "text";
            eye.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input.type = "password";
            eye.classList.replace("fa-eye-slash", "fa-eye");
        }
    }
</script>
</body>
>>>>>>> f1f996b39031b13ecb1c00a432fd157d25e86313
</html>