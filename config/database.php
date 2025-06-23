<?php
// Konfigurasi database SQLite
$db_path = __DIR__ . '/../data/jobmatch.db';
$db_dir = dirname($db_path);

try {
    // Create data directory if it doesn't exist
    if (!file_exists($db_dir)) {
        mkdir($db_dir, 0777, true);
    }
    
    // Create PDO connection to SQLite
    $conn = new PDO("sqlite:$db_path");
    
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Enable foreign keys
    $conn->exec('PRAGMA foreign_keys = ON');
    
} catch (PDOException $e) {
    // Log error
    error_log("Database Error: " . $e->getMessage());
    
    // Show user-friendly error
    die("
        <div style='text-align: center; padding: 20px; font-family: Arial, sans-serif;'>
            <h2 style='color: #e74c3c;'>Database Error</h2>
            <p>Maaf, terjadi kesalahan pada sistem. Silakan coba beberapa saat lagi.</p>
            <p><small>Error: " . htmlspecialchars($e->getMessage()) . "</small></p>
            <p><a href='index.html' style='color: #3498db; text-decoration: none;'>Kembali ke Beranda</a></p>
        </div>
    ");
}
?>
