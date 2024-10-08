<?php
namespace App\Module\MapModule\Controller;

use App\Controller\BootController;
use App\Module\MapModule\Repository\RegionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Module\MapModule\Service\SiteMapService;

class MapController extends BootController
{
    public function __construct(
        protected SiteMapService $siteMapService
    ){}

    #[Route('/map', name: 'app_map', methods: ['GET'])]
    public function index(RegionRepository $regionRepository): Response
    {
        $this->addBreadcrumb('Home', '/');
        $this->addBreadcrumb('Carte des maladies', null);

        $regions = $regionRepository->findAll();

        foreach($regions as $region){
            // Define svg parameters
            $svgWidth = 1000;
            $svgHeight = 960;
            // Calculate SVG coordinates
            $x = (($region->getLongitude() + 5.1) / (9.5 + 5.1)) * $svgWidth;
            $y = $svgHeight - (($region->getLatitude() - 41.3) / (51.1 - 41.3)) * $svgHeight;
            $region->setSvgY($y);
            $region->setSvgX($x);
        }

        return $this->render('index.html.twig', [
            'regions' => $regions,
        ]);
    }
}
