<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'conexao.php';

// Inicializa variáveis de filtro
$filtro_tipo = $_GET['tipo'] ?? '';
$filtro_data_inicio = $_GET['data_inicio'] ?? '';
$filtro_data_fim = $_GET['data_fim'] ?? '';

// Monta a Query SQL dinamicamente
$sql = "SELECT id, nome, descricao, data_registro, custo, tipo_receita FROM receita WHERE 1=1";
$params = [];

if ($filtro_tipo !== '') {
    $sql .= " AND tipo_receita = :tipo";
    $params[':tipo'] = $filtro_tipo;
}
if ($filtro_data_inicio !== '') {
    $sql .= " AND DATE(data_registro) >= :data_inicio";
    $params[':data_inicio'] = $filtro_data_inicio;
}
if ($filtro_data_fim !== '') {
    $sql .= " AND DATE(data_registro) <= :data_fim";
    $params[':data_fim'] = $filtro_data_fim;
}

$sql .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->execute();
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
        <div>
            <a href="exportar_pdf.php?<?= http_build_query($_GET) ?>" target="_blank" class="btn btn-danger me-2">Exportar PDF</a>
            <a href="cadastro_receita.php" class="btn btn-success">Nova Receita</a>
        </div>
    </div>      

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="listagem.php" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" for="filtro_tipo">Tipo de Receita</label>
                    <select name="tipo_receita" id="filtro_tipo" class="form-select">
                        <option value="">Todas</option>
                        <option value="doce" <?= $filtro_tipo == 'doce' ? 'selected' : '' ?>>Doce</option>
                        <option value="salgada" <?= $filtro_tipo == 'salgada' ? 'selected' : '' ?>>Salgada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="data_inicio">Data Início</label>
                    <input type="date" name="data_inicio" id="data_inicio" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="data_fim">Data Fim</label>
                    <input type="date" name="data_fim" id="data_fim" class="form-control">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Data Registro</th>
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
                                <td>
                                    <?php if ($receita['tipo_receita'] == 'doce'): ?>
                                        <span class="badge bg-warning text-dark">Doce</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Salgada</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($receita['data_registro'])) ?></td>
                                <td>R$ <?= number_format($receita['custo'], 2, ',', '.') ?></td>
                                <td>
                                    <a href="editar_receita.php?id=<?= $receita['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                    <a href="excluir_receita.php?id=<?= $receita['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta receita?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Nenhuma receita foi encontrada com estes filtros.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>