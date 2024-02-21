<?php
// download.php

session_start();

if (!isset($_SESSION["login"])){
    header('Location: login');
    exit;
}

require_once("database.php");

$fileMap = [
    'Internal' => 'HEHHE',
    'External'=> 'HEHE'
];

header('Content-Type: application/json');


function downloadFile($url) {

    $file_name = uniqid('vision_', true);

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Content-Disposition: attachment; filename=\"$file_name\"");
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    flush();
    readfile($url);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $content = trim(file_get_contents("php://input"));
    $decoded = json_decode($content, true);
    if (is_array($decoded) && isset($decoded['file_key'])) {
        $file_key = $decoded['file_key'];
        
        
        $userses = strip_tags($_SESSION['username']);

        $stmt = $pdo->prepare("SELECT * FROM keyauth_keys WHERE username = :username");
        $stmt->bindParam(':username', $userses, PDO::PARAM_STR);
        $stmt->execute();
        $result_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result_user) {
            if ($result_user["level"] == 2){
                $check = "Internal";
            }

            else if ($result_user["level"] == 3){
                $check = "External";
            }


            if ($file_key == $check){
                $url = $fileMap[$file_key] ?? null;
                downloadFile($url);
                echo json_encode(['success' => true, 'message' => 'Downloading Vision Binary!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'You have no Access to download this vision resource!. If this is an issue please contact visions support!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'You Dont have an active Vision key!']);
        }
       
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'message' => 'Failed Request']);
        exit;
    }
} else {
  
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Failed Request']);
    exit;
}
?>
