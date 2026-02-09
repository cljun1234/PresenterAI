<?php

class DashboardController {

    private function getDomainAndUser() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        // Fetch configured domain
        $stmt = $pdo->prepare("SELECT * FROM domains WHERE user_id = ? LIMIT 1");
        $stmt->execute([$user_id]);
        $domain = $stmt->fetch();

        return [$pdo, $user_id, $domain];
    }

    public function index() {
        list($pdo, $user_id, $domain) = $this->getDomainAndUser();
        require_once __DIR__ . '/../../views/pages/home.php';
    }

    public function settings() {
        list($pdo, $user_id, $domain) = $this->getDomainAndUser();
        require_once __DIR__ . '/../../views/pages/settings.php';
    }

    public function saveDomain() {
         if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        $domain_name = $_POST['domain'] ?? '';

        if ($domain_name) {
             // Check if domain config exists
             $stmt = $pdo->prepare("SELECT id FROM domains WHERE user_id = ?");
             $stmt->execute([$user_id]);
             if ($stmt->fetch()) {
                 $stmt = $pdo->prepare("UPDATE domains SET domain = ? WHERE user_id = ?");
                 $stmt->execute([$domain_name, $user_id]);
             } else {
                 $stmt = $pdo->prepare("INSERT INTO domains (user_id, domain) VALUES (?, ?)");
                 $stmt->execute([$user_id, $domain_name]);
             }
        }

        header('Location: /settings');
    }
}
