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
        self::$guzzleClient = new Client();
    }

    public function upload($FILES)
    {
        if (!isset($FILES['arquivo'])) {
            throw new Exception("Nenhum arquivo enviado!");
        }
    
        $arquivo     = $FILES['arquivo']['tmp_name'];
        $nomeArquivo = $FILES['arquivo']['name'];
        $tipoArquivo = $FILES['arquivo']['type'];
        $mimetype    = mime_content_type($FILES['arquivo']['tmp_name']);

        $contents = \file_get_contents($arquivo);

        $respGuzzle = self::$guzzleClient->post(self::$apiUrl.'/api/documentos', [
            'headers'     => [ 
                'Authorization'  => self::$apiAuthorization, 
            ],
            'multipart'   => [[
                'name'       => 'arquivo', 
                'contents'   => $contents, 
                'filename'   => $nomeArquivo
            ]],
            // 'debug'       => true,
            'http_errors' => false,
            'verify'      => false,
        ]);
        //pega o corpo da resposta da api e já decodifica para json
        $resposta = \json_decode($respGuzzle->getBody()->getContents());
        $status = $respGuzzle->getStatusCode();

        return compact('resposta', 'status');
    }

    public function download($nome_minio, $base64 = true)
    {
        // recupera arquivo pela API
        $result = self::$guzzleClient->get(self::$apiUrl."/api/documentos/$nome_minio", [
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

    public function getURL($nome_minio, $expiracao = '') {
        // vai até a API para recuperar a URL do arquivo
        $result = self::$guzzleClient->request('GET', self::$apiUrl.'/api/documentos/url/'.$nome_minio, [
            'headers' => [ 'Authorization' => self::$apiAuthorization ],
            // 'debug'       => true,
            'http_errors' => false,
            'verify'      => false,
        ]);
        $resposta = $result->getBody()->getContents();
        $status = $result->getStatusCode();

        return compact('resposta', 'status');
    }

    public function delete($nome_minio) {
        // realiza delete de arquivo pela API
        $result = self::$guzzleClient->delete(self::$apiUrl."/api/documentos/$nome_minio", [
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

