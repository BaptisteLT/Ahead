<?php
// src/Service/TwigPathLoader.php
namespace App\Service;

use Twig\Environment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class TwigPathLoader
{
    private $twig;
    private $params;
    private $module = [];

    public function __construct(Environment $twig, ParameterBagInterface $params)
    {
        $this->twig = $twig;
        $this->params = $params;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->registerModuleTemplates();
        $this->registerHooksTemplates();
    }

    /**
     * Register the templates of the Module
     */
    public function registerModuleTemplates(): void
    {
        $moduleDir = $this->params->get('kernel.project_dir') . '/src/Module';
        $moduleTemplates = glob($moduleDir . '/*/templates');

        

        foreach ($moduleTemplates as $modulePath) {
            $moduleName = basename(str_replace('templates', '', $modulePath));
            $this->twig->getLoader()->addPath($modulePath, $moduleName);
        }
    }

    /**
     * Register the templates of the Hooks inside a Module
     */
    public function registerHooksTemplates(): void
    {
        $moduleDir = $this->params->get('kernel.project_dir') . '/src/Module';
        
        $hooksTemplatesPath = glob($moduleDir . '/*/Hooks/*/templates');
        
        foreach ($hooksTemplatesPath as $hookPath) {
            $hookName = basename(str_replace('templates', '', $hookPath));
            $this->twig->getLoader()->addPath($hookPath, $hookName);
        }
    }
}
