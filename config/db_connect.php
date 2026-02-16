<?php
// db_connect.php

$host = getenv('DB_HOST') ?: 'localhost';
$db = getenv('DB_NAME') ?: 'cafe_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ''; // Default XAMPP password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Enable SSL for TiDB Cloud (Required)
if (strpos($host, 'tidbcloud.com') !== false) {
    // Standard CA path for Debian/Ubuntu (Render default)
    $options[PDO::MYSQL_ATTR_SSL_CA] = '/etc/ssl/certs/ca-certificates.crt';
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

// Define Base URL dynamically
$script_name = $_SERVER['SCRIPT_NAME'];
$dir = dirname($script_name);

// Logic to find project root: assuming config/db_connect.php is one level deep or root
// Actually, let's define it based on where this file is included from, or specific key folder
// Better: detection based on known structure.
// If accessing localhost/cafeonline/index.php -> root is /cafeonline/
// If accessing localhost/cafeonline/admin/dashboard.php -> root is /cafeonline/

// Simple detection: remove 'admin' or 'pages' or 'includes' or 'config' from the current path
$root = str_replace(['/admin', '/pages', '/includes', '/config'], '', $dir);
if (substr($root, -1) !== '/') {
    $root .= '/';
}
define('BASE_URL', $root);