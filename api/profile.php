<?php

session_start();


if (!isset($_SESSION['login'])) {
   
    $errorResponse = json_encode(['success' => false, 'error' => 'Unauthorized access']);
    header('Content-Type: application/json');
    echo $errorResponse;
    exit();
}


$imageUrl = $_SESSION['profilepic'];
if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {

    $errorResponse = json_encode(['success' => false, 'error' => 'Invalid image URL']);
    header('Content-Type: application/json');
    echo $errorResponse;
    exit();
}


$sanitizedUrl = htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8');


$imageContent = file_get_contents($sanitizedUrl);
if ($imageContent === false) {
  
    $errorResponse = json_encode(['success' => false, 'error' => 'Failed to fetch image content']);
    header('Content-Type: application/json');
    echo $errorResponse;
    exit();
}

$detectedContentType = exif_imagetype($sanitizedUrl);
$validImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
if (!in_array($detectedContentType, $validImageTypes)) {
    $errorResponse = json_encode(['success' => false, 'error' => 'Invalid image type']);
    header('Content-Type: application/json');
    echo $errorResponse;
    exit();
}

header('Content-Type: ' . image_type_to_mime_type($detectedContentType));


echo $imageContent;