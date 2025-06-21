<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// ลบสินค้า
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: admin_products.php");
    exit();
}

// ดึงรายการสินค้า
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php">Admin Dashboard</a>
        <a href="admin_logout.php" class="btn btn-outline-light btn-sm">ออกจากระบบ</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>📦 จัดการสินค้า</h3>
        <a href="admin_add_product.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> เพิ่มสินค้า
        </a>
    </div>

    <div class="table-responsive bg-white shadow-sm rounded">
        <table class="table table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>ภาพ</th>
                    <th>ชื่อสินค้า</th>
                    <th>ประเภท</th>
                    <th>หมวดหมู่</th>
                    <th>ราคา</th>
                    <th>คงเหลือ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="uploads/<?= htmlspecialchars($row['img']) ?>" class="product-img rounded" alt=""></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['product_type']) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td><?= number_format($row['price'], 2) ?> บาท</td>
                    <td><?= $row['stock'] ?></td>
                    <td>
                        <a href="admin_edit_product.php?id=<?= $row['product_id'] ?>" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-square"></i> แก้ไข
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $row['product_id'] ?>)">
                            <i class="bi bi-trash"></i> ลบ
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'ยืนยันการลบสินค้า?',
        text: 'คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'admin_products.php?delete=' + id;
        }
    });
}
</script>

</body>
</html>
