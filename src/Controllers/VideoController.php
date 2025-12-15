<?php

class VideoController {

    private function getWidgetAndUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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

    public function index($widget_id = null) {
        if ($widget_id === null) {
            list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
            $widget_id = $widget['id'];
        } else {
             $pdo = Database::getInstance();
             $stmt = $pdo->prepare("SELECT * FROM widgets WHERE id = ?");
             $stmt->execute([$widget_id]);
             $widget = $stmt->fetch();
        }

        // Fetch Videos
        $stmt = $pdo->prepare("SELECT * FROM videos WHERE widget_id = ? ORDER BY created_at DESC");
        $stmt->execute([$widget_id]);
        $videos = $stmt->fetchAll();

        // Analytics
        foreach ($videos as &$video) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM video_analytics WHERE video_id = ? AND event_type = 'view'");
            $stmt->execute([$video['id']]);
            $video['views'] = $stmt->fetchColumn();

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM video_analytics WHERE video_id = ? AND event_type = 'click'");
            $stmt->execute([$video['id']]);
            $video['clicks'] = $stmt->fetchColumn();
        }

        require_once __DIR__ . '/../../views/campaigns/video.php';
    }

    public function save() {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
        $widget_id = $widget['id'];

        $title = $_POST['title'];
        $message = $_POST['message'];
        $video_url = $_POST['video_url'];
        $btn_text = $_POST['btn_text'];
        $btn_action = $_POST['btn_action'] ?? 'link';
        $btn_link = $_POST['btn_link'] ?? '';
        $bg_color = $_POST['bg_color'];
        $text_color = $_POST['text_color'];
        $trigger_type = $_POST['trigger_type'];
        $trigger_delay = (int)$_POST['trigger_delay'];
        $frequency = $_POST['frequency'];
        $match_url = $_POST['match_url'] ?: null;
        $active = isset($_POST['active']) ? 1 : 0;
        $remove_branding = isset($_POST['remove_branding']) ? 1 : 0;

        $video_id = $_POST['video_id'] ?? null;

        if ($video_id) {
            // Update
            $stmt = $pdo->prepare("UPDATE videos SET title=?, message=?, video_url=?, btn_text=?, btn_action=?, btn_link=?, bg_color=?, text_color=?, trigger_type=?, trigger_delay=?, frequency=?, match_url=?, active=?, remove_branding=? WHERE id=? AND widget_id=?");
            $stmt->execute([$title, $message, $video_url, $btn_text, $btn_action, $btn_link, $bg_color, $text_color, $trigger_type, $trigger_delay, $frequency, $match_url, $active, $remove_branding, $video_id, $widget_id]);

        } else {
            // Create
            $stmt = $pdo->prepare("INSERT INTO videos (widget_id, title, message, video_url, btn_text, btn_action, btn_link, bg_color, text_color, trigger_type, trigger_delay, frequency, match_url, active, remove_branding) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$widget_id, $title, $message, $video_url, $btn_text, $btn_action, $btn_link, $bg_color, $text_color, $trigger_type, $trigger_delay, $frequency, $match_url, $active, $remove_branding]);
        }

        header("Location: /campaigns/video");
        exit;
    }

    public function delete($video_id) {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
        $widget_id = $widget['id'];

        // Secure delete
        $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ? AND widget_id = ?");
        $stmt->execute([$video_id, $widget_id]);

        header("Location: /campaigns/video");
        exit;
    }
}
