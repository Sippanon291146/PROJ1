<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $issue_id = $_POST['issue_id'];
    $new_status = $_POST['new_status'];

    $stmt = $conn->prepare("UPDATE issues SET status = ? WHERE issue_id = ?");
    $stmt->bind_param("si", $new_status, $issue_id);

    if ($stmt->execute()) {
        header("Location: admin_issues.php?updated=1");
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตสถานะ";
    }
}
?>
