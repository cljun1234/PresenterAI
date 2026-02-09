<?php
session_start();

require_once __DIR__ . '/../src/AltoRouter.php';
require_once __DIR__ . '/../config/database.php';

// Autoload Controllers
spl_autoload_register(function ($class) {
    if (strpos($class, 'Controller') !== false) {
        $file = __DIR__ . '/../src/Controllers/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

$router = new AltoRouter();

// Define Routes

// Auth
$router->map('GET', '/login', 'AuthController#showLogin', 'login');
$router->map('POST', '/login', 'AuthController#processLogin', 'login_post');
$router->map('GET', '/logout', 'AuthController#logout', 'logout');

// Dashboard
$router->map('GET', '/', 'DashboardController#index', 'dashboard');
$router->map('GET', '/settings', 'DashboardController#settings', 'settings');
$router->map('POST', '/domain/save', 'DashboardController#saveDomain', 'save_domain');

// Match request
$match = $router->match();

if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} elseif ($match) {
    list($controller, $action) = explode('#', $match['target']);
    if (class_exists($controller) && method_exists($controller, $action)) {
        $obj = new $controller();
        call_user_func_array([$obj, $action], $match['params']);
    } else {
        // Handle error: controller or method not found
        header($_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error');
        echo "Error: Controller or action not found.";
    }
} else {
    // 404
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo "404 Not Found";
}
