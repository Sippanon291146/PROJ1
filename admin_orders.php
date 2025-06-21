<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>รายการคำสั่งซื้อ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Prompt', sans-serif;
        }
        .badge-status {
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php"><i class="bi bi-speedometer2 me-2"></i>แดชบอร์ดแอดมิน</a>
        <div>
            <span class="navbar-text text-white me-3">👋 สวัสดี, <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
            <a href="admin_logout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0"><i class="bi bi-card-list me-2"></i>รายการคำสั่งซื้อ</h5>
            <div class="btn-group" role="group" aria-label="สถานะคำสั่งซื้อ">
                <a href="admin_orders.php" class="btn btn-outline-secondary btn-sm">ดูทั้งหมด</a>
                <a href="admin_orders.php?status=Pending" class="btn btn-outline-warning btn-sm">รอดำเนินการ</a>
                <a href="admin_orders.php?status=Paid" class="btn btn-outline-success btn-sm">ชำระเงินแล้ว</a>
                <a href="admin_orders.php?status=Cancelled" class="btn btn-outline-danger btn-sm">ยกเลิก</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0" id="ordersTable">
                    <thead class="table-light">
                        <tr>
                            <th>รหัสคำสั่งซื้อ</th>
                            <th>ผู้สั่งซื้อ</th>
                            <th>วันที่สั่งซื้อ</th>
                            <th>สถานะ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="ordersBody">
                        <!-- ข้อมูลจะโหลดด้วย JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// ดึงค่าพารามิเตอร์ status จาก URL
const urlParams = new URLSearchParams(window.location.search);
const statusParam = urlParams.get('status') || '';

// ฟังก์ชันโหลดข้อมูลคำสั่งซื้อ
function fetchOrders() {
    fetch('api_orders.php?status=' + encodeURIComponent(statusParam))
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const tbody = document.getElementById('ordersBody');
            tbody.innerHTML = ''; // ล้างข้อมูลเก่า

            if (!data || data.length === 0) {
                tbody.innerHTML = `<tr>
                    <td colspan="5" class="text-center py-4 text-muted">ไม่มีคำสั่งซื้อในสถานะนี้</td>
                </tr>`;
                return;
            }

            data.forEach(order => {
                let statusClass = 'secondary';
                switch(order.status) {
                    case 'Pending': statusClass = 'warning'; break;
                    case 'Paid': statusClass = 'success'; break;
                    case 'Cancelled': statusClass = 'danger'; break;
                }

                const createdAt = new Date(order.created_at);
                const formattedDate = createdAt.toLocaleDateString('th-TH', {
                    day: '2-digit', month: '2-digit', year: 'numeric'
                }) + ' ' + createdAt.toLocaleTimeString('th-TH', {
                    hour: '2-digit', minute: '2-digit'
                });

                const row = `
                    <tr>
                        <td>#${order.order_id}</td>
                        <td>${order.username}</td>
                        <td>${formattedDate}</td>
                        <td><span class="badge bg-${statusClass} badge-status">${order.status}</span></td>
                        <td class="text-center">
                            <a href="admin_order_detail.php?order_id=${order.order_id}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> ดูรายละเอียด
                            </a>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        })
        .catch(err => {
            console.error('เกิดข้อผิดพลาดในการโหลดข้อมูลคำสั่งซื้อ:', err);
        });
}

// โหลดข้อมูลคำสั่งซื้อครั้งแรกตอนเปิดหน้า
fetchOrders();

// อัพเดตข้อมูลอัตโนมัติทุก 5 วินาที
setInterval(fetchOrders, 1000);
</script>

</body>
</html>
