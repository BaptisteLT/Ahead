<?php
namespace App\Module\ConditionModule\Controller;

use App\Controller\BootController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Module\ConditionModule\Service\SiteMapService;

class ConditionController extends BootController
{
    public function __construct(
        protected SiteMapService $siteMapService
    ){}

    #[Route('/form', name: 'app_form', methods: ['GET'])]
    public function indexForm(): Response
    {
        $this->addBreadcrumb('Accueil', '/');
        $this->addBreadcrumb('Formulaire', null);

        return $this->render('index_form.html.twig');
    }
}
