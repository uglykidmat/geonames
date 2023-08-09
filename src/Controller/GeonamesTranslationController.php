<?php

namespace App\Controller;

use App\Entity\GeonamesTranslation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/geonames/translation')]
class GeonamesTranslationController extends AbstractController
{
    #[Route('/update', name: 'app_geonames_translation_update')]
    public function update(EntityManagerInterface $geonamesTranslationEntityManager): Response
    {
        $geonamesTranslationResponse = '';
        $geonamesTranslationJson = json_decode(file_get_contents(__DIR__ . '/../../base_data/geonames_translation.json'),true);

        foreach ($geonamesTranslationJson as $geonamesTranslationJsonKey => $geonamesTranslationJsonValue) {
            if (!$geonamesTranslationEntityManager->getRepository(GeonamesTranslation::class)
            ->findByCountryCode($geonamesTranslationJsonValue["countryCode"])){
                $geonamesTranslation = new GeonamesTranslation();
                $geonamesTranslation
                ->setGeonameId($geonamesTranslationJsonValue["geonameId"])
                ->setName($geonamesTranslationJsonValue["name"])
                ->setCountryCode($geonamesTranslationJsonValue["countryCode"])
                ->setFcode($geonamesTranslationJsonValue["fcode"])
                ->setLocale($geonamesTranslationJsonValue["locale"]);

                $geonamesTranslationEntityManager->persist($geonamesTranslation);
            }
            else {
                //$geonamesTranslationResponse .= 'KO';
            }
        }

        $geonamesTranslationEntityManager->flush();

        return $this->render('geonames_translation/index.html.twig', [
            'controller_name' => 'GeonamesTranslationController',
            'response' => $geonamesTranslationResponse,
            'geonames_translation' => $geonamesTranslationJson
        ]);
    }
}
