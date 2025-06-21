<?php
session_start();
include 'db.php';

if (isset($_SESSION["user_id"])) {
    $uid = $_SESSION["user_id"];

    // อัปเดตให้คำสั่งซื้อที่แจ้งเตือนไปแล้วเปลี่ยน is_notified = 1
    $stmt = $conn->prepare("UPDATE orders SET is_notified = 1 WHERE user_id = ? AND is_notified = 0");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
}
?>
