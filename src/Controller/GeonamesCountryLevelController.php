<?php

namespace App\Controller;

use App\Entity\GeonamesCountryLevel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/country/level')]
class GeonamesCountryLevelController extends AbstractController
{
    #[Route('/update', name: 'country_level_update')]
    public function update(EntityManagerInterface $CountryLevelEntityManager): Response
    {
        $response = '';
        $countryLevelJson = json_decode(file_get_contents(__DIR__ . '/../../base_data/geonames_country_level.json'),true);

        foreach ($countryLevelJson as $countryLevelJsonIndex => $countryLevelJsonValue) {
                if (!$CountryLevelEntityManager->getRepository(GeonamesCountryLevel::class)
                ->findByCountryCode($countryLevelJsonValue["countrycode"])) {
                    $CountryLevel = new GeonamesCountryLevel();
                    $CountryLevel
                    ->setCountryCode($countryLevelJsonValue["countrycode"])
                    ->setMaxLevel($countryLevelJsonValue["max_level"])
                    ->setUsedLevel($countryLevelJsonValue["used_level"])
                    ->setADM1($countryLevelJsonValue["ADM1"])
                    ->setADM2($countryLevelJsonValue["ADM2"])
                    ->setADM3($countryLevelJsonValue["ADM3"])
                    ->setADM4($countryLevelJsonValue["ADM4"])
                    ->setADM5($countryLevelJsonValue["ADM5"])
                    ->setDone($countryLevelJsonValue["done"]);
                
                    $CountryLevelEntityManager->persist($CountryLevel);

                    $response .= '<br/>Levels for country code <b>'.$countryLevelJsonValue["countrycode"].'</b> have been imported.<br />';
                }
                else {
                    $response .= '<br/>Levels for country code <b>'.$countryLevelJsonValue["countrycode"].'</b> already found in database';
                }
        }
        $CountryLevelEntityManager->flush();

        return new Response($response);
    }

    #[Route('/get', name: 'country_level_get')]
    public function getonelevel(EntityManagerInterface $entityManager): JsonResponse
    {
       $CountryLevels = $entityManager->getRepository(GeonamesCountryLevel::class)
               ->findAll();

       $result = array_map(static fn(GeonamesCountryLevel $value): array => $value->toArray(), $CountryLevels);

        return new JsonResponse($result);
    }

    #[Route('/get/{countrycode}', name: 'country_level_get_country_code')]
    public function get(EntityManagerInterface $entityManager, string $countrycode): JsonResponse
    {
        $CountryLevel = $entityManager->getRepository(GeonamesCountryLevel::class)
                ->findOneByCountryCode($countrycode);

        return new JsonResponse($CountryLevel->toArray());
    }
}
