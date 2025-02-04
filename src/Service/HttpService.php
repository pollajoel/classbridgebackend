<?php
namespace App\Service;

use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpService {
     
    public function __construct(private readonly HttpClientInterface $httpClientInterface)
    {}

    public function postData( string $url, array $options)
    {
         try{
             $response = $this->httpClientInterface->request('POST', $url, $options);
             return $response;
         }catch( TransportException $e){
            throw new \Exception($e->getMessage(), $e->getCode());
         }
    }


    public function getData( string $url, array $options = []){
        try{
            $response = $this->httpClientInterface->request('GET', $url, $options);
            return $response;
        }catch(TransportException $e){
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }


    public function updateData( string $url, array $options = []){
        try{
            $response = $this->httpClientInterface->request('PUT', $url, $options);
            return $response;
        }catch(TransportException $e){
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

}