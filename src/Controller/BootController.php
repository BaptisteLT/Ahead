<?php
namespace App\Controller;

use App\Service\BreadcrumbService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\Required;

class BootController extends AbstractController
{
    protected BreadcrumbService $breadcrumbService;

    //Doc pour #[Required] https://symfony.com/doc/current/service_container/calls.html
    #[Required]
    public function setBreadcrumbService(BreadcrumbService $breadcrumbService): void
    {
        $this->breadcrumbService = $breadcrumbService;
    }

    /**
     * Permet d'ajouter un fil d'ariane qui sera appelé dans twig automatiquement
    */
    protected function addBreadcrumb(string $label, ?string $url): void
    {
        $this->breadcrumbService->addBreadcrumb($label, $url);
    }

    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        // Get the module name dynamically from the class namespace
        $moduleName = $this->getModuleName();
        // Modify the view to include the module name
        $viewWithModule = '@'.$moduleName . '/' . $view;
        return parent::render($viewWithModule, $parameters, $response);
    }

    
    private function getModuleName(): string
    {
        // Use reflection to get the class name
        $reflector = new \ReflectionClass($this);
        $namespace = $reflector->getNamespaceName();

        // Extract the module name from the namespace
        // Assuming the structure is 'App\Module\ModuleName\Controller\SomeController'
        if (preg_match('/App\\\Module\\\([^\\\]*)/', $namespace, $matches)) {
            return $matches[1]; // This will return 'ModuleName'
        }

        // Fallback if no module name found
        return 'DefaultModule'; // Change as necessary
    }


    /**
     * Return 404 page
     */
    protected function return404(string $message = 'Page not found'){
        throw new NotFoundHttpException($message);
    }
}
