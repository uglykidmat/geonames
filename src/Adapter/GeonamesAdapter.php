<?php

namespace App\Adapter;

use stdClass;
use App\Entity\GeonamesAdministrativeDivision;

class GeonamesAdapter
{
    public function __construct()
    {
    }

    public static function AdaptObjToSubdiv(
        stdClass $subdivision
    ): GeonamesAdministrativeDivision {
        return (new GeonamesAdministrativeDivision())
            ->setGeonameId($subdivision->geonameId)
            ->setName($subdivision->name)
            ->setAsciiName($subdivision->asciiName ?? null)
            ->setToponymName($subdivision->toponymName ?? null)
            ->setContinentCode($subdivision->continentCode ?? null)
            ->setCc2($subdivision->cc2 ?? null)
            ->setCountryCode($subdivision->countryCode ?? null)
            ->setCountryId($subdivision->countryId ?? null)
            ->setAdminName1($subdivision->adminName1 ?? null)
            ->setAdminName2($subdivision->adminName2 ?? null)
            ->setAdminName3($subdivision->adminName3 ?? null)
            ->setAdminName4($subdivision->adminName4 ?? null)
            ->setAdminName5($subdivision->adminName5 ?? null)
            ->setAdminId1($subdivision->adminId1 ?? null)
            ->setAdminId2($subdivision->adminId2 ?? null)
            ->setAdminId3($subdivision->adminId3 ?? null)
            ->setAdminId4($subdivision->adminId4 ?? null)
            ->setAdminId5($subdivision->adminId5 ?? null)
            ->setAdminCode1($subdivision->adminCode1 ?? null)
            ->setAdminCode2($subdivision->adminCode2 ?? null)
            ->setAdminCode3($subdivision->adminCode3 ?? null)
            ->setAdminCode4($subdivision->adminCode4 ?? null)
            ->setLat($subdivision->lat ?? null)
            ->setLng($subdivision->lng ?? null)
            ->setPopulation($subdivision->population ?? null)
            ->setTimezoneGmtOffset($subdivision->timezone->gmtOffset ?? null)
            ->setTimezoneTimeZoneId($subdivision->timezone->timeZoneId ?? null)
            ->setTimezoneDstOffset($subdivision->timezone->dstOffset ?? null)
            ->setAdminTypeName($subdivision->adminTypeName ?? null)
            ->setFcode($subdivision->fcode ?? null)
            ->setFcl($subdivision->fcl ?? null)
            ->setSrtm3($subdivision->srtm3 ?? null)
            ->setAstergdem($subdivision->astergdem ?? null);
    }
}
