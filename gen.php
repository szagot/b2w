<?php
/**
 * Gerador de planilha CSV para B2W
 * User: Daniel Bispo
 * Date: 23/08/2016
 * Time: 15:05
 */
require_once '../config/conecta.class.php';

// Inicializa variáveis
$idParceiro = filter_input(INPUT_POST, 'id_parceiro');
$descType = filter_input(INPUT_POST, 'desc_type');
$pathConf = __DIR__ . DIRECTORY_SEPARATOR . 'b2w.conf';
$pdo = new Conecta();

// ID Parceiro não informado?
if (! $idParceiro || empty($idParceiro)) {
    header('Content-type: text/html; charset=utf-8');

    // Verificando arquivo de pesquisa
    if (file_exists($pathConf)) {
        $config = json_decode(file_get_contents($pathConf));
        // Verificando existencia de chaves
        if (isset($config->idParceiro, $config->descType)) {
            $idParceiro = $config->idParceiro;
            $descType = $config->descType;
        }
    }

    // Pegando Descrições disponíveis
    $descricoes = $pdo->execute('SELECT DES_NOME FROM descricao_prod GROUP BY DES_NOME ORDER BY DES_NOME');
    $descTypes = [];
    if ($descricoes) {
        foreach ($descricoes as $desc) {
            $descTypes[] = $desc->DES_NOME;
        }
    }

    // Montando form
    require_once 'nav/form.php';
    exit;
}

// Gravando Valores escolhidos por padrão
file_put_contents($pathConf, json_encode([
    'idParceiro' => $idParceiro,
    'descType'   => $descType
]));

// Gerando pesquisa
$produtos = $pdo->execute("
    
");