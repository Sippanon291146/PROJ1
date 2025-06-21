<?php
session_start();

// ลบสินค้า
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

// อัปเดตจำนวนสินค้า
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $qty) {
        $_SESSION['cart'][$product_id]['quantity'] = max(1, intval($qty));
    }
    header("Location: cart.php");
    exit();
}

// คำนวณราคารวม
$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตะกร้าสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white text-center">
            <h4><i class="bi bi-cart4 me-2"></i>ตะกร้าสินค้าของคุณ</h4>
        </div>
        <div class="card-body">

            <?php if (empty($_SESSION['cart'])): ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> ยังไม่มีสินค้าในตะกร้า
                </div>
                <div class="text-center mt-3">
                    <a href="index.php" class="btn btn-success"><i class="bi bi-box-arrow-in-left"></i> เลือกสินค้าต่อ</a>
                </div>
            <?php else: ?>
                <form method="post">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-success text-center">
                                <tr>
                                    <th>สินค้า</th>
                                    <th>ราคา</th>
                                    <th>จำนวน</th>
                                    <th>รวม</th>
                                    <th>ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                                    <tr data-price="<?= $item['price'] ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="uploads/<?= htmlspecialchars($item['img']) ?>" width="50" height="50" class="rounded me-2 border">
                                                <span><?= htmlspecialchars($item['product_name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center"><?= number_format($item['price'], 2) ?> ฿</td>
                                        <td width="100" class="text-center">
                                            <input type="number" name="quantities[<?= $id ?>]" value="<?= $item['quantity'] ?>" class="form-control text-center qty-input" min="1">
                                        </td>
                                        <td class="text-center"><span class="subtotal"><?= number_format($item['price'] * $item['quantity'], 2) ?> ฿</span></td>
                                        <td class="text-center">
                                            <a href="cart.php?remove=<?= $id ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('ลบสินค้านี้ใช่หรือไม่?')">
                                                <i class="bi bi-trash3"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-chevron-left"></i> เลือกสินค้าต่อ</a>
                        <div class="text-end">
                            <div class="mb-2 fs-5">
                                <strong>ราคารวม: <span class="text-success" id="total-price"><?= number_format($total, 2) ?> ฿</span></strong>
                            </div>
                            <div>
                                <button type="submit" name="update_cart" class="btn btn-info me-2">
                                    <i class="bi bi-arrow-repeat"></i> อัปเดตจำนวน
                                </button>
                                <a href="payment_page.php" class="btn btn-success">
                                    <i class="bi bi-credit-card-2-front"></i> ดำเนินการชำระเงิน
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript: คำนวณราคาทันทีเมื่อเปลี่ยนจำนวน -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const quantityInputs = document.querySelectorAll('.qty-input');
        const totalPriceEl = document.getElementById('total-price');

        quantityInputs.forEach(input => {
            input.addEventListener('input', function () {
                const row = input.closest('tr');
                const pricePerUnit = parseFloat(row.dataset.price);
                const quantity = parseInt(input.value) || 1;
                const subtotalEl = row.querySelector('.subtotal');
                const newSubtotal = pricePerUnit * quantity;

                subtotalEl.textContent = newSubtotal.toFixed(2) + " ฿";

                let newTotal = 0;
                document.querySelectorAll('tr[data-price]').forEach(tr => {
                    const price = parseFloat(tr.dataset.price);
                    const qty = parseInt(tr.querySelector('input[type="number"]').value) || 1;
                    newTotal += price * qty;
                });
                totalPriceEl.textContent = newTotal.toFixed(2) + " ฿";
            });
        });
    });
</script>

</body>
</html>
