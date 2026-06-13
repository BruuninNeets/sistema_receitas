<?php
class ReceitaRepository {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // CREATE
    public function salvar($nome, $descricao, $tipo_receita, $custo) {
        $stmt = $this->pdo->prepare("INSERT INTO receita (nome, descricao, tipo_receita, custo) VALUES (:nome, :descricao, :tipo, :custo)");
        return $stmt->execute([
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':tipo' => $tipo_receita,
            ':custo' => $custo
        ]);
    }

    // READ
    public function listarTodas() {
        $stmt = $this->pdo->query("SELECT * FROM receita ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // DELETE
    public function excluir($id) {
        // Regra de segurança: se o ID for vazio, nem tenta apagar
        if (empty($id)) {
            return false;
        }
        $stmt = $this->pdo->prepare("DELETE FROM receita WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>