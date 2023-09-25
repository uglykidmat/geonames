<?php
// src/Interface/GeonamesAPIServiceInterface.php
namespace App\Interface;

use stdClass;
use Symfony\Component\HttpFoundation\Response;

interface GeonamesAPIServiceInterface
{
    public function postalCodeSearchJSON(string $postalCode): array;

    public function postalCodeLookupJSON(string $postalCode, string $countrycode): array;

    public function latLngSearch(float $lat, float $lng): ?int;

    public function getJsonSearch(int $geonameId): ?stdClass;

    public function countrySubDivisionSearch(float $lat, float $lng): Response;

    public function searchJSON(string $fCode, int $startRow, array $countries);
}
