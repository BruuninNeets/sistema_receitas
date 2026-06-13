<?php
class EmailManager {
    public function montarCorpoHtml($nome, $tipo, $custo) {
        $custoFormatado = number_format((float)$custo, 2, ',', '.');
        return "<h1>Nova Receita: {$nome}</h1>
                <p>Tipo: {$tipo}</p>
                <p>Custo: R$ {$custoFormatado}</p>";
    }

    public function enviarAlerta($assunto, $html) {
        // Em produção, aqui ficaria a chamada real do dispararEmail().
        // Como estamos focados na regra de negócio, o teste vai vigiar essa função.
        return true;
    }
}
?>