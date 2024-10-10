<?php
namespace App\Service;

use Twig\Environment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class TwigPathLoader
{
    private $twig;
    private $params;

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
        $modules = glob($moduleDir . '/*');

        foreach ($modules as $modulePath) {
            if (is_dir($modulePath)) {
                $templatesPath = $modulePath . '/templates';
                if (is_dir($templatesPath)) {
                    // Register all template directories
                    $this->twig->getLoader()->addPath($templatesPath, basename($modulePath));
                }
            }
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
            if (is_dir($hookPath)) {
                $this->twig->getLoader()->addPath($hookPath, basename(dirname($hookPath))); // Register the hook path
            }
        }
    }
}
