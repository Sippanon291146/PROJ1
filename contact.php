<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>ติดต่อผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f8f9fa;
        }
        .contact-box {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            border-radius: 12px;
            padding: 30px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        h3 {
            color: #0d6efd;
            margin-bottom: 20px;
            font-weight: 700;
        }
        label {
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="contact-box">
        <h3><i class="bi bi-headset"></i> ติดต่อผู้ดูแลระบบ</h3>
        
        <p>หากคุณพบปัญหาในการใช้งาน กรุณาติดต่อผ่านแบบฟอร์มด้านล่าง หรือใช้ช่องทางอื่น ๆ :</p>
        <ul>
            <li><strong>อีเมล:</strong> admin@yourpharmacy.com</li>
            <li><strong>โทร:</strong> 012-345-6789</li>
            <li><strong>เวลาทำการ:</strong> จันทร์ - ศุกร์ เวลา 9:00 - 17:00</li>
        </ul>
        
        <form id="contactForm" method="POST" action="contact_submit.php">
            <div class="mb-3">
                <label for="fullname" class="form-label">ชื่อของคุณ</label>
                <input type="text" id="fullname" name="fullname" class="form-control" 
                    value="<?=htmlspecialchars($_SESSION['username'] ?? '')?>" readonly>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">อีเมลติดต่อ</label>
                <input type="email" id="email" name="email" class="form-control" 
                    value="<?=htmlspecialchars($_SESSION['email'] ?? '')?>" readonly>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">ข้อความ</label>
                <textarea id="message" name="message" rows="5" class="form-control" placeholder="กรุณากรอกข้อความ..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send"></i> ส่งข้อความ</button>
        </form>
        
        <a href="index.php" class="btn btn-link mt-3"><i class="bi bi-arrow-left"></i> กลับหน้าหลัก</a>
    </div>
</div>

<?php if (isset($_SESSION['contact_result'])): ?>
<script>
    Swal.fire({
        icon: '<?=htmlspecialchars($_SESSION['contact_result']['status'])?>',
        title: '<?=htmlspecialchars($_SESSION['contact_result']['title'])?>',
        text: '<?=htmlspecialchars($_SESSION['contact_result']['message'])?>',
        confirmButtonText: 'ตกลง'
    });
</script>
<?php unset($_SESSION['contact_result']); endif; ?>

</body>
</html>
