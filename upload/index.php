<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/sessions.php');
$fileName = $_FILES["file1"]["name"];
$fileTmpLoc = $_FILES["file1"]["tmp_name"];
$fileType = $_FILES["file1"]["type"];
$fileSize = $_FILES["file1"]["size"];
$fileErrorMsg = $_FILES["file1"]["error"];

if (!$fileTmpLoc) {
    echo "ERROR: Please browse for a file before clicking upload";
    exit();
}

// create session id folder, if needed
$targetFolder = $_SERVER['DOCUMENT_ROOT'] . "/fileUpload/cart/" . session_id();
if (!is_dir($targetFolder)) {
    mkdir($targetFolder);
}

$fileName = str_replace(" ","_", $fileName);

if (move_uploaded_file($fileTmpLoc, "$targetFolder/$fileName")) {
    echo "$fileName upload is complete";
} else {
    echo "could not move file to folder";
}
?>