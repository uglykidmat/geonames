<?php
// src/Service/AdminCodesMapperService.php
namespace App\Service;

use App\Entity\GeonamesAdministrativeDivision;

class AdminCodesMapperService
{
    public function codesMapper(GeonamesAdministrativeDivision $IdFoundInDb, int $usedLevel): array
    {
        $adminCodes = array_slice($IdFoundInDb->getAdminCodes(), 0, $usedLevel);
        $adminKeys = ['adminCode1', 'adminCode2', 'adminCode3', 'adminCode4'];
        $adminKeysArray = array_slice($adminKeys, 0, $usedLevel);
        $adminCodesArray = array_combine($adminKeysArray, $adminCodes);

        return $adminCodesArray;
    }
}
