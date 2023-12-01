<?php

namespace App\Controller;

use App\Entity\GeonamesCountry;
use App\Entity\GeonamesCountryLevel;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\GeonamesCountryLevelService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[Route('/country/level')]
#[OA\Tag(name: 'Geonames Countries Level')]
class GeonamesCountryLevelController extends AbstractController
{
    public function __construct(
        private GeonamesCountryLevelService $levelService,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/update', name: 'country_level_update', methods: ['GET'])]
    public function update(): JsonResponse
    {
        $updatedLevels = 0;
        $newLevels = 0;
        $countryLevelJson = json_decode(file_get_contents(__DIR__ . '/../../base_data/geonames_country_level.json'), true);

        foreach ($countryLevelJson as $countryLevelJsonValue) {
            if (!$this->entityManager->getRepository(GeonamesCountryLevel::class)
                ->findByCountryCode($countryLevelJsonValue["countrycode"])) {
                $level = $this->levelService->addCountryLevel($countryLevelJsonValue);
                $newLevels++;
            } else {
                $level = $this->levelService->setCountryLevel($countryLevelJsonValue);
                $updatedLevels++;
            }
            $countryAssociated = $this->entityManager->getRepository(GeonamesCountry::class)->findOneByCountryCode($countryLevelJsonValue["countrycode"]);

            $countryAssociated->setLevel($level);
        }
        $this->entityManager->flush();

        return new JsonResponse([
            'Status' => 'Success',
            'Levels already found' => $updatedLevels,
            'Levels inserted' => $newLevels
        ]);
    }

    #[Route('/get', name: 'country_level_get', methods: ['GET'])]
    public function getAllLevels(): JsonResponse
    {
        $response = new JsonResponse();
        $countryLevels = $this->entityManager->getRepository(GeonamesCountryLevel::class)
            ->findAll();

        $result = array_map(static fn (GeonamesCountryLevel $value): array => $value->toArray(), $countryLevels);

        $response->setContent(json_encode($result));

        return $response;
    }

    #[Route('/get/{countrycode}', name: 'country_level_get_country_code', methods: ['GET'])]
    public function get(string $countrycode): JsonResponse
    {
        $countryLevel = $this->entityManager->getRepository(GeonamesCountryLevel::class)
            ->findOneByCountryCode($countrycode);

        return new JsonResponse($countryLevel->toArray());
    }
}
