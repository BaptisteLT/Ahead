<?php
namespace App\Module\MapModule\Controller;

use App\Controller\BootController;
use App\Module\MapModule\Form\FiltersType;
use App\Module\MapModule\Repository\RegionRepository;
use App\Module\MapModule\Service\MapService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Module\MapModule\Service\SiteMapService;

class MapController extends BootController
{
    public function __construct(
        protected SiteMapService $siteMapService
    ){}

    #[Route('/map', name: 'app_map', methods: ['GET'])]
    public function index(RegionRepository $regionRepository, MapService $mapService): Response
    {
        $this->addBreadcrumb('Home', '/');
        $this->addBreadcrumb('Carte des clusters', null);

        $regions = $regionRepository->findAll();
        foreach($regions as $region){
            // Calculate SVG coordinates for the region
            $mapService->setCoordinates($region);
        }

        $form = $this->createForm(FiltersType::class);

        return $this->render('index.html.twig', [
            'regions' => $regions,
            'form' => $form
        ]); 
    }
}
