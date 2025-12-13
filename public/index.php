<?php
session_start();

require_once __DIR__ . '/../src/AltoRouter.php';
require_once __DIR__ . '/../config/database.php';

// Autoload Controllers
spl_autoload_register(function ($class) {
    if (strpos($class, 'Controller') !== false) {
        require_once __DIR__ . '/../src/Controllers/' . $class . '.php';
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
$router->map('POST', '/widget/save', 'DashboardController#saveConfig', 'save_config');
$router->map('POST', '/notification/add', 'DashboardController#addNotification', 'add_notification');
$router->map('GET', '/notification/delete/[i:id]', 'DashboardController#deleteNotification', 'delete_notification');

// API / Widget
$router->map('GET', '/api/widget.js', 'WidgetController#serveScript', 'widget_js');
$router->map('GET', '/api/data', 'WidgetController#getData', 'widget_data');
$router->map('POST', '/api/heartbeat', 'WidgetController#heartbeat', 'widget_heartbeat');
$router->map('POST', '/api/track', 'WidgetController#trackEvent', 'widget_track');

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
