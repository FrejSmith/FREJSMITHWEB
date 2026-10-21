<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\SiteController;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $dotenv->load($envFile);
    // Forcer le chargement des variables d'environnement
    $envContent = file_get_contents($envFile);
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $value = trim($value, '"');
        if (getenv($key) !== false) {
            continue;
        }
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

session_start();

$adminUser = getenv('ADMIN_USER') ?: 'admin';
$adminPass = getenv('ADMIN_PASS') ?: 'ChangeMe123!';

$controller = new SiteController();
$loggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'logout') {
        session_destroy();
        header('Location: admin.php');
        exit;
    }

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === $adminUser && $password === $adminPass) {
        $_SESSION['admin_logged_in'] = true;
        $loggedIn = true;
        $controller->logAdminAttempt($username, true, $_SERVER);
    } else {
        $controller->logAdminAttempt($username, false, $_SERVER);
        $message = 'Identifiants incorrects.';
    }
}

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if ($loggedIn) {
    $controller = new SiteController();
    $contacts = $controller->getContacts();
    $reviews = $controller->getReviews();
    $reservations = $controller->getReservations();
    $visitors = $controller->getVisitors();
    $securityLogs = $controller->getSecurityLogs();
    $jsErrors = $controller->getJsErrors();
    $adminAttempts = $controller->getAdminAttempts();
    $threatStats = $controller->getThreatStats();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin FrejSmithWeb</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .admin-section { margin-bottom: 32px; }
        .admin-section h2 { margin-bottom: 12px; }
        .admin-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 8px; }
        .admin-table th { background: #f4f4f4; text-align: left; }
        .form-message { color: #b91c1c; margin-bottom: 16px; }
        .admin-login { max-width: 400px; margin: 40px auto; }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php if (!$loggedIn): ?>
            <div class="admin-login">
                <h1>Connexion administrateur</h1>
                <?php if ($message): ?>
                    <div class="form-message"><?= escapeHtml($message) ?></div>
                <?php endif; ?>
                <form method="post" action="admin.php">
                    <div class="form-group">
                        <label for="username">Utilisateur</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <input type="hidden" name="action" value="login">
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </form>
            </div>
        <?php else: ?>
            <div class="admin-header">
                <div>
                    <h1>Administration FrejSmithWeb</h1>
                    <p>Page réservée à l'administrateur.</p>
                </div>
                <form method="post" action="admin.php">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="btn btn-secondary">Se déconnecter</button>
                </form>
            </div>

            <div class="admin-section">
                <h2>📊 Statistiques de Sécurité</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                    <div style="background: #120926; padding: 16px; border-radius: 16px; text-align: center; border: 1px solid rgba(124, 58, 237, 0.25);">
                        <h3 style="margin: 0; color: #c4b5fd;"><?= $threatStats['total_visitors'] ?? 0 ?></h3>
                        <p style="margin: 4px 0 0 0; font-size: 14px; color: #d8b4fe;">Visiteurs totaux</p>
                    </div>
                    <div style="background: #120926; padding: 16px; border-radius: 16px; text-align: center; border: 1px solid rgba(124, 58, 237, 0.25);">
                        <h3 style="margin: 0; color: #fca5a5;"><?= $threatStats['total_security_events'] ?? 0 ?></h3>
                        <p style="margin: 4px 0 0 0; font-size: 14px; color: #d8b4fe;">Événements de sécurité</p>
                    </div>
                    <div style="background: #120926; padding: 16px; border-radius: 16px; text-align: center; border: 1px solid rgba(124, 58, 237, 0.25);">
                        <h3 style="margin: 0; color: #fbbf24;"><?= $threatStats['high_threat_events'] ?? 0 ?></h3>
                        <p style="margin: 4px 0 0 0; font-size: 14px; color: #d8b4fe;">Menaces élevées</p>
                    </div>
                    <div style="background: #120926; padding: 16px; border-radius: 16px; text-align: center; border: 1px solid rgba(124, 58, 237, 0.25);">
                        <h3 style="margin: 0; color: #86efac;"><?= $threatStats['blocked_attempts'] ?? 0 ?></h3>
                        <p style="margin: 4px 0 0 0; font-size: 14px; color: #d8b4fe;">Tentatives bloquées</p>
                    </div>
                </div>
            </div>

            <div class="admin-section">
                <h2>🚨 Événements de Sécurité Récent (Dernière heure)</h2>
                <table class="admin-table">
                    <thead>
                        <tr><th>Type</th><th>Sévérité</th><th>IP</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach (($threatStats['recent_events'] ?? []) as $event): ?>
                            <tr>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px;
                                        <?php
                                        $color = match($event['severity']) {
                                            'critical' => 'background: #dc2626; color: white;',
                                            'high' => 'background: #ea580c; color: white;',
                                            'medium' => 'background: #d97706; color: white;',
                                            default => 'background: #6b7280; color: white;'
                                        };
                                        echo $color;
                                        ?>">
                                        <?= escapeHtml($event['event_type']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px;
                                        <?php
                                        $color = match($event['severity']) {
                                            'critical' => 'background: #dc2626; color: white;',
                                            'high' => 'background: #ea580c; color: white;',
                                            'medium' => 'background: #d97706; color: white;',
                                            default => 'background: #6b7280; color: white;'
                                        };
                                        echo $color;
                                        ?>">
                                        <?= escapeHtml($event['severity']) ?>
                                    </span>
                                </td>
                                <td><?= escapeHtml($event['ip_address']) ?></td>
                                <td><?= escapeHtml($event['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-section">
                <h2>📍 IPs Suspectes</h2>
                <table class="admin-table">
                    <thead>
                        <tr><th>IP Address</th><th>Nombre d'événements</th><th>Score de menace max</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach (($threatStats['suspicious_ips'] ?? []) as $ip): ?>
                            <tr>
                                <td><?= escapeHtml($ip['ip_address']) ?></td>
                                <td><?= escapeHtml((string)$ip['count']) ?></td>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px;
                                        <?php
                                        $score = (int)$ip['max_threat'];
                                        $color = $score > 80 ? 'background: #dc2626; color: white;' :
                                                ($score > 60 ? 'background: #ea580c; color: white;' :
                                                ($score > 40 ? 'background: #d97706; color: white;' :
                                                'background: #6b7280; color: white;'));
                                        echo $color;
                                        ?>">
                                        <?= $score ?>/100
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-section">
                <h2>Messages de contact (<?= count($contacts) ?>)</h2>
                <table class="admin-table">
                    <thead>
                        <tr><th>ID</th><th>Nom</th><th>Email</th><th>Sujet</th><th>Message</th><th>Créé le</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td><?= escapeHtml((string)$contact['id']) ?></td>
                                <td><?= escapeHtml($contact['name']) ?></td>
                                <td><?= escapeHtml($contact['email']) ?></td>
                                <td><?= escapeHtml($contact['subject']) ?></td>
                                <td><?= escapeHtml($contact['message']) ?></td>
                                <td><?= escapeHtml($contact['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-section">
                <h2>Avis (<?= count($reviews) ?>)</h2>
                <table class="admin-table">
                    <thead>
                        <tr><th>ID</th><th>Nom</th><th>Email</th><th>Note</th><th>Avis</th><th>Créé le</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?= escapeHtml((string)$review['id']) ?></td>
                                <td><?= escapeHtml($review['name']) ?></td>
                                <td><?= escapeHtml($review['email']) ?></td>
                                <td><?= escapeHtml((string)$review['rating']) ?></td>
                                <td><?= escapeHtml($review['review']) ?></td>
                                <td><?= escapeHtml($review['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-section">
                <h2>Réservations (<?= count($reservations) ?>)</h2>
                <table class="admin-table">
                    <thead>
                        <tr><th>ID</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Date</th><th>Heure</th><th>Service</th><th>Message</th><th>Créé le</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?= escapeHtml((string)$reservation['id']) ?></td>
                                <td><?= escapeHtml($reservation['name']) ?></td>
                                <td><?= escapeHtml($reservation['email']) ?></td>
                                <td><?= escapeHtml($reservation['phone']) ?></td>
                                <td><?= escapeHtml($reservation['reservation_date']) ?></td>
                                <td><?= escapeHtml($reservation['reservation_time']) ?></td>
                                <td><?= escapeHtml($reservation['service']) ?></td>
                                <td><?= escapeHtml($reservation['message']) ?></td>
                                <td><?= escapeHtml($reservation['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-section">
                <h2>🔒 Tentatives de Connexion Admin (<?= count($adminAttempts) ?>)</h2>
                <table class="admin-table">
                    <thead>
                        <tr><th>IP</th><th>Utilisateur</th><th>Succès</th><th>User-Agent</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($adminAttempts as $attempt): ?>
                            <tr>
                                <td><?= escapeHtml($attempt['ip_address']) ?></td>
                                <td><?= escapeHtml($attempt['username']) ?></td>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px;
                                        background: <?= $attempt['success'] ? '#16a34a' : '#dc2626' ?>; color: white;">
                                        <?= $attempt['success'] ? 'Succès' : 'Échec' ?>
                                    </span>
                                </td>
                                <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= escapeHtml($attempt['user_agent']) ?>
                                </td>
                                <td><?= escapeHtml($attempt['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-section">
                <h2>⚠️ Erreurs JavaScript (<?= count($jsErrors) ?>)</h2>
                <table class="admin-table">
                    <thead>
                        <tr><th>Message</th><th>Fichier</th><th>Ligne</th><th>Colonne</th><th>URL</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jsErrors as $error): ?>
                            <tr>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= escapeHtml($error['message']) ?>
                                </td>
                                <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= escapeHtml($error['filename']) ?>
                                </td>
                                <td><?= escapeHtml((string)$error['lineno']) ?></td>
                                <td><?= escapeHtml((string)$error['colno']) ?></td>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= escapeHtml($error['url']) ?>
                                </td>
                                <td><?= escapeHtml($error['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-section">
                <h2>🛡️ Logs de Sécurité Détaillés (<?= count($securityLogs) ?>)</h2>
                <table class="admin-table">
                    <thead>
                        <tr><th>Type</th><th>Sévérité</th><th>IP</th><th>Score</th><th>Bloqué</th><th>Session</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($securityLogs as $log): ?>
                            <tr>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px;
                                        <?php
                                        $color = match($log['severity']) {
                                            'critical' => 'background: #dc2626; color: white;',
                                            'high' => 'background: #ea580c; color: white;',
                                            'medium' => 'background: #d97706; color: white;',
                                            default => 'background: #6b7280; color: white;'
                                        };
                                        echo $color;
                                        ?>">
                                        <?= escapeHtml($log['event_type']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px;
                                        <?php
                                        $color = match($log['severity']) {
                                            'critical' => 'background: #dc2626; color: white;',
                                            'high' => 'background: #ea580c; color: white;',
                                            'medium' => 'background: #d97706; color: white;',
                                            default => 'background: #6b7280; color: white;'
                                        };
                                        echo $color;
                                        ?>">
                                        <?= escapeHtml($log['severity']) ?>
                                    </span>
                                </td>
                                <td><?= escapeHtml($log['ip_address']) ?></td>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px;
                                        <?php
                                        $score = (int)$log['threat_score'];
                                        $color = $score > 80 ? 'background: #dc2626; color: white;' :
                                                ($score > 60 ? 'background: #ea580c; color: white;' :
                                                ($score > 40 ? 'background: #d97706; color: white;' :
                                                'background: #6b7280; color: white;'));
                                        echo $color;
                                        ?>">
                                        <?= $score ?>/100
                                    </span>
                                </td>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px;
                                        background: <?= $log['blocked'] ? '#dc2626' : '#16a34a' ?>; color: white;">
                                        <?= $log['blocked'] ? 'Oui' : 'Non' ?>
                                    </span>
                                </td>
                                <td style="max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= escapeHtml(substr($log['session_id'], 0, 8)) ?>...
                                </td>
                                <td><?= escapeHtml($log['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
