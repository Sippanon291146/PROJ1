<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// รายการสถานะคำสั่งซื้อ
$status_list = ['รอชำระเงิน', 'รออนุมัติ', 'อนุมัติ', 'ส่งแล้ว'];
$status_colors = [
    'รอชำระเงิน' => 'bg-secondary',
    'รออนุมัติ' => 'bg-warning text-dark',
    'อนุมัติ' => 'bg-success',
    'ส่งแล้ว' => 'bg-info'
];
$status_icons = [
    'รอชำระเงิน' => 'bi-cash',
    'รออนุมัติ' => 'bi-clock-history',
    'อนุมัติ' => 'bi-check-circle',
    'ส่งแล้ว' => 'bi-truck'
];
$status_counts = [];
$status_sums = []; // เก็บยอดเงินรวมของแต่ละสถานะ

foreach ($status_list as $status) {
    // นับจำนวนคำสั่งซื้อ
    $stmt = $conn->prepare("SELECT COUNT(*) AS count, COALESCE(SUM(total_price), 0) AS sum_amount FROM orders WHERE status = ?");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $status_counts[$status] = $row['count'];
    $status_sums[$status] = $row['sum_amount'];
}

// จำนวนปัญหาที่ยังไม่ถูกดำเนินการ
$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM issues WHERE status = 'รอรับเรื่อง'");
$stmt->execute();
$result = $stmt->get_result();
$issue_count = $result->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>แดชบอร์ดผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Kanit', sans-serif;
        }
        .card-title i {
            margin-right: 6px;
        }
        .amount {
            font-size: 1rem;
            margin-top: -8px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="admin_dashboard.php">📊 Admin Dashboard</a>
        <div class="d-flex">
            <span class="navbar-text text-white me-3">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
            <a href="admin_logout.php" class="btn btn-outline-light btn-sm">ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">ภาพรวมคำสั่งซื้อ</h3>
        <div>
            <a href="admin_users.php" class="btn btn-outline-primary btn-sm me-2">
                <i class="bi bi-people"></i> จัดการลูกค้า
            </a>
            <a href="admin_products.php" class="btn btn-outline-success btn-sm">
                <i class="bi bi-box-seam"></i> จัดการสินค้า
            </a>
        </div>
    </div>

    <div class="row">
        <?php foreach ($status_counts as $status => $count): ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-white <?= $status_colors[$status] ?> shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">
                            <i class="bi <?= $status_icons[$status] ?>"></i> <?= $status ?>
                        </h5>
                        <p class="display-6 fw-bold"><?= $count ?></p>
                        <p class="amount">ยอดเงินรวม: <strong><?= number_format($status_sums[$status], 2) ?> บาท</strong></p>
                        <a href="admin_orders.php?status=<?= urlencode($status) ?>" class="btn btn-light btn-sm mt-2">
                            ดูรายละเอียด
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- การ์ดปัญหาที่แจ้ง -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-white bg-danger shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">
                        <i class="bi bi-exclamation-triangle"></i> รับเรื่องปัญหา
                    </h5>
                    <p class="display-6 fw-bold"><?= $issue_count ?></p>
                    <a href="admin_issues.php" class="btn btn-light btn-sm mt-2">
                        ดูรายละเอียด
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
