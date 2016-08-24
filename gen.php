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

header('Content-type: text/csv; charset=utf-8');
//header('Content-Disposition: attachment; filename=b2w-' . date('Y-m-d-H-i') . '.csv');
//header('Pragma: no-cache');
//header('Expires: 0');

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

// Verifica Promo
$ePromo = 'PRO_PROMOCAO > 0 AND 
          ( DATE(PRO_PROMO_INI) <= DATE(NOW()) OR PRO_PROMO_INI = \'0000-00-00\' OR PRO_PROMO_INI IS NULL ) AND
          ( DATE(PRO_PROMO_FIM) >= DATE(NOW()) OR PRO_PROMO_FIM = \'0000-00-00\' OR PRO_PROMO_FIM IS NULL )';

// Gerando pesquisa
$produtos = $pdo->execute("
    SELECT 
      '$idParceiro' AS ID_PARCEIRO,
      PRO_REF AS ID_ITEM_PARCEIRO,
      PRO_NOME AS NOME_ITEM,
      PRO_PESO AS PESO_UNITARIO,
      $getDesc AS DESCRICAO_ITEM,
      '' AS IMAGEM_ITEM,
      PRO_LARGURA AS LARGURA,
      PRO_COMPRIMENTO AS COMPRIMENTO,
      PRO_GTIN AS EAN,
      '' AS ID_ITEM_PAI,
      '' AS NOME_ITEM_PAI,
      'E' AS TIPO_ITEM,
      IF( PRO_ATIVO = 1 AND PRO_ESTOQUE > 0 AND PRO_VISIBILIDADE NOT LIKE '%loja%', 'A', 'I' ) AS SITUACAO_ITEM,
      PRO_DIAS_PRAZO AS PRAZO_XD,
      IF( $ePromo, PRO_VALOR, '' ) AS PRECO_DE,
      IF( $ePromo, PRO_PROMOCAO, PRO_VALOR ) AS PRECO_POR,
      PRO_ESTOQUE AS QTDE_ESTOQUE,
      '' AS DEPARTAMENTO,
      '' AS SETOR,
      '' AS FAMILIA,
      '' AS SUB_FAMILIA,
      SEC_URL AS TEMP,
      IF( PRO_SOB_ENCOMENDA = 1, '1', '0' ) AS PROCEDENCIA_ITEM,
      FOR_NOME AS MARCA

    FROM produto
    LEFT JOIN fornecedor ON produto.FOR_ID = fornecedor.FOR_ID
    INNER JOIN secao_prod ON produto.SEC_ID = secao_prod.SEC_ID
    
    WHERE
      SUB_PRO_ID IS NULL 
");

// Tratando os dados

//die(json_encode($produtos));