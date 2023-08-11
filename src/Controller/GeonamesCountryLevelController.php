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
    #[Route('/update', name: 'app_geonames_country_level_update')]
    public function update(EntityManagerInterface $geonamesCountryLevelEntityManager): Response
    {
        $response = '';
        $countryLevelJson = json_decode(file_get_contents(__DIR__ . '/../../base_data/geonames_country_level.json'),true);

        foreach ($countryLevelJson as $countryLevelJsonIndex => $countryLevelJsonValue) {
                if (!$geonamesCountryLevelEntityManager->getRepository(GeonamesCountryLevel::class)
                ->findByCountryCode($countryLevelJsonValue["countrycode"])) {
                    $geonamesCountryLevel = new GeonamesCountryLevel();
                    $geonamesCountryLevel
                    ->setCountryCode($countryLevelJsonValue["countrycode"])
                    ->setMaxLevel($countryLevelJsonValue["max_level"])
                    ->setUsedLevel($countryLevelJsonValue["used_level"])
                    ->setADM1($countryLevelJsonValue["ADM1"])
                    ->setADM2($countryLevelJsonValue["ADM2"])
                    ->setADM3($countryLevelJsonValue["ADM3"])
                    ->setADM4($countryLevelJsonValue["ADM4"])
                    ->setADM5($countryLevelJsonValue["ADM5"])
                    ->setDone($countryLevelJsonValue["done"]);
                
                    $geonamesCountryLevelEntityManager->persist($geonamesCountryLevel);

                    $response .= '<br/>Levels for country code <b>'.$countryLevelJsonValue["countrycode"].'</b> have been imported.<br />';
                }
                else {
                    $response .= '<br/>Levels for country code <b>'.$countryLevelJsonValue["countrycode"].'</b> already found in database';
                }
        }
        $geonamesCountryLevelEntityManager->flush();

        return new Response($response);
    }

    #[Route('/get', name: 'app_geonames_country_level_get')]
    public function getonelevel(EntityManagerInterface $entityManager): JsonResponse
    {
       $geonamesCountryLevels = $entityManager->getRepository(GeonamesCountryLevel::class)
               ->findAll();

       $result = array_map(static fn(GeonamesCountryLevel $value): array => $value->toArray(), $geonamesCountryLevels);

        return new JsonResponse($result);
    }

    #[Route('/get/{countrycode}', name: 'app_geonames_country_level_get_country_code')]
    public function get(EntityManagerInterface $entityManager, string $countrycode): JsonResponse
    {
        $geonamesCountryLevel = $entityManager->getRepository(GeonamesCountryLevel::class)
                ->findOneByCountryCode($countrycode);

        return new JsonResponse($geonamesCountryLevel->toArray());
    }
}
