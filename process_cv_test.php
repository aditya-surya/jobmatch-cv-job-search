<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'debug.log');
error_log("=== Starting CV processing test ===");
error_log("Server Info: " . php_uname());
error_log("PHP Version: " . PHP_VERSION);
error_log("Max Upload Size: " . ini_get('upload_max_filesize'));
error_log("Max Post Size: " . ini_get('post_max_size'));

// Setup upload directory
$upload_dir = "uploads/";
try {
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            throw new Exception("Failed to create uploads directory");
        }
        error_log("Created uploads directory: $upload_dir");
    }

    // Check directory permissions
    if (!is_writable($upload_dir)) {
        throw new Exception("Uploads directory is not writable");
    }

    // Log directory status
    error_log("Upload directory ready: " . realpath($upload_dir));
    error_log("Directory permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4));
} catch (Exception $e) {
    error_log("Directory setup error: " . $e->getMessage());
    die("Server configuration error: " . $e->getMessage());
}

require_once 'classes/CVParser.php';

// Array untuk menyimpan pesan
$messages = [];
$errors = [];
$cv_text = "";

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("Form submitted via POST");
    
    // File upload handling
    if (isset($_FILES["cv_file"])) {
        error_log("File upload detected. Processing...");
        error_log("Upload details: " . print_r($_FILES["cv_file"], true));

        // Check for upload errors
        if ($_FILES["cv_file"]["error"] !== UPLOAD_ERR_OK) {
            $upload_errors = array(
                UPLOAD_ERR_INI_SIZE => "File exceeds PHP upload_max_filesize",
                UPLOAD_ERR_FORM_SIZE => "File exceeds form MAX_FILE_SIZE",
                UPLOAD_ERR_PARTIAL => "File was only partially uploaded",
                UPLOAD_ERR_NO_FILE => "No file was uploaded",
                UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload"
            );
            $error_message = isset($upload_errors[$_FILES["cv_file"]["error"]]) 
                ? $upload_errors[$_FILES["cv_file"]["error"]]
                : "Unknown upload error";
            
            error_log("Upload error: " . $error_message);
            $errors[] = "Upload failed: " . $error_message;
        } else {
            $allowed_types = ["application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document"];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            $file_type = $_FILES["cv_file"]["type"];
            $file_size = $_FILES["cv_file"]["size"];
            $file_name = $_FILES["cv_file"]["name"];
            $file_tmp = $_FILES["cv_file"]["tmp_name"];
            
            // Log informasi file
            error_log("Processing file: $file_name, Type: $file_type, Size: $file_size bytes");
            $messages[] = "File detected: $file_name ($file_type)";
            
            // Cek tipe file
            if (!in_array($file_type, $allowed_types)) {
                $errors[] = "Format file tidak didukung. Silakan upload file PDF, DOC, atau DOCX.";
                error_log("Error: Unsupported file format: $file_type");
            }
            
            // Cek ukuran file
            if ($file_size > $max_size) {
                $errors[] = "Ukuran file terlalu besar. Maksimal 5MB.";
                error_log("Error: File too large: $file_size bytes");
            }
            
            // Jika tidak ada error, proses file
            if (empty($errors)) {
                // Generate nama file unik
                $new_file_name = uniqid() . "_" . $file_name;
                $upload_file = $upload_dir . $new_file_name;
                
                // Upload file
                if (move_uploaded_file($file_tmp, $upload_file)) {
                    error_log("File successfully uploaded to: $upload_file");
                    $messages[] = "File berhasil diupload";
                    
                try {
                    // Load required files and check database connection
                    require_once 'config/database.php';
                    if (!isset($conn) || $conn->connect_error) {
                        throw new Exception("Database connection failed: " . ($conn->connect_error ?? "Connection not established"));
                    }
                    error_log("Database connection successful");
                    
                    require_once 'classes/NaiveBayes.php';
                    require_once 'classes/CVParser.php';

                    // Parse CV
                    $parser = new CVParser();
                    $cv_text = $parser->parseCV($upload_file, $file_type);
                    
                    // Cek hasil parsing
                    if ($cv_text && !empty($cv_text) && strpos($cv_text, "Error:") !== 0) {
                        error_log("CV successfully parsed, text length: " . strlen($cv_text));
                        $messages[] = "CV berhasil diproses";

                        // Inisialisasi Naive Bayes
                        $naive_bayes = new NaiveBayes($conn);

                        // Deteksi bahasa secara otomatis dari teks CV
                        $cv_language = $naive_bayes->detectLanguage($cv_text);
                        error_log("Detected language: " . $cv_language);
                        
                        // Proses CV dengan Naive Bayes
                        $matching_jobs = $naive_bayes->findMatchingJobs($cv_text, $cv_language);
                        error_log("Found " . count($matching_jobs) . " matching jobs");
                        
                        // Simpan hasil di session untuk ditampilkan
                        $_SESSION["matching_jobs"] = $matching_jobs;
                        $_SESSION["cv_file"] = $new_file_name;
                        $_SESSION["cv_text"] = substr($cv_text, 0, 300) . "..."; // Simpan sebagian teks CV
                        
                        // Redirect ke halaman hasil
                        header("Location: results.php");
                        exit();
                    } else {
                        if (strpos($cv_text, "Error:") === 0) {
                            $errors[] = $cv_text;
                        } else {
                            $errors[] = "Gagal mengekstrak teks dari CV. Pastikan file tidak rusak.";
                        }
                        error_log("Error parsing CV: " . ($cv_text ?: "Empty result"));
                    }
                } catch (Exception $e) {
                    error_log("Error processing CV: " . $e->getMessage());
                    $errors[] = "Terjadi kesalahan saat memproses CV: " . $e->getMessage();
                }
                } else {
                    $errors[] = "Gagal mengupload file. Silakan coba lagi.";
                    error_log("Error uploading file: Could not move file to $upload_file");
                }
            }
        }
    } else {
        $errors[] = "Silakan pilih file CV untuk diupload.";
        error_log("No file uploaded");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test CV Processing - JobMatch</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/upload.css">
</head>
<body>
    <div class="container">
        <div class="upload-container text-center">
            <h1 class="mt-5 mb-4">Test CV Processing</h1>
            
            <!-- Form Upload -->
            <div class="card p-4 shadow upload-form">
                <div class="card-body">
                    <p class="card-text mb-4">Upload your CV in PDF, DOC, or DOCX format. We will analyze it and show the parsed content.</p>
                    <form action="" method="post" enctype="multipart/form-data" id="uploadForm">
                        <div class="mb-4">
                            <label for="cv_file" class="form-label">Pilih File CV</label>
                            <input class="form-control" type="file" id="cv_file" name="cv_file" accept=".pdf,.doc,.docx" required 
                                onchange="validateFile(this)">
                            <div class="form-text">Format yang didukung: PDF, DOC, DOCX (Maks. 5MB)</div>
                            <div id="fileError" class="invalid-feedback"></div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="loadingSpinner" role="status" aria-hidden="true"></span>
                                <span id="submitText">Upload & Process</span>
                            </button>
                        </div>
                    </form>

                    <script>
                    function validateFile(input) {
                        const file = input.files[0];
                        const fileError = document.getElementById('fileError');
                        const submitBtn = document.getElementById('submitBtn');
                        
                        if (file) {
                            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                            const maxSize = 5 * 1024 * 1024; // 5MB
                            
                            if (!allowedTypes.includes(file.type)) {
                                input.value = '';
                                fileError.textContent = 'Format file tidak didukung. Silakan upload file PDF, DOC, atau DOCX.';
                                input.classList.add('is-invalid');
                                submitBtn.disabled = true;
                                return;
                            }
                            
                            if (file.size > maxSize) {
                                input.value = '';
                                fileError.textContent = 'Ukuran file terlalu besar. Maksimal 5MB.';
                                input.classList.add('is-invalid');
                                submitBtn.disabled = true;
                                return;
                            }
                            
                            input.classList.remove('is-invalid');
                            submitBtn.disabled = false;
                        }
                    }

                    document.getElementById('uploadForm').addEventListener('submit', function() {
                        document.getElementById('loadingSpinner').classList.remove('d-none');
                        document.getElementById('submitText').textContent = 'Processing...';
                        document.getElementById('submitBtn').disabled = true;
                    });
                    </script>
                </div>

                <!-- Messages -->
                <?php if (!empty($messages)): ?>
                    <div class="alert alert-info mt-4 text-start">
                        <h5 class="alert-heading mb-3">Process Log:</h5>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($messages as $message): ?>
                                <li><i class="fas fa-info-circle me-2"></i><?php echo htmlspecialchars($message); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Errors -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mt-4 text-start">
                        <h5 class="alert-heading mb-3">Errors:</h5>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Parsed CV Text -->
                <?php if (!empty($cv_text)): ?>
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Parsed CV Content</h5>
                        </div>
                        <div class="card-body">
                            <pre class="bg-light p-4 rounded border"><?php echo htmlspecialchars($cv_text); ?></pre>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
