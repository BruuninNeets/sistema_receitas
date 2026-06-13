<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../ReceitaRepository.php';

class ReceitaRepositoryTest extends TestCase {

    private function criarPdoMock($metodoStmt, $retornoStmt) {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method($metodoStmt)->willReturn($retornoStmt);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);
        $pdoMock->method('query')->willReturn($stmtMock);

        return $pdoMock;
    }

    // TESTE 13: Salvar (Create)
    public function testSalvarReceitaRetornaTrueEmSucesso() {
        // Simulamos o execute() do banco retornando "True" (Deu certo)
        $pdoFalso = $this->criarPdoMock('execute', true);
        $repo = new ReceitaRepository($pdoFalso);

        $resultado = $repo->salvar('Bolo', 'Bolo de chocolate', 'doce', 20.00);
        $this->assertTrue($resultado, 'O repositório deveria retornar true ao salvar com sucesso.');
    }

    // TESTE 14: Listar (Read)
    public function testListarTodasRetornaArrayDeReceitas() {
        // Simulamos o banco devolvendo um array falso (Fake Data) com 2 receitas
        $fakeData = [
            ['id' => 1, 'nome' => 'Bolo', 'tipo_receita' => 'doce'],
            ['id' => 2, 'nome' => 'Coxinha', 'tipo_receita' => 'salgada']
        ];
        
        $pdoFalso = $this->criarPdoMock('fetchAll', $fakeData);
        $repo = new ReceitaRepository($pdoFalso);

        $receitas = $repo->listarTodas();
        
        $this->assertCount(2, $receitas);
        $this->assertEquals('Coxinha', $receitas[1]['nome']);
    }

    // TESTE 15: Segurança no Delete
    public function testExcluirIgnoraIdVazio() {
        // Passamos Null como Dummy data
        $pdoFalso = $this->createMock(PDO::class); // Mock limpo, não deveria ser acionado
        $repo = new ReceitaRepository($pdoFalso);

        $resultado = $repo->excluir(null);
        $this->assertFalse($resultado);
    }

    // TESTE 16: Excluir (Delete)
    public function testExcluirReceitaComSucesso() {
        $pdoFalso = $this->criarPdoMock('execute', true);
        $repo = new ReceitaRepository($pdoFalso);

        $resultado = $repo->excluir(5); // Apagando a receita ID 5
        $this->assertTrue($resultado);
    }

    // TESTE 17: Tratamento de Exceções do Banco
    public function testLancaExcecaoQuandoBancoCai() {
        // Simulamos o banco de dados fora do ar (lançando erro fatal)
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willThrowException(new PDOException("Conexão perdida"));

        $pdoFalso = $this->createMock(PDO::class);
        $pdoFalso->method('prepare')->willReturn($stmtMock);

        $repo = new ReceitaRepository($pdoFalso);

        // Avisamos o PHPUnit: "Prepare-se, o código abaixo TEM QUE explodir um erro"
        $this->expectException(PDOException::class);
        
        // Ação que causa o erro
        $repo->salvar('Pão', '', 'salgada', 5.00);
    }
}
?>