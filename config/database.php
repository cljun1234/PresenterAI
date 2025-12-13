<?php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // In a real environment, use environment variables
        // For this sandbox, we'll default to localhost if not set, or a sqlite file for fallback testing

        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db   = getenv('DB_NAME') ?: 'trustabee';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $driver = getenv('DB_DRIVER') ?: 'mysql';

        try {
            if ($driver === 'sqlite') {
                // For sandbox testing without MySQL
                $this->pdo = new PDO("sqlite:" . __DIR__ . "/../database/trustabee.sqlite");
            } else {
                $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
                $this->pdo = new PDO($dsn, $user, $pass);
            }

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // In production, log this, don't echo
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}
