<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

$pageTitle = "Admin Dashboard";

// Count Statistics
$userCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$categoryCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Menunggu'")->fetchColumn();

// Recent Orders
$recentOrders = $pdo->query("SELECT o.*, u.full_name 
                             FROM orders o 
                             JOIN users u ON o.user_id = u.id 
                             ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Restoran Prime</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6d4c41; --sidebar-bg: #3e2723; }
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; }
        .sidebar { min-width: 250px; height: 100vh; background: var(--sidebar-bg); color: white; position: fixed; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); border-radius: 5px; margin: 5px 15px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .content { margin-left: 250px; padding: 20px; }
        .stat-card { border: none; border-radius: 15px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center mb-4"><i class="fas fa-utensils me-2"></i>ADMIN</h4>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link active"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
        </li>
        <li>
            <a href="categories.php" class="nav-link"><i class="fas fa-list me-2"></i>Kategori</a>
        </li>
        <li>
            <a href="products.php" class="nav-link"><i class="fas fa-box me-2"></i>Produk</a>
        </li>
        <li>
            <a href="orders.php" class="nav-link"><i class="fas fa-shopping-bag me-2"></i>Pesanan <span class="badge bg-danger ms-auto"><?php echo $pendingOrders; ?></span></a>
        </li>
        <li>
            <a href="users.php" class="nav-link"><i class="fas fa-users me-2"></i>User</a>
        </li>
    </ul>
    <hr>
    <div class="px-3">
        <a href="../logout.php" class="text-white text-decoration-none"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a>
    </div>
</div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Dashboard</h2>
        <a href="../index.php" class="btn btn-outline-brown btn-sm"><i class="fas fa-external-link-alt me-1"></i> Lihat Toko</a>
    </div>

    <?php displayFlashMessage(); ?>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Total Produk</h6>
                        <h3><?php echo $productCount; ?></h3>
                    </div>
                    <i class="fas fa-box fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-success text-white p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Pesanan Selesai</h6>
                        <h3><?php echo $orderCount; ?></h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-info text-white p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Total User</h6>
                        <h3><?php echo $userCount; ?></h3>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-warning text-dark p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Kategori</h6>
                        <h3><?php echo $categoryCount; ?></h3>
                    </div>
                    <i class="fas fa-list fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Pesanan Terbaru</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recentOrders as $order): ?>
                            <tr>
                                <td>#ORD-<?php echo $order['id']; ?></td>
                                <td><?php echo $order['full_name']; ?></td>
                                <td><?php echo formatRupiah($order['total_price']); ?></td>
                                <td>
                                    <?php 
                                    $statusClass = [
                                        'Menunggu' => 'bg-warning',
                                        'Diproses' => 'bg-info',
                                        'Dikirim' => 'bg-primary',
                                        'Selesai' => 'bg-success'
                                    ];
                                    ?>
                                    <span class="badge <?php echo $statusClass[$order['status']]; ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="orders.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-light">Detail</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
