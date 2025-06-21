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

$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    echo "ไม่พบคำสั่งซื้อของคุณ";
    exit();
}

$order = $result->fetch_assoc();

$success_msg = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? '';
    $payment_date = $_POST['payment_date'] ?? null;
    $slip_file = null;

    if (empty($payment_method)) {
        $error_msg = "กรุณาเลือกวิธีการชำระเงิน";
    } elseif (empty($payment_date)) {
        $error_msg = "กรุณาระบุวันที่และเวลาชำระเงิน";
    } else {
        if (in_array($payment_method, ['transfer', 'qr'])) {
            if (isset($_FILES['slip']) && $_FILES['slip']['error'] === 0) {
                $target_dir = "uploads/slips/";
                $ext = strtolower(pathinfo($_FILES['slip']['name'], PATHINFO_EXTENSION));
                $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];

                if (!in_array($ext, $allowed_ext)) {
                    $error_msg = "ไฟล์สลิปต้องเป็น jpg, jpeg, png หรือ pdf";
                } else {
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $filename = uniqid() . "." . $ext;
                    $target_file = $target_dir . $filename;

                    if (move_uploaded_file($_FILES['slip']['tmp_name'], $target_file)) {
                        $slip_file = $filename;
                    } else {
                        $error_msg = "อัปโหลดไฟล์สลิปไม่สำเร็จ";
                    }
                }
            } else {
                $error_msg = "กรุณาอัปโหลดสลิปการชำระเงิน";
            }
        }

        if (empty($error_msg)) {
            $check_column = $conn->query("SHOW COLUMNS FROM payments LIKE 'payment_method'");
            if ($check_column->num_rows == 0) {
                die("❌ กรุณาเพิ่มคอลัมน์ payment_method ในตาราง payments");
            }

            $stmt_insert = $conn->prepare("INSERT INTO payments (order_id, payment_date, slip_file, payment_method) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("isss", $order_id, $payment_date, $slip_file, $payment_method);
            $stmt_insert->execute();

            $stmt_update = $conn->prepare("UPDATE orders SET status = 'รออนุมัติ' WHERE order_id = ?");
            $stmt_update->bind_param("i", $order_id);
            $stmt_update->execute();

            $success_msg = "✅ แจ้งชำระเงินสำเร็จ รอการอนุมัติจากร้านขายยา";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>แจ้งชำระเงิน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-4">
    <h3 class="mb-4 text-center">แจ้งชำระเงินคำสั่งซื้อเลขที่ #<?= htmlspecialchars($order_id) ?></h3>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
        <div class="text-center">
            <a href="index.php" class="btn btn-primary">กลับหน้าหลัก</a>
        </div>
    <?php else: ?>

        <?php if ($error_msg): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="card p-4 shadow bg-white" id="paymentForm">
            <div class="mb-3">
                <label for="payment_method" class="form-label fw-bold">วิธีการชำระเงิน</label>
                <select name="payment_method" id="payment_method" class="form-select" required>
                    <option value="" disabled selected>-- กรุณาเลือกวิธีการชำระเงิน --</option>
                    <option value="transfer" <?= ($_POST['payment_method'] ?? '') === 'transfer' ? 'selected' : '' ?>>โอนเงิน</option>
                    <option value="qr" <?= ($_POST['payment_method'] ?? '') === 'qr' ? 'selected' : '' ?>>QR Code</option>
                    <option value="cash" <?= ($_POST['payment_method'] ?? '') === 'cash' ? 'selected' : '' ?>>ชำระที่ร้าน</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="payment_date" class="form-label">วันที่และเวลาชำระเงิน</label>
                <input type="datetime-local" id="payment_date" name="payment_date" class="form-control" required>
            </div>

            <div class="mb-3" id="slipUploadSection" style="display: none;">
                <label for="slip" class="form-label">อัปโหลดสลิปการชำระเงิน (jpg, png, pdf)</label>
                <input type="file" id="slip" name="slip" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
            </div>

            <div class="mb-3" id="bankInfoSection" style="display: none;">
                <div class="alert alert-info">
                    <strong>โอนเงินผ่านบัญชีธนาคาร:</strong><br>
                    ธนาคาร: <strong>กสิกรไทย</strong><br>
                    ชื่อบัญชี: <strong>ร้านขายยาออนไลน์</strong><br>
                    เลขที่บัญชี: <strong>092-3-29065-6</strong><br>
                    กรุณาโอนให้ตรงตามยอดที่ระบบแจ้งและแนบสลิปการชำระเงิน
                </div>
            </div>

            <div class="mb-3" id="qrInfoSection" style="display: none;">
                <div class="alert alert-info text-center">
                    <strong>สแกน QR Code เพื่อชำระเงิน</strong><br>
                    <img src="uploads/QR.jpg" alt="QR Code" style="max-width: 250px;" class="my-2"><br>
                    <a href="uploads/QR.jpg" download="qr_payment.jpg" class="btn btn-outline-primary btn-sm my-2">📥 ดาวน์โหลด QR Code</a><br>
                    กรุณาแนบสลิปหลังชำระเงินผ่าน QR
                </div>
            </div>

            <div class="text-end">
                <a href="payment_page.php?order_id=<?= htmlspecialchars($order_id) ?>" class="btn btn-secondary">ย้อนกลับ</a>
                <button type="submit" class="btn btn-success">แจ้งชำระเงิน</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
    const paymentMethod = document.getElementById('payment_method');
    const slipSection = document.getElementById('slipUploadSection');
    const bankInfoSection = document.getElementById('bankInfoSection');
    const qrInfoSection = document.getElementById('qrInfoSection');
    const slipInput = document.getElementById('slip');

    function toggleSlipSection() {
        if (paymentMethod.value === 'transfer' || paymentMethod.value === 'qr') {
            slipSection.style.display = 'block';
            slipInput.required = true;

            if (paymentMethod.value === 'transfer') {
                bankInfoSection.style.display = 'block';
                qrInfoSection.style.display = 'none';
            } else {
                qrInfoSection.style.display = 'block';
                bankInfoSection.style.display = 'none';
            }

        } else {
            slipSection.style.display = 'none';
            bankInfoSection.style.display = 'none';
            qrInfoSection.style.display = 'none';
            slipInput.required = false;
            slipInput.value = null;
        }
    }

    paymentMethod.addEventListener('change', toggleSlipSection);
    window.addEventListener('load', () => {
        toggleSlipSection();

        const paymentInput = document.getElementById('payment_date');
        const now = new Date();
        const offset = now.getTimezoneOffset();
        now.setMinutes(now.getMinutes() - offset);
        const localDatetime = now.toISOString().slice(0, 16);
        paymentInput.max = localDatetime;

        if (!paymentInput.value) {
            paymentInput.value = localDatetime;
        }
    });
</script>

</body>
</html>
