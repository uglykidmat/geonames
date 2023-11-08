<?php
// src/Service/GeonameAPIService.php
namespace App\Service;

use stdClass;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Interface\GeonamesAPIServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

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

        try {
            $postalCodeSearchResponse = $this->client->request(
                'GET',
                $this->urlBase
                    . 'postalCodeLookupJSON',
                $postalCodeRequest
            );
        } catch (\Exception $e) {
            throw new BadRequestException('Invalid Geonames.org API token.');
        }

        $this->responseCheck($postalCodeRequest, $postalCodeSearchResponse, "postalcode");

        return $postalCodeSearchResponse->toArray();
    }

    public function latLngSearch(float $lat, float $lng): ?int
    {
            $latlngSearchResponse = $this->client->request(
                'GET',
                $this->urlBase
                    . 'findNearbyPlaceNameJSON',
                ['query' => [
                    'formatted' => 'true',
                    'lat' => $lat,
                    'lng' => $lng,
                    'username' => $this->token,
                    'style' => 'full',
                    'maxRows' => '1'
                ],]
            )->toArray();

        if (!empty($latlngSearchResponse->geonames) && is_array($latlngSearchResponse->geonames)) {

            return reset($latlngSearchResponse->geonames)->geonameId;
        }

        throw new HttpException(500, 'Empty response from Geonames Service.');
    }

    public function getJsonSearch(int $geonameId): ?stdClass
    {
        try {
            $getJsonSearchResponse = json_decode($this->client->request(
                'GET',
                $this->urlBase
                    . 'getJSON?geonameId=' . $geonameId
                    . '&username=' . $this->token
                    . '&style=full'
            )->getContent());
        } catch (\Exception $e) {
            throw new BadRequestException('Invalid Geonames.org API token.');
        }
        return $getJsonSearchResponse;
    }

    public function searchJSON(string $fCode, int $startRow, array|string $countries): JsonResponse
    {
        $response = new JsonResponse();
        if (gettype($countries) !== 'string') {
            foreach ($countries as $country) {
                $countriesArray[] = ['country' => $country];
            }
            try {
                $searchResponse = $this->client->request(
                    'GET',
                    $this->urlBase
                        . 'searchJSON',
                    ['query' => [
                        'style' => 'full',
                        'maxRows' => '1000',
                        'formatted' => 'true',
                        'startRow' => $startRow,
                        'username' => $this->token,
                        'featureCode' => $fCode,
                        ...$countriesArray
                    ],]
                );
            } catch (\Exception $e) {
                throw new BadRequestException('Invalid Geonames.org API token.');
            }
        } else {
            try {
                $searchResponse = $this->client->request(
                    'GET',
                    $this->urlBase
                        . 'searchJSON',
                    ['query' => [
                        'style' => 'full',
                        'maxRows' => '1000',
                        'formatted' => 'true',
                        'startRow' => $startRow,
                        'username' => $this->token,
                        'featureCode' => $fCode,
                        'country' => $countries
                    ],]
                );
            } catch (\Exception $e) {
                throw new BadRequestException('Invalid Geonames.org API token.');
            }
        }
        $this->responseCheck(null, $searchResponse, "search");
        $response->setContent($searchResponse->getContent());

        return $response;
    }

    private function responseCheck(array|null $request, object $searchResponse, string $searchType): void
    {
        if ($searchResponse->getStatusCode() >= 400) {
            throw new Exception('Unavailable Webservice or malformed API url');
        }

        $formattedSearchResponse = array_change_key_case($searchResponse->toArray(), CASE_LOWER);

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
                break;
        }
    }
}
