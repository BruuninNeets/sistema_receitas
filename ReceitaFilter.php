<?php
class ReceitaFilter {
    public function filtrarPorTipo(array $receitas, $tipoPermitido) {
        if (empty($tipoPermitido)) {
            return $receitas; // Sem filtro, devolve tudo
        }
        
        $filtradas = [];
        foreach ($receitas as $receita) {
            if ($receita['tipo_receita'] === $tipoPermitido) {
                $filtradas[] = $receita;
            }
        }
        return $filtradas;
    }
}
?>