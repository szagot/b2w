<?php
/**
 * Gerador de planilha CSV para B2W
 * User: Daniel Bispo
 * Date: 23/08/2016
 * Time: 15:05
 */
require_once '../config/conecta.class.php';

$idParceiro = filter_input(INPUT_POST, 'id_parceiro');
$descType = filter_input(INPUT_POST, 'desc_type');
$pdo = new Conecta();

// ID Parceiro não informado?
if (! $idParceiro) {
    header('Content-type: text/html; charset=utf-8');

    // Pegando Descrições disponíveis
    $descicoes = $pdo->execute('SELECT DES_NOME FROM descricao_prod GROUP BY DES_NOME ORDER BY DES_NOME');
    $descTypes = [];
    foreach ($descicoes as $desc) {
        $descTypes[] = $desc->DES_NOME;
    }

    // Montando form
    require_once 'nav/form.php';
    exit;
}

// Gerando pesquisa