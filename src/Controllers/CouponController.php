<?php

class CouponController {

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

    // Called from DashboardController so we can pass widget_id if we want,
    // but sticking to the pattern of self-retrieval ensures safety if routing changes.
    // However, DashboardController already does the check. I'll make the argument optional.
    public function index($widget_id = null) {
        if ($widget_id === null) {
            list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
            $widget_id = $widget['id'];
        } else {
             $pdo = Database::getInstance();
             // We assume caller (DashboardController) did auth checks
             $stmt = $pdo->prepare("SELECT * FROM widgets WHERE id = ?");
             $stmt->execute([$widget_id]);
             $widget = $stmt->fetch();
        }

        // Fetch Coupons
        $stmt = $pdo->prepare("SELECT * FROM coupons WHERE widget_id = ? ORDER BY created_at DESC");
        $stmt->execute([$widget_id]);
        $coupons = $stmt->fetchAll();

        // Analytics (Simple total views/clicks for now per coupon)
        foreach ($coupons as &$coupon) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM coupon_analytics WHERE coupon_id = ? AND event_type = 'view'");
            $stmt->execute([$coupon['id']]);
            $coupon['views'] = $stmt->fetchColumn();

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM coupon_analytics WHERE coupon_id = ? AND event_type = 'click'");
            $stmt->execute([$coupon['id']]);
            $coupon['clicks'] = $stmt->fetchColumn();
        }

        require_once __DIR__ . '/../../views/campaigns/coupon.php';
    }

    public function save() {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
        $widget_id = $widget['id'];

        $title = $_POST['title'];
        $description = $_POST['description'];
        $coupon_code = $_POST['coupon_code'];
        $button_text = $_POST['button_text'];
        $bg_color = $_POST['bg_color'];
        $text_color = $_POST['text_color'];
        $trigger_type = $_POST['trigger_type'];
        $trigger_delay = (int)$_POST['trigger_delay'];
        $frequency = $_POST['frequency'];
        $match_url = $_POST['match_url'] ?: null;
        $active = isset($_POST['active']) ? 1 : 0;

        $coupon_id = $_POST['coupon_id'] ?? null;

        if ($coupon_id) {
            // Update - AND verify ownership by widget_id
            $stmt = $pdo->prepare("UPDATE coupons SET title=?, description=?, coupon_code=?, button_text=?, bg_color=?, text_color=?, trigger_type=?, trigger_delay=?, frequency=?, match_url=?, active=? WHERE id=? AND widget_id=?");
            $stmt->execute([$title, $description, $coupon_code, $button_text, $bg_color, $text_color, $trigger_type, $trigger_delay, $frequency, $match_url, $active, $coupon_id, $widget_id]);
        } else {
            // Create
            $stmt = $pdo->prepare("INSERT INTO coupons (widget_id, title, description, coupon_code, button_text, bg_color, text_color, trigger_type, trigger_delay, frequency, match_url, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$widget_id, $title, $description, $coupon_code, $button_text, $bg_color, $text_color, $trigger_type, $trigger_delay, $frequency, $match_url, $active]);
        }

        header("Location: /campaigns/coupon");
        exit;
    }

    public function delete($coupon_id) {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
        $widget_id = $widget['id'];

        // Secure delete: ensure the coupon belongs to the user's widget
        $stmt = $pdo->prepare("DELETE FROM coupons WHERE id = ? AND widget_id = ?");
        $stmt->execute([$coupon_id, $widget_id]);

        header("Location: /campaigns/coupon");
        exit;
    }
}
