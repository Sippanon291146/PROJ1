<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_products.php");
    exit();
}

$product_id = intval($_GET['id']);
$error = '';
$success = '';

// ดึงข้อมูลสินค้าเดิม
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: admin_products.php");
    exit();
}

$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $product_type = $_POST['product_type'];
    $category = $_POST['category'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = $_POST['description'];

    $img = $product['img']; // เก็บชื่อไฟล์รูปเดิมไว้

    // อัปโหลดรูปภาพใหม่ถ้ามีไฟล์ส่งมาและไม่มี error
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $target_dir = "uploads/";
        $ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed_ext)) {
            $error = "ไฟล์รูปภาพต้องเป็นนามสกุล jpg, jpeg, png หรือ gif เท่านั้น";
        } else {
            // สร้างโฟลเดอร์ถ้ายังไม่มี
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $filename = uniqid() . "." . $ext;
            $target_file = $target_dir . $filename;

            if (move_uploaded_file($_FILES['img']['tmp_name'], $target_file)) {
                // ลบรูปเดิมถ้ามี
                if (!empty($product['img']) && file_exists($target_dir . $product['img'])) {
                    unlink($target_dir . $product['img']);
                }
                $img = $filename; // อัปเดตรูปใหม่
            } else {
                $error = "อัปโหลดรูปภาพไม่สำเร็จ";
            }
        }
    }

    if (empty($error)) {
        $stmt_update = $conn->prepare("UPDATE products SET product_name=?, product_type=?, category=?, price=?, stock=?, description=?, img=? WHERE product_id=?");
        // bind_param ต้องใส่ให้ถูกต้อง: s = string, d = double, i = int
        $stmt_update->bind_param("sssdissi", $product_name, $product_type, $category, $price, $stock, $description, $img, $product_id);

        if ($stmt_update->execute()) {
            $success = "แก้ไขข้อมูลสินค้าสำเร็จ";
            // โหลดข้อมูลใหม่หลังแก้ไข
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
        } else {
            $error = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>แก้ไขสินค้า | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-label {
            font-weight: 600;
        }
        .product-preview {
            max-width: 150px;
            border-radius: 0.5rem;
            border: 1px solid #ddd;
        }
        .form-section {
            background: #fff;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 0.75rem 1.5rem rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container py-4">
    <h3 class="mb-4 text-primary">
        <i class="bi bi-pencil-square me-2"></i>แก้ไขสินค้า: <?= htmlspecialchars($product['product_name']) ?>
    </h3>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="form-section">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="product_name" class="form-label">ชื่อสินค้า</label>
                <input type="text" id="product_name" name="product_name" class="form-control" required value="<?= htmlspecialchars($product['product_name']) ?>">
            </div>
            <div class="col-md-6">
                <label for="product_type" class="form-label">ประเภท</label>
                <input type="text" id="product_type" name="product_type" class="form-control" required value="<?= htmlspecialchars($product['product_type']) ?>">
            </div>
            <div class="col-md-6">
                <label for="category" class="form-label">หมวดหมู่</label>
                <input type="text" id="category" name="category" class="form-control" required value="<?= htmlspecialchars($product['category']) ?>">
            </div>
            <div class="col-md-3">
                <label for="price" class="form-label">ราคา (บาท)</label>
                <input type="number" step="0.01" id="price" name="price" class="form-control" required value="<?= htmlspecialchars($product['price']) ?>">
            </div>
            <div class="col-md-3">
                <label for="stock" class="form-label">จำนวนคงเหลือ</label>
                <input type="number" id="stock" name="stock" class="form-control" required value="<?= htmlspecialchars($product['stock']) ?>">
            </div>
            <div class="col-12">
                <label for="description" class="form-label">คำอธิบาย</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="col-md-6">
                <label for="img" class="form-label">อัปโหลดรูปใหม่ (ถ้ามี)</label>
                <input type="file" id="img" name="img" class="form-control" accept=".jpg,.jpeg,.png,.gif">
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <?php if ($product['img']): ?>
                    <img src="uploads/<?= htmlspecialchars($product['img']) ?>" alt="รูปสินค้า" class="product-preview">
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="admin_products.php" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> ย้อนกลับ
            </a>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> บันทึกการแก้ไข
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

