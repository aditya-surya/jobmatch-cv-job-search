<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Database configuration
    $db_path = __DIR__ . '/data/jobmatch.db';
    $db_dir = dirname($db_path);

    // Create data directory if it doesn't exist
    if (!file_exists($db_dir)) {
        mkdir($db_dir, 0777, true);
    }

    // Create PDO connection
    $pdo = new PDO("sqlite:$db_path");
    
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected successfully\n";

    // Enable foreign keys
    $pdo->exec('PRAGMA foreign_keys = ON');

    // Create tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS kategori (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nama_kategori TEXT NOT NULL
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS lowongan (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            judul TEXT NOT NULL,
            perusahaan TEXT NOT NULL,
            deskripsi TEXT NOT NULL,
            persyaratan TEXT NOT NULL,
            lokasi TEXT NOT NULL,
            kategori_id INTEGER NOT NULL,
            sumber TEXT NOT NULL,
            tanggal_posting TEXT NOT NULL,
            tanggal_dibuat TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (kategori_id) REFERENCES kategori(id)
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS keywords (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            keyword TEXT NOT NULL,
            kategori_id INTEGER,
            bobot REAL DEFAULT 1.0,
            FOREIGN KEY (kategori_id) REFERENCES kategori(id)
        )
    ");

    echo "Tables created successfully\n";

    // Insert sample data
    // First, check if kategori table is empty
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM kategori");
    $row = $stmt->fetch();
    
    if ($row['count'] == 0) {
        // Insert categories
        $categories = [
            'IT & Software',
            'Marketing',
            'Design',
            'Sales',
            'Customer Service',
            'Finance & Accounting',
            'Human Resources',
            'Engineering',
            'Administration',
            'Education & Training'
        ];

        $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
        foreach ($categories as $category) {
            $stmt->execute([$category]);
        }

        echo "Categories inserted successfully\n";

        // Insert sample keywords for IT category
        $it_keywords = [
            ['php', 1.5],
            ['javascript', 1.5],
            ['java', 1.5],
            ['python', 1.5],
            ['html', 1.0],
            ['css', 1.0],
            ['sql', 1.2],
            ['react', 1.3],
            ['node.js', 1.3],
            ['docker', 1.3]
        ];

        $stmt = $pdo->prepare("INSERT INTO keywords (keyword, kategori_id, bobot) VALUES (?, 1, ?)");
        foreach ($it_keywords as $keyword) {
            $stmt->execute($keyword);
        }

        echo "Keywords inserted successfully\n";

        // Insert sample job listings
        $stmt = $pdo->prepare("
            INSERT INTO lowongan (judul, perusahaan, deskripsi, persyaratan, lokasi, kategori_id, sumber, tanggal_posting)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $jobs = [
            [
                'Senior PHP Developer',
                'TechNova Solutions',
                'Kami mencari Senior PHP Developer untuk bergabung dengan tim pengembangan kami.',
                'Minimal 3 tahun pengalaman dengan PHP, MySQL, dan JavaScript',
                'Jakarta',
                1,
                'LinkedIn',
                date('Y-m-d')
            ],
            [
                'Frontend Developer',
                'Digital Kreasi',
                'Digital Kreasi mencari Frontend Developer yang berpengalaman.',
                'Menguasai HTML, CSS, JavaScript, dan React',
                'Bandung',
                1,
                'JobStreet',
                date('Y-m-d')
            ]
        ];

        foreach ($jobs as $job) {
            $stmt->execute($job);
        }

        echo "Sample jobs inserted successfully\n";
    }

    echo "Database initialized successfully!\n";
    echo "You can now use the application!\n";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
