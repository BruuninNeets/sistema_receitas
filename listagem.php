<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require 'conexao.php';

try {
    $stmt = $pdo->query("SELECT id, nome, descricao, data_registro, custo, tipo_receita FROM receita ORDER BY id DESC");
    $receitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar receitas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Receitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Sistema de Receitas</a>
        <div class="d-flex text-white align-items-center">
            <span class="me-3">Ola, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</span>
            <a href="logout.php" class="btn btn-sm btn-outline-light">Sair</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Receitas Cadastradas</h2>
        <a href="cadastro_receita.php" class="btn btn-success">Nova Receita</a>
    </div>      
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Tipo</th>
                        <th>Custo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($receitas) > 0): ?>
                        <?php foreach ($receitas as $receita): ?>
                            <tr>
                                <td><?= $receita['id'] ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($receita['nome']) ?></td>
                                <td><?= htmlspecialchars($receita['descricao']) ?></td>
                                <td>
                                    <?php if ($receita['tipo_receita'] == 'doce'): ?>
                                        <span class="badge bg-warning text-dark">Doce</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Salgada</span>
                                    <?php endif; ?>
                                </td>
                                <td>R$ <?= number_format($receita['custo'], 2, ',', '.') ?></td>
                                <td>
                                    <a href="editar_receita.php?id=<?= $receita['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                    <a href="excluir_receita.php?id=<?= $receita['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta receita?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Nenhuma receita foi achada</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>