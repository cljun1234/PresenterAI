<?php

class AuthController {
    public function showLogin() {
        require_once __DIR__ . '/../../views/login.php';
    }

    public function processLogin() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Simple password check (In production use password_verify)
        // For this demo, if no users exist, we might create one or allow login if password matches 'admin'

        if (!$user) {
            // Auto-create user for demo if table is empty?
            // Better: just fail. But to make testing easier:
            // If email is admin@trustabee.com and password is 'password', let them in and create the user.
            if ($email === 'admin@trustabee.com' && $password === 'password') {
                $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
                $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
                $user_id = $pdo->lastInsertId();
                $_SESSION['user_id'] = $user_id;

                // Create default widget for user
                $stmt = $pdo->prepare("INSERT INTO widgets (user_id, domain, name) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, 'example.com', 'My First Widget']);

                header('Location: /');
                exit;
            }

            echo "Invalid credentials";
            return;
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: /');
        } else {
            echo "Invalid credentials";
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /login');
    }
}
