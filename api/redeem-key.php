
<?php

session_start();

if (!isset($_SESSION["login"])){
    header('Location: login');
    exit;
}


require_once('vendor/autoload.php');
require_once('database.php');

$client = new \GuzzleHttp\Client();

$seller_key = '';


class CreateUser {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function Create($seller_key, $user_key, $duration, $level) {
        $client = new \GuzzleHttp\Client();
        $randomUsername = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 8);
        $randomPassword = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 12);

        $response = $client->request('GET', "https://keyauth.win/api/seller/?sellerkey=$seller_key&type=activate&user=$randomUsername&key=$user_key&pass=$randomPassword", [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);
        $responseBody = $response->getBody();
        $responseData = json_decode($responseBody, true);

        
        
        if ($responseData['message'] == "Key Already Used.") {
            exit(json_encode(['success' => false, 'message' => 'Vision Key Already Used']));
        }
        $timeleft = $responseData['info']['subscriptions']['subscriptions'][0]['timeleft'];
        $expires = $timeleft = $responseData['info']['subscriptions']['subscriptions'][0]['expiry'];
        

        $userses = $_SESSION['username'];
        $stmt = $this->db->prepare("INSERT INTO keyauth_keys (keyauth_username, keyauth_password, expires, `key`, username, timeleft, `level`) VALUES (:username, :password, :duration, :key, :paneluser, :timeleft, :level)");
        $stmt->bindParam(':username', $randomUsername, PDO::PARAM_STR);
        $stmt->bindParam(':password', $randomPassword, PDO::PARAM_STR);
        $stmt->bindParam(':duration', $expires, PDO::PARAM_INT);
        $stmt->bindParam(':key', $user_key, PDO::PARAM_STR);
        $stmt->bindParam(':paneluser', $userses, PDO::PARAM_STR);
        $stmt->bindParam(':timeleft', $timeleft, PDO::PARAM_INT);
        $stmt->bindParam(':level', $level, PDO::PARAM_INT);
        
        if($stmt->execute() == true){
            echo json_encode(['success' => true, 'message' => 'Vision key is now Active!']);
        }else{
            echo json_encode(['success'=> false, 'message'=> 'Something went wrong, contact vision support!']);
        }
        

         
    }
    
}


class KeyInfo {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getInfo($seller_key, $user_key) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM keyauth_keys WHERE username = :username");
        $stmt->bindParam(':username', strip_tags($_SESSION['username']), PDO::PARAM_STR);
        $stmt->execute();
        $activeKeyCount = $stmt->fetchColumn();
        
        if ($activeKeyCount > 0) {
            echo json_encode(['success' => false, 'message' => 'More than 1 active key found']);
            return;
        }

	if (preg_match('/[^a-zA-Z0-9-]/', $user_key)) {
            exit(json_encode(['success' => false, 'message' => 'Key contains special characters']));
        }

	$stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE `key` = :user_key AND username != :username");
        $stmt->bindParam(':user_key', strip_tags($user_key), PDO::PARAM_STR);
        $stmt->bindParam(':username', strip_tags($_SESSION['username']), PDO::PARAM_STR);
        $stmt->execute();
        $checkkey = $stmt->fetchColumn();

        if ($checkkey > 0){
            exit(json_encode(['success' => false, 'message' => 'Key has been registered to another user. Please use a different key!']));
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', "https://keyauth.win/api/seller/?sellerkey=$seller_key&type=info&key=$user_key", [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);

        $responseBody = $response->getBody();
        $responseData = json_decode($responseBody, true);
        #echo $responseBody;
        $duration = $responseData['duration'];
        $level = $responseData['level'];

        $createUser = new CreateUser($this->db);
        $createUser->Create($seller_key, $user_key, $duration, $level);
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $user_key = $data['key'] ?? '';
    
    if (!empty($user_key)) {
        try {
            $response = $client->request('GET', "https://keyauth.win/api/seller/?sellerkey=$seller_key&type=verify&key=$user_key", [
                'headers' => [
                  'accept' => 'application/json',
                ],
            ]);
            $responseBody = $response->getBody();
            $responseData = json_decode($responseBody, true);
            
            if ($responseData['success'] === true) {
                $keyInfo = new KeyInfo($pdo);
                $keyInfo->getInfo($seller_key, $user_key);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid Vision Key']);
            }
            
        } catch (GuzzleHttp\Exception\ClientException $e) {
            echo json_encode(['success' => false, 'message' => 'Invalid Vision Key']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid Vision Key']);
    }
}
?>
