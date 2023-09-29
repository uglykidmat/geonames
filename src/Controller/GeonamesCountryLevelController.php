<?php

namespace App\Controller;

use App\Entity\GeonamesCountryLevel;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\GeonamesCountryLevelService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/country/level')]
class GeonamesCountryLevelController extends AbstractController
{
    public function __construct(
        private GeonamesCountryLevelService $levelService,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/update', name: 'country_level_update')]
    public function update(): JsonResponse
    {
        $updatedLevels = [];
        $levelsFound = [];
        $countryLevelJson = json_decode(file_get_contents(__DIR__ . '/../../base_data/geonames_country_level.json'), true);

        foreach ($countryLevelJson as $countryLevelJsonIndex => $countryLevelJsonValue) {

            if (!$this->entityManager->getRepository(GeonamesCountryLevel::class)
                ->findByCountryCode($countryLevelJsonValue["countrycode"])) {
                $this->levelService->addCountryLevel($countryLevelJsonValue);

                $updatedLevels[] = $countryLevelJsonValue['countrycode'];
            } else {

                $levelsFound[] = $countryLevelJsonValue['countrycode'];
            }
        }
        $this->entityManager->flush();

        return new JsonResponse(['Status' => 'Success', 'Countries OK' => implode(',', $levelsFound), 'Countries KO' => implode(',', $updatedLevels)]);
    }

    #[Route('/get', name: 'country_level_get')]
    public function getAllLevels(): JsonResponse
    {
        $response = new JsonResponse();
        $countryLevels = $this->entityManager->getRepository(GeonamesCountryLevel::class)
            ->findAll();

        $result = array_map(static fn (GeonamesCountryLevel $value): array => $value->toArray(), $countryLevels);

        $response->setContent(json_encode($result));

        return $response;
    }

    #[Route('/get/{countrycode}', name: 'country_level_get_country_code')]
    public function get(string $countrycode): JsonResponse
    {
        $countryLevel = $this->entityManager->getRepository(GeonamesCountryLevel::class)
            ->findOneByCountryCode($countrycode);

        return new JsonResponse($countryLevel->toArray());
    }
}
