<?php
namespace App\Module\ProfileModule\Service;

//use App\Module\ProfileModule\Repository\PagesRepository;
use App\Interface\SiteMapInterface;
use Symfony\Component\Routing\RouterInterface;

class SiteMapService implements SiteMapInterface
{
    public function __construct(
        private RouterInterface $router,
        //private PagesRepository $pagesRepository
    ){}

    // CODE A MODIFIER POUR GENERER LE BON SITEMAP
    // L'objectif est de générer et lister toutes les URL des controleurs de ce module avec $router->generate('url', [])
    public function getSiteMap(): array
    {
        $urls = [];

        $urls[] = [
            'loc' => '/profile', 
            'lastmod' => new \DateTime() //Remplacer par createdAt/UpdatedAt de votre entité
        ];
        

        return $urls;
    }
}