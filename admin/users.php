<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Prevent self-deletion
    if ($id == $_SESSION['user_id']) {
        setFlashMessage('danger', 'Anda tidak bisa menghapus diri sendiri!');
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$id])) {
            setFlashMessage('success', 'User berhasil dihapus!');
        }
    }
    redirect('users.php');
}

$users = $pdo->query("SELECT * FROM users ORDER BY role ASC, full_name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Admin</title>
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
        <li><a href="orders.php" class="nav-link"><i class="fas fa-shopping-bag me-2"></i>Pesanan</a></li>
        <li><a href="users.php" class="nav-link active"><i class="fas fa-users me-2"></i>User</a></li>
    </ul>
    <hr>
    <div class="px-3"><a href="../logout.php" class="text-white text-decoration-none"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></div>
</div>

<div class="content">
    <h2>Kelola User</h2>
    <p class="text-muted">Daftar seluruh pengguna sistem (Admin & User).</p>

    <?php displayFlashMessage(); ?>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo $u['username']; ?></td>
                        <td><?php echo $u['email']; ?></td>
                        <td><?php echo $u['full_name']; ?></td>
                        <td>
                            <span class="badge <?php echo $u['role'] === 'admin' ? 'bg-danger' : 'bg-secondary'; ?>">
                                <?php echo strtoupper($u['role']); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if($u['id'] != $_SESSION['user_id']): ?>
                                <a href="users.php?delete=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus user ini?')"><i class="fas fa-trash"></i></a>
                            <?php else: ?>
                                <span class="text-muted small">Diri Sendiri</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
