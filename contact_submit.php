<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = "ข้อความจากผู้ใช้ " . ($_SESSION['username'] ?? 'ไม่ระบุ');
    $description = trim($_POST['message']);

    if (empty($description)) {
        $_SESSION['contact_result'] = [
            'status' => 'warning',
            'title' => 'กรอกข้อมูลไม่ครบ',
            'message' => 'กรุณากรอกข้อความของคุณด้วย'
        ];
        header("Location: contact.php");
        exit();
    }

    // เตรียม INSERT ลง issues
    $stmt = $conn->prepare("INSERT INTO issues (user_id, title, description, status, created_at) VALUES (?, ?, ?, 'รอรับเรื่อง', NOW())");
    $stmt->bind_param("iss", $user_id, $title, $description);

    if ($stmt->execute()) {
        $_SESSION['contact_result'] = [
            'status' => 'success',
            'title' => 'ส่งข้อความสำเร็จ',
            'message' => 'ระบบได้รับข้อความของคุณแล้ว'
        ];
    } else {
        $_SESSION['contact_result'] = [
            'status' => 'error',
            'title' => 'เกิดข้อผิดพลาด',
            'message' => 'ไม่สามารถบันทึกข้อความได้ กรุณาลองใหม่'
        ];
    }

    header("Location: contact.php");
    exit();
}
?>
