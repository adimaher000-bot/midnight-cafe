<?php
// InfinityFree Database Connection
// Host: sql108.infinityfree.com
// User: if0_41166252
// DB:   if0_41166252_cafedb

$host = 'sql108.infinityfree.com';
$db = 'if0_41166252_cafedb';
$user = 'if0_41166252';
$pass = 'EbvUHOIRNaI'; // Password from screenshot

$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Show a cleaner error if connection fails
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
?>