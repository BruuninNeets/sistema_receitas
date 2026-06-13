<?php
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase {
    
    // TESTE 1: Banco de Dados Configurado
    public function testBancoDeDadosHostEstaConfigurado() {
        $dbHost = getenv('DB_HOST');
        // Afirma que a variável não está vazia (evita falha de conexão)
        $this->assertNotEmpty($dbHost, 'A variável DB_HOST não foi injetada no container.');
    }

    // TESTE 2: Senha de E-mail Existe
    public function testSenhaSmtpEstaConfigurada() {
        $smtpPass = getenv('SMTP_PASS');
        $this->assertNotEmpty($smtpPass, 'A senha do app do Gmail (SMTP_PASS) sumiu do ambiente.');
    }

    // TESTE 3: Porta do Servidor é um Número
    public function testPortaSmtpEhNumerica() {
        $smtpPort = getenv('SMTP_PORT');
        // Afirma que o valor recebido na porta é realmente matemático
        $this->assertTrue(is_numeric($smtpPort), 'A porta SMTP configurada não é um número válido.');
    }
}
?>