<?php
// src/Service/GeonameAdapterService.php
namespace App\Service;

use stdClass;
use App\Adapter\GeonamesAdapter;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GeonamesAdministrativeDivision;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeonamesDBCachingService
{
    public function __construct(
        private HttpClientInterface $httpClientInterface,
        private EntityManagerInterface $entityManager)
    {
    }

    public function searchSubdivisionInDatabase(float $lat, float $lng): Response {
        $dbresponse = $this->entityManager
        ->getRepository(GeonamesAdministrativeDivision::class)
        ->findBy(array('lat' => $lat, 'lng' => $lng));
        foreach ($dbresponse as $subdivisionFound) {
            $subdivisionFound=json_encode($subdivisionFound);
        }

        return new Response($dbresponse);
    }

    public function saveSubdivisionToDatabase(stdClass $subdivision): void {
        //foreach ($subDivisionContent->geonames[0] as $subDivisionContentKey => $subDivisionContentValue) {
        $newSubDivision = GeonamesAdapter::AdaptObjToSubdiv($subdivision);

        $this->entityManager->persist($newSubDivision);                  
        $this->entityManager->flush();
       // }
    }
}
