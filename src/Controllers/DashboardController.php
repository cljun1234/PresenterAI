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
            // Fetch notifications (Simulated)
            $stmt = $pdo->prepare("SELECT * FROM notifications WHERE widget_id = ? ORDER BY created_at DESC");
            $stmt->execute([$widget['id']]);
            $notifications = $stmt->fetchAll();

            // Fetch Real Events (Form Submits)
            $stmt = $pdo->prepare("SELECT * FROM events WHERE widget_id = ? AND type='form_submit' ORDER BY created_at DESC LIMIT 50");
            $stmt->execute([$widget['id']]);
            $real_events = $stmt->fetchAll();

            require_once __DIR__ . '/../../views/campaigns/live_conversion.php';
        } elseif ($type === 'live-visitors') {

            // Current Live Count (Last 30 mins)
            $cutoff = date('Y-m-d H:i:s', strtotime('-30 minutes'));
            $stmt = $pdo->prepare("SELECT COUNT(DISTINCT visitor_id) as count FROM live_visitors WHERE widget_id = ? AND last_seen > ?");
            $stmt->execute([$widget['id'], $cutoff]);
            $current_live = $stmt->fetch()['count'];

            // Historical Graph Data (Traffic Snapshots)
            // Fetch last 24 hours (or limit to last N points)
            $stmt = $pdo->prepare("SELECT visitor_count, created_at FROM traffic_snapshots WHERE widget_id = ? ORDER BY created_at DESC LIMIT 288"); // 288 * 5 mins = 24 hours
            $stmt->execute([$widget['id']]);
            $graph_data = array_reverse($stmt->fetchAll()); // Oldest first for graph

            // Config
            $config = [
                'enabled' => (bool)($widget['live_visitor_enabled'] ?? false),
                'settings' => json_decode($widget['live_visitor_config'] ?? '{}', true)
            ];

            require_once __DIR__ . '/../../views/campaigns/live_visitors.php';
        } else {
            // Generic placeholder
            $campaignType = $type;
            require_once __DIR__ . '/../../views/campaigns/placeholder.php';
        }
    }

    public function saveLiveVisitorConfig() {
        if (!isset($_SESSION['user_id'])) {
           header('Location: /login');
           exit;
       }

       $pdo = Database::getInstance();
       $user_id = $_SESSION['user_id'];
       $widget_id = $_POST['widget_id'];

       // Ownership check
       $stmt = $pdo->prepare("SELECT id FROM widgets WHERE id = ? AND user_id = ?");
       $stmt->execute([$widget_id, $user_id]);
       if (!$stmt->fetch()) { die("Unauthorized"); }

       $enabled = isset($_POST['enabled']) ? 1 : 0;
       $config = [
           'position' => $_POST['position'] ?? 'bottom-left',
           'bg_color' => $_POST['bg_color'] ?? '#ffffff',
           'text_color' => $_POST['text_color'] ?? '#333333'
       ];

       $stmt = $pdo->prepare("UPDATE widgets SET live_visitor_enabled = ?, live_visitor_config = ? WHERE id = ?");
       $stmt->execute([$enabled, json_encode($config), $widget_id]);

       header('Location: /campaigns/live-visitors');
    }

    public function toggleFeature() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        // Parse JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        $feature = $input['feature'] ?? '';
        $enabled = !empty($input['enabled']); // boolean true/false
        $widget_id = $input['widget_id'] ?? 0;

        // Map feature name to column name
        $column = '';
        if ($feature === 'live_visitor') $column = 'live_visitor_enabled';
        elseif ($feature === 'live_conversion') $column = 'live_conversion_enabled';
        elseif ($feature === 'magical_detection') $column = 'magical_detection';
        elseif ($feature === 'use_real_conversion') $column = 'use_real_conversion';
        elseif ($feature === 'use_simulated_conversion') $column = 'use_simulated_conversion';

        if (!$column) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid feature']);
            exit;
        }

        // Update
        // Ensure the widget belongs to the user
        $stmt = $pdo->prepare("UPDATE widgets SET $column = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$enabled ? 1 : 0, $widget_id, $user_id]);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'feature' => $feature, 'enabled' => $enabled]);
        exit;
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

    public function deleteEvent($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        // Verify ownership via widget join
        $stmt = $pdo->prepare("DELETE e FROM events e JOIN widgets w ON e.widget_id = w.id WHERE e.id = ? AND w.user_id = ?");
        $stmt->execute([$id, $user_id]);

        header('Location: /campaigns/live-conversion');
    }
}
