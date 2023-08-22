<?php
// src/Service/GeonameAPIService.php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Service\GeonamesDBCachingService;
use Symfony\Component\HttpClient\HttpClient;
use App\Entity\GeonamesAdministrativeDivision;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class GeonamesAPIService
{
    public function __construct(
        private HttpClientInterface $httpClientInterface,
        private string $token,
        private string $urlBase,
        private EntityManagerInterface $entityManager,
        private GeonamesDBCachingService $dbCachingService)
    {
        //$this->entityManager = $entityManager;
        $entityManager->getRepository(GeonamesAdministrativeDivision::class);
    }

    public function postalCodeSearchJSON(string $postalCode): array
    {
        $postalCodeSearchResponse = $this->httpClientInterface->request(
            'GET',
            $this->urlBase
            . 'postalCodeSearchJSON?formatted=true&postalcode=' . $postalCode
            . '&maxRows=10&username=' . $this->token
            . '&style=full');
        
        $this->responseCheck($postalCodeSearchResponse, "postalcode");

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

        $this->responseCheck($postalCodeSearchResponse, "postalcode");

        return json_decode($postalCodeSearchResponse->getContent(), true);
    }

    private function responseCheck(object $searchResponse, string $searchType): void
    {    
        if($searchResponse->getStatusCode() != 200) {
            throw new Exception('Unavailable Webservice or malformed API url');
        }

        switch ($searchType) {
            case "postalcode":
                if (empty ($searchResponse->toArray()['postalcodes'])){
                    throw new Exception('Empty content');
                }
            
            case "latlng":
                if (empty ($searchResponse->toArray()['geonames'])) {
                    throw new Exception('Empty content');
                }
        }
    }

    public function latLngSearch(float $lat, float $lng): Response {

        $latlngSearchRequest = HttpClient::create();
        $latlngSearchResponse = $latlngSearchRequest->request('GET',
        $this->urlBase
        . 'findNearbyJSON?formatted=true&lat=' . $lat
        . '&lng=' . $lng
        . '&fclass=P&fcode=PPLA&fcode=PPL&fcode=PPLC&username=' . $this->token
        . '&style=full')->getContent();

        $latlngSearchResponseContent = json_decode($latlngSearchResponse);

        $this->dbCachingService->saveSubdivisionToDatabase($latlngSearchResponseContent);

        return new Response(json_encode($latlngSearchResponseContent));
    }

    public function countrySubDivisionSearch(float $lat, float $lng): Response {
        $countrySubDivisionSearchResponse = $this->httpClientInterface->request(
            'GET',
            $this->urlBase
            . 'countrySubdivisionJSON?formatted=true&level=3&lat=' . $lat
            . '&lng=' . $lng
            . '&username=' . $this->token
            . '&style=full&maxRows=10&radius=40');

        return new Response($countrySubDivisionSearchResponse->getContent());
    }
}
