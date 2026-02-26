<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        if ($stmt->execute([$name])) {
            setFlashMessage('success', 'Kategori berhasil ditambahkan!');
        } else {
            setFlashMessage('danger', 'Gagal menambahkan kategori!');
        }
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    if ($stmt->execute([$id])) {
        setFlashMessage('success', 'Kategori berhasil dihapus!');
    } else {
        setFlashMessage('danger', 'Gagal menghapus kategori!');
    }
    redirect('categories.php');
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Admin</title>
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
        <li><a href="categories.php" class="nav-link active"><i class="fas fa-list me-2"></i>Kategori</a></li>
        <li><a href="products.php" class="nav-link"><i class="fas fa-box me-2"></i>Produk</a></li>
        <li><a href="orders.php" class="nav-link"><i class="fas fa-shopping-bag me-2"></i>Pesanan</a></li>
        <li><a href="users.php" class="nav-link"><i class="fas fa-users me-2"></i>User</a></li>
    </ul>
    <hr>
    <div class="px-3"><a href="../logout.php" class="text-white text-decoration-none"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></div>
</div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Kelola Kategori</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Tambah Kategori</button>
    </div>

    <?php displayFlashMessage(); ?>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="10%">ID</th>
                        <th>Nama Kategori</th>
                        <th>Tanggal Dibuat</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($categories)): ?>
                        <tr><td colspan="4" class="text-center py-4">Belum ada kategori.</td></tr>
                    <?php endif; ?>
                    <?php foreach($categories as $cat): ?>
                    <tr>
                        <td><?php echo $cat['id']; ?></td>
                        <td><?php echo $cat['name']; ?></td>
                        <td><?php echo date('d M Y', strtotime($cat['created_at'])); ?></td>
                        <td class="text-center">
                            <a href="categories.php?delete=<?php echo $cat['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kategori ini?')"><i class="fas fa-trash"></i></a>
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
    <div class="modal-dialog">
        <form action="categories.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" class="form-control" required placeholder="Contoh: Desserts">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal">Batal</button>
                <button type="submit" name="add_category" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
