<?php

namespace App\Controller;

use App\Entity\GeonamesTranslation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/translation')]
class GeonamesTranslationController extends AbstractController
{
    #[Route('/', name: 'translation_get', methods: ['GET', 'HEAD'])]
    public function get(EntityManagerInterface $translationEntityManager): JsonResponse
    {
        $getResponse = $translationEntityManager->getRepository(GeonamesTranslation::class)->findAll();

        $result = array_map(static fn(GeonamesTranslation $value): array => $value->toArray(), $getResponse);
        //TODO: return paginated filtered list
        return new JsonResponse($result);
    }

    #[Route('/', name: 'translation_post', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {
        //TODO - mettre des if $request etc
        // https://medium.com/@peter.lafferty/converting-a-json-post-in-symfony-13a24c98fc0e
        // json_last_error invalid json body etc
        $content = json_decode($request->getContent());

        return new JsonResponse($content);
    }

    #[Route('/update', name: 'translation_update')]
    public function update(EntityManagerInterface $translationEntityManager): Response
    {
        $translationResponse = '';
        $translationJson = json_decode(file_get_contents(__DIR__ . '/../../base_data/geonames_translation.json'),true);

        foreach ($translationJson as $translationJsonKey => $translationJsonValue) {
            if (!$translationEntityManager->getRepository(GeonamesTranslation::class)
            ->findByCountryCode($translationJsonValue["countryCode"])){
                $translation = new GeonamesTranslation();
                $translation
                ->setGeonameId($translationJsonValue["geonameId"])
                ->setName($translationJsonValue["name"])
                ->setCountryCode($translationJsonValue["countryCode"])
                ->setFcode($translationJsonValue["fcode"])
                ->setLocale($translationJsonValue["locale"]);

                $translationEntityManager->persist($translation);
            }
            else {
                //$translationResponse .= 'KO';
            }
        }

        $translationEntityManager->flush();

        return $this->render('translation/index.html.twig', [
            'controller_name' => 'GeonamesTranslationController',
            'response' => $translationResponse,
            'translation' => $translationJson
        ]);
    }
}
