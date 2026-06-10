<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    require 'conexao.php';
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM receita WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        die("Erro ao excluir receita: " . $e->getMessage());
    }
}

header("Location: listagem.php");
exit;
?>