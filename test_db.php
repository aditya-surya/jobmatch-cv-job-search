<?php
require_once 'config/database.php';

echo "Testing database connection...\n\n";

try {
    // Test kategori table
    $stmt = $conn->query("SELECT * FROM kategori");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Categories found: " . count($categories) . "\n";
    foreach ($categories as $category) {
        echo "- " . $category['nama_kategori'] . "\n";
    }
    echo "\n";

    // Test keywords table
    $stmt = $conn->query("SELECT * FROM keywords");
    $keywords = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Keywords found: " . count($keywords) . "\n";
    echo "Sample keywords:\n";
    for ($i = 0; $i < min(5, count($keywords)); $i++) {
        echo "- " . $keywords[$i]['keyword'] . " (weight: " . $keywords[$i]['bobot'] . ")\n";
    }
    echo "\n";

    // Test lowongan table
    $stmt = $conn->query("SELECT l.*, k.nama_kategori FROM lowongan l JOIN kategori k ON l.kategori_id = k.id");
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Jobs found: " . count($jobs) . "\n";
    foreach ($jobs as $job) {
        echo "- " . $job['judul'] . " at " . $job['perusahaan'] . " (" . $job['nama_kategori'] . ")\n";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
