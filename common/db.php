<?php
// common/db.php
// $host = 'localhost';
// $db   = 'schooldb';
// $user = 'root';
// $pass = '';

// Live
$host = 'localhost';
$db   = 'xdcdkpfd_education';
$user = 'xdcdkpfd_education';
$pass = '$BxQY=s6NojUxNn8';

$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
     die("Database Connection Error: " . $e->getMessage());
}
?>
