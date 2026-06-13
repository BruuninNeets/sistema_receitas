<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require 'conexao.php';
$erro = '';

// Verifica se o ID foi passado na URL
if (!isset($_GET['id'])) {
    header("Location: listagem.php");
    exit;
}
$id = $_GET['id'];

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $tipo_receita = $_POST['tipo_receita'];
    $custo = str_replace(',', '.', trim($_POST['custo']));

    if (empty($nome) || empty($tipo_receita) || empty($custo)) {
        $erro = 'Por favor, preencha os campos obrigatórios.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE receita SET nome = :nome, descricao = :descricao, tipo_receita = :tipo_receita, custo = :custo WHERE id = :id");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':tipo_receita', $tipo_receita);
            $stmt->bindParam(':custo', $custo);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                require_once 'mailer.php';
                $assunto = "Receita Atualizada: " . $nome;
                $mensagem = "<h1>Receita Atualizada!</h1>
                             <p>A receita <strong>{$nome}</strong> foi modificada com sucesso no sistema.</p>
                             <p><strong>Novo Tipo:</strong> {$tipo_receita}</p>
                             <p><strong>Novo Custo:</strong> R$ " . number_format((float)$custo, 2, ',', '.') . "</p>";

                dispararEmail($assunto, $mensagem);

                header("Location: listagem.php");
                exit;
            }
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar receita: " . $e->getMessage();
        }
    }
}

// Busca os dados da receita para preencher o formulário
try {
    $stmt = $pdo->prepare("SELECT * FROM receita WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $receita = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receita) {
        header("Location: listagem.php"); // Volta se o ID não existir
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao buscar receita: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Receita</title>
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
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Editar Receita</h4>
                </div>
                <div class="card-body">
                    <?php if ($erro) : ?>
                        <div class="alert alert-danger"><?= $erro ?></div>
                    <?php endif; ?>

                    <form method="POST" action="editar_receita.php?id=<?= $receita['id'] ?>">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome da Receita *</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($receita['nome']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= htmlspecialchars($receita['descricao']) ?></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo_receita" class="form-label">Tipo *</label>
                                <select class="form-select" id="tipo_receita" name="tipo_receita" required>
                                    <option value="doce" <?= $receita['tipo_receita'] == 'doce' ? 'selected' : '' ?>>Doce</option>
                                    <option value="salgada" <?= $receita['tipo_receita'] == 'salgada' ? 'selected' : '' ?>>Salgada</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="custo" class="form-label">Custo de Produção (R$) *</label>
                                <input type="text" class="form-control" id="custo" name="custo" value="<?= number_format($receita['custo'], 2, ',', '') ?>" placeholder="00,00" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="listagem.php" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Atualizar Receita</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
