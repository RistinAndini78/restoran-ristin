<?php
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                       FROM products p 
                       JOIN categories c ON p.category_id = c.id 
                       WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    redirect('index.php');
}

// Related Products
$stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4");
$stmt->execute([$product['category_id'], $id]);
$related = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card overflow-hidden">
            <?php if($product['image']): ?>
                <img src="uploads/<?php echo $product['image']; ?>" class="img-fluid" alt="<?php echo $product['name']; ?>">
            <?php else: ?>
                <div class="bg-light d-flex align-items-center justify-content-center" style="min-height:400px;"><i class="fas fa-image fa-5x text-muted"></i></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="index.php?category=<?php echo $product['category_id']; ?>"><?php echo $product['category_name']; ?></a></li>
                <li class="breadcrumb-item active"><?php echo $product['name']; ?></li>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold mb-3"><?php echo $product['name']; ?></h1>
        <p class="price h3 mb-4 text-primary"><?php echo formatRupiah($product['price']); ?></p>
        
        <div class="mb-4">
            <span class="badge bg-light text-dark border p-2"><i class="fas fa-layer-group me-1"></i> Stok: <?php echo $product['stock']; ?></span>
            <span class="badge bg-light text-dark border p-2"><i class="fas fa-tags me-1"></i> <?php echo $product['category_name']; ?></span>
        </div>

        <div class="card border-0 bg-light p-3 mb-4">
            <h6 class="fw-bold">Deskripsi Hidangan:</h6>
            <p class="mb-0 text-muted"><?php echo nl2br($product['description']); ?></p>
        </div>

        <form action="cart.php" method="POST" class="row g-3 items-center">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="action" value="add">
            <div class="col-auto">
                <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock']; ?>" style="width: 80px;">
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary w-100 py-2"><i class="fas fa-shopping-cart me-2"></i> Tambah ke Keranjang</button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($related)): ?>
<div class="mt-5">
    <h3 class="section-title">Menu Serupa</h3>
    <div class="row g-4 pt-3">
        <?php foreach($related as $r): ?>
        <div class="col-6 col-md-3">
            <div class="card h-100 shadow-sm border-0">
                <a href="detail.php?id=<?php echo $r['id']; ?>" class="text-decoration-none">
                    <?php if($r['image']): ?>
                        <img src="uploads/<?php echo $r['image']; ?>" class="card-img-top" style="height:150px; object-fit:cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h6 class="card-title text-dark mb-1"><?php echo $r['name']; ?></h6>
                        <p class="text-primary fw-bold mb-0"><?php echo formatRupiah($r['price']); ?></p>
                    </div>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
