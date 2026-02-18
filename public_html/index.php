<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Define controllers
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/DashboardController.php';
require_once __DIR__ . '/../src/Controllers/PresentationController.php';

// Configure database
require_once __DIR__ . '/../config/database.php';

session_start();

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Basic Router
switch ($path) {
    case '/':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $controller = new DashboardController();
        $controller->index();
        break;

    case '/login':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->processLogin();
        } else {
            $controller->showLogin();
        }
        break;

    case '/logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case '/create':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        // Simple view render
        require __DIR__ . '/../views/pages/create.php';
        break;

    case '/generate-pptx':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $controller = new PresentationController();
        $controller->generate();
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
