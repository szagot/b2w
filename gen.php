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

// Diferentes descrições
$desCurta = 'IF( PRO_DESC_CURTA IS NULL, PRO_NOME, PRO_DESC_CURTA )';
$desDefault = "IF( PRO_DESCRICAO IS NULL, $desCurta, PRO_DESCRICAO )";
$desPadrao = "(SELECT DES_CONT FROM descricao_prod WHERE descricao_prod.PRO_ID = produto.PRO_ID AND DES_NOME = '$descType' ORDER BY DES_ORDEM LIMIT 0,1)";

// Qual deve ser?
if ($descType == 'curta') {
    $getDesc = $desCurta;
} elseif ($descType == 'default') {
    $getDesc = $desDefault;
} else {
    $getDesc = "IF( $desPadrao IS NULL, $desDefault, $desPadrao )";
}

// Gerando pesquisa
$produtos = $pdo->execute("
    SELECT 
      '$idParceiro' AS ID_PARCEIRO,
      PRO_REF AS ID_ITEM_PARCEIRO,
      PRO_NOME AS NOME_ITEM,
      PRO_PESO AS PESO_UNITARIO,
      $getDesc AS DESCRICAO_ITEM
    FROM produto
    
    LIMIT 0,2
");

die(json_encode($produtos));