<?php
// db.php (Version locale modifiée pour envoyer vers Hangar en direct)
$host = '178.33.122.21'; // L'IP de ta base Hangar
$db   = 'hangardb_loch63011';
$user = 'hangardb_loch63011';
$pass = '9xf1UX3SIXZa7dyoWLdob91i';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=3306";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>