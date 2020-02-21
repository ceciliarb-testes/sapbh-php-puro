<?php
namespace Prodabel\StorageAdapter;

require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\SessionCookieJar;

class StorageService
{
    protected static $apiAuthorization;
    protected static $apiUrl;
    protected static $guzzleClient;
    protected const FILE_FIELD_NAME = 'arquivo';  // name do input file do form

    public function __construct($apiUrl, $apiCredentials) {
        self::$apiUrl = $apiUrl;
        self::$apiAuthorization = $apiCredentials;
        $jar = new SessionCookieJar('PHPSESSID', true);
        self::$guzzleClient = new Client([ 'base_uri' => $apiUrl, 'cookies' => $jar,]);
    }

    /**
     * Salva um arquivo no storage
     * 
     * @param Array $arquivo no formato do $_FILES[]
     * 
     * @return Array Dados do arquivo armazenado e resultado da operação
     */
    public function upload($FILES)
    {
        if (!isset($FILES['arquivo'])) {
            throw new Exception("Nenhum arquivo enviado!");
        }
    
        $name        = self::FILE_FIELD_NAME;
        $nomeArquivo = $FILES['arquivo']['name'];
        $arquivo     = $FILES['arquivo']['tmp_name'];
        $tipoArquivo = $FILES['arquivo']['type'];
        $mimetype    = mime_content_type($FILES['arquivo']['tmp_name']);

        $contents = \file_get_contents($arquivo);
// var_dump(self::$apiAuthorization);
// exit;
        // insere arquivo via AP
        $respGuzzle = self::$guzzleClient->post('/', [
            'headers'     => [ 'Authorization' => 'teste:123456789', 'X-Request-With' => 'XMLHttpRequest' ],
            'multipart'   => [compact('name', 'contents', 'arquivo')],
            // 'debug'       => true,
            'http_errors' => false,
            'verify'      => false,
            
        ]);
        //pega o corpo da resposta da api e já decodifica para json
        $resposta = \json_decode($respGuzzle->getBody()->getContents());
        $status = $respGuzzle->getStatusCode();

        return compact('resposta', 'status');
    }

    /**
     * Faz download de arquivo
     *
     * @param String $nome_minio Nome do documento a ser recuperado
     * @param bool $base64 Se o conteúdo deve ser codificado como base64
     * 
     * @return Array Conteúdo do documento e status do download
     */
    public function download($nome_minio, $base64 = true)
    {
        // recupera arquivo pela API
        $result = self::$guzzleClient->get("/$nome_minio", [
            'headers'     => [ 'Authorization' => self::$apiAuthorization ],
            // 'debug'       => true,
            'http_errors' => false,
            'verify'      => false,

        ]);

        $resposta = $result->getBody()->getContents();
        if($base64) {
            $resposta = \base64_encode($resposta);
        }
        $status = $result->getStatusCode();

        return compact('resposta', 'status');
    }

    /**
     * Recupera URL do arquivo
     *
     * @param String $nome_minio Nome do documento para o qual será produzida a URL de download
     * @param String $expiracao Tempo de expiração do link de download. Seguindo o formato do AWS S3
     * @return Array dados da URL criada e resultado da operação
     */
    public function getURL($nome_minio, $expiracao = '') {
        // vai até a API para recuperar a URL do arquivo
        $result = self::$guzzleClient->get('/url', [
            'headers' => [ 'Authorization' => self::$apiAuthorization ],
            // 'debug'       => true,
            'http_errors' => false,
            'verify'      => false,
        ]);
        $resposta = $result->getBody()->getContents();
        $status = $result->getStatusCode();

        return compact('resposta', 'status');
    }

    /**
     * Remove arquivo
     *
     * @param String $nome_minio Nome do documento a ser deletado
     * @return Array resultado da operação
     */
    public function delete($nome_minio) {
        // realiza delete de arquivo pela API
        $result = self::$guzzleClient->delete("/$nome_minio", [
            'headers' => [ 'Authorization' => self::$apiAuthorization ],
            // 'debug'       => true,
            'http_errors' => false,
            'verify'      => false,
        ]);
        $resposta = $result->getBody()->getContents();
        $status = $result->getStatusCode();

        return compact('resposta', 'status');
    }

}

