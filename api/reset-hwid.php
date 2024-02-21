<?php

session_start();
if (!isset($_SESSION["login"])){
    header('Location: login');
    exit;
}

require_once("database.php");
require_once('vendor/autoload.php');
class ResetUser {
    private $client;

    public function __construct() {
        $this->client = new \GuzzleHttp\Client();
    }

    public function resetUser($sellerKey, $user) {
        $response = $this->client->request('POST', "https://keyauth.win/api/seller/?sellerkey=$sellerKey&type=resetuser&user=$user", [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);
       
        echo json_encode(['success' => true, 'message' => 'Reset HWID!']);
       
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userses = strip_tags($_SESSION['username']);

    $stmt = $pdo->prepare("SELECT * FROM keyauth_keys WHERE username = :username");
    $stmt->bindParam(':username', $userses, PDO::PARAM_STR);
    $stmt->execute();
    $result_user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result_user) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM hwid_reset WHERE username = :username");
        $stmt->bindParam(':username', $userses, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchColumn();

        if ($result > 2) {
            echo json_encode(['success' => false, 'message' => 'You cannot have more than 3 HWID resets, contact vision support if you need more!']);
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO hwid_reset (username) VALUES (:username)");


        $stmt->bindParam(':username', $userses, PDO::PARAM_STR);
        if($stmt->execute()){
            $reset = new ResetUser();
            
            $reset->resetUser("Key", $result_user['keyauth_username']);
        }

        
    } else {
        echo json_encode(['success' => false, 'message' => 'No Active license']);
    }
}
?>