<?php
require_once 'includes/header.php';

requireLogin();

if (empty($_SESSION['cart'])) {
    redirect('index.php');
}

$cartItems = [];
$totalPrice = 0;
$ids = implode(',', array_keys($_SESSION['cart']));
$stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
while ($row = $stmt->fetch()) {
    $qty = $_SESSION['cart'][$row['id']];
    $subtotal = $row['price'] * $qty;
    $totalPrice += $subtotal;
    $row['quantity'] = $qty;
    $row['subtotal'] = $subtotal;
    $cartItems[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']);
    $user_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // 1. Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, address) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $totalPrice, $address]);
        $order_id = $pdo->lastInsertId();

        // 2. Create Order Items and Update Stock
        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmtStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

        foreach ($cartItems as $item) {
            $stmtItem->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
            $stmtStock->execute([$item['quantity'], $item['id']]);
        }

        $pdo->commit();
        unset($_SESSION['cart']);
        setFlashMessage('success', 'Pesanan Anda berhasil ditempatkan! Terima kasih.');
        redirect('user/orders.php');
    } catch (Exception $e) {
        $pdo->rollBack();
        setFlashMessage('danger', 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage());
    }
}

$userStmt = $pdo->prepare("SELECT full_name, address FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$user = $userStmt->fetch();
?>

<div class="row">
    <div class="col-md-7">
        <div class="card shadow-sm border-0 p-4 mb-4">
            <h4>Alamat Pengiriman</h4>
            <hr>
            <form action="checkout.php" method="POST" id="checkoutForm">
                <div class="mb-3">
                    <label class="form-label">Nama Penerima</label>
                    <input type="text" class="form-control" value="<?php echo $user['full_name']; ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="address" class="form-control" rows="4" required placeholder="Masukkan alamat pengiriman lengkap..."><?php echo $user['address']; ?></textarea>
                </div>
                <div class="alert alert-info small">
                    <i class="fas fa-info-circle me-2"></i> Pembayaran saat ini hanya tersedia melalui metode <strong>Bayar di Tempat (COD)</strong> untuk area Jakarta.
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card shadow-sm border-0 p-4">
            <h4>Ringkasan Pesanan</h4>
            <hr>
            <?php foreach($cartItems as $item): ?>
            <div class="d-flex justify-content-between mb-2">
                <span><?php echo $item['name']; ?> (x<?php echo $item['quantity']; ?>)</span>
                <span><?php echo formatRupiah($item['subtotal']); ?></span>
            </div>
            <?php endforeach; ?>
            <hr>
            <div class="d-flex justify-content-between mb-4 h5">
                <strong>Total Bayar</strong>
                <strong class="text-primary"><?php echo formatRupiah($totalPrice); ?></strong>
            </div>
            <button type="submit" form="checkoutForm" class="btn btn-primary w-100 py-3 shadow-sm rounded-pill">Konfirmasi Pesanan</button>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
