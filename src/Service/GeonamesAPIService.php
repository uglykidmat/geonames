<?php
// src/Service/GeonameAPIService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class GeonamesAPIService
{
    public function __construct(private HttpClientInterface $httpClientInterface, private string $token, private string $urlBase)
    {

    }

    public function postalCodeSearchJSON(string $postalCode): array
    {
        $postalCodeSearchResponse = $this->httpClientInterface->request(
            'GET',
            $this->urlBase
            . 'postalCodeSearchJSON?formatted=true&postalcode=' . $postalCode
            . '&maxRows=10&username=' . $this->token
            . '&style=full');
        
        $this->responseCheck($postalCodeSearchResponse);

        return json_decode($postalCodeSearchResponse->getContent(), true);
    }

    public function postalCodeLookupJSON(string $postalCode, string $countrycode): array
    {
        $postalCodeSearchResponse = $this->httpClientInterface->request(
            'GET',
            $this->urlBase
            . 'postalCodeLookupJSON?formatted=true&postalcode=' .$postalCode
            . '&maxRows=10&username=' . $this->token
            . '&country=' . $countrycode
            . '&style=full');

        $this->responseCheck($postalCodeSearchResponse);

        return json_decode($postalCodeSearchResponse->getContent(), true);
    }

    private function responseCheck(object $postalCodeSearchResponse): void
    {    

        if($postalCodeSearchResponse->getStatusCode() != 200) {
            throw new Exception('Unavailable Webservice or malformed API url');
        }
        if ($postalCodeSearchResponse->toArray()['postalcodes'] == []){
            throw new Exception('Empty content');
        }
    }
}
