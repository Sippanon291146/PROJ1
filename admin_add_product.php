<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['product_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    $type = $_POST['product_type'];

    // อัปโหลดรูป
    $img_name = $_FILES['img']['name'];
    $img_tmp = $_FILES['img']['tmp_name'];
    $img_path = "uploads/" . time() . "_" . basename($img_name);

    if (move_uploaded_file($img_tmp, $img_path)) {
        $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, stock, category, product_type, img) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdisss", $name, $desc, $price, $stock, $category, $type, $img_path);

        if ($stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'เพิ่มสินค้าเรียบร้อย',
                    text: 'ระบบได้เพิ่มสินค้าใหม่แล้ว',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location.href = 'admin_products.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเพิ่มสินค้าได้'
                });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'อัปโหลดรูปไม่สำเร็จ',
                text: 'กรุณาลองใหม่'
            });
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มสินค้า | ผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php">Admin Dashboard</a>
        <a href="admin_logout.php" class="btn btn-outline-light btn-sm">ออกจากระบบ</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="bg-white p-4 rounded shadow col-md-8 offset-md-2">
        <h3 class="mb-4">➕ เพิ่มสินค้าใหม่</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">ชื่อสินค้า</label>
                <input type="text" name="product_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">คำอธิบาย</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">ราคา (บาท)</label>
                <input type="number" name="price" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label class="form-label">จำนวนสินค้าในสต็อก</label>
                <input type="number" name="stock" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">หมวดหมู่</label>
                <input type="text" name="category" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">ประเภทสินค้า</label>
                <input type="text" name="product_type" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">รูปสินค้า</label>
                <input type="file" name="img" accept="image/*" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">บันทึกสินค้า</button>
        </form>
    </div>
</div>

</body>
</html>
