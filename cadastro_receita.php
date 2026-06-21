<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'conexao.php';

    $nome = trim($_POST['nome']);
    ECHO "testando erro";
    $descricao = trim($_POST['descricao']);
    $tipo_receita = $_POST['tipo_receita'];

    $custo = str_replace(',', '.', trim($_POST['custo']));

    if (empty($nome) || empty($tipo_receita) || empty($custo)) {
        $erro = 'Por favor, preencha os campos obrigatórios.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO receita (nome, descricao, tipo_receita, custo) VALUES (:nome, :descricao, :tipo_receita, :custo)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':tipo_receita', $tipo_receita);
            $stmt->bindParam(':custo', $custo);

            if ($stmt->execute()) {
                require_once 'mailer.php';
                $assunto = "Nova Receita Cadastrada: " . $nome;
                $mensagem = "<h1>Nova Receita Adicionada!</h1>
                             <p>A receita <strong>{$nome}</strong> foi cadastrada com sucesso no sistema.</p>
                             <p><strong>Tipo:</strong> {$tipo_receita}</p>
                             <p><strong>Custo:</strong> R$ " . number_format((float)$custo, 2, ',', '.') . "</p>";

                dispararEmail($assunto, $mensagem);

                header("Location: listagem.php");
                exit;
            }
        } catch (PDOException $e) {
            $erro = "Erro ao cadastrar receita: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Receita</title>
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
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Cadastrar Nova Receita</h4>
                </div>
                <div class="card-body">
                    <?php if ($erro) : ?>
                        <div class="alert alert-danger"><?= $erro ?></div>
                    <?php endif; ?>

                    <form method="POST" action="cadastro_receita.php">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome da Receita *</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo_receita" class="form-label">Tipo *</label>
                                <select class="form-select" id="tipo_receita" name="tipo_receita" required>
                                    <option value="">Selecione...</option>
                                    <option value="doce">Doce</option>
                                    <option value="salgada">Salgada</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="custo" class="form-label">Custo de Produção (R$) *</label>
                                <input type="text" class="form-control" id="custo" name="custo" placeholder="00,00" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="listagem.php" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success">Salvar Receita</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
