<?php
session_start();
if (!isset($_SESSION['user_id'])) exit;
require_once 'db_connect.php';

// Get the last sale ID or a specific ID from the URL
$sale_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT s.*, p.name as p_name, p.price as p_price 
                       FROM sales s 
                       JOIN products p ON s.product_id = p.id 
                       WHERE s.id = ?");
$stmt->execute([$sale_id]);
$sale = $stmt->fetch();

if (!$sale) { echo "Receipt not found."; exit; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GloryStock_Receipt_<?php echo $sale_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=JetBrains+Mono&display=swap');

        body { background-color: #f1f5f9; font-family: 'Inter', sans-serif; }
        
        /* Premium Receipt Card */
        .receipt-card { 
            background: white; 
            width: 90mm; 
            margin: 40px auto; 
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            position: relative;
        }

        /* Top Color Accent */
        .receipt-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .receipt-logo {
            height: 60px;
            width: auto;
            margin-bottom: 12px;
            background: white;
            padding: 8px;
            border-radius: 12px;
        }

        .brand-title {
            font-weight: 800;
            letter-spacing: 2px;
            font-size: 1.5rem;
            margin: 0;
        }

        .receipt-body { padding: 25px; }

        /* Meta Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 20px;
        }

        .info-label { font-weight: 600; text-transform: uppercase; color: #94a3b8; display: block; }
        .info-value { color: #1e293b; font-weight: 700; }

        /* Modern Table */
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .item-table th { 
            border-bottom: 2px solid #f1f5f9; 
            padding: 10px 0; 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            color: #94a3b8; 
        }
        .item-table td { padding: 12px 0; font-size: 0.9rem; }
        
        .total-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            margin-top: 10px;
        }

        .grand-total {
            font-family: 'JetBrains+Mono', monospace;
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
        }

        .footer-decor {
            text-align: center;
            padding: 20px;
            font-size: 0.75rem;
            color: #94a3b8;
            border-top: 1px dashed #e2e8f0;
        }

        /* Zig-Zag Edge Effect (Bottom) */
        .receipt-card::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background: linear-gradient(-45deg, #f1f5f9 5px, transparent 0), 
                        linear-gradient(45deg, #f1f5f9 5px, transparent 0);
            background-size: 10px 10px;
        }

        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            .receipt-card { 
                box-shadow: none; 
                margin: 0 auto; 
                width: 100%;
                border: 1px solid #f1f5f9;
            }
        }
    </style>
</head>
<body>

<div class="no-print text-center mt-5">
    <button onclick="window.print()" class="btn btn-dark shadow rounded-pill px-5 py-2 fw-bold">
        <i class="fas fa-print me-2"></i> PRINT RECEIPT
    </button>
    <div class="mt-3">
        <a href="pos.php" class="text-decoration-none text-muted small">
            <i class="fas fa-arrow-left me-1"></i> Back to POS Dashboard
        </a>
    </div>
</div>

<div class="receipt-card">
    <div class="receipt-header">
        <img src="logo.png" alt="GloryStock" class="receipt-logo">
        <h2 class="brand-title">GLORYSTOCK</h2>
        <div class="small opacity-75">Inventory Management System</div>
    </div>

    <div class="receipt-body">
        <div class="info-grid">
            <div>
                <span class="info-label">Receipt Number</span>
                <span class="info-value">#GS-<?php echo sprintf("%05d", $sale_id); ?></span>
            </div>
            <div class="text-end">
                <span class="info-label">Date & Time</span>
                <span class="info-value"><?php echo date('M d, Y | H:i', strtotime($sale['sale_date'])); ?></span>
            </div>
            <div>
                <span class="info-label">Served By</span>
                <span class="info-value"><?php echo strtoupper(htmlspecialchars($_SESSION['username'])); ?></span>
            </div>
            <div class="text-end">
                <span class="info-label">Contact Store</span>
                <span class="info-value">0617008046</span>
            </div>
        </div>

        <table class="item-table">
            <thead>
                <tr>
                    <th class="text-start">Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($sale['p_name']); ?></td>
                    <td class="text-center text-muted">x<?php echo $sale['quantity']; ?></td>
                    <td class="text-end fw-bold"><?php echo number_format($sale['total_price']); ?> /=</td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small text-muted fw-bold">SUBTOTAL</span>
                <span class="small fw-bold"><?php echo number_format($sale['total_price']); ?> /=</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small text-muted fw-bold">TAX (VAT 0%)</span>
                <span class="small fw-bold">0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center border-top pt-2">
                <span class="fw-800 text-dark">GRAND TOTAL</span>
                <span class="grand-total text-primary"><?php echo number_format($sale['total_price']); ?> /=</span>
            </div>
        </div>
    </div>

    <div class="footer-decor">
        <div class="fw-bold text-dark mb-1">Thank you for your business!</div>
        <div>Please visit us again for more quality stock.</div>
        <div class="mt-3 text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 10px;">
            0617008046 • Dar es Salaam • GloryStock
        </div>
    </div>
</div>

</body>
</html>