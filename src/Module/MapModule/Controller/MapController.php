<?php
namespace App\Module\MapModule\Controller;

use App\Controller\BootController;
use App\Module\ConditionModule\Repository\ReportRepository;
use App\Module\MapModule\Form\FiltersType;
use App\Module\MapModule\Service\MapService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Module\MapModule\Service\SiteMapService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Module\MapModule\Repository\RegionRepository;
use DateTime;

class MapController extends BootController
{
    private const MAX_PIXELS_RADIUS = 60;

    public function __construct(
        protected SiteMapService $siteMapService
    ){}

    #[Route('/map', name: 'app_map', methods: ['GET'])]
    public function index(): Response
    {
        $this->addBreadcrumb('Accueil', '/');
        $this->addBreadcrumb('Carte des clusters', null);

        $form = $this->createForm(FiltersType::class);

        return $this->render('index.html.twig', [
            'form' => $form
        ]); 
    }

    #[Route('/api/filters', name: 'api_filters', methods: ['POST'])]
    public function apiFilters(Request $request, ReportRepository $reportRepository, MapService $mapService): JsonResponse
    {
        $dateFrom = $request->request->get('dateFrom', null);
        $dateTo = $request->request->get('dateTo', null);
        $diseaseId = $request->request->get('diseaseId', null);
        $symptoms = $request->request->get('symptoms', null);

        //Early return
        if(empty($diseaseId) && empty($symptoms)){
            return new JsonResponse([]);
        }

        $today = new \DateTime();
        $dateFrom = (empty($dateFrom) ? (clone $today)->modify('-1 month') : new \DateTime($dateFrom));
        $dateTo = (empty($dateTo) ? $today : new \DateTime($dateTo));

        $reports = $reportRepository->findByFilters($dateFrom, $dateTo, $diseaseId, $symptoms);
    
        //TODO: Mettre la logique dans le service
        $maxNumber = 0;
        foreach($reports as $key => $report){
            $number = ($report['countReports']*100)/$report['nbResidents'];
            if($number > $maxNumber){
                $maxNumber = $number;
            }
            $reports[$key]['number'] = $number;
        }
        
        foreach($reports as $key => $report){
            
            if($maxNumber !== 0){
                $reports[$key]['pixelsSize'] = (int)(($report['number'] / $maxNumber) * self::MAX_PIXELS_RADIUS);
            }else{
                $reports[$key]['pixelsSize'] = 0;
            }
            if($reports[$key]['pixelsSize'] < 15){
                $reports[$key]['pixelsSize'] = 15;
            }
            $reports[$key]['x'] = $mapService->calculateX($report['longitude']);
            $reports[$key]['y'] = $mapService->calculateY($report['latitude']);
        }

        return new JsonResponse($reports);
    }
}
