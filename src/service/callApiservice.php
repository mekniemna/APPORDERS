<?php 
namespace App\service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class callApiservice
{
    private $client;

    //Using the HttpClient class to make requests

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }
    //fonction pour récupérer les données de l'Api défini dans la fonction getData avec /orders
    public function getOrders():array{
        return $this->getData('orders');
    }
    public function getContacts():array{
        return $this->getData('contacts');
    }

   private function getData(String $var):array
   {
    $response = $this->client->request(
        'GET',
        'https://4ebb0152-1174-42f0-ba9b-4d6a69cf93be.mock.pstmn.io/'.$var
        ,[
            'headers' => ['x-api-key' => 'PMAK-62642462da39cd50e9ab4ea7-815e244f4fdea2d2075d8966cac3b7f10b']
        ]
    );
    return $response->toArray(); //pour le retourner un tableau
   }
}