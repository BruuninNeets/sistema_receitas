<?php
class AuthManager {
    private $pdo;

    // Recebe o PDO (Pode ser o real ou o nosso Dublê/Stub)
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function login($usuario, $senha) {
        // Tenta achar o usuário
        $stmt = $this->pdo->prepare("SELECT id, nome, senha, status FROM usuario WHERE nome = :nome");
        $stmt->execute(['nome' => $usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Regra 1: Usuário não existe
        if (!$user) {
            return ['sucesso' => false, 'erro' => 'Usuário não encontrado.'];
        }

        // Regra 2: Status do usuário (bloqueado)
        if ($user['status'] === 'inativo') {
            return ['sucesso' => false, 'erro' => 'Conta inativa.'];
        }

        // Regra 3: Validação da senha
        if ($senha !== $user['senha']) {
            return ['sucesso' => false, 'erro' => 'Senha incorreta.'];
        }

        // Sucesso!
        return ['sucesso' => true, 'id' => $user['id'], 'nome' => $user['nome']];
    }
}
?>