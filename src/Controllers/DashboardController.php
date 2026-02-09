<?php

class DashboardController {

    private function getWidgetAndUser() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        // Fetch widget (domain configuration)
        $stmt = $pdo->prepare("SELECT * FROM widgets WHERE user_id = ? LIMIT 1");
        $stmt->execute([$user_id]);
        $widget = $stmt->fetch();

        return [$pdo, $user_id, $widget];
    }

    public function index() {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
        require_once __DIR__ . '/../../views/pages/home.php';
    }

    public function settings() {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
        require_once __DIR__ . '/../../views/pages/settings.php';
    }

    public function saveConfig() {
         if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        // Example: Saving domain or other settings
        // For now, we just redirect back as we stripped specific settings logic
        // But let's assume we might want to update the domain in the future.

        $domain = $_POST['domain'] ?? '';

        if ($domain) {
             // Check if widget exists
             $stmt = $pdo->prepare("SELECT id FROM widgets WHERE user_id = ?");
             $stmt->execute([$user_id]);
             if ($stmt->fetch()) {
                 $stmt = $pdo->prepare("UPDATE widgets SET domain = ? WHERE user_id = ?");
                 $stmt->execute([$domain, $user_id]);
             } else {
                 $stmt = $pdo->prepare("INSERT INTO widgets (user_id, domain) VALUES (?, ?)");
                 $stmt->execute([$user_id, $domain]);
             }
        }

        header('Location: /settings');
    }
}
