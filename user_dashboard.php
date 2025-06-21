<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>แดชบอร์ดผู้ใช้ - <?= htmlspecialchars($username) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        /* ปรับปุ่มพิมพ์ให้เล็กลง */
        .btn-print {
            font-size: 0.9rem;
        }
    </style>
    <script>
        function printOrder(orderId) {
            // เปิดหน้าใหม่เพื่อแสดงรายงานคำสั่งซื้อพร้อมพิมพ์
            const printWindow = window.open('order_print.php?order_id=' + orderId, '_blank');
            printWindow.focus();
        }
    </script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-capsule-pill me-1"></i>ระบบสั่งจองเวชภัทฑ์</a>
        <div class="d-flex align-items-center">
            <span class="navbar-text me-3">สวัสดี, <strong><?= htmlspecialchars($username) ?></strong></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm">
            <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item active" aria-current="page">ประวัติคำสั่งซื้อ</li>
        </ol>
    </nav>

    <h3 class="mb-4"><i class="bi bi-clock-history me-1"></i>ประวัติคำสั่งซื้อของคุณ</h3>

    <?php if ($orders->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover bg-white shadow-sm rounded">
            <thead class="table-success text-center">
                <tr>
                    <th>รหัสคำสั่งซื้อ</th>
                    <th>วันที่สั่งซื้อ</th>
                    <th>สถานะ</th>
                    <th>นัดหมาย</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = $orders->fetch_assoc()): ?>
                <tr class="align-middle text-center">
                    <td>#<?= $order['order_id'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                    <td>
                        <?php
                        $status = htmlspecialchars($order['status']);
                        $badge_class = match ($status) {
                            'รอดำเนินการ' => 'warning',
                            'กำลังเตรียมยา' => 'info',
                            'พร้อมรับยา' => 'primary',
                            'สำเร็จ' => 'success',
                            'ยกเลิก' => 'danger',
                            default => 'secondary',
                        };
                        ?>
                        <span class="badge bg-<?= $badge_class ?>"><?= $status ?></span>
                    </td>
                    <td>
                        <?= $order['appointment_date'] ? date('d/m/Y H:i', strtotime($order['appointment_date'])) : '<span class="text-muted">ยังไม่กำหนด</span>' ?>
                    </td>
                    <td>
                        <a href="order_detail.php?order_id=<?= $order['order_id'] ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye"></i> ดูรายละเอียด
                        </a>
                        <button type="button" onclick="printOrder(<?= $order['order_id'] ?>)" class="btn btn-outline-secondary btn-sm btn-print ms-1">
                            <i class="bi bi-printer"></i> พิมพ์
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="alert alert-info shadow-sm">คุณยังไม่มีคำสั่งซื้อใด ๆ</div>
    <?php endif; ?>
</div>

</body>
</html>
