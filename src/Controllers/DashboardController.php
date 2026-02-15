<?php

class DashboardController {

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("SELECT * FROM presentations WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        $presentations = $stmt->fetchAll();

        if (empty($presentations)) {
            require_once __DIR__ . '/../../views/pages/dashboard_empty.php';
        } else {
            require_once __DIR__ . '/../../views/pages/dashboard_list.php';
        }
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        require_once __DIR__ . '/../../views/pages/create.php';
    }

    public function templates() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        require_once __DIR__ . '/../../views/pages/templates.php';
    }

    public function themes() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        require_once __DIR__ . '/../../views/pages/themes.php';
    }

    public function fonts() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        require_once __DIR__ . '/../../views/pages/fonts.php';
    }

    public function trash() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        require_once __DIR__ . '/../../views/pages/trash.php';
    }
}
