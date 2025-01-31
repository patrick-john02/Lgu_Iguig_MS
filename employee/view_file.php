<?php
include('../config/config.php');

// Get the file path from the query string
$file_path = $_GET['file_path'] ?? null;

if (!$file_path) {
    echo "No file specified.";
    exit;
}

// Ensure the file exists
if (!file_exists($file_path)) {
    echo "File not found.";
    exit;
}

// Get the file's mime type
$mime_type = mime_content_type($file_path);
header('Content-Type: ' . $mime_type);

// Serve the file content
readfile($file_path);
exit;
?>
