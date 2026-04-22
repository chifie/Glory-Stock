<?php
// --- MARCH 15: EXPENSE MANAGEMENT (ADMIN ONLY) ---
session_start();

/** * 1. ACCESS CONTROL 
 * Only users with the 'admin' role can enter this page.
 */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';

$message = "";

// 2. HANDLE SAVING NEW EXPENSE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_expense'])) {
    $category = htmlspecialchars($_POST['category']);
    $amount = (float)$_POST['amount'];
    $description = htmlspecialchars($_POST['description']);
    $expense_date = $_POST['expense_date'];

    try {
        $stmt = $pdo->prepare("INSERT INTO expenses (category, amount, description, expense_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$category, $amount, $description, $expense_date]);
        $message = "<div class='alert alert-success shadow-sm border-0'>✅ Expense of " . number_format($amount) . " recorded!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
    }
}

// 3. FETCH ALL EXPENSES (Most recent first)
$expenses = $pdo->query("SELECT * FROM expenses ORDER BY expense_date DESC, created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker | GloryStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .card { border-radius: 20px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); background: white; }
        .btn-record { font-weight: 700; border-radius: 12px; background: #0f172a; border: none; transition: 0.3s; }
        .btn-record:hover { background: #334155; transform: translateY(-1px); }
        .badge-expense { font-size: 11px; font-weight: 700; padding: 5px 10px; border-radius: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .page-logo { height: 50px; width: auto; margin-right: 15px; }
        .form-control, .form-select { border-radius: 10px; border: 1px solid #e2e8f0; padding: 10px; background: #f8fafc; }
        .form-control:focus { background: white; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4 border-bottom pb-3">
        <img src="logo.png" alt="GloryStock" class="page-logo">
        <div>
            <h3 class="fw-800 mb-0">Expense Management</h3>
            <p class="text-muted small mb-0">Track and record business outgoings</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card p-4">
                <h5 class="fw-bold mb-4">Record New Expense</h5>
                <?php echo $message; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Category</label>
                        <select name="category" class="form-select" required>
                            <option value="Rent">Rent</option>
                            <option value="Electricity">Electricity</option>
                            <option value="Water">Water Bill</option>
                            <option value="Salaries">Staff Salaries</option>
                            <option value="Transport">Transport/Fuel</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Amount (TZS)</label>
                        <div class="input-group">
                            <input type="number" name="amount" class="form-control" placeholder="e.g. 50000" required>
                            <span class="input-group-text bg-white small fw-bold">/=</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Date</label>
                        <input type="date" name="expense_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="What was this for?"></textarea>
                    </div>
                    <button type="submit" name="add_expense" class="btn btn-record text-white w-100 py-3 shadow-sm">
                        <i class="fas fa-save me-2"></i> Save to Records
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card h-100 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Recent Outgoings</h5>
                    <span class="badge bg-light text-dark border fw-bold px-3 py-2"><?php echo count($expenses); ?> Total Items</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small text-muted">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Category</th>
                                <th>Details</th>
                                <th class="text-end pe-4">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($expenses) > 0): ?>
                                <?php foreach($expenses as $exp): ?>
                                <tr>
                                    <td class="ps-4 small text-muted">
                                        <i class="far fa-calendar-alt me-1"></i> 
                                        <?php echo date('M d, Y', strtotime($exp['expense_date'])); ?>
                                    </td>
                                    <td>
                                        <span class="badge-expense bg-danger-subtle text-danger border border-danger-subtle">
                                            <?php echo $exp['category']; ?>
                                        </span>
                                    </td>
                                    <td class="small text-dark fw-medium"><?php echo htmlspecialchars($exp['description']); ?></td>
                                    <td class="text-end pe-4 fw-bold text-dark"><?php echo number_format($exp['amount']); ?> /=</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-5 text-muted">No expenses recorded yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>