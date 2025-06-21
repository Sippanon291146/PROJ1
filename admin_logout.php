<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8" />
<title>ออกจากระบบผู้ดูแล</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  body, html {
    height: 100%;
    margin: 0;
    background: #343a40;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #fff;
  }
</style>
</head>
<body>

<script>
Swal.fire({
    icon: 'success',
    title: 'ออกจากระบบผู้ดูแลเรียบร้อย',
    text: 'กำลังพาไปยังหน้าเข้าสู่ระบบ...',
    timer: 2500,
    timerProgressBar: true,
    showConfirmButton: true,
    confirmButtonText: 'ไปยังหน้าเข้าสู่ระบบ',
    background: '#343a40',
    color: '#fff',
    allowOutsideClick: false,
    allowEscapeKey: false,
    willClose: () => {
        window.location.href = 'admin_login.php';
    }
}).then((result) => {
    if (result.isConfirmed) {
        window.location.href = 'admin_login.php';
    }
});
</script>

</body>
</html>
