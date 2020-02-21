<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("StorageService.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projeto Exemplo PHP Puro acessando SAPBH</title>
</head>
<body>
    <div>
       Upload de arquivo | 
       <a href="/download.php">Download de arquivo</a> | 
       <a href="/url.php">Recupera url de arquivo</a> | 
       <a href="/delete.php">Remove arquivo</a> | 
    </div>
    <?php
    if($_FILES) {
        $ss = new Prodabel\StorageAdapter\StorageService('https://sapbh.pbh.gov.br/api/documentos', 'teste:123456789');
        $resposta = $ss->upload($_FILES);
        var_dump($resposta);
        echo "<pre>".json_encode($resposta)."</pre>";
    }

    ?>
    <p></p>
    <div>
        <form enctype="multipart/form-data" action="" method="POST">
            Enviar esse arquivo: 
            <input name="arquivo" type="file" />
            <input type="submit" value="Enviar arquivo" />
        </form>
    </div>
</body>
</html>