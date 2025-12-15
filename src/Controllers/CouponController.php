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

        // Fetch Coupons
        $stmt = $pdo->prepare("SELECT * FROM coupons WHERE widget_id = ? ORDER BY created_at DESC");
        $stmt->execute([$widget_id]);
        $coupons = $stmt->fetchAll();

        // Analytics
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
        $image_style = $_POST['image_style'] ?? 'top';
        $remove_branding = isset($_POST['remove_branding']) ? 1 : 0;

        $coupon_id = $_POST['coupon_id'] ?? null;
        $image_url = null;

        // Handle File Upload
        if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] == 0) {
            $file = $_FILES['image_upload'];
            $max_size = 15 * 1024 * 1024; // 15MB
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if ($file['size'] > $max_size) {
                die("File is too large. Max 15MB.");
            }

            // Secure Extension Check
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_exts)) {
                die("Invalid file type. Allowed: jpg, png, gif, webp.");
            }

            // Ensure directory exists
            $upload_dir = __DIR__ . '/../../public_html/uploads/coupons/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $filename = uniqid('banner_') . '.' . $ext;
            $upload_path = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $image_url = '/uploads/coupons/' . $filename;
            } else {
                 die("Failed to move uploaded file.");
            }
        }

        if ($coupon_id) {
            // Check if we need to update image
            $sql = "UPDATE coupons SET title=?, description=?, coupon_code=?, button_text=?, bg_color=?, text_color=?, trigger_type=?, trigger_delay=?, frequency=?, match_url=?, active=?, image_style=?, remove_branding=? ";
            $params = [$title, $description, $coupon_code, $button_text, $bg_color, $text_color, $trigger_type, $trigger_delay, $frequency, $match_url, $active, $image_style, $remove_branding];

            if ($image_url) {
                // Get old image to delete
                $stmt = $pdo->prepare("SELECT image_url FROM coupons WHERE id = ? AND widget_id = ?");
                $stmt->execute([$coupon_id, $widget_id]);
                $old_img = $stmt->fetchColumn();
                if ($old_img && file_exists(__DIR__ . '/../../public_html' . $old_img)) {
                    unlink(__DIR__ . '/../../public_html' . $old_img);
                }

                $sql .= ", image_url=? ";
                $params[] = $image_url;
            }

            $sql .= "WHERE id=? AND widget_id=?";
            $params[] = $coupon_id;
            $params[] = $widget_id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

        } else {
            // Create
            $stmt = $pdo->prepare("INSERT INTO coupons (widget_id, title, description, coupon_code, button_text, bg_color, text_color, trigger_type, trigger_delay, frequency, match_url, active, image_style, remove_branding, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$widget_id, $title, $description, $coupon_code, $button_text, $bg_color, $text_color, $trigger_type, $trigger_delay, $frequency, $match_url, $active, $image_style, $remove_branding, $image_url]);
        }

        header("Location: /campaigns/coupon");
        exit;
    }

    public function delete($coupon_id) {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
        $widget_id = $widget['id'];

        // Get image to delete
        $stmt = $pdo->prepare("SELECT image_url FROM coupons WHERE id = ? AND widget_id = ?");
        $stmt->execute([$coupon_id, $widget_id]);
        $img = $stmt->fetchColumn();

        if ($img && file_exists(__DIR__ . '/../../public_html' . $img)) {
            unlink(__DIR__ . '/../../public_html' . $img);
        }

        // Secure delete
        $stmt = $pdo->prepare("DELETE FROM coupons WHERE id = ? AND widget_id = ?");
        $stmt->execute([$coupon_id, $widget_id]);

        header("Location: /campaigns/coupon");
        exit;
    }
}
