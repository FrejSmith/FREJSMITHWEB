<?php

namespace App\Controller;

use App\Database;

class SiteController
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    private function sanitize(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    private function isValidEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function isValidPhone(string $value): bool
    {
        return preg_match('/^[0-9+\s\-\.\(\)]{6,25}$/', $value) === 1;
    }

    public function submitContact(array $post): array
    {
        $name = $this->sanitize($post['name'] ?? '');
        $email = $this->sanitize($post['email'] ?? '');
        $subject = $this->sanitize($post['subject'] ?? '');
        $message = $this->sanitize($post['message'] ?? '');

        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            return ['success' => false, 'message' => 'Tous les champs sont requis.'];
        }

        if (!$this->isValidEmail($email)) {
            return ['success' => false, 'message' => 'Email invalide.'];
        }

        $this->db->insert('contacts', [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return ['success' => true, 'message' => 'Message envoyé avec succès !'];
    }

    public function submitReview(array $post): array
    {
        $name = $this->sanitize($post['name'] ?? '');
        $email = $this->sanitize($post['email'] ?? '');
        $rating = (int) ($post['rating'] ?? 0);
        $review = $this->sanitize($post['review'] ?? '');

        if (empty($name) || empty($email) || empty($review) || $rating < 1 || $rating > 5) {
            return ['success' => false, 'message' => 'Tous les champs sont requis et la note doit être entre 1 et 5.'];
        }

        if (!$this->isValidEmail($email)) {
            return ['success' => false, 'message' => 'Email invalide.'];
        }

        $this->db->insert('reviews', [
            'name' => $name,
            'email' => $email,
            'rating' => $rating,
            'review' => $review,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return ['success' => true, 'message' => 'Merci pour votre avis !'];
    }

    public function submitReservation(array $post): array
    {
        $name = $this->sanitize($post['name'] ?? '');
        $email = $this->sanitize($post['email'] ?? '');
        $phone = $this->sanitize($post['phone'] ?? '');
        $date = $this->sanitize($post['date'] ?? '');
        $time = $this->sanitize($post['time'] ?? '');
        $service = $this->sanitize($post['service'] ?? '');
        $message = $this->sanitize($post['message'] ?? '');

        if (empty($name) || empty($email) || empty($phone) || empty($date) || empty($time) || empty($service)) {
            return ['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis.'];
        }

        if (!$this->isValidEmail($email)) {
            return ['success' => false, 'message' => 'Email invalide.'];
        }

        if (!$this->isValidPhone($phone)) {
            return ['success' => false, 'message' => 'Téléphone invalide.'];
        }

        $this->db->insert('reservations', [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'reservation_date' => $date,
            'reservation_time' => $time,
            'service' => $service,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return ['success' => true, 'message' => 'Réservation enregistrée, je vous recontacterai bientôt !'];
    }

    public function trackVisitor(array $post, array $server): array
    {
        $page = $this->sanitize($post['page'] ?? '/');
        $ip = $this->sanitize($server['REMOTE_ADDR'] ?? '');
        $userAgent = $this->sanitize($server['HTTP_USER_AGENT'] ?? '');
        $referrer = $this->sanitize($server['HTTP_REFERER'] ?? '');
        $sessionId = $this->sanitize($post['session_id'] ?? '');
        $fingerprint = $this->sanitize($post['fingerprint'] ?? '');
        $securityData = $post['security_data'] ?? null;

        // Calcul du score de menace
        $threatScore = $this->calculateThreatScore($securityData, $ip);

        $this->db->insert('visitors', [
            'page' => $page,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'referrer' => $referrer,
            'session_id' => $sessionId,
            'fingerprint' => $fingerprint,
            'security_data' => $securityData ? json_encode($securityData) : null,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return ['success' => true, 'message' => 'Visiteur suivi', 'threat_score' => $threatScore];
    }

    public function logSecurityEvent(array $post, array $server): array
    {
        $securityLog = $post['security_log'] ?? null;
        if (!$securityLog) {
            return ['success' => false, 'message' => 'Données de sécurité manquantes'];
        }

        $data = json_decode($securityLog, true);
        if (!$data) {
            return ['success' => false, 'message' => 'Format JSON invalide'];
        }

        $ip = $this->sanitize($server['REMOTE_ADDR'] ?? '');
        $eventType = $this->detectEventType($data);
        $severity = $this->calculateSeverity($data);
        $threatScore = $this->calculateThreatScore($data, $ip);

        $this->db->insert('security_logs', [
            'event_type' => $eventType,
            'severity' => $severity,
            'ip_address' => $ip,
            'user_agent' => $this->sanitize($data['userAgent'] ?? ''),
            'session_id' => $this->sanitize($data['sessionId'] ?? ''),
            'fingerprint' => $this->sanitize($data['fingerprint'] ?? ''),
            'page_url' => $this->sanitize($data['url'] ?? ''),
            'event_data' => json_encode($data),
            'threat_score' => $threatScore,
            'blocked' => $threatScore > 80,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return ['success' => true, 'message' => 'Événement de sécurité loggé', 'severity' => $severity];
    }

    public function logJsError(array $post, array $server): array
    {
        $securityLog = $post['security_log'] ?? null;
        if (!$securityLog) {
            return ['success' => false, 'message' => 'Données d\'erreur manquantes'];
        }

        $data = json_decode($securityLog, true);
        if (!$data) {
            return ['success' => false, 'message' => 'Format JSON invalide'];
        }

        $this->db->insert('js_errors', [
            'message' => $this->sanitize($data['error']['message'] ?? ''),
            'filename' => $this->sanitize($data['error']['filename'] ?? ''),
            'lineno' => (int) ($data['error']['lineno'] ?? 0),
            'colno' => (int) ($data['error']['colno'] ?? 0),
            'user_agent' => $this->sanitize($data['userAgent'] ?? ''),
            'url' => $this->sanitize($data['url'] ?? ''),
            'session_id' => $this->sanitize($data['sessionId'] ?? ''),
            'fingerprint' => $this->sanitize($data['fingerprint'] ?? ''),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return ['success' => true, 'message' => 'Erreur JavaScript loggée'];
    }

    public function logAdminAttempt(string $username, bool $success, array $server): void
    {
        $this->db->insert('admin_attempts', [
            'ip_address' => $this->sanitize($server['REMOTE_ADDR'] ?? ''),
            'username' => $this->sanitize($username),
            'success' => $success,
            'user_agent' => $this->sanitize($server['HTTP_USER_AGENT'] ?? ''),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function calculateThreatScore(?array $securityData, string $ip): int
    {
        $score = 0;

        if (!$securityData) {
            return $score;
        }

        // Vérifier les IP suspectes (liste noire basique)
        $suspiciousIPs = ['127.0.0.1', '0.0.0.0'];
        if (in_array($ip, $suspiciousIPs)) {
            $score += 50;
        }

        // Vérifier les comportements suspects
        if (isset($securityData['suspicious'])) {
            $suspicious = $securityData['suspicious'];

            if ($suspicious['automationDetected'] ?? false) $score += 40;
            if ($suspicious['rapidClicks'] ?? false) $score += 20;
            if ($suspicious['devToolsOpen'] ?? false) $score += 15;
            if ($suspicious['unusualNavigation'] ?? false) $score += 25;
            if ($suspicious['evalAttempt'] ?? false) $score += 60;
            if ($suspicious['scriptInjection'] ?? false) $score += 80;
        }

        // Vérifier les user agents suspects
        $userAgent = $securityData['userAgent'] ?? '';
        $suspiciousUA = ['bot', 'crawler', 'spider', 'scanner', 'exploit'];
        foreach ($suspiciousUA as $ua) {
            if (stripos($userAgent, $ua) !== false) {
                $score += 30;
                break;
            }
        }

        // Vérifier les referrers suspects
        $referrer = $securityData['referrer'] ?? '';
        if (preg_match('/(sql|union|select|script|javascript|onload|onerror)/i', $referrer)) {
            $score += 70;
        }

        return min($score, 100);
    }

    private function detectEventType(array $data): string
    {
        if (isset($data['suspicious'])) {
            $suspicious = $data['suspicious'];

            if ($suspicious['automationDetected'] ?? false) return 'automation_detected';
            if ($suspicious['rapidClicks'] ?? false) return 'rapid_clicks';
            if ($suspicious['devToolsOpen'] ?? false) return 'dev_tools_open';
            if ($suspicious['evalAttempt'] ?? false) return 'eval_attempt';
            if ($suspicious['scriptInjection'] ?? false) return 'script_injection';
        }

        if (isset($data['error'])) {
            return 'javascript_error';
        }

        return 'suspicious_activity';
    }

    private function calculateSeverity(array $data): string
    {
        $threatScore = $this->calculateThreatScore($data, $data['ip'] ?? '');

        if ($threatScore >= 80) return 'critical';
        if ($threatScore >= 60) return 'high';
        if ($threatScore >= 40) return 'medium';
        return 'low';
    }

    public function getSecurityLogs(): array
    {
        return $this->db->select('security_logs', null, 'created_at DESC');
    }

    public function getJsErrors(): array
    {
        return $this->db->select('js_errors', null, 'created_at DESC');
    }

    public function getAdminAttempts(): array
    {
        return $this->db->select('admin_attempts', null, 'created_at DESC');
    }

    public function getThreatStats(): array
    {
        $pdo = $this->db->getPdo();

        // Statistiques générales
        $totalVisitors = $pdo->query("SELECT COUNT(*) FROM visitors")->fetchColumn();
        $totalSecurityEvents = $pdo->query("SELECT COUNT(*) FROM security_logs")->fetchColumn();
        $highThreatEvents = $pdo->query("SELECT COUNT(*) FROM security_logs WHERE severity IN ('high', 'critical')")->fetchColumn();
        $blockedAttempts = $pdo->query("SELECT COUNT(*) FROM security_logs WHERE blocked = 1")->fetchColumn();

        // Top IPs suspectes
        $suspiciousIPs = $pdo->query("
            SELECT ip_address, COUNT(*) as count, MAX(threat_score) as max_threat
            FROM security_logs
            GROUP BY ip_address
            HAVING count > 1
            ORDER BY count DESC
            LIMIT 10
        ")->fetchAll();

        // Événements récents
        $recentEvents = $pdo->query("
            SELECT event_type, severity, ip_address, created_at
            FROM security_logs
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ORDER BY created_at DESC
            LIMIT 20
        ")->fetchAll();

        return [
            'total_visitors' => $totalVisitors,
            'total_security_events' => $totalSecurityEvents,
            'high_threat_events' => $highThreatEvents,
            'blocked_attempts' => $blockedAttempts,
            'suspicious_ips' => $suspiciousIPs,
            'recent_events' => $recentEvents
        ];
    }

    public function getContacts(): array
    {
        return $this->db->select('contacts', null, 'created_at DESC');
    }

    public function getReviews(): array
    {
        return $this->db->select('reviews', null, 'created_at DESC');
    }

    public function getReservations(): array
    {
        return $this->db->select('reservations', null, 'created_at DESC');
    }

    public function getVisitors(): array
    {
        return $this->db->select('visitors', null, 'created_at DESC');
    }
}
