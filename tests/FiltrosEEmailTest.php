<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../ReceitaFilter.php';
require_once __DIR__ . '/../EmailManager.php';

class FiltrosEEmailTest extends TestCase {

    // TESTE 18: Lógica de Filtragem de Arrays
    public function testFiltroDeReceitasPorTipoDoce() {
        $filter = new ReceitaFilter();
        
        // Dummy Data: Array de receitas simulando a resposta do banco
        $receitasFalsas = [
            ['id' => 1, 'nome' => 'Bolo', 'tipo_receita' => 'doce'],
            ['id' => 2, 'nome' => 'Torta Salgada', 'tipo_receita' => 'salgada'],
            ['id' => 3, 'nome' => 'Pudim', 'tipo_receita' => 'doce']
        ];

        $resultado = $filter->filtrarPorTipo($receitasFalsas, 'doce');

        // Afirmações: Tem que sobrar exatamente 2 doces, e o primeiro é o Bolo
        $this->assertCount(2, $resultado);
        $this->assertEquals('Bolo', $resultado[0]['nome']);
        $this->assertEquals('Pudim', $resultado[1]['nome']);
    }

    // TESTE 19: Formatação do Template de E-mail
    public function testMontaCorpoDoEmailCorretamente() {
        $emailManager = new EmailManager();
        $htmlGerado = $emailManager->montarCorpoHtml('Bolo de Cenoura', 'doce', 15.5);

        // Afirmamos que o sistema converteu 15.5 para o formato monetário R$ 15,50
        $this->assertStringContainsString('Bolo de Cenoura', $htmlGerado);
        $this->assertStringContainsString('R$ 15,50', $htmlGerado);
    }

    // TESTE 20: Mock do Envio de E-mail (O Grande Espião)
    public function testMockGaranteQueOEmailFoiDisparado() {
        // Criamos um espião (Mock) da nossa classe de E-mail
        $mockEmail = $this->createMock(EmailManager::class);
        
        // Programamos o Mock: A função 'enviarAlerta' DEVE ser chamada exatamente 1 vez!
        $mockEmail->expects($this->once())
                  ->method('enviarAlerta')
                  ->with(
                      $this->stringContains('Alerta'), // O assunto tem que ter a palavra Alerta
                      $this->anything() // O corpo pode ser qualquer coisa
                  )
                  ->willReturn(true);

        // Simulamos o sistema chamando a função de enviar (sem mandar de verdade)
        $resultado = $mockEmail->enviarAlerta('Alerta de Receita Nova', '<p>Olá</p>');
        
        $this->assertTrue($resultado);
    }
}
?>