<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\SiteController;
use Symfony\Component\Dotenv\Dotenv;

// Headers de sécurité
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}

// Charger les variables d'environnement
$dotenv = new Dotenv();
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, '"');
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

$action = $_GET['action'] ?? $_POST['action'] ?? null;
$controller = new SiteController();
$result = ['success' => false, 'message' => 'Action non reconnue'];

try {
    // Gérer les données JSON du POST
    $inputData = $_POST;
    if (empty($inputData) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputData = json_decode(file_get_contents('php://input'), true) ?: [];
    }

    switch ($action) {
        // Formulaires
        case 'submit-contact':
            $result = $controller->submitContact($inputData);
            break;

        case 'submit-review':
            $result = $controller->submitReview($inputData);
            break;

        case 'submit-reservation':
            $result = $controller->submitReservation($inputData);
            break;

        // Tracking et sécurité
        case 'track-visitor':
            $result = $controller->trackVisitor($inputData, $_SERVER);
            break;

        case 'log-security-event':
            $result = $controller->logSecurityEvent($inputData, $_SERVER);
            break;

        case 'log-js-error':
            $result = $controller->logJsError($inputData, $_SERVER);
            break;

        // Admin endpoints (nécessitent authentification)
        case 'get-threat-stats':
            session_start();
            if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
                http_response_code(403);
                $result = ['success' => false, 'message' => 'Accès refusé'];
                break;
            }
            $result = $controller->getThreatStats();
            break;

        case 'get-security-logs':
            session_start();
            if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
                http_response_code(403);
                $result = ['success' => false, 'message' => 'Accès refusé'];
                break;
            }
            $result = ['success' => true, 'logs' => $controller->getSecurityLogs()];
            break;

        case 'get-js-errors':
            session_start();
            if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
                http_response_code(403);
                $result = ['success' => false, 'message' => 'Accès refusé'];
                break;
            }
            $result = ['success' => true, 'errors' => $controller->getJsErrors()];
            break;

        case 'get-admin-attempts':
            session_start();
            if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
                http_response_code(403);
                $result = ['success' => false, 'message' => 'Accès refusé'];
                break;
            }
            $result = ['success' => true, 'attempts' => $controller->getAdminAttempts()];
            break;

        // Données publiques
        case 'get-contacts':
            $result = ['success' => true, 'data' => $controller->getContacts()];
            break;

        case 'get-reviews':
            $result = ['success' => true, 'data' => $controller->getReviews()];
            break;

        case 'get-reservations':
            $result = ['success' => true, 'data' => $controller->getReservations()];
            break;

        case 'get-visitors':
            $result = ['success' => true, 'data' => $controller->getVisitors()];
            break;

        default:
            http_response_code(400);
            $result = ['success' => false, 'message' => 'Action non trouvée'];
    }
} catch (Exception $e) {
    http_response_code(500);
    $result = ['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()];
}

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
