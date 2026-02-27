<?php
/**
 * Script untuk memperbaiki izin folder uploads secara otomatis.
 * Jalankan file ini sekali saja melalui browser: restoristin/fix_permissions.php
 */

$folder = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';

echo "<h3>Memperbaiki Izin Folder...</h3>";

if (!is_dir($folder)) {
    if (mkdir($folder, 0777, true)) {
        echo "<p style='color:green;'>✅ Folder 'uploads' berhasil dibuat.</p>";
    } else {
        echo "<p style='color:red;'>❌ Gagal membuat folder 'uploads'. Silakan buat secara manual.</p>";
    }
}

if (is_dir($folder)) {
    if (chmod($folder, 0777)) {
        echo "<p style='color:green;'>✅ Izin folder 'uploads' berhasil diubah menjadi 777.</p>";
        echo "<p>Sekarang Anda seharusnya sudah bisa mengunggah gambar produk.</p>";
    } else {
        echo "<p style='color:red;'>❌ Gagal mengubah izin folder. Hubungi penyedia hosting atau gunakan cara manual.</p>";
    }
}

echo "<hr><p><b>Saran Keamanan:</b> Hapus file ini setelah selesai digunakan.</p>";
?>
