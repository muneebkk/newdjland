<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=djland', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get the request URI and extract the endpoint
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove the base path to get just the endpoint
$base_path = '/newdjland/app/api_fallback.php';
$endpoint = str_replace($base_path, '', $path);
$endpoint = trim($endpoint, '/');

// Handle different endpoints
switch ($endpoint) {
    case 'member/1/active_shows':
    case 'member/1/shows/active':
        try {
            // Query for shows assigned to member_id = 1
            $stmt = $pdo->prepare("
                SELECT ms.show_id as id, s.name, s.host, s.crtc_default as crtc, s.lang_default as lang
                FROM member_show ms
                JOIN shows s ON ms.show_id = s.id
                WHERE ms.member_id = 1 AND s.active = 1
                ORDER BY s.name
            ");
            $stmt->execute();
            $shows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format response to match expected structure
            $response = ['shows' => []];
            foreach ($shows as $show) {
                $response['shows'][] = [
                    'id' => $show['id'],
                    'show' => [
                        'id' => $show['id'],
                        'name' => $show['name'],
                        'host' => $show['host']
                    ],
                    'crtc' => $show['crtc'],
                    'lang' => $show['lang']
                ];
            }
            echo json_encode($response);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
        }
        break;
        
    case 'show/active':
        try {
            $stmt = $pdo->prepare("
                SELECT id, name, host
                FROM shows
                WHERE active = 1
                ORDER BY name
            ");
            $stmt->execute();
            $shows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($shows);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
        }
        break;
        
    default:
        // For any other endpoint, return empty array or appropriate response
        echo json_encode([]);
        break;
}
?> 