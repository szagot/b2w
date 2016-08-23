<?php
/**
 * Gerador de planilha CSV para B2W
 * User: Daniel Bispo
 * Date: 23/08/2016
 * Time: 15:05
 */

$idParceiro = filter_input(INPUT_POST, 'id_parceiro');
$descType = filter_input(INPUT_POST, 'desc_type');

if( ! $idParceiro ){
    require_once 'nav/form.php';
    exit;
}

require_once '../config/conecta.class.php';
