<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['cart'])) {
        $error = "ตะกร้าว่าง ไม่มีสินค้าให้ชำระเงิน";
    } else {
        $appointment_date = $_POST['appointment_date'] ?? date('Y-m-d', strtotime('+1 day'));

        // เริ่ม transaction เพื่อความสมบูรณ์ของข้อมูล
        $conn->begin_transaction();

        try {
            $status = 'รอชำระเงิน';
            $prescription_file = null;

            $stmt_order = $conn->prepare("INSERT INTO orders (user_id, appointment_date, status, prescription_file) VALUES (?, ?, ?, ?)");
            $stmt_order->bind_param("isss", $user_id, $appointment_date, $status, $prescription_file);
            $stmt_order->execute();
            $order_id = $stmt_order->insert_id;

            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

            foreach ($_SESSION['cart'] as $product_id => $item) {
                $quantity = $item['quantity'];
                $price = $item['price'];
                $stmt_item->bind_param("iiid", $order_id, $product_id, $quantity, $price);
                $stmt_item->execute();
            }

            $conn->commit();

            unset($_SESSION['cart']);
            header("Location: order_detail.php?order_id=$order_id");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "เกิดข้อผิดพลาดในการบันทึกคำสั่งซื้อ: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>ชำระเงิน - ระบบร้านขายยาออนไลน์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="bi bi-credit-card-2-front me-2"></i>กรุณายืนยันการชำระเงิน</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label for="appointment_date" class="form-label fw-bold">เลือกวันที่นัดหมายรับเวชภัณฑ์</label>
                            <input
                                type="date"
                                id="appointment_date"
                                name="appointment_date"
                                class="form-control"
                                required
                                min="<?= date('Y-m-d') ?>"
                                value="<?= htmlspecialchars($_POST['appointment_date'] ?? date('Y-m-d', strtotime('+1 day'))) ?>"
                            />
                        </div>

                        <div class="mb-3">
                            <h5>สรุปรายการสินค้าในตะกร้า</h5>
                            <ul class="list-group">
                                <?php
                                $total = 0;
                                if (!empty($_SESSION['cart'])):
                                    foreach ($_SESSION['cart'] as $item):
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total += $subtotal;
                                ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($item['product_name']) ?> x <?= intval($item['quantity']) ?>
                                        <span><?= number_format($subtotal, 2) ?> ฿</span>
                                    </li>
                                <?php endforeach; endif; ?>
                                <li class="list-group-item d-flex justify-content-between fw-bold bg-light">
                                    ราคารวม
                                    <span class="text-success"><?= number_format($total, 2) ?> ฿</span>
                                </li>
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold">
                            <i class="bi bi-check-circle me-2"></i> ยืนยันและชำระเงิน
                        </button>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="cart.php" class="btn btn-outline-secondary"><i class="bi bi-chevron-left"></i> กลับไปแก้ไขตะกร้า</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
