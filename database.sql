-- Create database if not exists
CREATE DATABASE IF NOT EXISTS jobmatch_db;
USE jobmatch_db;

-- Create kategori table
CREATE TABLE IF NOT EXISTS kategori (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create lowongan table
CREATE TABLE IF NOT EXISTS lowongan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(200) NOT NULL,
    perusahaan VARCHAR(200) NOT NULL,
    deskripsi TEXT NOT NULL,
    persyaratan TEXT NOT NULL,
    lokasi VARCHAR(100) NOT NULL,
    kategori_id INT NOT NULL,
    sumber VARCHAR(100) NOT NULL,
    tanggal_posting DATE NOT NULL,
    tanggal_dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create keywords table
CREATE TABLE IF NOT EXISTS keywords (
    id INT PRIMARY KEY AUTO_INCREMENT,
    keyword VARCHAR(100) NOT NULL,
    kategori_id INT,
    bobot FLOAT DEFAULT 1.0,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample categories
INSERT INTO kategori (nama_kategori) VALUES 
('IT & Software'),
('Marketing'),
('Design'),
('Sales'),
('Customer Service'),
('Finance & Accounting'),
('Human Resources'),
('Engineering'),
('Administration'),
('Education & Training');

-- Insert sample keywords for IT category
INSERT INTO keywords (keyword, kategori_id, bobot) VALUES 
('php', 1, 1.5),
('javascript', 1, 1.5),
('java', 1, 1.5),
('python', 1, 1.5),
('html', 1, 1.0),
('css', 1, 1.0),
('sql', 1, 1.2),
('react', 1, 1.3),
('node.js', 1, 1.3),
('docker', 1, 1.3);

-- Insert sample job listings
INSERT INTO lowongan (judul, perusahaan, deskripsi, persyaratan, lokasi, kategori_id, sumber, tanggal_posting) VALUES 
(
    'Senior PHP Developer',
    'TechNova Solutions',
    'Kami mencari Senior PHP Developer untuk bergabung dengan tim pengembangan kami. Posisi ini akan bertanggung jawab untuk:

- Mengembangkan dan memelihara aplikasi web berbasis PHP
- Mengoptimasi performa aplikasi dan database
- Mengimplementasikan fitur baru sesuai kebutuhan bisnis
- Melakukan code review dan mentoring junior developer
- Berkolaborasi dengan tim product dan design',
    'Kualifikasi yang dibutuhkan:

- Minimal 5 tahun pengalaman sebagai PHP Developer
- Mahir dalam PHP, MySQL, dan JavaScript
- Pengalaman dengan framework Laravel atau CodeIgniter
- Familiar dengan Git dan version control
- Kemampuan problem solving yang baik
- Bisa bekerja dalam tim maupun mandiri',
    'Jakarta',
    1,
    'LinkedIn',
    CURRENT_DATE
),
(
    'Frontend Developer',
    'Digital Kreasi',
    'Digital Kreasi membuka kesempatan untuk posisi Frontend Developer. Tanggung jawab:

- Mengembangkan user interface website dan aplikasi
- Mengimplementasikan responsive design
- Optimasi performa frontend
- Integrasi dengan REST API
- Maintenance dan debugging',
    'Persyaratan:

- Minimal 3 tahun pengalaman frontend development
- Menguasai HTML5, CSS3, dan JavaScript
- Familiar dengan React.js atau Vue.js
- Paham tentang cross-browser compatibility
- Memiliki portfolio project yang relevan
- Bisa bekerja dalam tim Agile',
    'Bandung',
    1,
    'JobStreet',
    CURRENT_DATE
);
