<?php
session_start();
include 'db.php';

// รับค่าการค้นหาและหมวดหมู่จาก GET
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// สร้าง SQL แบบไดนามิก
$params = [];
$where = [];

if (!empty($search)) {
    $where[] = "product_name LIKE ?";
    $params[] = "%$search%";
}
if (!empty($category)) {
    $where[] = "category = ?";
    $params[] = $category;
}

$where_sql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";
$sql = "SELECT * FROM products $where_sql ORDER BY created_at DESC";

// ใช้ prepared statement ถ้ามี parameter
if (count($params) > 0) {
    $stmt = $conn->prepare($sql);
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

// ดึงหมวดหมู่ทั้งหมด
$category_sql = "SELECT DISTINCT category FROM products ORDER BY category ASC";
$category_result = $conn->query($category_sql);

// ดึงจำนวนตะกร้าและแจ้งเตือน
$cart_count = 0;
$notification_count = 0;

if (isset($_SESSION["user_id"])) {
    $uid = $_SESSION["user_id"];

    $stmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $cart_count = $res['count'] ?? 0;

    $stmt_notify = $conn->prepare("SELECT COUNT(*) as notify_count FROM orders WHERE user_id = ? AND is_notified = 0");
    $stmt_notify->bind_param("i", $uid);
    $stmt_notify->execute();
    $res_notify = $stmt_notify->get_result()->fetch_assoc();
    $notification_count = $res_notify['notify_count'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบสั่งจองเวชภัณฑ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f8f9fa; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: 0.2s; }
        .card:hover { transform: translateY(-5px); }
        .card-img-top { height: 200px; object-fit: contain; background: #fff; }
        .btn-add { background-color: #198754; font-weight: bold; color: white; }
        .btn-outstock { background-color: #dc3545; color: white; font-weight: bold; }
        .cart-badge { position: absolute; top: 8px; right: 10px; background: red; color: white; border-radius: 50%; padding: 2px 7px; font-size: 0.75rem; font-weight: bold; }
        footer { background: #f1f1f1; font-size: 0.95rem; color: #333; margin-top: 60px; }
        footer a { color: #007bff; text-decoration: none; }
        footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">ระบบสั่งจองเวชภัณฑ์</a>
        <ul class="navbar-nav ms-auto align-items-center">
            <?php if (isset($_SESSION["user_id"])): ?>
                <li class="nav-item me-3">
                    <a class="nav-link" href="user_dashboard.php">สวัสดี, <?= htmlspecialchars($_SESSION["fullname"]) ?></a>
                </li>
                <li class="nav-item position-relative me-3">
                    <a class="nav-link" href="user_dashboard.php">
                        <i class="bi bi-bell-fill" style="font-size: 1.3rem; color:<?= $notification_count > 0 ? '#ffc107' : '#6c757d' ?>;"></i>
                        <?php if ($notification_count > 0): ?>
                            <span class="cart-badge"><?= $notification_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item position-relative me-3">
                    <a class="nav-link" href="cart.php">
                        <i class="bi bi-cart4" style="font-size:1.3rem;"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-badge"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link btn btn-outline-secondary px-3" href="contact.php">
                        <i class="bi bi-headset"></i> ติดต่อผู้ดูแลระบบ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-danger px-3" href="logout.php">ออกจากระบบ</a>
                </li>
            <?php else: ?>
                <li class="nav-item me-3">
                    <a class="nav-link btn btn-outline-primary px-3" href="login.php">เข้าสู่ระบบ</a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link btn btn-primary px-3 text-white" href="register.php">สมัครสมาชิก</a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link btn btn-outline-secondary px-3" href="contact.php">
                        <i class="bi bi-headset"></i> ติดต่อผู้ดูแลระบบ
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4 text-primary">รายการสินค้า</h2>

    <!-- ฟอร์มค้นหาและกรองหมวดหมู่ -->
    <form method="GET" class="row mb-4">
        <div class="col-md-4 mb-2">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาสินค้า..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-4 mb-2">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="">-- หมวดหมู่ทั้งหมด --</option>
                <?php
                $category_result->data_seek(0); // reset pointer
                while($cat = $category_result->fetch_assoc()):
                ?>
                    <option value="<?= htmlspecialchars($cat['category']) ?>" <?= ($cat['category'] == $category) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> ค้นหา</button>
        </div>
    </form>

    <!-- แสดงสินค้า -->
    <div class="row" id="productList">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="uploads/<?= htmlspecialchars($row['img']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['product_name']) ?>" />
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($row['product_name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
                            <p><strong>ราคา:</strong> <?= number_format($row['price'], 2) ?> บาท</p>
                            <form method="POST" action="add_to_cart.php" class="mt-auto">
                                <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>" />
                                <button type="submit" class="btn <?= $row['stock'] > 0 ? 'btn-add' : 'btn-outstock' ?> w-100" <?= $row['stock'] <= 0 ? 'disabled' : '' ?>>
                                    <?= $row['stock'] > 0 ? 'เพิ่มลงตะกร้า' : 'สินค้าหมด' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted">
                <p>ไม่พบสินค้าที่คุณค้นหา</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- แจ้งเตือนการอัปเดตคำสั่งซื้อ -->
<?php if ($notification_count > 0): ?>
<script>
Swal.fire({
    title: "มีการอัปเดตคำสั่งซื้อ",
    text: "ตรวจสอบสถานะล่าสุดของคำสั่งซื้อของคุณ",
    icon: "info",
    confirmButtonText: "ดูคำสั่งซื้อ",
    confirmButtonColor: "#3085d6"
}).then((result) => {
    if (result.isConfirmed) {
        fetch("notify_update.php", { method: "POST" }).then(() => {
            window.location.href = "user_dashboard.php";
        });
    }
});
</script>
<?php endif; ?>

<!-- ส่วนติดต่อผู้ดูแลระบบ -->
<!--<footer class="py-4 border-top">-->
    <!--<div class="container text-center">
        <p class="mb-1 fw-bold">📞 ติดต่อผู้ดูแลระบบ</p>
        <p class="mb-1">อีเมล: <a href="mailto:support@pharmacy.com">support@pharmacy.com</a></p>
        <p class="mb-0">โทร: 098-765-4321 (เวลาทำการ 09:00 - 17:00 น.)</p>
    </div>-->
<!--</footer>-->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
