<<<<<<< HEAD
<?php
session_start();
require_once 'db_connect.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
    $stmt->execute([$user, $role]);
    $user_data = $stmt->fetch();

    if ($user_data && password_verify($pass, $user_data['password'])) {
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['username'] = $user_data['username'];
        $_SESSION['role'] = $user_data['role'];

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username, password, or role selection.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GloryStock</title> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background-color: #f1f5f9; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: 'Inter', sans-serif; 
        }
        .login-card { 
            width: 100%; 
            max-width: 420px; 
            border: none; 
            border-radius: 16px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            background: #ffffff; 
            padding: 2.5rem;
        }
        .form-control, .form-select {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            padding: 12px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .btn-login { 
            background: #0f172a; 
            color: #ffffff;
            border: none; 
            padding: 13px; 
            font-weight: 700; 
            border-radius: 8px; 
            transition: all 0.2s;
            text-transform: uppercase;
        }
        .btn-login:hover { 
            background: #1e293b;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="card login-card">
    <div class="text-center mb-4">
        <img src="logo.png" alt="Logo" style="height: 65px; width: auto; margin-bottom: 15px;">
        <h2 class="fw-bold text-dark mb-0">GLORYSTOCK</h2>
        <p class="text-muted small fw-bold">SIGN IN TO YOUR ACCOUNT</p>
        <div class="mx-auto mt-2" style="width: 40px; height: 3px; background: #3b82f6; border-radius: 2px;"></div>
    </div>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger py-2 small text-center mb-4 border-0">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="mb-3">
            <label class="form-label small fw-bold text-muted text-uppercase">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Enter username" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label small fw-bold text-muted text-uppercase">Password</label>
            <div class="input-group">
                <input type="password" name="password" id="passwordField" class="form-control" placeholder="••••••••" required>
                <span class="input-group-text bg-white" onclick="togglePassword()">
                    <i class="fas fa-eye text-muted" id="eyeIcon"></i>
                </span>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold text-muted text-uppercase">Login As</label>
            <select name="role" class="form-select" required>
                <option value="staff">Staff / Cashier</option>
                <option value="admin">Administrator</option>
            </select>
        </div>

        <button type="submit" class="btn btn-login w-100 mb-4">Sign In</button>
    </form>
    
    <div class="text-center border-top pt-3">
        <p class="text-muted small mb-0">
            Don't have an account? <a href="register.php" class="text-primary fw-bold text-decoration-none">Register Here</a>
        </p>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('passwordField');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>

</body>
=======
<?php
session_start();
require_once 'db_connect.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
    $stmt->execute([$user, $role]);
    $user_data = $stmt->fetch();

    if ($user_data && password_verify($pass, $user_data['password'])) {
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['username'] = $user_data['username'];
        $_SESSION['role'] = $user_data['role'];

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username, password, or role selection.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GloryStock</title> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background-color: #f1f5f9; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: 'Inter', sans-serif; 
        }
        .login-card { 
            width: 100%; 
            max-width: 420px; 
            border: none; 
            border-radius: 16px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            background: #ffffff; 
            padding: 2.5rem;
        }
        .form-control, .form-select {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            padding: 12px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .btn-login { 
            background: #0f172a; 
            color: #ffffff;
            border: none; 
            padding: 13px; 
            font-weight: 700; 
            border-radius: 8px; 
            transition: all 0.2s;
            text-transform: uppercase;
        }
        .btn-login:hover { 
            background: #1e293b;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="card login-card">
    <div class="text-center mb-4">
        <img src="logo.png" alt="Logo" style="height: 65px; width: auto; margin-bottom: 15px;">
        <h2 class="fw-bold text-dark mb-0">GLORYSTOCK</h2>
        <p class="text-muted small fw-bold">SIGN IN TO YOUR ACCOUNT</p>
        <div class="mx-auto mt-2" style="width: 40px; height: 3px; background: #3b82f6; border-radius: 2px;"></div>
    </div>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger py-2 small text-center mb-4 border-0">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="mb-3">
            <label class="form-label small fw-bold text-muted text-uppercase">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Enter username" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label small fw-bold text-muted text-uppercase">Password</label>
            <div class="input-group">
                <input type="password" name="password" id="passwordField" class="form-control" placeholder="••••••••" required>
                <span class="input-group-text bg-white" onclick="togglePassword()">
                    <i class="fas fa-eye text-muted" id="eyeIcon"></i>
                </span>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold text-muted text-uppercase">Login As</label>
            <select name="role" class="form-select" required>
                <option value="staff">Staff / Cashier</option>
                <option value="admin">Administrator</option>
            </select>
        </div>

        <button type="submit" class="btn btn-login w-100 mb-4">Sign In</button>
    </form>
    
    <div class="text-center border-top pt-3">
        <p class="text-muted small mb-0">
            Don't have an account? <a href="register.php" class="text-primary fw-bold text-decoration-none">Register Here</a>
        </p>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('passwordField');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>

</body>
>>>>>>> f1f996b39031b13ecb1c00a432fd157d25e86313
</html>