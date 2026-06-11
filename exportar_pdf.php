<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("Acesso negado.");
}

// Carrega o autoload do Composer para habilitar o Dompdf
require 'vendor/autoload.php';
require 'conexao.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Captura os mesmos filtros que vieram da listagem
$filtro_tipo = $_GET['tipo'] ?? '';
$filtro_data_inicio = $_GET['data_inicio'] ?? '';
$filtro_data_fim = $_GET['data_fim'] ?? '';

$sql = "SELECT id, nome, descricao, data_registro, custo, tipo_receita FROM receita WHERE 1=1";
$params = [];

if ($filtro_tipo !== '') {
    $sql .= " AND tipo_receita = :tipo";
    $params[':tipo'] = $filtro_tipo;
}
if ($filtro_data_inicio !== '') {
    $sql .= " AND DATE(data_registro) >= :data_inicio";
    $params[':data_inicio'] = $filtro_data_inicio;
}
if ($filtro_data_fim !== '') {
    $sql .= " AND DATE(data_registro) <= :data_fim";
    $params[':data_fim'] = $filtro_data_fim;
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$receitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Monta o HTML que será convertido em PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Relatório de Receitas</h2>
    <p>Data de geração: ' . date('d/m/Y H:i') . '</p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Data</th>
                <th>Custo (R$)</th>
            </tr>
        </thead>
        <tbody>';

foreach ($receitas as $r) {
    $html .= '<tr>
                <td>' . $r['id'] . '</td>
                <td>' . htmlspecialchars($r['nome']) . '</td>
                <td>' . ucfirst($r['tipo_receita']) . '</td>
                <td>' . date('d/m/Y', strtotime($r['data_registro'])) . '</td>
                <td>' . number_format($r['custo'], 2, ',', '.') . '</td>
              </tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

// Instancia e configura o Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait'); // Formato A4, orientação retrato
$dompdf->render();

// Envia o PDF para o navegador
$dompdf->stream("relatorio_receitas.pdf", ["Attachment" => false]);
?>