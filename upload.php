<?php
session_start();
require 'dbconn.php';

$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!isset($_FILES['file']) || !isset($_POST['form_id'])) {
    http_response_code(400);
    echo "Missing file or form ID";
    exit();
}

$formID = mysqli_real_escape_string($conn, $_POST['form_id']);
$file = $_FILES['file'];
$fileName = basename($file['name']);
$fileTmp = $file['tmp_name'];
$fileSize = $file['size'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];

if (!in_array($fileExt, $allowedExts)) {
    http_response_code(400);
    echo "Invalid file type.";
    exit();
}

if ($fileSize > 10485760) {
    http_response_code(400);
    echo "File too large.";
    exit();
}

// Unique filename like in code.php
$timestamp = date('YmdHis');
$storedName = $timestamp . "_" . $fileName;
$targetFile = $uploadDir . $storedName;

if (move_uploaded_file($fileTmp, $targetFile)) {
    // Fetch existing files for this form
    $result = mysqli_query($conn, "SELECT file FROM permit WHERE id='$formID'");
    $row = mysqli_fetch_assoc($result);
    $existingFiles = !empty($row['file']) ? explode(",", $row['file']) : [];

    // Append and update the DB
    $existingFiles[] = $targetFile;
    $updatedFileList = implode(",", $existingFiles);

    mysqli_query($conn, "UPDATE permit SET file='$updatedFileList' WHERE id='$formID'");

    echo $targetFile;
} else {
    http_response_code(500);
    echo "Failed to move uploaded file.";
}
?>
