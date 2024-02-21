
<?php
require_once("database.php");

require_once('../vendor/autoload.php');



class Register {
    private $db;
    

    public function __construct($db) {
        $this->db = $db;
        
        

    }

    public function checkKey($key) {
        try {
            $client = new \GuzzleHttp\Client();
            $seller_key = 'Key';
            $response =  $client->request('GET', "https://keyauth.win/api/seller/?sellerkey=$seller_key&type=verify&key=$key", [
                'headers' => [
                  'accept' => 'application/json',
                ],
            ]);
            $responseBody = $response->getBody();
            $responseData = json_decode($responseBody, true);
            return true;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return false;
        }
    }

    public function registerUser($username, $password, $key) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $profile = "https://media.discordapp.net/attachments/1183410827521425459/1186825647516033064/image-255.png?ex=6594a884&is=65823384&hm=eb9cce540cf37bd1ea7e6038d5797d0edb8dfeec0dd5e9be888f8396da7170f3&=&format=webp&quality=lossless&width=628&height=473";


        $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $checkStmt->bindParam(':username', $username, PDO::PARAM_STR);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();
        
        if ($count > 0) {
            exit(json_encode(['success' => false, 'message' => 'Username already registered']));
        }

        $checkUsedStmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE `key` = :key");
        $checkUsedStmt->bindParam(':key', $key, PDO::PARAM_STR);
        $checkUsedStmt->execute();
        $keyCount = $checkUsedStmt->fetchColumn();
        
        if ($keyCount > 0) {
            exit(json_encode(['success' => false, 'message' => 'Key has already been registered']));
        }

        $stmt = $this->db->prepare("INSERT INTO users (username, password, `key`, profiepic) VALUES (:username, :password, :key, :profiepic)");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':key', $key, PDO::PARAM_STR);
        $stmt->bindParam(':profiepic', $profile, PDO::PARAM_STR);
        $stmt->execute();
        
        return $this->db->lastInsertId();
    }
}

$register = new Register($pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = htmlspecialchars(strip_tags($data['username']));
    $password = htmlspecialchars(strip_tags($data['password']));
    $key = htmlspecialchars(strip_tags($data['key']));

    if (strlen($username) < 3) {
        exit(json_encode(['success' => false, 'message' => 'Username has to be at least three letters long']));
    }

    if (preg_match('/[^a-zA-Z0-9-]/', $key)) {
        exit(json_encode(['success' => false, 'message' => 'Key contains special characters']));
    }

    if (preg_match('/[^a-zA-Z0-9-]/', $username)) {
        exit(json_encode(['success' => false, 'message' => 'Username contains special characters']));
    }

    $check_key = $register->checkKey($key);
    if ($check_key) {
        $registerId = $register->registerUser($username, $password, $key);
        if ($registerId) {
            echo json_encode(['success' => true, 'message' => 'Registration Successful', 'registerId' => $registerId]);
        }
        else {
            exit(json_encode(['success' => false, 'message' => 'Registration Failed']));
        }
    }else {
        exit(json_encode(['success' => false, 'message' => 'Key Not Found']));
    }
    
   
}
?>

