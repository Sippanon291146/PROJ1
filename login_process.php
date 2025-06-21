<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            // เข้าสู่ระบบสำเร็จ
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["fullname"] = $user["fullname"];

            $_SESSION['login_success'] = true;
            header("Location: index.php");
            exit;
        } else {
            // รหัสผ่านผิด
            $_SESSION['login_error'] = "รหัสผ่านไม่ถูกต้อง";
            header("Location: login.php");
            exit;
        }
    } else {
        // ไม่พบบัญชีผู้ใช้
        $_SESSION['login_error'] = "ไม่พบบัญชีผู้ใช้นี้";
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
