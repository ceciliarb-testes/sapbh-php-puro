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
        $ss = new Prodabel\StorageAdapter\StorageService('https://sapbh.pbh.gov.br', 'teste:123456789');
        // $ss = new Prodabel\StorageAdapter\StorageService('http://localhost:8000', 'teste:123456789');
        $resposta = $ss->upload($_FILES);
        var_dump($resposta);
        echo "<pre>".json_encode($resposta)."</pre>";
    }

    ?>
    <p></p>
    <div>
        <form enctype="multipart/form-data" action="" method="POST">
            <input type="hidden" name="_token" id="_token">

            Enviar esse arquivo: 
            <input name="arquivo" type="file" />
            <input type="submit" value="Enviar arquivo" />
        </form>
    </div>
    <script defer>
        function getCookie(name) {
            var value = "; " + document.cookie;
            var parts = value.split("; " + name + "=");
            if (parts.length == 2) return parts.pop().split(";").shift();
        }
        var v = getCookie("PHPSESSID");
        document.getElementById("_token").value = v;
        console.log(v);
    </script>
</body>
</html>