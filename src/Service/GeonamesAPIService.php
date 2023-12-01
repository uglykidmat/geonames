<?php
// src/Service/GeonameAPIService.php
namespace App\Service;

use stdClass;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Interface\GeonamesAPIServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeonamesAPIService implements GeonamesAPIServiceInterface
{
    public function __construct(
        public HttpClientInterface $client,
        private string $token,
        private string $urlBase,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
        $this->client = $client->withOptions([
            'headers' => [
                'Content-Type: application/json',
            ],
            'base_uri' => $urlBase
        ]);
    }

    public function latLngSearch(float $lat, float $lng): ?int
    {
        $query = ['query' => [
            'formatted' => 'true',
            'lat' => $lat,
            'lng' => $lng,
            'username' => $this->token,
            'style' => 'full',
            'maxRows' => '1',
            'fcode' => 'PPLA',
            'fcode' => 'PPLX',
            'fcode' => 'PPLC',
            'fcode' => 'PPL',
        ],];
        $latlngSearchResponse = $this->client->request(
            'GET',
            $this->urlBase
                . 'findNearbyJSON',
            $query
        )->toArray();

        if (!empty($latlngSearchResponse['geonames'][0]) && is_array($latlngSearchResponse['geonames'][0])) {
            $this->responseCheck($query, $latlngSearchResponse, "latlng");

            return $latlngSearchResponse['geonames'][0]['geonameId'];
        }
        $this->logger->error('Empty response from Geonames Service for Coordinates lat/lng search.');

        return null;
    }

    public function postalCodeLookupJSON(string $postalCode, string $countrycode): array
    {
        $postalCodeRequest = [
            'query' => [
                'formatted' => 'true',
                'postalcode' => $postalCode,
                'maxRows' => '1',
                'username' => $this->token,
                'country' => $countrycode,
                'style' => 'full',
            ]
        ];

        $postalCodeLookupResponse = $this->client->request(
            'GET',
            $this->urlBase
                . 'postalCodeLookupJSON',
            $postalCodeRequest
        )->toArray();

        $this->responseCheck($postalCodeRequest, $postalCodeLookupResponse, "postalcode");

        return $postalCodeLookupResponse;
    }

    public function countrySubDivisionSearch(float $lat, float $lng): array
    {
        $query = ['query' => [
            'level' => '3',
            'lat' => $lat,
            'lng' => $lng,
            'username' => $this->token,
            'formatted' => 'true',
        ],];

        $countrySubDivisionSearchResponse = $this->client->request(
            'GET',
            $this->urlBase
                . 'countrySubdivisionJSON',
            $query
        )->toArray();

        return $countrySubDivisionSearchResponse;
    }

    public function getJsonSearch(int $geonameId): stdClass|string
    {
        $options = [
            'query' => [
                'username' => $this->token,
                'geonameId' => $geonameId,
                'style' => 'full',
            ],
            'timeout' => 250
        ];

        $getJsonSearchResponse = $this->client->request(
            'GET',
            $this->urlBase
                . 'getJSON',
            $options
        );
        if ($getJsonSearchResponse->getStatusCode() == 200) {
            return json_decode($getJsonSearchResponse->getContent());
        }

        return $getJsonSearchResponse->getContent();
    }

    public function searchJSON(string $fCode, int $startRow, array|string $countries): JsonResponse
    {
        $response = new JsonResponse();

        if (gettype($countries) !== 'string') {
            foreach ($countries as $country) {
                $countriesArray[] = ['country' => $country];
            }
            $query = ['query' => [
                'style' => 'full',
                'maxRows' => '1000',
                'formatted' => 'true',
                'startRow' => $startRow,
                'username' => $this->token,
                'featureCode' => $fCode,
                ...$countriesArray
            ],];
            $searchResponse = $this->client->request(
                'GET',
                $this->urlBase
                    . 'searchJSON',
                $query
            )->toArray();
        } else {
            $query = ['query' => [
                'style' => 'full',
                'maxRows' => '1000',
                'formatted' => 'true',
                'startRow' => $startRow,
                'username' => $this->token,
                'featureCode' => $fCode,
                'country' => $countries
            ],];
            $searchResponse = $this->client->request(
                'GET',
                $this->urlBase
                    . 'searchJSON',
                $query
            )->toArray();
        }
        $this->responseCheck($query, $searchResponse, "search");
        $response->setContent(json_encode($searchResponse));

        return $response;
    }

    public function childrenJSON(int $geonameId): array
    {
        $options = [
            'query' => [
                'style' => 'full',
                'formatted' => 'true',
                'username' => $this->token,
                'geonameId' => $geonameId,
                'maxRows' => 1000,
                //'hierarchy' => 'dependency'
            ],
            'timeout' => 250
        ];

        return $this->client->request(
            'GET',
            $this->urlBase
                . 'childrenJSON',
            $options
        )->toArray();
    }

    private function responseCheck(array|null $request, array $searchResponse, string $searchType): void
    {
        $formattedSearchResponse = array_change_key_case($searchResponse, CASE_LOWER);
        switch ($searchType) {
            case "postalcode":
                if (empty($formattedSearchResponse['postalcodes'])) {
                    $this->logger->error('PostalCode search error - empty response', $request);
                }
                break;

            case "latlng":
                if (empty($formattedSearchResponse['geonames'])) {
                    $this->logger->error('Coordinates search error - empty response', $request);
                }
                break;
            case 'search':
                if (empty($formattedSearchResponse['geonames'])) {
                    $this->logger->error('Coordinates search error - empty response', $request);
                }
                break;
        }
    }
}
