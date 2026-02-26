<?php
require_once 'includes/header.php';

// Pagination and Filtering
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($category_id) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
}

if ($search) {
    $query .= " AND p.name LIKE ?";
    $params[] = "%$search%";
}

$query .= " ORDER BY p.id DESC LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Count for pagination
$countQuery = "SELECT COUNT(*) FROM products p WHERE 1=1";
if ($category_id) $countQuery .= " AND category_id = $category_id";
if ($search) $countQuery .= " AND name LIKE '%$search%'";
$totalProducts = $pdo->query($countQuery)->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<!-- Hero Section -->
<div class="row align-items-center mb-5 py-5" style="background: linear-gradient(rgba(245, 245, 220, 0.9), rgba(245, 245, 220, 0.9)), url('https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&q=80&w=2070') center/cover; border-radius: 20px;">
    <div class="col-md-8 offset-md-2 text-center">
        <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-color);">Cita Rasa Premium di Setiap Hidangan</h1>
        <p class="lead mb-4">Nikmati kelezatan masakan khas kami langsung dari dapur profesional untuk menemani momen spesial Anda.</p>
        <div class="input-group mb-3 shadow-sm mx-auto" style="max-width: 600px;">
            <form action="products.php" method="GET" class="d-flex w-100">
                <input type="text" name="search" class="form-control border-0 p-3" placeholder="Cari hidangan favoritmu..." value="<?php echo $search; ?>">
                <button class="btn btn-primary px-4" type="submit">Cari</button>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <h3 class="section-title">Menu Unggulan Kami</h3>
        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="products.php" class="btn <?php echo !$category_id ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm">Semua</a>
            <?php foreach($categories as $cat): ?>
                <a href="products.php?category=<?php echo $cat['id']; ?>" class="btn <?php echo $category_id == $cat['id'] ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm">
                    <?php echo $cat['name']; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <?php if (empty($products)): ?>
        <div class="col-12 text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <p class="text-muted">Oops! Produk yang Anda cari tidak ditemukan.</p>
        </div>
    <?php endif; ?>

    <?php foreach($products as $p): ?>
    <div class="col-6 col-md-4 col-lg-3">
        <div class="card h-100">
            <?php if($p['image']): ?>
                <img src="uploads/<?php echo $p['image']; ?>" class="card-img-top" alt="<?php echo $p['name']; ?>">
            <?php else: ?>
                <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;"><i class="fas fa-image fa-3x text-muted"></i></div>
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
                <small class="text-muted mb-1"><?php echo $p['category_name']; ?></small>
                <h5 class="card-title h6 fw-bold mb-2"><?php echo $p['name']; ?></h5>
                <p class="price mb-3"><?php echo formatRupiah($p['price']); ?></p>
                <div class="mt-auto">
                    <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-primary btn-sm w-100 rounded-pill mb-2">Detail</a>
                    <form action="cart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                        <input type="hidden" name="action" value="add">
                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill"><i class="fas fa-plus me-1"></i> Keranjang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if($totalPages > 1): ?>
<nav aria-label="Page navigation">
  <ul class="pagination justify-content-center">
    <?php for($i = 1; $i <= $totalPages; $i++): ?>
    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
        <a class="page-link" href="products.php?page=<?php echo $i; ?><?php echo $category_id ? '&category='.$category_id : ''; ?><?php echo $search ? '&search='.$search : ''; ?>">
            <?php echo $i; ?>
        </a>
    </li>
    <?php endfor; ?>
  </ul>
</nav>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
