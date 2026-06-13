<?php
class ReceitaValidator {
    
    // Função pura: apenas transforma o valor (Dummy data usará isso)
    public function formatarCusto($custo) {
        if (empty($custo)) return 0;
        return (float) str_replace(',', '.', trim($custo));
    }

    // Regras de negócio da sua aplicação
    public function validarReceita($nome, $tipo, $custo, $descricao = '') {
        $erros = [];

        // Regra 1: Campos obrigatórios
        if (empty(trim($nome)) || empty(trim($tipo)) || empty(trim($custo))) {
            $erros[] = 'Por favor, preencha os campos obrigatórios.';
        }

        // Regra 2: Custo não pode ser negativo
        $custoFormatado = $this->formatarCusto($custo);
        if ($custoFormatado < 0) {
            $erros[] = 'O custo não pode ser negativo.';
        }

        // Regra 3: Tipo de receita
        if (!empty($tipo) && $tipo !== 'doce' && $tipo !== 'salgada') {
            $erros[] = 'Tipo de receita inválido.';
        }

        if (strlen($descricao) > 255) {
            $erros[] = 'A descrição não pode ter mais que 255 caracteres.';
        }

        return $erros;
    }
}
?>