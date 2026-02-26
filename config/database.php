<?php
// Configuration for Database Connection
$host = 'localhost';
$db   = 'restoran_ecommerce';
$user = 'root'; // Adjust as per your XAMPP/Laragon settings
$pass = '';     // Adjust as per your XAMPP/Laragon settings
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
