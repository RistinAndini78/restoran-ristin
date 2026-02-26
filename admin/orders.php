<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if ($stmt->execute([$status, $order_id])) {
        setFlashMessage('success', 'Status pesanan berhasil diperbarui!');
    } else {
        setFlashMessage('danger', 'Gagal memperbarui status!');
    }
}

// Order Detail View
$orderDetail = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT o.*, u.full_name, u.email 
                           FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           WHERE o.id = ?");
    $stmt->execute([$_GET['id']]);
    $orderDetail = $stmt->fetch();

    if ($orderDetail) {
        $stmt = $pdo->prepare("SELECT oi.*, p.name 
                               FROM order_items oi 
                               JOIN products p ON oi.product_id = p.id 
                               WHERE oi.order_id = ?");
        $stmt->execute([$_GET['id']]);
        $items = $stmt->fetchAll();
    }
}

$orders = $pdo->query("SELECT o.*, u.full_name 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       ORDER BY o.created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin</title>
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
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center mb-4"><i class="fas fa-utensils me-2"></i>ADMIN</h4>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
        <li><a href="categories.php" class="nav-link"><i class="fas fa-list me-2"></i>Kategori</a></li>
        <li><a href="products.php" class="nav-link"><i class="fas fa-box me-2"></i>Produk</a></li>
        <li><a href="orders.php" class="nav-link active"><i class="fas fa-shopping-bag me-2"></i>Pesanan</a></li>
        <li><a href="users.php" class="nav-link"><i class="fas fa-users me-2"></i>User</a></li>
    </ul>
    <hr>
    <div class="px-3"><a href="../logout.php" class="text-white text-decoration-none"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></div>
</div>

<div class="content">
    <?php displayFlashMessage(); ?>

    <?php if ($orderDetail): ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Detail Pesanan #ORD-<?php echo $orderDetail['id']; ?></h2>
            <a href="orders.php" class="btn btn-outline-secondary">Kembali</a>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5>Item Pesanan</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($items as $item): ?>
                                <tr>
                                    <td><?php echo $item['name']; ?></td>
                                    <td><?php echo formatRupiah($item['price']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td class="text-end"><?php echo formatRupiah($item['price'] * $item['quantity']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total Akhir</th>
                                    <th class="text-end text-primary"><?php echo formatRupiah($orderDetail['total_price']); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5>Informasi Pelanggan</h5>
                        <p class="mb-1"><strong>Nama:</strong> <?php echo $orderDetail['full_name']; ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?php echo $orderDetail['email']; ?></p>
                        <p class="mb-3"><strong>Alamat Pengiriman:</strong><br><?php echo nl2br($orderDetail['address']); ?></p>
                        <hr>
                        <form action="orders.php?id=<?php echo $orderDetail['id']; ?>" method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $orderDetail['id']; ?>">
                            <label class="form-label">Update Status</label>
                            <select name="status" class="form-select mb-3">
                                <option value="Menunggu" <?php echo $orderDetail['status'] == 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="Diproses" <?php echo $orderDetail['status'] == 'Diproses' ? 'selected' : ''; ?>>Diproses</option>
                                <option value="Dikirim" <?php echo $orderDetail['status'] == 'Dikirim' ? 'selected' : ''; ?>>Dikirim</option>
                                <option value="Selesai" <?php echo $orderDetail['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary w-100">Update Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <h2 class="mb-4">Daftar Pesanan</h2>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $o): ?>
                        <tr>
                            <td>#ORD-<?php echo $o['id']; ?></td>
                            <td><?php echo $o['full_name']; ?></td>
                            <td><?php echo formatRupiah($o['total_price']); ?></td>
                            <td>
                                <span class="badge border <?php 
                                    echo match($o['status']) {
                                        'Menunggu' => 'bg-warning text-dark',
                                        'Diproses' => 'bg-info',
                                        'Dikirim' => 'bg-primary',
                                        'Selesai' => 'bg-success',
                                        default => 'bg-secondary'
                                    };
                                ?>">
                                    <?php echo $o['status']; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>
                            <td class="text-center">
                                <a href="orders.php?id=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
