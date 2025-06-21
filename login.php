<?php
session_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>เข้าสู่ระบบ | ร้านขายยาออนไลน์</title>
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
            padding: 20px;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            padding: 30px 30px;
            transition: transform 0.3s ease;
        }
        .login-card:hover {
            transform: translateY(-5px);
        }
        .login-logo {
            max-width: 250px;
            width: 70%;
            height: auto;
            margin-bottom: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        h3 {
            font-weight: 600;
            color: #333;
            margin-bottom: 28px;
            text-align: center;
            font-size: 1.75rem;
        }
        .form-control:focus {
            border-color: #000DFF;
            box-shadow: 0 0 8px rgba(0, 13, 255, 0.3);
        }
        .btn-login {
            background-color: #000DFF;
            border: none;
            font-weight: 600;
            letter-spacing: 0.05em;
            transition: background-color 0.3s ease;
            font-size: 1.1rem;
            padding: 10px 0;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .btn-login:hover {
            background-color: #4a57ff;
        }
        .form-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 1.3rem;
        }
        .input-group-text {
            background: transparent;
            border: none;
            padding-left: 0;
        }
        .input-group .form-control {
            font-size: 1rem;
            height: 42px;
        }
        .position-relative input.form-control {
            padding-left: 3.2rem;
        }
        .mb-4.position-relative {
            margin-bottom: 1.25rem !important;
        }
        .text-center a {
            color: #000DFF;
            font-weight: 500;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .text-center a:hover {
            text-decoration: underline;
        }

        /* Responsive: ลดขนาดฟอร์มบนมือถือ */
        @media (max-width: 576px) {
            .login-card {
                padding: 25px 20px;
                max-width: 100%;
            }
            h3 {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }
            .btn-login {
                font-size: 1rem;
                padding: 9px 0;
            }
            .login-logo {
                max-width: 180px;
                width: 60%;
                margin-bottom: 15px;
            }
        }

        /* Shake animation for invalid inputs */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }
        .was-validated :invalid {
            animation: shake 0.3s ease;
            border-color: #dc3545 !important;
        }
    </style>
</head>
<body>

    <div class="login-card shadow-sm text-center">
        <img src="uploads/logo.png" alt="โลโก้ร้านขายยา" class="login-logo" />
        <h3>เข้าสู่ระบบ</h3>
        <form method="POST" action="login_process.php" novalidate>
            <div class="mb-4 position-relative">
                <span class="form-icon"><i class="bi bi-person-circle"></i></span>
                <input type="text" name="username" class="form-control" placeholder="ชื่อผู้ใช้" required autofocus />
                <div class="invalid-feedback">
                    กรุณากรอกชื่อผู้ใช้
                </div>
            </div>

            <div class="mb-4 position-relative">
                <span class="form-icon"><i class="bi bi-lock-fill"></i></span>
                <input type="password" name="password" class="form-control" placeholder="รหัสผ่าน" required />
                <div class="invalid-feedback">
                    กรุณากรอกรหัสผ่าน
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 py-2" id="btnLogin">
                <span id="btnText">เข้าสู่ระบบ</span>
                <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
            </button>

            <div class="text-center mt-3">
                <small>ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></small>
            </div>
        </form>
    </div>

    <!-- Audio for login feedback -->
    <audio id="audioSuccess" src="success.mp3" preload="auto"></audio>
    <audio id="audioError" src="error.mp3" preload="auto"></audio>

<script>
    // Bootstrap form validation + show loading spinner on submit
    (() => {
      'use strict'
      const form = document.querySelector('form')
      const btnLogin = document.getElementById('btnLogin')
      const btnText = document.getElementById('btnText')
      const btnSpinner = document.getElementById('btnSpinner')

      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
          form.classList.add('was-validated')
          return
        }
        // Show spinner and disable button
        btnText.textContent = 'กำลังเข้าสู่ระบบ...'
        btnSpinner.classList.remove('d-none')
        btnLogin.disabled = true
      })
    })();

    // SweetAlert + sound feedback from PHP session
    <?php if (isset($_SESSION['login_error'])): ?>
        Swal.fire({
            icon: 'error',
            title: '<?= $_SESSION['login_error'] ?>',
            confirmButtonText: 'ลองอีกครั้ง',
            background: '#fff',
            color: '#333',
        });
        document.getElementById('audioError').play();
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['login_success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'เข้าสู่ระบบสำเร็จ',
            timer: 1500,
            timerProgressBar: true,
            showConfirmButton: false,
            background: '#fff',
            color: '#333',
        });
        document.getElementById('audioSuccess').play();
        <?php unset($_SESSION['login_success']); ?>
    <?php endif; ?>
</script>

</body>
</html>
