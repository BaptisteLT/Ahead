<?php
namespace App\Module\MapModule\Service;

use App\Module\MapModule\Entity\Region;

class MapService
{
    // Define SVG height and width in pixels
    private const SVG_HEIGHT = 960;
    private const SVG_WIDTH = 1000;
    
    //public function __construct(/*Use Dependency Injection if needed*/){}

    public function setCoordinates(Region &$region): Region
    {
        $x = (($region->getLongitude() + 5.1) / (9.5 + 5.1)) * self::SVG_WIDTH;
        $y = self::SVG_HEIGHT - (($region->getLatitude() - 41.3) / (51.1 - 41.3)) * self::SVG_HEIGHT;
        $region->setSvgY($y);
        $region->setSvgX($x);
    
        // You must return the region object
        return $region;
    }
}