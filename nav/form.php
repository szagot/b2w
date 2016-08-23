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
        <link href="estilo.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <form id="formB2W" action="" method="post">
            <input class="text" type="text" name="id_parceiro" id="id_parceiero">
            <select class="desc" name="desc_type" id="desc_type">
                <option value="curta">Descrição Curta</option>
                <option value="default" selected>Descrição Padrão</option>
                <?php
                foreach ($descTypes as $descType) {
                    ?>
                    <option><?= $descType ?></option>
                    <?php
                }
                ?>
            </select>
            <button class="btn" type="submit">Gerar</button>
        </form>
    </body>
</html>