<?php
session_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ติดต่อผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f8f9fa; }
        .contact-box { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="contact-box mx-auto" style="max-width: 600px;">
        <h3 class="mb-4 text-primary"><i class="bi bi-headset"></i> ติดต่อผู้ดูแลระบบ</h3>

        <p>หากคุณพบปัญหาในการใช้งานระบบ กรุณาติดต่อเราผ่านช่องทางด้านล่าง หรือกรอกแบบฟอร์ม:</p>
        <ul>
            <li><strong>อีเมล:</strong> admin@yourpharmacy.com</li>
            <li><strong>โทร:</strong> 012-345-6789</li>
            <li><strong>เวลาทำการ:</strong> จันทร์ - ศุกร์ เวลา 9:00 - 17:00</li>
        </ul>

        <!-- แบบฟอร์มติดต่อ -->
        <form id="contactForm" method="POST">
            <div class="mb-3">
                <label for="fullname" class="form-label">ชื่อของคุณ</label>
                <input type="text" class="form-control" id="fullname" name="fullname" required />
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">อีเมลติดต่อ</label>
                <input type="email" class="form-control" id="email" name="email" required />
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">ข้อความ</label>
                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send"></i> ส่งข้อความ</button>
        </form>

        <a href="index.php" class="btn btn-link mt-3"><i class="bi bi-arrow-left"></i> กลับหน้าหลัก</a>
    </div>
</div>

<script>
document.getElementById("contactForm").addEventListener("submit", function(e) {
    e.preventDefault(); // ป้องกันไม่ให้ reload หน้า

    // เก็บข้อมูลจากฟอร์ม
    const name = document.getElementById("fullname").value.trim();
    const email = document.getElementById("email").value.trim();
    const msg = document.getElementById("message").value.trim();

    // ส่งแจ้งเตือน
    if (name && email && msg) {
        Swal.fire({
            title: "ส่งข้อความสำเร็จ!",
            text: "ระบบได้รับข้อความของคุณแล้ว",
            icon: "success",
            confirmButtonText: "ตกลง"
        }).then(() => {
            document.getElementById("contactForm").reset(); // รีเซ็ตฟอร์ม
        });
    } else {
        Swal.fire({
            title: "กรอกข้อมูลไม่ครบ!",
            text: "กรุณากรอกชื่อ อีเมล และข้อความ",
            icon: "warning",
            confirmButtonText: "ตกลง"
        });
    }
});
</script>

</body>
</html> 