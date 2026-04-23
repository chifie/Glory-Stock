<<<<<<< HEAD
<?php
session_start();
require_once 'db_connect.php';

// --- SECURITY UPDATE ---
// Allows any logged-in user to view, but you can change 'staff' to 'admin' later if needed.
if (!isset($_SESSION['user_id'])) {
    die("Access Denied: Please log in to the dashboard first.");
}

// Fetch items with low stock (5 or less)
$stmt = $pdo->query("SELECT p.*, c.name as cat_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.stock <= 5 
                     ORDER BY p.stock ASC");
$items = $stmt->fetchAll();
$total_low_items = count($items);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reorder Report | Stock Manager Pro</title>
    <style>
        body { font-family: 'Inter', -apple-system, sans-serif; color: #1e293b; padding: 40px; line-height: 1.5; background: #fff; }
        
        .brand-section { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; }
        .logo-placeholder { 
            background: #2c3e50; color: white; width: 50px; height: 50px; 
            display: flex; align-items: center; justify-content: center; 
            font-weight: 900; font-size: 20px; border-radius: 10px; margin-right: 15px;
        }
        .brand-text h1 { margin: 0; font-size: 24px; letter-spacing: -0.5px; color: #0f172a; }
        .brand-text p { margin: 0; color: #64748b; font-size: 13px; }

        .report-info { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .badge { background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 12px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 50px; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #e2e8f0; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 14px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        
        .qty-input-line { border-bottom: 1px solid #cbd5e1; width: 80px; display: inline-block; height: 20px; }
        .status-danger { color: #e11d48; font-weight: 700; }

        .footer { margin-top: 80px; display: flex; justify-content: space-between; }
        .sig-box { border-top: 1px solid #94a3b8; width: 220px; text-align: center; font-size: 12px; padding-top: 10px; color: #64748b; }

        @media print { 
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="background: #f8fafc; padding: 15px; margin: -40px -40px 40px -40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0;">
        <span style="font-size: 13px; color: #64748b;">💡 Print this list to take to the wholesaler.</span>
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Print Now</button>
    </div>

    <div class="brand-section">
        <div style="display: flex; align-items: center;">
            <div class="logo-placeholder">SM</div> 
            <div class="brand-text">
                <h1>STOCK MANAGER PRO</h1>
                <p>Inventory Intelligence Report | Dar es Salaam</p>
            </div>
        </div>
        <div style="text-align: right;">
            <p style="margin:0; font-weight: bold; color: #1e293b;">REORDER SHEET</p>
            <p style="margin:0; font-size: 12px; color: #64748b;"><?php echo date('D, d M Y'); ?></p>
        </div>
    </div>

    <div class="report-info">
        <div class="badge">⚠️ <?php echo $total_low_items; ?> Items Need Restocking</div>
        <div style="font-size: 12px; color: #64748b;">Prepared by: <?php echo htmlspecialchars($_SESSION['username']); ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Product Description</th>
                <th>Category</th>
                <th>Current Stock</th>
                <th>Order Qty</th>
                <th>Estimated Unit Cost</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_low_items > 0): ?>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td style="font-family: monospace; color: #64748b;"><?php echo $item['sku']; ?></td>
                    <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($item['cat_name'] ?? 'General'); ?></td>
                    <td><span class="status-danger"><?php echo $item['stock']; ?> units left</span></td>
                    <td><span class="qty-input-line"></span></td>
                    <td style="color: #94a3b8;"><?php echo number_format($item['price']); ?> /=</td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 50px; color: #64748b;">
                        ✅ All stock levels are healthy. Nothing to reorder!
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="sig-box">Store Manager Signature</div>
        <div class="sig-box">Authorized Approval</div>
    </div>

    <script>
        // Helpful for mobile users - tells them if the print failed
        window.onafterprint = function() {
            console.log("Print job finished or cancelled.");
        }
    </script>
</body>
=======
<?php
session_start();
require_once 'db_connect.php';

// --- SECURITY UPDATE ---
// Allows any logged-in user to view, but you can change 'staff' to 'admin' later if needed.
if (!isset($_SESSION['user_id'])) {
    die("Access Denied: Please log in to the dashboard first.");
}

// Fetch items with low stock (5 or less)
$stmt = $pdo->query("SELECT p.*, c.name as cat_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.stock <= 5 
                     ORDER BY p.stock ASC");
$items = $stmt->fetchAll();
$total_low_items = count($items);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reorder Report | Stock Manager Pro</title>
    <style>
        body { font-family: 'Inter', -apple-system, sans-serif; color: #1e293b; padding: 40px; line-height: 1.5; background: #fff; }
        
        .brand-section { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; }
        .logo-placeholder { 
            background: #2c3e50; color: white; width: 50px; height: 50px; 
            display: flex; align-items: center; justify-content: center; 
            font-weight: 900; font-size: 20px; border-radius: 10px; margin-right: 15px;
        }
        .brand-text h1 { margin: 0; font-size: 24px; letter-spacing: -0.5px; color: #0f172a; }
        .brand-text p { margin: 0; color: #64748b; font-size: 13px; }

        .report-info { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .badge { background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 12px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 50px; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #e2e8f0; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 14px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        
        .qty-input-line { border-bottom: 1px solid #cbd5e1; width: 80px; display: inline-block; height: 20px; }
        .status-danger { color: #e11d48; font-weight: 700; }

        .footer { margin-top: 80px; display: flex; justify-content: space-between; }
        .sig-box { border-top: 1px solid #94a3b8; width: 220px; text-align: center; font-size: 12px; padding-top: 10px; color: #64748b; }

        @media print { 
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="background: #f8fafc; padding: 15px; margin: -40px -40px 40px -40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0;">
        <span style="font-size: 13px; color: #64748b;">💡 Print this list to take to the wholesaler.</span>
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Print Now</button>
    </div>

    <div class="brand-section">
        <div style="display: flex; align-items: center;">
            <div class="logo-placeholder">SM</div> 
            <div class="brand-text">
                <h1>STOCK MANAGER PRO</h1>
                <p>Inventory Intelligence Report | Dar es Salaam</p>
            </div>
        </div>
        <div style="text-align: right;">
            <p style="margin:0; font-weight: bold; color: #1e293b;">REORDER SHEET</p>
            <p style="margin:0; font-size: 12px; color: #64748b;"><?php echo date('D, d M Y'); ?></p>
        </div>
    </div>

    <div class="report-info">
        <div class="badge">⚠️ <?php echo $total_low_items; ?> Items Need Restocking</div>
        <div style="font-size: 12px; color: #64748b;">Prepared by: <?php echo htmlspecialchars($_SESSION['username']); ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Product Description</th>
                <th>Category</th>
                <th>Current Stock</th>
                <th>Order Qty</th>
                <th>Estimated Unit Cost</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_low_items > 0): ?>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td style="font-family: monospace; color: #64748b;"><?php echo $item['sku']; ?></td>
                    <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($item['cat_name'] ?? 'General'); ?></td>
                    <td><span class="status-danger"><?php echo $item['stock']; ?> units left</span></td>
                    <td><span class="qty-input-line"></span></td>
                    <td style="color: #94a3b8;"><?php echo number_format($item['price']); ?> /=</td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 50px; color: #64748b;">
                        ✅ All stock levels are healthy. Nothing to reorder!
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="sig-box">Store Manager Signature</div>
        <div class="sig-box">Authorized Approval</div>
    </div>

    <script>
        // Helpful for mobile users - tells them if the print failed
        window.onafterprint = function() {
            console.log("Print job finished or cancelled.");
        }
    </script>
</body>
>>>>>>> f1f996b39031b13ecb1c00a432fd157d25e86313
</html>