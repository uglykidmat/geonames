<?php

namespace App\Service;

use App\Entity\GeonamesCountryLevel;
use Doctrine\ORM\EntityManagerInterface;

class GeonamesCountryLevelService
{
    public function __construct(
        public EntityManagerInterface $entityManager,

    ) {
    }

    public function addCountryLevel(array $countryLevel): void
    {
        $countryLevel = new GeonamesCountryLevel();
        $countryLevel
            ->setCountryCode($countryLevel["countrycode"])
            ->setMaxLevel($countryLevel["max_level"])
            ->setUsedLevel($countryLevel["used_level"])
            ->setADM1($countryLevel["ADM1"])
            ->setADM2($countryLevel["ADM2"])
            ->setADM3($countryLevel["ADM3"])
            ->setADM4($countryLevel["ADM4"])
            ->setADM5($countryLevel["ADM5"])
            ->setDone($countryLevel["done"]);

        $this->entityManager->persist($countryLevel);
    }
}
