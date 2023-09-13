<?php
// src/Service/GeonameAPIService.php
namespace App\Service;

use stdClass;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use App\Interface\GeonamesAPIServiceInterface;

class GeonamesAPIService implements GeonamesAPIServiceInterface
{
    public function __construct(
        public HttpClientInterface $client,
        private string $token,
        private string $urlBase,
        private EntityManagerInterface $entityManager
    ) {
        $this->client = $client->withOptions([
            'headers' => [
                'Content-Type: application/json',
            ],
            'base_uri' => $urlBase
        ]);
    }

    public function postalCodeSearchJSON(string $postalCode): array
    {
        $postalCodeSearchResponse = $this->client->request(
            'GET',
            $this->urlBase
                . 'postalCodeSearchJSON?formatted=true&postalcode=' . $postalCode
                . '&maxRows=10&username=' . $this->token
                . '&style=full'
        );

        $this->responseCheck($postalCodeSearchResponse, "postalcode");

        return json_decode($postalCodeSearchResponse->getContent(), true);
    }

    public function postalCodeLookupJSON(string $postalCode, string $countrycode): array
    {
        $postalCodeSearchResponse = $this->client->request(
            'GET',
            $this->urlBase
                . 'postalCodeLookupJSON?formatted=true&postalcode=' . $postalCode
                . '&maxRows=1&username=' . $this->token
                . '&country=' . $countrycode
                . '&style=full'
        );
        $this->responseCheck($postalCodeSearchResponse, "postalcode");

        return json_decode($postalCodeSearchResponse->getContent(), true);
    }

    private function responseCheck(object $searchResponse, string $searchType): void
    {
        if ($searchResponse->getStatusCode() >= 400) {
            throw new Exception('Unavailable Webservice or malformed API url');
        }

        switch ($searchType) {
            case "postalcode":
                $searchResponse = array_change_key_case($searchResponse->toArray(), CASE_LOWER);
                if (empty($searchResponse['postalcodes'])) {

                    throw new Exception('Empty content');
                }
                break;

            case "latlng":
                if (empty($searchResponse->toArray()['geonames'])) {
                    throw new Exception('Empty content');
                }
                break;
        }
    }

    public function latLngSearch(float $lat, float $lng): ?int
    {

        $latlngSearchResponse = json_decode($this->client->request(
            'GET',
            $this->urlBase
                . 'findNearbyJSON?formatted=true&lat=' . $lat
                . '&lng=' . $lng
                . '&username=' . $this->token
                . '&style=full&maxRows=1&featureCode=ADM1&featureCode=PPL'
        )->getContent());

        if (!empty($latlngSearchResponse->geonames) && is_array($latlngSearchResponse->geonames)) {
            return reset($latlngSearchResponse->geonames)->geonameId;
        }
        throw new Exception('Empty content from Geonames');
    }

    public function getJsonSearch(int $geonameId): ?stdClass
    {
        $getJsonSearchResponse = json_decode($this->client->request(
            'GET',
            $this->urlBase
                . 'getJSON?geonameId=' . $geonameId
                . '&username=' . $this->token
                . '&style=full'
        )->getContent());

        return $getJsonSearchResponse;
    }

    public function countrySubDivisionSearch(float $lat, float $lng): Response
    {
        $countrySubDivisionSearchResponse = $this->client->request(
            'GET',
            $this->urlBase
                . 'countrySubdivisionJSON?formatted=true&level=3&lat=' . $lat
                . '&lng=' . $lng
                . '&username=' . $this->token
                . '&style=full&maxRows=10&radius=40'
        );

        return new Response($countrySubDivisionSearchResponse->getContent());
    }
}
