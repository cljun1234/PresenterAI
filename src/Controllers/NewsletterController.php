<?php

class NewsletterController {

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

        // Fetch Newsletters
        $stmt = $pdo->prepare("SELECT * FROM newsletters WHERE widget_id = ? ORDER BY created_at DESC");
        $stmt->execute([$widget_id]);
        $newsletters = $stmt->fetchAll();

        // Analytics
        foreach ($newsletters as &$newsletter) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM newsletter_analytics WHERE newsletter_id = ? AND event_type = 'view'");
            $stmt->execute([$newsletter['id']]);
            $newsletter['views'] = $stmt->fetchColumn();

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM newsletter_leads WHERE newsletter_id = ?");
            $stmt->execute([$newsletter['id']]);
            $newsletter['leads'] = $stmt->fetchColumn();
        }

        require_once __DIR__ . '/../../views/campaigns/newsletter.php';
    }

    public function save() {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
        $widget_id = $widget['id'];

        $title = $_POST['title'];
        $description = $_POST['description'];
        $btn_text = $_POST['btn_text'];

        $allow_name = isset($_POST['allow_name']) ? 1 : 0;
        $allow_phone = isset($_POST['allow_phone']) ? 1 : 0;

        $webhook_url = $_POST['webhook_url'] ?? '';
        $success_action = $_POST['success_action'] ?? 'message';
        $success_message = $_POST['success_message'] ?? 'Thanks for subscribing!';
        $redirect_url = $_POST['redirect_url'] ?? '';

        $bg_color = $_POST['bg_color'];
        $text_color = $_POST['text_color'];

        $trigger_type = $_POST['trigger_type'];
        $trigger_delay = (int)$_POST['trigger_delay'];
        $frequency = $_POST['frequency'];
        $match_url = $_POST['match_url'] ?: null;

        $active = isset($_POST['active']) ? 1 : 0;
        $image_style = $_POST['image_style'] ?? 'top';
        $remove_branding = isset($_POST['remove_branding']) ? 1 : 0;

        $newsletter_id = $_POST['newsletter_id'] ?? null;
        $image_url = null;

        // Handle File Upload
        if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] == 0) {
            $file = $_FILES['image_upload'];
            $max_size = 15 * 1024 * 1024; // 15MB
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if ($file['size'] > $max_size) {
                die("File is too large. Max 15MB.");
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_exts)) {
                die("Invalid file type. Allowed: jpg, png, gif, webp.");
            }

            $upload_dir = __DIR__ . '/../../public_html/uploads/newsletters/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $filename = uniqid('newsletter_') . '.' . $ext;
            $upload_path = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $image_url = '/uploads/newsletters/' . $filename;
            }
        }

        if ($newsletter_id) {
            $sql = "UPDATE newsletters SET title=?, description=?, btn_text=?, allow_name=?, allow_phone=?, webhook_url=?, success_action=?, success_message=?, redirect_url=?, bg_color=?, text_color=?, trigger_type=?, trigger_delay=?, frequency=?, match_url=?, active=?, image_style=?, remove_branding=? ";
            $params = [$title, $description, $btn_text, $allow_name, $allow_phone, $webhook_url, $success_action, $success_message, $redirect_url, $bg_color, $text_color, $trigger_type, $trigger_delay, $frequency, $match_url, $active, $image_style, $remove_branding];

            if ($image_url) {
                $stmt = $pdo->prepare("SELECT image_url FROM newsletters WHERE id = ? AND widget_id = ?");
                $stmt->execute([$newsletter_id, $widget_id]);
                $old_img = $stmt->fetchColumn();
                if ($old_img && file_exists(__DIR__ . '/../../public_html' . $old_img)) {
                    unlink(__DIR__ . '/../../public_html' . $old_img);
                }

                $sql .= ", image_url=? ";
                $params[] = $image_url;
            }

            $sql .= "WHERE id=? AND widget_id=?";
            $params[] = $newsletter_id;
            $params[] = $widget_id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

        } else {
            $stmt = $pdo->prepare("INSERT INTO newsletters (widget_id, title, description, btn_text, allow_name, allow_phone, webhook_url, success_action, success_message, redirect_url, bg_color, text_color, trigger_type, trigger_delay, frequency, match_url, active, image_style, remove_branding, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$widget_id, $title, $description, $btn_text, $allow_name, $allow_phone, $webhook_url, $success_action, $success_message, $redirect_url, $bg_color, $text_color, $trigger_type, $trigger_delay, $frequency, $match_url, $active, $image_style, $remove_branding, $image_url]);
        }

        header("Location: /campaigns/newsletter");
        exit;
    }

    public function delete($newsletter_id) {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
        $widget_id = $widget['id'];

        $stmt = $pdo->prepare("SELECT image_url FROM newsletters WHERE id = ? AND widget_id = ?");
        $stmt->execute([$newsletter_id, $widget_id]);
        $img = $stmt->fetchColumn();

        if ($img && file_exists(__DIR__ . '/../../public_html' . $img)) {
            unlink(__DIR__ . '/../../public_html' . $img);
        }

        $stmt = $pdo->prepare("DELETE FROM newsletters WHERE id = ? AND widget_id = ?");
        $stmt->execute([$newsletter_id, $widget_id]);

        header("Location: /campaigns/newsletter");
        exit;
    }

    public function leads($newsletter_id) {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
        $widget_id = $widget['id'];

        // Verify ownership
        $stmt = $pdo->prepare("SELECT * FROM newsletters WHERE id = ? AND widget_id = ?");
        $stmt->execute([$newsletter_id, $widget_id]);
        $newsletter = $stmt->fetch();

        if (!$newsletter) {
            die("Newsletter not found.");
        }

        $stmt = $pdo->prepare("SELECT * FROM newsletter_leads WHERE newsletter_id = ? ORDER BY created_at DESC");
        $stmt->execute([$newsletter_id]);
        $leads = $stmt->fetchAll();

        require_once __DIR__ . '/../../views/campaigns/newsletter_leads.php';
    }

    public function export_leads($newsletter_id) {
        list($pdo, $user_id, $widget) = $this->getWidgetAndUser();
        $widget_id = $widget['id'];

        // Verify ownership
        $stmt = $pdo->prepare("SELECT * FROM newsletters WHERE id = ? AND widget_id = ?");
        $stmt->execute([$newsletter_id, $widget_id]);
        $newsletter = $stmt->fetch();

        if (!$newsletter) {
            die("Newsletter not found.");
        }

        $stmt = $pdo->prepare("SELECT name, email, phone, created_at FROM newsletter_leads WHERE newsletter_id = ? ORDER BY created_at DESC");
        $stmt->execute([$newsletter_id]);
        $leads = $stmt->fetchAll();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="newsletter_leads_' . $newsletter_id . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Name', 'Email', 'Phone', 'Date']);
        foreach ($leads as $lead) {
            fputcsv($output, $lead);
        }
        fclose($output);
        exit;
    }
}
