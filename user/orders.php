<?php
require_once '../includes/header.php'; // Adjusted paths since it's in /user/

requireLogin();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Handle Order Detail View
$orderDetail = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $user_id]);
    $orderDetail = $stmt->fetch();

    if ($orderDetail) {
        $stmt = $pdo->prepare("SELECT oi.*, p.name, p.image 
                               FROM order_items oi 
                               JOIN products p ON oi.product_id = p.id 
                               WHERE oi.order_id = ?");
        $stmt->execute([$_GET['id']]);
        $items = $stmt->fetchAll();
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                <li class="breadcrumb-item active">Pesanan Saya</li>
            </ol>
        </nav>

        <?php if ($orderDetail): ?>
            <div class="card shadow-sm border-0 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Detail Pesanan #ORD-<?php echo $orderDetail['id']; ?></h3>
                    <span class="badge <?php 
                        echo match($orderDetail['status']) {
                            'Menunggu' => 'bg-warning text-dark',
                            'Diproses' => 'bg-info',
                            'Dikirim' => 'bg-primary',
                            'Selesai' => 'bg-success',
                            default => 'bg-secondary'
                        };
                    ?> p-2"><?php echo $orderDetail['status']; ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($items as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="../uploads/<?php echo $item['image']; ?>" width="40" class="rounded me-2">
                                        <span><?php echo $item['name']; ?></span>
                                    </div>
                                </td>
                                <td><?php echo formatRupiah($item['price']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td class="text-end"><?php echo formatRupiah($item['price'] * $item['quantity']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Pembayaran</th>
                                <th class="text-end text-primary h5"><?php echo formatRupiah($orderDetail['total_price']); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mt-3 p-3 bg-light rounded">
                    <h6>Alamat Pengiriman:</h6>
                    <p class="mb-0 small"><?php echo nl2br($orderDetail['address']); ?></p>
                </div>
                <a href="orders.php" class="btn btn-outline-brown mt-4">Kirim Kembali</a>
            </div>
        <?php else: ?>
            <h2 class="mb-4">Riwayat Pesanan</h2>
            <?php if (empty($orders)): ?>
                <div class="card shadow-sm border-0 p-5 text-center">
                    <i class="fas fa-shopping-bag fa-4x text-light mb-3"></i>
                    <p class="text-muted">Anda belum memiliki riwayat pesanan.</p>
                    <a href="../index.php" class="btn btn-primary d-inline-block mx-auto rounded-pill px-4">Pesan Sekarang</a>
                </div>
            <?php else: ?>
                <div class="card shadow-sm border-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $o): ?>
                                <tr>
                                    <td>#ORD-<?php echo $o['id']; ?></td>
                                    <td><?php echo date('d M Y, H:i', strtotime($o['created_at'])); ?></td>
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
                                    <td class="text-center">
                                        <a href="orders.php?id=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Detail</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
