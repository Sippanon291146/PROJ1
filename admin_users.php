<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// เพิ่มแอดมินใหม่
$error = "";
if (isset($_POST['add_admin'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $fullname = trim($_POST['fullname']);

    // ตรวจสอบว่าชื่อผู้ใช้ซ้ำไหม
    $stmt = $conn->prepare("SELECT admin_id FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "ชื่อผู้ใช้นี้ถูกใช้งานแล้ว กรุณาใช้ชื่ออื่น";
    } else {
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admins (username, password, fullname) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hash_password, $fullname);
        $stmt->execute();
        header("Location: admin_users.php");
        exit();
    }
}

// ลบผู้ใช้
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: admin_users.php");
    exit();
}

// อัปเดตข้อมูลผู้ใช้ (แก้ไข)
if (isset($_POST['edit_user'])) {
    $user_id = intval($_POST['user_id']);
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, phone = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $fullname, $email, $phone, $user_id);
    $stmt->execute();

    header("Location: admin_users.php");
    exit();
}

// ดึงข้อมูลผู้ใช้ทั้งหมด
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>จัดการลูกค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php">Admin Dashboard</a>
        <a href="admin_logout.php" class="btn btn-outline-light btn-sm">ออกจากระบบ</a>
    </div>
</nav>

<div class="container mt-4">
    <h3 class="mb-3">📋 จัดการข้อมูลลูกค้า</h3>

    <div class="mb-3">
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAdminModal">
        <i class="bi bi-person-plus"></i> เพิ่มแอดมินคนใหม่
      </button>
    </div>

    <div class="table-responsive bg-white shadow-sm rounded">
        <table class="table table-striped align-middle">
            <thead class="table-primary">
                <tr>
                    <th>ชื่อผู้ใช้</th>
                    <th>ชื่อ - นามสกุล</th>
                    <th>อีเมล</th>
                    <th>เบอร์โทร</th>
                    <th>วันที่สมัคร</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editUserModal"
                                data-user_id="<?= $row['user_id'] ?>"
                                data-fullname="<?= htmlspecialchars($row['fullname'], ENT_QUOTES) ?>"
                                data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>"
                                data-phone="<?= htmlspecialchars($row['phone'], ENT_QUOTES) ?>"
                            >
                                <i class="bi bi-pencil-square"></i> แก้ไข
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $row['user_id'] ?>)">
                                <i class="bi bi-trash"></i> ลบ
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal แก้ไขข้อมูลผู้ใช้ -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="editUserForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel">แก้ไขข้อมูลลูกค้า</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
      </div>
      <div class="modal-body">
          <input type="hidden" name="user_id" id="edit_user_id" />

          <div class="mb-3">
            <label for="edit_fullname" class="form-label">ชื่อ - นามสกุล</label>
            <input type="text" class="form-control" id="edit_fullname" name="fullname" required />
          </div>
          <div class="mb-3">
            <label for="edit_email" class="form-label">อีเมล</label>
            <input type="email" class="form-control" id="edit_email" name="email" required />
          </div>
          <div class="mb-3">
            <label for="edit_phone" class="form-label">เบอร์โทรศัพท์</label>
            <input type="text" class="form-control" id="edit_phone" name="phone" required />
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="submit" class="btn btn-primary" name="edit_user">บันทึกการแก้ไข</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal เพิ่มแอดมินใหม่ -->
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content" id="addAdminForm">
      <div class="modal-header">
        <h5 class="modal-title" id="addAdminModalLabel">เพิ่มแอดมินคนใหม่</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
      </div>
      <div class="modal-body">
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <div class="mb-3">
          <label for="add_username" class="form-label">ชื่อผู้ใช้</label>
          <input type="text" class="form-control" id="add_username" name="username" required />
        </div>
        <div class="mb-3">
          <label for="add_password" class="form-label">รหัสผ่าน</label>
          <input type="password" class="form-control" id="add_password" name="password" required />
        </div>
        <div class="mb-3">
          <label for="add_fullname" class="form-label">ชื่อ - นามสกุล</label>
          <input type="text" class="form-control" id="add_fullname" name="fullname" required />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="submit" class="btn btn-success" name="add_admin">เพิ่มแอดมิน</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: 'คุณแน่ใจหรือไม่ว่าต้องการลบลูกค้ารายนี้?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'admin_users.php?delete=' + id;
        }
    });
}

// กำหนดข้อมูลตอนเปิด modal แก้ไข
var editUserModal = document.getElementById('editUserModal');
editUserModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var user_id = button.getAttribute('data-user_id');
    var fullname = button.getAttribute('data-fullname');
    var email = button.getAttribute('data-email');
    var phone = button.getAttribute('data-phone');

    document.getElementById('edit_user_id').value = user_id;
    document.getElementById('edit_fullname').value = fullname;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phone').value = phone;
});
</script>

</body>
</html>
