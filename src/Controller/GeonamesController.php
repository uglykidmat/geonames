<?php
// src/Controller/GeonamesController.php
namespace App\Controller;

use Symfony\Component\Asset\Package;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Response;

class GeonamesController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/', name: 'welcome')]
    public function welcome(RouterInterface $router): Response
    {
        $geonamesPackage = new Package(new EmptyVersionStrategy());
        $geonamesRouteCollection = $router->getRouteCollection();
        $allRoutes = $geonamesRouteCollection->all();

        return $this->render(
            'Geonames/geonameswelcome.html.twig',
            [
                'allRoutes' => $allRoutes,
                'geonameshomebackgroundjpg' => $geonamesPackage->getUrl('geonames_home_background.jpg'),
            ]
        );
    }
}
