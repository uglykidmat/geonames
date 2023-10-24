<?php
// src/Service/AdminCodesMapperService.php
namespace App\Service;

use App\Entity\GeonamesAdministrativeDivision;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AdminCodesMapperService
{
    public function __construct(
        #[Autowire('%env(ALT_CODES_FETCH)%')]
        private readonly bool $altCodes,
    ) {
    }

    public function codesMapper(GeonamesAdministrativeDivision $IdFoundInDb, int $usedLevel): array
    {
        $adminCodes = array_slice($IdFoundInDb->getAdminCodes($this->altCodes), 0, $usedLevel);
        $adminKeys = ['adminCode1', 'adminCode2', 'adminCode3', 'adminCode4'];
        $adminKeysArray = array_slice($adminKeys, 0, $usedLevel);
        $adminCodesArray = array_combine($adminKeysArray, $adminCodes);

        return $adminCodesArray;
    }
}
