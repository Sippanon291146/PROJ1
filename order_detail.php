<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    echo "ไม่พบคำสั่งซื้อ";
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

// ตรวจสอบคำสั่งซื้อของผู้ใช้
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    echo "ไม่พบคำสั่งซื้อของคุณ";
    exit();
}

$order = $result->fetch_assoc();

// ดึงรายการสินค้า
$stmt_items = $conn->prepare("
    SELECT oi.*, p.product_name 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();

// ดึงข้อมูลแจ้งชำระเงิน (ถ้ามี)
$stmt_pay = $conn->prepare("SELECT * FROM payments WHERE order_id = ? ORDER BY payment_date DESC LIMIT 1");
$stmt_pay->bind_param("i", $order_id);
$stmt_pay->execute();
$payment = $stmt_pay->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>รายละเอียดคำสั่งซื้อ #<?= $order_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-4">
    <h3 class="mb-4 text-center">รายละเอียดคำสั่งซื้อเลขที่ #<?= $order_id ?></h3>

    <div class="card p-4 mb-4 shadow-sm">
        <p><strong>วันที่สั่งซื้อ:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        <p><strong>สถานะคำสั่งซื้อ:</strong> <?= htmlspecialchars($order['status']) ?></p>
        <p><strong>วันที่นัดหมายรับสินค้า:</strong> <?= $order['appointment_date'] ? date('d/m/Y', strtotime($order['appointment_date'])) : '-' ?></p>

        <?php if ($order['prescription_file']): ?>
            <p><strong>ใบสั่งยา:</strong> 
                <a href="uploads/prescriptions/<?= htmlspecialchars($order['prescription_file']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                    ดูไฟล์ใบสั่งยา
                </a>
            </p>
        <?php endif; ?>
    </div>

    <table class="table table-bordered bg-white shadow-sm">
        <thead class="table-light">
            <tr>
                <th>สินค้า</th>
                <th>ราคา/ชิ้น (บาท)</th>
                <th>จำนวน</th>
                <th>รวม (บาท)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            while ($item = $items->fetch_assoc()):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($subtotal, 2) ?></td>
                </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="3" class="text-end"><strong>ราคารวมทั้งหมด</strong></td>
                <td><strong><?= number_format($total, 2) ?> บาท</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="card p-3 bg-white shadow-sm mt-4">
        <h5>ข้อมูลแจ้งชำระเงิน</h5>
        <?php if ($payment): ?>
            <p><strong>วันที่แจ้งชำระเงิน:</strong> <?= date('d/m/Y H:i', strtotime($payment['payment_date'])) ?></p>
            <p><strong>สลิปชำระเงิน:</strong> 
                <a href="uploads/slips/<?= htmlspecialchars($payment['slip_file']) ?>" target="_blank" class="btn btn-outline-success btn-sm">
                    ดูสลิป
                </a>
            </p>
        <?php else: ?>
            <p>ยังไม่มีการแจ้งชำระเงิน</p>
        <?php endif; ?>
    </div>

    <div class="mt-4 text-center">
        <?php if ($order['status'] == 'รอชำระเงิน'): ?>
            <a href="payment_notify.php?order_id=<?= $order_id ?>" class="btn btn-primary btn-lg">แจ้งชำระเงิน</a>
        <?php elseif ($order['status'] == 'รออนุมัติ'): ?>
            <span class="badge bg-warning text-dark fs-5">รอการอนุมัติจากร้านยา</span>
        <?php elseif ($order['status'] == 'อนุมัติ'): ?>
            <span class="badge bg-success fs-5">อนุมัติเรียบร้อย</span>
        <?php else: ?>
            <span class="badge bg-secondary fs-5"><?= htmlspecialchars($order['status']) ?></span>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
