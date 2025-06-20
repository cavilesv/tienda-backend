<?php


$host = 'localhost'; // o 127.0.0.1
$db   = 'neering';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Para mostrar errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Para obtener arrays asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Mejora seguridad y compatibilidad
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Conexión exitosa!";
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}