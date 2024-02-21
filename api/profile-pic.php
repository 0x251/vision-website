<?php
require_once("database.php");
session_start();

if (!isset($_SESSION["login"])){
    header('Location: login');
    exit;
}


function isImageUrl($url) {
    $url_components = parse_url($url);
    
    
    if (!isset($url_components['scheme']) || !isset($url_components['host'])) {
        return false; 
    }

    if (!isset($url_components['path'])) {
        return false; 
    }

    $path = pathinfo($url_components['path']);
    if (!isset($path['extension'])) {
        return false;
    }

    $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    return in_array(strtolower($path['extension']), $image_extensions);
}


function isKnownImageHost($url) {
   
    $parsedUrl = parse_url($url, PHP_URL_HOST);
    
    $knownHosts = [
        'cdn.discordapp.com', 
        'images.google.com', 
        'www.bing.com', 
        'i.imgur.com', 
        'imgur.com',
        'media.discordapp.com'
    ];
    
    
    foreach ($knownHosts as $host) {
        if (stripos($parsedUrl, $host) !== false) {
            return true;
        }
    }
    
    return false;
}
    

function validateProfilePictureUrl($pdo) {
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if ($data && isset($data['url'])) {
            $url = $data['url'];
            $url = filter_var($url, FILTER_SANITIZE_URL);

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return json_encode(['success' => false, 'message' => 'Invalid Url']);
            }

            $parsed_url = parse_url($url);
            $host = $parsed_url['host'];
            if ($host === 'localhost' || $host === $_SERVER['HTTP_HOST']) {
                return json_encode(['success' => false, 'message' => 'URL cannot be localhost or visions domain']);
            }

          
            if (strpos($url, '.php') !== false) {
                return json_encode(['success' => false, 'message' => 'SKID ALERT CALLING ALL SKIDS - Nano, nice try dummy but did you really think i would allow that!']);
            }
            
            
            if (preg_match('/<script(\s|>)/i', $url) || preg_match('/<\/script(\s|>)/i', $url)) {
                return json_encode(['success' => false, 'message' => 'Alr buddy, I know you think you are a hacker but do you really think i am stupid']);
            }

            $badWords = ['nigger', 'nigga', 'wow', 'dick', 'slut', 'hoe', 'dox', 'logger'];
            if (preg_match('/' . implode('|', $badWords) . '/i', $url)) {
                return json_encode(['success' => false, 'message' => 'Do you really think you are funny... no you arent']);
            }

            if (!isKnownImageHost($url)){
                return json_encode(['success' => false, 'message' => 'This isnt a Whitelisted domain, use [discordCDN, imgur, google-images, bing-images]']);
            }
            
            if (isImageUrl($url)) {
               
                $host = parse_url($url, PHP_URL_HOST);
                if (filter_var($host, FILTER_VALIDATE_IP) && !filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return json_encode(['success' => false, 'message' => 'Invalid Url']);
                }
                
                $headers = get_headers($url, 1);
                if ((!isset($headers['content-type']) && !isset($headers['Content-Type'])) || (isset($headers['content-type']) && strpos($headers['content-type'], 'image/') !== 0) || (isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'image/') !== 0)) {
                    return json_encode(['success' => false, 'message' => 'Failed to get image data']);
                }

               
                $username = strip_tags($_SESSION['username']);
                $insertQuery = "UPDATE users SET `profiepic` = :url WHERE username = :username";
                $stmt = $pdo->prepare($insertQuery);
                $stmt->bindParam(':url', $url);
                $stmt->bindParam(':username', $username);
                $insertResult = $stmt->execute();

                if (!$insertResult) {
                    return json_encode(['success' => false, 'message' => 'Failed to update profile picture']);
                }

                return json_encode(['success' => true, 'message' => 'Updated profile pic. Please relogin to apply changes!']);
            } else {
                return json_encode(['success' => false, 'message' => 'Url provided doesnt contain an image']);
            }
        } else {
            return json_encode(['success' => false, 'message' => 'No Url Provide']);
        }
    } else {
        return json_encode(['success' => false, 'message' => 'Request Failed']);
    }
}


$result = validateProfilePictureUrl($pdo);
echo $result;

?>