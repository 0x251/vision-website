<?php

session_start();
session_write_close();
require_once("database.php");

if (!isset($_SESSION["login"])){
    header('Location: login');
    exit;
}


ignore_user_abort(true);
set_time_limit(0);


header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');

function send_message($id, $message) {
    echo "id: {$id}\n";
    echo "data: {$message}\n\n";
    ob_flush();
    flush();
}

$lastId = isset($_SERVER["HTTP_LAST_EVENT_ID"]) ? intval($_SERVER["HTTP_LAST_EVENT_ID"]) : null;
if ($lastId === null) {
  $lastId = 0;
} else {
  $lastId++;
}

$userses = strip_tags($_SESSION["username"]);
$stmt = $pdo->prepare("SELECT * FROM `keyauth_keys` WHERE username = :username");
$stmt->bindParam(':username', $userses, PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetch(PDO::FETCH_ASSOC);


while (!connection_aborted()) {

    if ($results){

        $expires = $results['expires'];
        $auth_user = $results['keyauth_username'];
        $auth_pass = $results['keyauth_password'];

        $level = $results['level'];

        if ($level == 2){
            $level = "Internal";
        }

        if ($level == 3){
            $level = "External";
        }
        
        if (time() > $expires) {
            $stmt = $pdo->prepare("DELETE FROM `keyauth_keys` WHERE username = :username");
            $stmt->bindParam(':username', $userses, PDO::PARAM_STR);
            $stmt->execute();
        }
        
        $data = [
        'success' => true, 
        'timeleft' => $expires, 
        'auth_username' => $auth_user, 
        'auth_password' => $auth_pass, 
        'level' => $level, 
       
        ];
        send_message($lastId, json_encode($data));
        
    } else {
        exit(json_encode(['success' => false, 'message' => 'NO']));
    }
    if ( connection_aborted() ) exit();
    sleep(1);

    
}
?>