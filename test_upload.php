<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once 'config/database.php';
require_once 'classes/NaiveBayes.php';
require_once 'classes/CVParser.php';

echo "Starting test upload process...\n\n";

try {
    // Initialize CVParser
    $parser = new CVParser();
    
    // Test file path
    $test_file = 'uploads/test_cv.txt';
    
    echo "Reading test CV from: $test_file\n";
    
    // Parse CV
    $cv_text = $parser->parseCV($test_file, 'text/plain');
    
    if ($cv_text && !empty($cv_text) && strpos($cv_text, "Error:") !== 0) {
        echo "Successfully parsed CV text (" . strlen($cv_text) . " characters)\n";
        
        // Initialize Naive Bayes
        $naive_bayes = new NaiveBayes($conn);
        
        // Detect language
        $cv_language = $naive_bayes->detectLanguage($cv_text);
        echo "Detected language: $cv_language\n";
        
        // Find matching jobs
        $matching_jobs = $naive_bayes->findMatchingJobs($cv_text, $cv_language);
        
        echo "\nFound " . count($matching_jobs) . " matching jobs:\n";
        foreach ($matching_jobs as $job) {
            echo "\n--------------------\n";
            echo "Title: " . $job['judul'] . "\n";
            echo "Company: " . $job['perusahaan'] . "\n";
            echo "Match Score: " . round($job['match_score'] * 100) . "%\n";
            echo "Location: " . $job['lokasi'] . "\n";
        }
        
    } else {
        echo "Error parsing CV: " . ($cv_text ?: "Empty result") . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
