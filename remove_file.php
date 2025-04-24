<?php
session_start();
require 'dbconn.php';

if (!isset($_POST['file']) || !isset($_POST['form_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing file or form ID']);
    exit();
}

$formID = mysqli_real_escape_string($conn, $_POST['form_id']);
$fileToDelete = $_POST['file'];

// 1. Fetch existing files from database
$result = mysqli_query($conn, "SELECT file FROM permit WHERE id='$formID'");
if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Form ID not found']);
    exit();
}

$row = mysqli_fetch_assoc($result);
$existingFiles = !empty($row['file']) ? explode(",", $row['file']) : [];

// 2. Remove the file from the array
$updatedFiles = array_filter($existingFiles, function($file) use ($fileToDelete) {
    return trim($file) !== trim($fileToDelete);
});

// 3. Delete the physical file
if (file_exists($fileToDelete)) {
    unlink($fileToDelete); // remove file
}

// 4. Update the database
$updatedList = implode(",", $updatedFiles);
$update = mysqli_query($conn, "UPDATE permit SET file='$updatedList' WHERE id='$formID'");

if ($update) {
    echo json_encode(['success' => true, 'message' => 'File deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update database']);
}
?>
