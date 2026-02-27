<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

// Handle Add/Edit Product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    $image = "";
    if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
        if ($_FILES['image']['error'] === 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = time() . '.' . $ext;
            $upload_dir = __DIR__ . "/../uploads/";
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $target = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image = $filename;
            } else {
                setFlashMessage('danger', 'Gagal memindahkan file yang diunggah.');
                redirect('products.php');
            }
        } else {
            $error_code = $_FILES['image']['error'];
            $error_msg = 'Terjadi kesalahan saat mengunggah file.';
            switch ($error_code) {
                case 1: $error_msg = 'Ukuran file terlalu besar (Limit Server).'; break;
                case 2: $error_msg = 'Ukuran file terlalu besar (Limit Form).'; break;
                case 3: $error_msg = 'File hanya terunggah sebagian.'; break;
                case 6: $error_msg = 'Folder sementara tidak ditemukan.'; break;
                case 7: $error_msg = 'Gagal menulis file ke disk.'; break;
            }
            setFlashMessage('danger', $error_msg);
            redirect('products.php');
        }
    }

    if ($id) {
        // Edit
        if ($image != "") {
            $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, image=? WHERE id=?");
            $params = [$category_id, $name, $description, $price, $stock, $image, $id];
        } else {
            $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=? WHERE id=?");
            $params = [$category_id, $name, $description, $price, $stock, $id];
        }
    } else {
        // Add
        $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
        $params = [$category_id, $name, $description, $price, $stock, $image];
    }

    if ($stmt->execute($params)) {
        setFlashMessage('success', 'Produk berhasil disimpan!');
    } else {
        setFlashMessage('danger', 'Gagal menyimpan data produk ke database!');
    }
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Get image name to delete file
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch();
    if ($prod['image']) {
        @unlink("../uploads/" . $prod['image']);
    }

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$id])) {
        setFlashMessage('success', 'Produk berhasil dihapus!');
    }
    redirect('products.php');
}

$products = $pdo->query("SELECT p.*, c.name as category_name 
                         FROM products p 
                         JOIN categories c ON p.category_id = c.id 
                         ORDER BY p.id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin</title>
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
        .img-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center mb-4"><i class="fas fa-utensils me-2"></i>ADMIN</h4>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
        <li><a href="categories.php" class="nav-link"><i class="fas fa-list me-2"></i>Kategori</a></li>
        <li><a href="products.php" class="nav-link active"><i class="fas fa-box me-2"></i>Produk</a></li>
        <li><a href="orders.php" class="nav-link"><i class="fas fa-shopping-bag me-2"></i>Pesanan</a></li>
        <li><a href="users.php" class="nav-link"><i class="fas fa-users me-2"></i>User</a></li>
    </ul>
    <hr>
    <div class="px-3"><a href="../logout.php" class="text-white text-decoration-none"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></div>
</div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Kelola Produk</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Tambah Produk</button>
    </div>

    <?php displayFlashMessage(); ?>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $p): ?>
                    <tr>
                        <td>
                            <?php if($p['image']): ?>
                                <img src="../uploads/<?php echo $p['image']; ?>" class="img-thumb">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center border" style="width:50px; height:50px;"><i class="fas fa-image text-muted"></i></div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $p['name']; ?></td>
                        <td><span class="badge bg-light text-dark border"><?php echo $p['category_name']; ?></span></td>
                        <td><?php echo formatRupiah($p['price']); ?></td>
                        <td><?php echo $p['stock']; ?></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-info me-1 edit-btn" 
                                    data-id="<?php echo $p['id']; ?>"
                                    data-name="<?php echo $p['name']; ?>"
                                    data-category="<?php echo $p['category_id']; ?>"
                                    data-price="<?php echo $p['price']; ?>"
                                    data-stock="<?php echo $p['stock']; ?>"
                                    data-desc="<?php echo $p['description']; ?>"
                                    data-bs-toggle="modal" data-bs-target="#editModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="products.php?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk ini?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="products.php" method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 text-center mb-3">
                         <label class="form-label d-block text-start">Gambar Produk</label>
                         <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Harga (IDR)</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stock" class="form-control" value="0" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="products.php" method="POST" enctype="multipart/form-data" class="modal-content" id="editForm">
            <div class="modal-header">
                <h5 class="modal-title">Edit Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id">
                <div class="row g-3">
                    <div class="col-md-6">
                         <label class="form-label">Ganti Gambar (Kosongkan jika tidak diubah)</label>
                         <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" id="edit_category" class="form-select" required>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Harga (IDR)</label>
                        <input type="number" name="price" id="edit_price" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stock" id="edit_stock" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" id="edit_desc" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Update Produk</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('edit_name').value = this.dataset.name;
        document.getElementById('edit_category').value = this.dataset.category;
        document.getElementById('edit_price').value = this.dataset.price;
        document.getElementById('edit_stock').value = this.dataset.stock;
        document.getElementById('edit_desc').value = this.dataset.desc;
    });
});
</script>
</body>
</html>
