<?php
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$usuario = getenv('DB_USER');
$senha = getenv('DB_PASS');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar com BD: " . $e->getMessage());
}
?>