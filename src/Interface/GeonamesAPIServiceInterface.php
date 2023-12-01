<?php

namespace App\Interface;

use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;

interface GeonamesAPIServiceInterface
{
    public function postalCodeLookupJSON(string $postalCode, string $countrycode): array;

    public function latLngSearch(float $lat, float $lng): ?int;

    public function getJsonSearch(int $geonameId): stdClass|string;

    public function childrenJSON(int $geonameId): array;

    public function searchJSON(string $fCode, int $startRow, array $countries): JsonResponse;
}
