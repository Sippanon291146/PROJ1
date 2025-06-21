<?php
// ตั้ง Timezone ให้ตรงกับประเทศไทย
date_default_timezone_set('Asia/Bangkok');

session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    echo "ไม่พบคำสั่งซื้อ";
    exit();
}

$order_id = intval($_GET['order_id']);

// ดึงข้อมูลคำสั่งซื้อและผู้ใช้
$stmt = $conn->prepare("
    SELECT o.*, u.username, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    WHERE o.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "ไม่พบคำสั่งซื้อ";
    exit();
}

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

// ดึงข้อมูลการชำระเงินล่าสุด
$stmt_pay = $conn->prepare("SELECT * FROM payments WHERE order_id = ? ORDER BY payment_date DESC LIMIT 1");
$stmt_pay->bind_param("i", $order_id);
$stmt_pay->execute();
$payment = $stmt_pay->get_result()->fetch_assoc();

// อัพเดตสถานะคำสั่งซื้อและสถานะชำระเงิน
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'] ?? $order['status'];
    $new_payment_status = $_POST['payment_status'] ?? $order['payment_status'] ?? null;
    $appointment_date = !empty($_POST['appointment_date']) ? $_POST['appointment_date'] : null;

    // อัพเดต orders
    $stmt_update = $conn->prepare("UPDATE orders SET status = ?, appointment_date = ?, payment_status = ? WHERE order_id = ?");
    $stmt_update->bind_param("sssi", $new_status, $appointment_date, $new_payment_status, $order_id);
    $stmt_update->execute();

    header("Location: admin_order_detail.php?order_id=" . $order_id . "&saved=1");
    exit();
}

$appointmentFormatted = '';
if (!empty($order['appointment_date'])) {
    $appointmentFormatted = date('Y-m-d\TH:i', strtotime($order['appointment_date']));
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>จัดการคำสั่งซื้อ #<?= $order_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php">Admin Dashboard</a>
        <div>
            <span class="navbar-text me-3">สวัสดี, <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
            <a href="admin_logout.php" class="btn btn-outline-light btn-sm">ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h3>คำสั่งซื้อหมายเลข #<?= $order_id ?></h3>
    <p><strong>ผู้สั่งซื้อ:</strong> <?= htmlspecialchars($order['username']) ?> (<?= htmlspecialchars($order['email']) ?>)</p>
    <p><strong>วันที่สั่งซื้อ:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
    <p><strong>สถานะปัจจุบัน:</strong> <?= htmlspecialchars($order['status']) ?></p>
    <p><strong>สถานะชำระเงิน:</strong> <?= htmlspecialchars($order['payment_status'] ?? '-') ?></p>

    <h5>รายการสินค้า</h5>
    <table class="table table-bordered bg-white shadow-sm">
        <thead>
            <tr>
                <th>สินค้า</th>
                <th>จำนวน</th>
                <th>ราคา/หน่วย</th>
                <th>ราคารวม</th>
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
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'], 2) ?> บาท</td>
                    <td><?= number_format($subtotal, 2) ?> บาท</td>
                </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="3" class="text-end"><strong>รวมทั้งหมด</strong></td>
                <td><strong><?= number_format($total, 2) ?> บาท</strong></td>
            </tr>
        </tbody>
    </table>

    <?php if ($payment): ?>
    <h5>ข้อมูลแจ้งชำระเงิน</h5>
    <p><strong>จำนวนเงินที่ชำระ:</strong> <?= number_format($payment['amount'], 2) ?> บาท</p>
    <p><strong>วันที่แจ้งชำระ:</strong> <?= date('d/m/Y H:i', strtotime($payment['payment_date'])) ?></p>
    <p><strong>สถานะการชำระเงินล่าสุดในระบบแจ้งชำระ:</strong> <?= isset($payment['payment_status']) ? htmlspecialchars($payment['payment_status']) : '-' ?></p>
    <?php if (!empty($payment['slip_file'])): ?>
        <p><a href="uploads/slips/<?= htmlspecialchars($payment['slip_file']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">ดูสลิป</a></p>
    <?php endif; ?>
    <?php else: ?>
    <p><em>ยังไม่มีการแจ้งชำระเงิน</em></p>
    <?php endif; ?>

    <form method="post" class="mt-4 card p-4 shadow-sm bg-white">
        <div class="mb-3">
            <label for="status" class="form-label">เปลี่ยนสถานะคำสั่งซื้อ</label>
            <select name="status" id="status" class="form-select" required>
                <option value="รอชำระเงิน" <?= $order['status'] == 'รอชำระเงิน' ? 'selected' : '' ?>>รอชำระเงิน</option>
                <option value="รออนุมัติ" <?= $order['status'] == 'รออนุมัติ' ? 'selected' : '' ?>>รออนุมัติ</option>
                <option value="อนุมัติ" <?= $order['status'] == 'อนุมัติ' ? 'selected' : '' ?>>อนุมัติ</option>
                <option value="ส่งแล้ว" <?= $order['status'] == 'ส่งแล้ว' ? 'selected' : '' ?>>ส่งแล้ว</option>
                <option value="ยกเลิก" <?= $order['status'] == 'ยกเลิก' ? 'selected' : '' ?>>ยกเลิก</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="payment_status" class="form-label">สถานะชำระเงิน</label>
            <select name="payment_status" id="payment_status" class="form-select" required>
                <option value="รอดำเนินการ" <?= ($order['payment_status'] ?? '') == 'รอดำเนินการ' ? 'selected' : '' ?>>รอดำเนินการ</option>
                <option value="ตรวจสอบแล้ว" <?= ($order['payment_status'] ?? '') == 'ตรวจสอบแล้ว' ? 'selected' : '' ?>>ตรวจสอบแล้ว</option>
                <option value="ชำระเงินเรียบร้อย" <?= ($order['payment_status'] ?? '') == 'ชำระเงินเรียบร้อย' ? 'selected' : '' ?>>ชำระเงินเรียบร้อย</option>
                <option value="ปฏิเสธ" <?= ($order['payment_status'] ?? '') == 'ปฏิเสธ' ? 'selected' : '' ?>>ปฏิเสธ</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="appointment_date" class="form-label">นัดหมายรับเวชภัณฑ์ (ถ้ามี)</label>
            <input type="datetime-local" id="appointment_date" name="appointment_date" class="form-control" value="<?= $appointmentFormatted ?>" />
        </div>

        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
        <a href="admin_orders.php" class="btn btn-secondary">กลับไป</a>
    </form>
</div>

<?php if (isset($_GET['saved'])): ?>
<script>
Swal.fire({
    title: 'บันทึกเรียบร้อย!',
    text: 'ข้อมูลคำสั่งซื้อถูกอัปเดตแล้ว',
    icon: 'success',
    confirmButtonText: 'ตกลง'
});
</script>
<?php endif; ?>

</body>
</html>
