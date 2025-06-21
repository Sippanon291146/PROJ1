<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "กรุณาเข้าสู่ระบบก่อน";
    exit();
}

if (!isset($_GET['order_id'])) {
    echo "ไม่พบรหัสคำสั่งซื้อ";
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลคำสั่งซื้อของผู้ใช้คนนี้เท่านั้น
$stmt = $conn->prepare("
    SELECT o.*, u.username, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    WHERE o.order_id = ? AND o.user_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "ไม่พบคำสั่งซื้อหรือคุณไม่มีสิทธิ์เข้าถึงข้อมูลนี้";
    exit();
}

// ดึงรายการสินค้าในคำสั่งซื้อ
$stmt_items = $conn->prepare("
    SELECT oi.*, p.product_name 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>ใบสั่งซื้อ #<?= $order_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        body {
            padding: 20px;
            font-family: 'Tahoma', sans-serif;
        }
    </style>
</head>
<body>

    <div class="mb-4">
        <h2>ใบสั่งซื้อ #<?= $order_id ?></h2>
        <p><strong>ชื่อผู้สั่งซื้อ:</strong> <?= htmlspecialchars($order['username']) ?> (<?= htmlspecialchars($order['email']) ?>)</p>
        <p><strong>วันที่สั่งซื้อ:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        <p><strong>สถานะ:</strong> <?= htmlspecialchars($order['status']) ?></p>
        <p><strong>นัดหมาย:</strong> <?= $order['appointment_date'] ? date('d/m/Y H:i', strtotime($order['appointment_date'])) : 'ยังไม่กำหนด' ?></p>
    </div>

    <table class="table table-bordered">
        <thead class="table-success">
            <tr>
                <th>สินค้า</th>
                <th>จำนวน</th>
                <th>ราคา/หน่วย (บาท)</th>
                <th>ราคารวม (บาท)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            while ($item = $items->fetch_assoc()):
                $subtotal = $item['quantity'] * $item['price'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td class="text-center"><?= $item['quantity'] ?></td>
                <td class="text-end"><?= number_format($item['price'], 2) ?></td>
                <td class="text-end"><?= number_format($subtotal, 2) ?></td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="3" class="text-end"><strong>รวมทั้งหมด</strong></td>
                <td class="text-end"><strong><?= number_format($total, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <div class="mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> พิมพ์หน้านี้</button>
        <button onclick="window.close()" class="btn btn-secondary ms-2">ปิดหน้านี้</button>
    </div>

</body>
</html>
