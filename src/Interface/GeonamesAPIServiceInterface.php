<?php

namespace App\Interface;

use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;

interface GeonamesAPIServiceInterface
{
    public function latLngSearch(float $lat, float $lng): ?int;

    public function postalCodeLookupJSON(string $postalCode, string $countrycode): array;

    public function countrySubDivisionSearch(float $lat, float $lng): array;

    public function getJsonSearch(int $geonameId): stdClass|string;

    public function searchJSON(string $fCode, int $startRow, array|string $countries): JsonResponse;

    public function childrenJSON(int $geonameId): array;

    public function hierarchyJSON(int $geonameId): array;

    public function siblingsJSON(int $geonameId): array;

    public function neighboursJSON(int $geonameId): array;
}
