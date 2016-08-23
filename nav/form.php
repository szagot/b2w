<?php
if (! isset($descTypes)) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Gerador de Planilha da B2W</title>
        <link href="nav/estilo.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <form id="formB2W" action="" method="post">
            <label class="title">ID Parceiro</label>
            <input class="text" type="text" name="id_parceiro" id="id_parceiero" value="<?= $idParceiro ?>">
            <label class="title">Descrição</label>
            <select class="desc" name="desc_type" id="desc_type">
                <option value="curta" <?= ($descType == 'curta') ? 'selected' : '' ?>>Descrição Curta</option>
                <option value="default" <?= ($descType == 'default' || empty($descType)) ? 'selected' : '' ?>>Descrição Padrão</option>
                <?php
                foreach ($descTypes as $desc) {
                    ?>
                    <option <?= ($descType == $desc) ? 'selected' : '' ?>><?= $desc ?></option>
                    <?php
                }
                ?>
            </select>
            <button class="btn" type="submit">Gerar</button>
        </form>
    </body>
</html>