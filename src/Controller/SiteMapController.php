<?php
namespace App\Controller;

use App\Interface\SiteMapInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

class SiteMapController extends AbstractController
{
    private iterable $sitemapServices;

    public function __construct(iterable $sitemapServices)
    {
        $this->sitemapServices = $sitemapServices;
    }

    #[Route('/sitemap.xml', name: 'app_sitemap_xml', methods: ['GET'], defaults: ["_format" => "xml"])]
    public function index(): Response
    {
        // Build XML content manually
        $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlContent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
        $xmlContent .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $xmlContent .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
        
        foreach ($this->sitemapServices as $sitemapService) {
            if ($sitemapService instanceof SiteMapInterface) {
                
                $urls = $sitemapService->getSiteMap();
                foreach ($urls as $url) {
                    if(array_key_exists('loc', $url))
                    {
                        $xmlContent .= '<url>';
                        $xmlContent .= '<loc>' . htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8') . '</loc>';
                        if(array_key_exists('lastmod', $url) && ($url['lastmod'] instanceof \DateTime || $url['lastmod'] instanceof \DateTimeImmutable)){
                            $xmlContent .= '<lastmod>' . $url['lastmod']->format(\DateTime::W3C) . '</lastmod>';
                        }
                        $xmlContent .= '</url>';
                    }
                }
            }
        }

        $xmlContent .= '</urlset>';


        // Return the XML response
        $response = new Response($xmlContent);
        $response->headers->set('Content-Type', 'application/xml');

        return $response;
    }
}
