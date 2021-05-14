<?php
namespace App\Service;
use \Symfony\Component\HttpClient\HttpClient;

class MyHttpClient
{
    protected $url;
    protected $client;

    public function __construct($url)
    {
        $this->url = $url;
        $this->client = HttpClient::create();
    }

    public function getHtmlContent()
    {
//        try {
            $response = $this->client->request('GET', $this->url, []);
            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                return $response->getContent();
            }
            return false;
//        } catch (\HttpException $exception) {
//            return \HttpException::class
//        }


    }
}