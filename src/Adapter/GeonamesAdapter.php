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
            ->setGeonameId($subdivision->geonames[0]->geonameId)
            ->setName($subdivision->geonames[0]->name)
            ->setAsciiName($subdivision->geonames[0]->asciiName)
            ->setToponymName($subdivision->geonames[0]->toponymName)
            ->setContinentCode($subdivision->geonames[0]->continentCode)
            //->setCc2($subdivision->geonames[0]->geonameId)
            ->setCountryCode($subdivision->geonames[0]->countryCode)
            ->setCountryId($subdivision->geonames[0]->countryId)
            ->setAdminName1($subdivision->geonames[0]->adminName1)
            ->setAdminName2($subdivision->geonames[0]->adminName2)
            ->setAdminName3($subdivision->geonames[0]->adminName3)
            ->setAdminName4($subdivision->geonames[0]->adminName4)
            ->setAdminName5($subdivision->geonames[0]->adminName5)
            ->setAdminId1($subdivision->geonames[0]->adminId1)
            ->setAdminId2($subdivision->geonames[0]->adminId2)
            ->setAdminId3($subdivision->geonames[0]->adminId3)
            ->setAdminId4($subdivision->geonames[0]->adminId4)
            //->setAdminId5($subdivision->geonames[0]->adminId5)
            ->setAdminCode1($subdivision->geonames[0]->adminCode1)
            ->setAdminCode2($subdivision->geonames[0]->adminCode2)
            ->setAdminCode3($subdivision->geonames[0]->adminCode3)
            ->setAdminCode4($subdivision->geonames[0]->adminCode4)
            //->setAdminCode5($subdivision->geonames[0]->geonameId);
            ->setLat($subdivision->geonames[0]->lat)
            ->setLng($subdivision->geonames[0]->lng)
            ->setPopulation($subdivision->geonames[0]->population)
            ->setTimezoneGmtOffset($subdivision->geonames[0]->timezone->gmtOffset)
            ->setTimezoneTimeZoneId($subdivision->geonames[0]->timezone->timeZoneId)
            ->setTimezoneDstOffset($subdivision->geonames[0]->timezone->dstOffset)
            //->setAdminTypeName($subdivision->geonames[0]->adminCode4)
            ->setFcode($subdivision->geonames[0]->fcode)
            ->setFcl($subdivision->geonames[0]->fcl)
            ->setSrtm3($subdivision->geonames[0]->srtm3)
            ->setAstergdem($subdivision->geonames[0]->astergdem);
    }
}
