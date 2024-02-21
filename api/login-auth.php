<?php
require_once("database.php");

class Login {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function loginUser($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }
}

$login = new Login($pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['username']) && isset($data['password'])){
    
        $username = filter_var($data['username'], FILTER_SANITIZE_STRING);
        $password = filter_var($data['password'], FILTER_SANITIZE_STRING);

        $check = $login->loginUser($username, $password);
        if ($check) {
            
            session_start();
            $_SESSION['login'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['pid'] = session_id();

            $stmt = $pdo->prepare("SELECT profiepic FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $profilePic = $stmt->fetchColumn();
            if ($profilePic) {
                $_SESSION['profilepic'] = $profilePic;
            }

            echo json_encode(['success' => true, 'message' => 'Login Successful']);
        }
        else {
            echo json_encode(['success' => false, 'message' => 'Incorrect username or password']);
        }
    }
    else {
        exit("Skids not allowed!");
    }
    
} else {
    exit("You found an easter egg - nano");
}



?>