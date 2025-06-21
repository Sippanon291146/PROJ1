<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['order_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit();
}

$order_id = intval($data['order_id']);
$status = $data['status'] ?? null;
$payment_status = $data['payment_status'] ?? null;
$appointment_date = $data['appointment_date'] ?? null;

// Validate (ถ้าต้องการเพิ่มตรวจสอบค่า)

// Prepare update query
$stmt = $conn->prepare("UPDATE orders SET status = ?, payment_status = ?, appointment_date = ? WHERE order_id = ?");
$stmt->bind_param("sssi", $status, $payment_status, $appointment_date, $order_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'อัพเดตข้อมูลเรียบร้อย']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'ไม่สามารถอัพเดตข้อมูลได้']);
}
