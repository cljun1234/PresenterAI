<?php

class DashboardController {

    private function getWidgetAndUser() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        // Fetch widget
        $stmt = $pdo->prepare("SELECT * FROM widgets WHERE user_id = ? LIMIT 1");
        $stmt->execute([$user_id]);
        $widget = $stmt->fetch();

        if (!$widget) {
            echo "No widget found.";
            exit;
        }

        return [$pdo, $user_id, $widget];
    }

    public function index() {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();

        // Count live visitors (Active in last 5 mins)
        // Adjust date logic for PHP/MySQL compatibility
        $cutoff = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT visitor_id) as count FROM live_visitors WHERE widget_id = ? AND last_seen > ?");
        $stmt->execute([$widget['id'], $cutoff]);
        $live_count = $stmt->fetch()['count'];

        // require_once __DIR__ . '/../../views/dashboard.php';
        require_once __DIR__ . '/../../views/pages/home.php';
    }

    public function settings() {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
        require_once __DIR__ . '/../../views/pages/settings.php';
    }

    public function campaigns($type) {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();

        if ($type === 'live-conversion') {
            // Fetch notifications
            $stmt = $pdo->prepare("SELECT * FROM notifications WHERE widget_id = ? ORDER BY created_at DESC");
            $stmt->execute([$widget['id']]);
            $notifications = $stmt->fetchAll();

            require_once __DIR__ . '/../../views/campaigns/live_conversion.php';
        } else {
            // Generic placeholder
            $campaignType = $type;
            require_once __DIR__ . '/../../views/campaigns/placeholder.php';
        }
    }

    public function saveConfig() {
         if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        $magical = isset($_POST['magical_detection']) ? 1 : 0;

        // Update first widget found
        $stmt = $pdo->prepare("UPDATE widgets SET magical_detection = ? WHERE user_id = ?");
        $stmt->execute([$magical, $user_id]);

        header('Location: /settings');
    }

    public function addNotification() {
         if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        $name = $_POST['name'];
        $action = $_POST['action_text'];

        // Get widget id
        $stmt = $pdo->prepare("SELECT id FROM widgets WHERE user_id = ? LIMIT 1");
        $stmt->execute([$user_id]);
        $widget = $stmt->fetch();

        if ($widget) {
            $stmt = $pdo->prepare("INSERT INTO notifications (widget_id, name, action_text) VALUES (?, ?, ?)");
            $stmt->execute([$widget['id'], $name, $action]);
        }

        header('Location: /campaigns/live-conversion');
    }

    public function deleteNotification($id) {
         if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        // Verify ownership via widget
        $stmt = $pdo->prepare("DELETE n FROM notifications n JOIN widgets w ON n.widget_id = w.id WHERE n.id = ? AND w.user_id = ?");
        $stmt->execute([$id, $user_id]);

        header('Location: /campaigns/live-conversion');
    }
}
