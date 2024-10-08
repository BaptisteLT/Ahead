<?php
namespace App\Module\HomepageModule\Controller;

use App\Controller\BootController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Module\HomepageModule\Service\SiteMapService;

class HomepageController extends BootController
{
    public function __construct(
        protected SiteMapService $siteMapService
    ){}

    #[Route('/', name: 'app_homepage', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'controllerName' => 'HomepageController',
        ]);
    }
}
