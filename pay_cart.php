<?php
session_start();
include 'db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบผู้ใช้งานล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่าตะกร้าว่างหรือไม่
if (empty($_SESSION['cart'])) {
    echo "<script>alert('ตะกร้าสินค้าว่าง'); window.location='cart.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $appointment_date = $_POST['appointment_date'];
    $prescription_file = '';

    // อัปโหลดไฟล์ใบสั่งยา
    if (isset($_FILES['prescription']['name']) && $_FILES['prescription']['error'] == 0) {
        $target_dir = "uploads/prescriptions/";
        $ext = pathinfo($_FILES['prescription']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . "." . $ext;
        $target_file = $target_dir . $filename;

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['prescription']['tmp_name'], $target_file)) {
            $prescription_file = $filename;
        } else {
            echo "<script>alert('อัปโหลดไฟล์ไม่สำเร็จ'); window.history.back();</script>";
            exit();
        }
    }

    // คำนวณราคารวม
    $total_price = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    // บันทึกคำสั่งซื้อ
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, appointment_date, prescription_file, status) VALUES (?, ?, ?, ?, 'รอชำระเงิน')");
    $stmt->bind_param("idss", $user_id, $total_price, $appointment_date, $prescription_file);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // รายการสินค้า
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $item) {
        $stmt_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt_item->execute();
    }

    unset($_SESSION['cart']);
    header("Location: payment_page.php?order_id=" . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>นัดหมาย & แนบใบสั่งยา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #e8f5e9);
        }
        .form-label i {
            color: #28a745;
        }
        .card {
            border-radius: 1rem;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4><i class="bi bi-calendar-check me-2"></i>กำหนดวันนัดหมาย & แนบใบสั่งยา</h4>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="appointment_date" class="form-label">
                                <i class="bi bi-calendar3"></i> วันที่นัดหมายรับสินค้า
                            </label>
                            <input type="date" name="appointment_date" id="appointment_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="prescription" class="form-label">
                                <i class="bi bi-file-earmark-medical"></i> ใบสั่งยา (ถ้ามี)
                            </label>
                            <input type="file" name="prescription" id="prescription" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text text-muted">* รองรับไฟล์ PDF, JPG, PNG เท่านั้น</div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="cart.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> ย้อนกลับ
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> ยืนยันการสั่งจอง
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="cart.php" class="text-decoration-none text-muted">
                    <i class="bi bi-basket-fill"></i> ดูตะกร้าสินค้า
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
