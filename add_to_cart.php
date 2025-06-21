<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    // ดึงข้อมูลสินค้าเพื่อเก็บใน session
    $stmt = $conn->prepare("SELECT product_name, price, img, stock FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if ($product) {
        // ถ้ามีสินค้าในตะกร้าแล้ว เพิ่มจำนวน
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$product_id] = [
                'product_name' => $product['product_name'],
                'price' => $product['price'],
                'img' => $product['img'],
                'quantity' => 1
            ];
        }
    }
    header('Location: cart.php');
    exit();
}
?>
