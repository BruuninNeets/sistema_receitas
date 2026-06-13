<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../AuthManager.php';

class AuthManagerTest extends TestCase {

    // Função que cria um STUB (Dublê) do banco de dados
    private function criarBancoFalso($respostaDoBanco) {
        // Cria uma falsa query que sempre responde o que a gente quiser
        $stmtStub = $this->createMock(PDOStatement::class);
        $stmtStub->method('execute')->willReturn(true);
        $stmtStub->method('fetch')->willReturn($respostaDoBanco);

        // Cria a falsa conexão que entrega a nossa query falsa
        $pdoStub = $this->createMock(PDO::class);
        $pdoStub->method('prepare')->willReturn($stmtStub);

        return $pdoStub;
    }

    // TESTE 9: Login Correto
    public function testLoginComSucesso() {
        // Programamos o Stub para "fingir" que achou o admin
        $bancoFalso = $this->criarBancoFalso([
            'id' => 1, 'nome' => 'admin', 'senha' => '123', 'status' => 'ativo'
        ]);

        $auth = new AuthManager($bancoFalso);
        $resultado = $auth->login('admin', '123');

        $this->assertTrue($resultado['sucesso']);
    }

    // TESTE 10: Senha Errada
    public function testRejeitaSenhaIncorreta() {
        // O banco falso devolve a senha '123'
        $bancoFalso = $this->criarBancoFalso([
            'id' => 1, 'nome' => 'admin', 'senha' => '123', 'status' => 'ativo'
        ]);

        $auth = new AuthManager($bancoFalso);
        // Mas o usuário digitou 'senha_errada'
        $resultado = $auth->login('admin', 'senha_errada');

        $this->assertFalse($resultado['sucesso']);
        $this->assertEquals('Senha incorreta.', $resultado['erro']);
    }

    // TESTE 11: Usuário Inativo
    public function testRejeitaUsuarioInativo() {
        // O banco falso avisa que o João foi demitido/inativado
        $bancoFalso = $this->criarBancoFalso([
            'id' => 2, 'nome' => 'joao', 'senha' => '123', 'status' => 'inativo'
        ]);

        $auth = new AuthManager($bancoFalso);
        $resultado = $auth->login('joao', '123');

        $this->assertFalse($resultado['sucesso']);
        $this->assertEquals('Conta inativa.', $resultado['erro']);
    }

    // TESTE 12: Usuário Não Existe
    public function testRejeitaUsuarioInexistente() {
        // O banco falso devolve "false" (não achou ninguem no select)
        $bancoFalso = $this->criarBancoFalso(false);

        $auth = new AuthManager($bancoFalso);
        $resultado = $auth->login('fantasma', '123');

        $this->assertFalse($resultado['sucesso']);
        $this->assertEquals('Usuário não encontrado.', $resultado['erro']);
    }
}
?>