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
<?php
    if(isset($_GET['arquivo'])) {
        $ss = new Prodabel\StorageAdapter\StorageService('https://sapbh-hm.pbh.gov.br', 'teste:123456789');
        $resposta = $ss->download($_GET['arquivo'], false);
        if($resposta['resposta']) {
            $finfo = new finfo(FILEINFO_MIME);
            $mimetype = $finfo->buffer($resposta['resposta']);
            header('Content-Description: File Transfer');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header("Content-type: ".$mimetype); 
            header("Content-Disposition: inline; filename=arquivo");
            ob_clean();
            flush();
            echo($resposta['resposta']);
            exit();
        } else {
            var_dump($resposta);
        }
        
    }

    ?>
    <div>
       <a href="/upload.php">Upload de arquivo</a> | 
       Download de arquivo | 
       <a href="/url.php">Recupera url de arquivo</a> | 
       <a href="/delete.php">Remove arquivo</a> | 
    </div>
    <p></p>
</body>
</html>
