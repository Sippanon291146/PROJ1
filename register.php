<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);

    // ตรวจสอบชื่อผู้ใช้ซ้ำ
    $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
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
        $stmt = $conn->prepare("INSERT INTO users (username, password, fullname, email, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password, $fullname, $email, $phone);

        if ($stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'สมัครสมาชิกสำเร็จ!',
                    text: 'กรุณาเข้าสู่ระบบ',
                    confirmButtonText: 'เข้าสู่ระบบ'
                }).then(() => {
                    window.location.href = 'login.php';
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
    <meta charset="UTF-8" />
    <title>สมัครสมาชิก | ร้านขายยาออนไลน์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
        }
        .register-card {
            background-color: #fff;
            border-radius: 15px;
            padding: 30px 35px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
        }
        .register-card:hover {
            transform: translateY(-5px);
        }
        h3 {
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            color: #000DFF;
            font-size: 1.9rem;
            letter-spacing: 0.05em;
        }
        .form-label {
            font-weight: 600;
            color: #333;
        }
        .form-control {
            height: 44px;
            padding-left: 2.8rem;
            font-size: 1rem;
            border-radius: 8px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            border-color: #000DFF;
            box-shadow: 0 0 8px rgba(0, 13, 255, 0.3);
            outline: none;
        }
        .input-group-text {
            background: transparent;
            border: none;
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 1.25rem;
            pointer-events: none;
        }
        .input-group {
            position: relative;
            margin-bottom: 1.2rem;
        }
        .btn-register {
            background-color: #000DFF;
            border: none;
            font-weight: 700;
            letter-spacing: 0.08em;
            font-size: 1.15rem;
            border-radius: 10px;
            padding: 12px 0;
            transition: background-color 0.3s ease;
        }
        .btn-register:hover {
            background-color: #4a57ff;
        }
        .text-center a {
            color: #000DFF;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .text-center a:hover {
            color: #4a57ff;
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .register-card {
                padding: 25px 20px;
            }
            h3 {
                font-size: 1.6rem;
                margin-bottom: 25px;
            }
            .btn-register {
                font-size: 1rem;
                padding: 10px 0;
            }
        }
    </style>
</head>
<body>

    <div class="register-card shadow-sm">
        <h3>สมัครสมาชิก</h3>
        <form method="POST" action="register.php" novalidate>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                <input type="text" name="username" class="form-control" placeholder="ชื่อผู้ใช้ *" required minlength="3" maxlength="20" />
                <div class="invalid-feedback">
                    กรุณากรอกชื่อผู้ใช้ 3-20 ตัวอักษร
                </div>
            </div>

            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" name="password" class="form-control" placeholder="รหัสผ่าน *" required minlength="6" />
                <div class="invalid-feedback">
                    กรุณากรอกรหัสผ่านอย่างน้อย 6 ตัวอักษร
                </div>
            </div>

            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                <input type="text" name="fullname" class="form-control" placeholder="ชื่อ-นามสกุล" maxlength="50" />
            </div>

            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                <input type="email" name="email" class="form-control" placeholder="อีเมล" />
            </div>

            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-phone-fill"></i></span>
                <input type="text" name="phone" class="form-control" placeholder="เบอร์โทรศัพท์" pattern="^\d{9,10}$" title="กรุณากรอกเบอร์โทรศัพท์ 9-10 ตัวเลข" />
                <div class="invalid-feedback">
                    กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง (9-10 ตัวเลข)
                </div>
            </div>

            <button type="submit" class="btn btn-register w-100">สมัครสมาชิก</button>

            <div class="text-center mt-3">
                <small>มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a></small>
            </div>
        </form>
    </div>

<script>
    // Bootstrap form validation
    (() => {
        'use strict';
        const forms = document.querySelectorAll('form');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

</body>
</html>
