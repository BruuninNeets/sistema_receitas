<?php
use PHPUnit\Framework\TestCase;

// Importamos a classe que criamos no Passo 2
require_once __DIR__ . '/../ReceitaValidator.php';

class ReceitaValidatorTest extends TestCase {
    
    private ReceitaValidator $validator;

    // A função setUp roda antes de CADA teste (prepara o terreno)
    protected function setUp(): void {
        $this->validator = new ReceitaValidator();
    }

    // TESTE 4: Campos Vazios
    public function testRejeitaCamposObrigatoriosVazios() {
        // Passamos strings vazias (isso é um conceito de 'Dummy' data)
        $erros = $this->validator->validarReceita('', '', '');
        
        // Afirmamos (Assert) que o array de erros tem que ter o aviso correto
        $this->assertContains('Por favor, preencha os campos obrigatórios.', $erros);
    }

    // TESTE 5: Formatação de Moeda
    public function testConverteVirgulaParaPontoCorretamente() {
        $custoFormatado = $this->validator->formatarCusto('15,50');
        
        // Afirmamos que o resultado tem que ser estritamente igual a 15.50 (float)
        $this->assertSame(15.50, $custoFormatado);
    }

    // TESTE 6: Custo Negativo
    public function testRejeitaCustoNegativo() {
        // O nome e o tipo estão corretos, mas o custo é negativo (-10)
        $erros = $this->validator->validarReceita('Bolo', 'doce', '-10,00');
        
        $this->assertContains('O custo não pode ser negativo.', $erros);
    }

    // TESTE 7: Tipo Inválido
    public function testRejeitaTipoDeReceitaInvalido() {
        // Alguém tentou burlar o HTML e mandou 'amarga' em vez de doce/salgada
        $erros = $this->validator->validarReceita('Café', 'amarga', '5.00');
        
        $this->assertContains('Tipo de receita inválido.', $erros);
    }
    // TESTE 8: Limite de Caracteres na Descrição
    public function testRejeitaDescricaoMuitoLonga() {
        // Dummy Data: Criamos um texto falso com 256 letras 'A'
        $descricaoLonga = str_repeat('A', 256); 
        
        $erros = $this->validator->validarReceita('Torta', 'doce', '15.00', $descricaoLonga);
        
        $this->assertContains('A descrição não pode ter mais que 255 caracteres.', $erros);
    }
}
?>