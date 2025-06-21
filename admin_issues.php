<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// ดึงข้อมูลปัญหาทั้งหมด พร้อมชื่อผู้ใช้จากตาราง users (ใช้ user_id)
$sql = "SELECT issues.*, users.username 
        FROM issues 
        LEFT JOIN users ON issues.user_id = users.user_id 
        ORDER BY issues.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>รายการปัญหาที่แจ้ง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Kanit', sans-serif;
        }
        .badge {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="admin_dashboard.php">📊 Admin Dashboard</a>
        <div class="d-flex">
            <a href="admin_logout.php" class="btn btn-outline-light btn-sm">ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container">
    <h3 class="mb-3">📌 รายการปัญหาที่แจ้งเข้ามา</h3>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle bg-white">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>ผู้แจ้ง</th>
                        <th>หัวข้อ</th>
                        <th>รายละเอียด</th>
                        <th>สถานะ</th>
                        <th>วันที่แจ้ง</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['username'] ?? 'ไม่ทราบ') ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                            <td>
                                <?php
                                    $status = $row['status'];
                                    $badge = 'secondary';
                                    if ($status == 'รอรับเรื่อง') $badge = 'danger';
                                    elseif ($status == 'กำลังดำเนินการ') $badge = 'warning';
                                    elseif ($status == 'เสร็จสิ้น') $badge = 'success';
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= $status ?></span>
                            </td>
                            <td><?= date("d/m/Y H:i", strtotime($row['created_at'])) ?></td>
                            <td>
                                <form action="admin_update_issue.php" method="POST" class="d-flex gap-1">
                                    <input type="hidden" name="issue_id" value="<?= (int)$row['issue_id'] ?>">
                                    <select name="new_status" class="form-select form-select-sm">
                                        <option <?= $status == 'รอรับเรื่อง' ? 'selected' : '' ?>>รอรับเรื่อง</option>
                                        <option <?= $status == 'กำลังดำเนินการ' ? 'selected' : '' ?>>กำลังดำเนินการ</option>
                                        <option <?= $status == 'เสร็จสิ้น' ? 'selected' : '' ?>>เสร็จสิ้น</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">อัปเดต</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">ยังไม่มีการแจ้งปัญหาในระบบ</div>
    <?php endif; ?>
</div>

</body>
</html>
