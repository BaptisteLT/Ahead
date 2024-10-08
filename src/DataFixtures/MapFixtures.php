<?php

namespace App\DataFixtures;

use App\Module\MapModule\Entity\Department;
use App\Module\MapModule\Entity\Region;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class MapFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /*$regions = [
            ['name' => 'Auvergne-Rhône-Alpes', 'number' =>'84', 'nbResidents' => 8235923, 'latitude' => 45.7387, 'longitude' => 4.8290],
            ['name' => 'Bourgogne-Franche-Comté', 'number' =>'27', 'nbResidents' => 2791719, 'latitude' => 47.0223, 'longitude' => 5.0415],
            ['name' => 'Bretagne', 'number' =>'27', 'nbResidents' => 3453023, 'latitude' => 48.2020, 'longitude' => -2.6522],
            ['name' => 'Centre-Val de Loire', 'number' =>'24', 'nbResidents' => 2573295, 'latitude' => 47.3410, 'longitude' => 1.6974],
            ['name' => 'Corse', 'number' =>'94', 'nbResidents' => 355528, 'latitude' => 41.9363, 'longitude' => 9.0128],
            ['name' => 'Grand Est', 'number' =>'44', 'nbResidents' => 5568711, 'latitude' => 48.8440, 'longitude' => 6.8712],
            ['name' => 'Hauts-de-France', 'number' =>'32', 'nbResidents' => 5983823, 'latitude' => 50.6320, 'longitude' => 3.0580],
            ['name' => 'Île-de-France', 'number' =>'11', 'nbResidents' => 12419961, 'latitude' => 48.8566, 'longitude' => 2.3522],
            ['name' => 'Normandie', 'number' =>'28', 'nbResidents' => 3327077, 'latitude' => 49.4144, 'longitude' => 0.8835],
            ['name' => 'Nouvelle-Aquitaine', 'number' =>'75', 'nbResidents' => 6154772, 'latitude' => 45.7490, 'longitude' => -0.6165],
            ['name' => 'Occitanie', 'number' =>'76', 'nbResidents' => 6154729, 'latitude' => 43.6119, 'longitude' => 1.4645],
            ['name' => 'Pays de la Loire', 'number' =>'52', 'nbResidents' => 3926389, 'latitude' => 47.3490, 'longitude' => -0.7668],
            ['name' => 'Provence-Alpes-Côte d\'Azur', 'number' =>'93', 'nbResidents' => 5198011, 'latitude' => 43.9493, 'longitude' => 6.1294],
        ];*/

        $regions = [
            [
                'name' => 'Auvergne-Rhône-Alpes', 
                'number' => '84', 
                'nbResidents' => 8235923, 
                'latitude' => 45.7387, 
                'longitude' => 4.8290,
                'departments' => [
                    ['name' => 'Allier', 'number' => '03'],
                    ['name' => 'Cantal', 'number' => '15'],
                    ['name' => 'Haute-Loire', 'number' => '43'],
                    ['name' => 'Puy-de-Dôme', 'number' => '63'],
                    ['name' => 'Rhône', 'number' => '69'],
                    ['name' => 'Savoie', 'number' => '73'],
                    ['name' => 'Haute-Savoie', 'number' => '74']
                ]
            ],
            [
                'name' => 'Bourgogne-Franche-Comté', 
                'number' => '27', 
                'nbResidents' => 2791719, 
                'latitude' => 47.0223, 
                'longitude' => 5.0415,
                'departments' => [
                    ['name' => 'Côte-d\'Or', 'number' => '21'],
                    ['name' => 'Doubs', 'number' => '25'],
                    ['name' => 'Jura', 'number' => '39'],
                    ['name' => 'Nièvre', 'number' => '58'],
                    ['name' => 'Saône-et-Loire', 'number' => '71'],
                    ['name' => 'Territoire de Belfort', 'number' => '90'],
                    ['name' => 'Yonne', 'number' => '89']
                ]
            ],
            [
                'name' => 'Bretagne', 
                'number' => '53', 
                'nbResidents' => 3453023, 
                'latitude' => 47.85, 
                'longitude' => -2.5522,
                'departments' => [
                    ['name' => 'Côtes-d\'Armor', 'number' => '22'],
                    ['name' => 'Finistère', 'number' => '29'],
                    ['name' => 'Ille-et-Vilaine', 'number' => '35'],
                    ['name' => 'Morbihan', 'number' => '56']
                ]
            ],
            [
                'name' => 'Centre-Val de Loire', 
                'number' => '24', 
                'nbResidents' => 2573295, 
                'latitude' => 47.3410, 
                'longitude' => 1.6974,
                'departments' => [
                    ['name' => 'Cher', 'number' => '18'],
                    ['name' => 'Eure-et-Loir', 'number' => '28'],
                    ['name' => 'Indre', 'number' => '36'],
                    ['name' => 'Indre-et-Loire', 'number' => '37'],
                    ['name' => 'Loir-et-Cher', 'number' => '41'],
                    ['name' => 'Loiret', 'number' => '45']
                ]
            ],
            [
                'name' => 'Corse', 
                'number' => '94', 
                'nbResidents' => 355528, 
                'latitude' => 42.3363, 
                'longitude' => 8.4828,
                'departments' => [
                    ['name' => 'Corse-du-Sud', 'number' => '2A'],
                    ['name' => 'Haute-Corse', 'number' => '2B']
                ]
            ],
            [
                'name' => 'Grand Est', 
                'number' => '44', 
                'nbResidents' => 5568711, 
                'latitude' => 48.4, 
                'longitude' => 5.4712,
                'departments' => [
                    ['name' => 'Ardennes', 'number' => '08'],
                    ['name' => 'Aube', 'number' => '10'],
                    ['name' => 'Bas-Rhin', 'number' => '67'],
                    ['name' => 'Haut-Rhin', 'number' => '68'],
                    ['name' => 'Marne', 'number' => '51'],
                    ['name' => 'Meurthe-et-Moselle', 'number' => '54'],
                    ['name' => 'Meuse', 'number' => '55'],
                    ['name' => 'Moselle', 'number' => '57'],
                    ['name' => 'Vosges', 'number' => '88']
                ]
            ],
            [
                'name' => 'Hauts-de-France', 
                'number' => '32', 
                'nbResidents' => 5983823, 
                'latitude' => 49.732, 
                'longitude' => 2.9,
                'departments' => [
                    ['name' => 'Aisne', 'number' => '02'],
                    ['name' => 'Nord', 'number' => '59'],
                    ['name' => 'Oise', 'number' => '60'],
                    ['name' => 'Pas-de-Calais', 'number' => '62'],
                    ['name' => 'Somme', 'number' => '80']
                ]
            ],
            [
                'name' => 'Île-de-France', 
                'number' => '11', 
                'nbResidents' => 12419961, 
                'latitude' => 48.4566, 
                'longitude' => 2.3522,
                'departments' => [
                    ['name' => 'Paris', 'number' => '75'],
                    ['name' => 'Seine-et-Marne', 'number' => '77'],
                    ['name' => 'Yvelines', 'number' => '78'],
                    ['name' => 'Essonne', 'number' => '91'],
                    ['name' => 'Hauts-de-Seine', 'number' => '92'],
                    ['name' => 'Seine-Saint-Denis', 'number' => '93'],
                    ['name' => 'Val-de-Marne', 'number' => '94'],
                    ['name' => 'Val-d\'Oise', 'number' => '95']
                ]
            ],
            [
                'name' => 'Normandie',
                'number' => '28',
                'nbResidents' => 3327077,
                'latitude' => 48.6,
                'longitude' => 0.5835,
                'departments' => [
                    ['name' => 'Calvados', 'number' => '14'],
                    ['name' => 'Eure', 'number' => '27'],
                    ['name' => 'Manche', 'number' => '50'],
                    ['name' => 'Orne', 'number' => '61'],
                    ['name' => 'Seine-Maritime', 'number' => '76']
                ]
            ],
            [
                'name' => 'Nouvelle-Aquitaine', 
                'number' => '75', 
                'nbResidents' => 6154772, 
                'latitude' => 45, 
                'longitude' => 0.8,
                'departments' => [
                    ['name' => 'Charente', 'number' => '16'],
                    ['name' => 'Charente-Maritime', 'number' => '17'],
                    ['name' => 'Corrèze', 'number' => '19'],
                    ['name' => 'Creuse', 'number' => '23'],
                    ['name' => 'Dordogne', 'number' => '24'],
                    ['name' => 'Landes', 'number' => '40'],
                    ['name' => 'Lot-et-Garonne', 'number' => '47'],
                    ['name' => 'Pyrénées-Atlantiques', 'number' => '64'],
                    ['name' => 'Deux-Sèvres', 'number' => '79'],
                    ['name' => 'Vienne', 'number' => '86'],
                    ['name' => 'Haute-Vienne', 'number' => '87']
                ]
            ],
            [
                'name' => 'Occitanie', 
                'number' => '76', 
                'nbResidents' => 6154729, 
                'latitude' => 43.8, 
                'longitude' => 2,
                'departments' => [
                    ['name' => 'Ariège', 'number' => '09'],
                    ['name' => 'Aude', 'number' => '11'],
                    ['name' => 'Aveyron', 'number' => '12'],
                    ['name' => 'Gard', 'number' => '30'],
                    ['name' => 'Gers', 'number' => '32'],
                    ['name' => 'Hautes-Pyrénées', 'number' => '65'],
                    ['name' => 'Hérault', 'number' => '34'],
                    ['name' => 'Lot', 'number' => '46'],
                    ['name' => 'Lozère', 'number' => '48'],
                    ['name' => 'Pyrénées-Orientales', 'number' => '66'],
                    ['name' => 'Tarn', 'number' => '81'],
                    ['name' => 'Tarn-et-Garonne', 'number' => '82']
                ]
            ],
            [
                'name' => 'Pays de la Loire', 
                'number' => '52', 
                'nbResidents' => 3926389, 
                'latitude' => 47.1, 
                'longitude' => -0.5668,
                'departments' => [
                    ['name' => 'Loire-Atlantique', 'number' => '44'],
                    ['name' => 'Maine-et-Loire', 'number' => '49'],
                    ['name' => 'Mayenne', 'number' => '53'],
                    ['name' => 'Sarthe', 'number' => '72'],
                    ['name' => 'Vendée', 'number' => '85']
                ]
            ],
            [
                'name' => 'Provence-Alpes-Côte d\'Azur', 
                'number' => '93', 
                'nbResidents' => 5198011, 
                'latitude' => 43.9493, 
                'longitude' => 6.1294,
                'departments' => [
                    ['name' => 'Alpes-de-Haute-Provence', 'number' => '04'],
                    ['name' => 'Alpes-Maritimes', 'number' => '06'],
                    ['name' => 'Bouches-du-Rhône', 'number' => '13'],
                    ['name' => 'Var', 'number' => '83'],
                    ['name' => 'Vaucluse', 'number' => '84']
                ]
            ],
        ];

        foreach($regions as $region){
            $regionEntity = new Region();
            $regionEntity->setName($region['name'])
                   ->setNumber($region['number'])
                   ->setNbResidents($region['nbResidents'])
                   ->setLatitude($region['latitude'])
                   ->setLongitude($region['longitude']);
            
            foreach($region['departments'] as $department){
                $departmentEntity = new Department();
                $departmentEntity->setNumber($department['name'])
                           ->setName((int)$department['number'])
                           ->setRegion($regionEntity);
                $manager->persist($departmentEntity);
            }

            $manager->persist($regionEntity);
        }

        $manager->flush();
    }
}
