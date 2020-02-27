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
       <a href="/upload.php">Upload de arquivo</a> | 
       <a href="/download.php">Download de arquivo</a> | 
       <a href="/url.php">Recupera url de arquivo</a> | 
       Remove arquivo | 
    </div>
    <?php
    if(isset($_GET['arquivo'])) {
        $ss = new Prodabel\StorageAdapter\StorageService('https://sapbh-hm.pbh.gov.br', 'teste:123456789');
        $resposta = $ss->delete($_GET['arquivo']);
        var_dump($resposta);
        echo "<pre>".json_encode($resposta)."</pre>";
    }

    ?>
    <p></p>
</body>
</html>
