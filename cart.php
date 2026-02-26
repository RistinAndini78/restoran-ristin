<?php
require_once 'includes/header.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Cart Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $product_id = $_POST['product_id'];

    if ($action === 'add') {
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        setFlashMessage('success', 'Produk ditambahkan ke keranjang!');
        redirect('index.php');
    }

    if ($action === 'update') {
        $quantity = (int)$_POST['quantity'];
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        redirect('cart.php');
    }

    if ($action === 'remove') {
        unset($_SESSION['cart'][$product_id]);
        redirect('cart.php');
    }
}

// Fetch Cart Items
$cartItems = [];
$totalPrice = 0;
if (!empty($_SESSION['cart'])) {
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
}
?>

<div class="row">
    <div class="col-md-9">
        <div class="card shadow-sm border-0 p-4">
            <h2 class="mb-4">Keranjang Belanja</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Hidangan</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cartItems)): ?>
                            <tr><td colspan="5" class="text-center py-5">Keranjang Anda kosong. <a href="index.php">Mulai Belanja</a></td></tr>
                        <?php endif; ?>
                        <?php foreach($cartItems as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="uploads/<?php echo $item['image']; ?>" width="50" class="rounded me-3">
                                    <span class="fw-bold"><?php echo $item['name']; ?></span>
                                </div>
                            </td>
                            <td><?php echo formatRupiah($item['price']); ?></td>
                            <td>
                                <form action="cart.php" method="POST" class="d-flex align-items-center">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity" class="form-control form-control-sm" value="<?php echo $item['quantity']; ?>" min="0" style="width: 70px;" onchange="this.form.submit()">
                                </form>
                            </td>
                            <td><?php echo formatRupiah($item['subtotal']); ?></td>
                            <td>
                                <form action="cart.php" method="POST">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn btn-sm text-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 p-4">
            <h5>Ringkasan Belanja</h5>
            <hr>
            <div class="d-flex justify-content-between mb-3">
                <span>Total Pesanan:</span>
                <span class="fw-bold text-primary h5 mb-0"><?php echo formatRupiah($totalPrice); ?></span>
            </div>
            <a href="checkout.php" class="btn btn-primary w-100 py-3 shadow-sm <?php echo empty($cartItems) ? 'disabled' : ''; ?>">Lanjut ke Checkout</a>
            <a href="index.php" class="btn btn-link w-100 text-decoration-none mt-2">Tambah Hidangan Lain</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
