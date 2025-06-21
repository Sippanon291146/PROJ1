<?php
include 'db.php';  // เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];

    // ตรวจสอบรหัสผ่านและยืนยันรหัสผ่าน
    if ($password !== $password_confirm) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'รหัสผ่านไม่ตรงกัน',
                text: 'กรุณากรอกรหัสผ่านให้ตรงกัน',
                confirmButtonText: 'ตกลง'
            });
        </script>";
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // ตรวจสอบชื่อผู้ใช้ซ้ำในตาราง admins
    $check = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ชื่อผู้ใช้นี้ถูกใช้แล้ว',
                text: 'กรุณาลองชื่อผู้ใช้อื่น',
                confirmButtonText: 'ตกลง'
            });
        </script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password_hash);

        if ($stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'สมัครสมาชิกผู้ดูแลระบบสำเร็จ!',
                    text: 'กรุณาเข้าสู่ระบบ',
                    confirmButtonText: 'เข้าสู่ระบบ'
                }).then(() => {
                    window.location.href = 'admin_login.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถบันทึกข้อมูลได้',
                    confirmButtonText: 'ลองใหม่อีกครั้ง'
                });
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิกผู้ดูแลระบบ | ร้านขายยาออนไลน์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="col-md-6 offset-md-3 bg-white p-4 rounded shadow">
        <h3 class="text-center mb-4">สมัครสมาชิกผู้ดูแลระบบ</h3>
        <form method="POST" action="register_admin.php">
            <div class="mb-3">
                <label>ชื่อผู้ใช้</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="mb-3">
                <label>ยืนยันรหัสผ่าน</label>
                <input type="password" name="password_confirm" class="form-control" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary w-100">สมัครสมาชิก</button>
            <div class="text-center mt-3">
                <a href="admin_login.php">มีบัญชีผู้ดูแลระบบแล้ว? เข้าสู่ระบบ</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
