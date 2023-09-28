<?php
// src/Service/GeonameAPIService.php
namespace App\Service;

use stdClass;
use Doctrine\ORM\EntityManagerInterface;
use App\Interface\GeonamesAPIServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        try {
            $postalCodeSearchResponse = $this->client->request(
                'GET',
                $this->urlBase
                    . 'postalCodeSearchJSON',
                ['query' => [
                    'formatted' => 'true',
                    'postalcode' => $postalCode,
                    'maxRows' => '10',
                    'username' => $this->token,
                    'style' => 'full',
                ]]
            );
        } catch (\Exception $e) {
            throw new BadRequestException('Invalid Geonames.org API token.');
        }

        $this->responseCheck($postalCodeSearchResponse, "postalcode");

        return json_decode($postalCodeSearchResponse->getContent(), true);
    }

    public function postalCodeLookupJSON(string $postalCode, string $countrycode): array
    {
        try {
            $postalCodeSearchResponse = $this->client->request(
                'GET',
                $this->urlBase
                    . 'postalCodeLookupJSON',
                ['query' => [
                    'formatted' => 'true',
                    'postalcode' => $postalCode,
                    'maxRows' => '1',
                    'username' => $this->token,
                    'country' => $countrycode,
                    'style' => 'full',
                ]]
            );
        } catch (\Exception $e) {
            throw new BadRequestException('Invalid Geonames.org API token.');
        }
        $this->responseCheck($postalCodeSearchResponse, "postalcode");

        return json_decode($postalCodeSearchResponse->getContent(), true);
    }

    public function latLngSearch(float $lat, float $lng): ?int
    {
        try {
            $latlngSearchResponse = json_decode($this->client->request(
                'GET',
                $this->urlBase
                    . 'findNearbyJSON',
                ['query' => [
                    'formatted' => 'true',
                    'lat' => $lat,
                    'lng' => $lng,
                    'username' => $this->token,
                    'style' => 'full',
                    'maxRows' => '1',
                    'fclass' => 'P',
                    'fcode' => 'PPLA',
                    'fcode' => 'PPLC',
                    'fcode' => 'PPL',
                ],]
            )->getContent());
        } catch (\Exception $e) {
            throw new BadRequestException('Invalid Geonames.org API token.');
        }

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

    public function countrySubDivisionSearch(float $lat, float $lng): Response
    {
        try {
            $countrySubDivisionSearchResponse = $this->client->request(
                'GET',
                $this->urlBase
                    . 'countrySubdivisionJSON',
                ['query' => [
                    'style' => 'full',
                    'maxRows' => '10',
                    'radius' => '40',
                    'level' => '3',
                    'lat' => $lat,
                    'lng' => $lng,
                    'username' => $this->token,
                    'formatted' => 'true',
                ],]
            );
        } catch (\Exception $e) {
            throw new BadRequestException('Invalid Geonames.org API token.');
        }

        return new Response($countrySubDivisionSearchResponse->getContent());
    }

    public function searchJSON(string $fCode, int $startRow, array $countries): JsonResponse
    {
        $response = new JsonResponse();

        $countriesUrl = '';
        foreach ($countries as $country) {
            $countriesUrl .= '&country=' . $country;
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
                    'featureCode' => $fCode . $countriesUrl,
                ],]
            );
        } catch (\Exception $e) {
            throw new BadRequestException('Invalid Geonames.org API token.');
        }

        $this->responseCheck($searchResponse, "search");
        $response->setContent($searchResponse->getContent());
        return $response;
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
            case 'search':
                break;
        }
    }
}
