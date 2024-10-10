<?php

namespace App\DataFixtures;
ini_set('memory_limit', '256M');

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Module\ConditionModule\Entity\Report;
use App\Module\ConditionModule\Entity\Disease;
use App\Module\ConditionModule\Entity\Symptoms;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ReportFixtures extends Fixture implements DependentFixtureInterface
{
    private $faker;

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        $symptoms = [];
        $symptomsString = [
            "Fièvre",
            "Nausée",
            "Vomissements",
            "Maux de tête",
            "Fatigue",
            "Toux",
            "Essoufflement",
            "Douleur thoracique",
            "Palpitations",
            "Démangeaisons",
            "Éruptions cutanées",
            "Changements d'appétit",
            "Perte de poids",
            "Gonflement",
            "Douleurs articulaires",
            "Raideur",
            "Incapacité à dormir",
            "Transpiration excessive",
            "Frissons",
            "Bouffées de chaleur",
            "Constipation",
            "Diarrhée",
            "Saignement",
            "Céphalées",
            "Sensation de brûlure",
            "Vertiges",
            "Troubles de la vision",
            "Sensibilité à la lumière",
            "Troubles de l'audition",
            "Acouphènes",
            "Sensation de pression dans les oreilles",
            "Difficulté à avaler",
            "Bouche sèche",
            "Gencives enflées",
            "Douleur abdominale",
            "Ballonnements",
            "Crampes",
            "Engourdissement",
            "Picotements",
            "Faiblesse musculaire",
            "Difficulté à marcher",
            "Tremblements",
            "Troubles de l'humeur",
            "Anxiété",
            "Dépression",
            "Perte de mémoire",
            "Confusion",
            "Évanouissements",
            "Douleur au cou",
            "Sensation de masse dans la gorge",
            "Changement de couleur de la peau",
            "Sensation de froid",
            "Sensation de chaud",
            "Maux de dos",
            "Douleur au bas du dos",
            "Troubles urinaires",
            "Sensation de brûlure en urinant",
            "Urine foncée",
            "Douleur dans les reins",
            "Écoulement nasal",
            "Gorge irritée",
            "Respiration sifflante",
            "Rhinite",
            "Sinusite",
            "Sensation de fatigue chronique",
            "Manque d'énergie",
            "Sensibilité accrue aux infections",
            "Éruptions sur les muqueuses",
            "Sensation de malaise général",
            "Crampe musculaire",
            "Difficulté à se concentrer",
            "Maux de dents",
            "Sensation de vide dans l'estomac",
            "Déshydratation",
            "Toux persistante",
            "Sensation de battements cardiaques irréguliers",
            "Sensation de flottement",
            "Palpitations cardiaques",
            "Changements de la vision",
            "Troubles de l'équilibre",
            "Hémorragies",
            "Sensation d'oppression",
            "Syndrome de fatigue chronique",
            "Toux sèche",
            "Toux productive",
            "Difficulté à respirer",
            "Sensation de malaise après les repas",
            "Sensation de vertige en se levant",
            "Crampes abdominales",
            "Sensation de lourdeur dans les jambes",
            "Sensation de picotement dans les doigts",
            "Maux de gorge",
            "Sensation de suffocation",
            "Irritabilité",
            "Sensation de fatigue mentale",
            "Sensation de brûlure d'estomac",
            "Sensation d'engourdissement",
            "Changements de rythme cardiaque",
            "Douleurs musculaires",
            "Sensibilité à la pression",
            "Difficulté à uriner",
            "Sensation de chaleur dans les mains",
            "Crachats sanguinolents",
            "Difficulté à se lever",
            "Douleur à la poitrine lors de la respiration",
            "Sensation de stress",
            "Difficulté à se reposer",
            "Difficulté à se souvenir des choses",
            "Sensation de vertige en se penchant",
            "Sensation de tristesse",
            "Sensation de faiblesse générale"
        ];
        // Generate 100 symptoms
        foreach($symptomsString as $symtomString){
            $symptom = new Symptoms();
            $symptom->setName($symtomString);
            $manager->persist($symptom);

            $symptoms[] = $symptom;
        }

        $diseasesString = [
            "COVID-19",
            "Grippe",
            "Rhume",
            "Varicelle",
            "Rougeole",
            "Oreillons",
            "Rubéole",
            "Tuberculose",
            "Hépatite A",
            "Hépatite B",
            "Hépatite C",
            "SIDA",
            "Gonorrhée",
            "Chlamydia",
            "Syphilis",
            "Herpès",
            "Fièvre typhoïde",
            "Malaria",
            "Dengue",
            "Fièvre jaune",
            "Chikungunya",
            "Zika",
            "Bactériémie",
            "Septicémie",
            "Scarlatine",
            "Coqueluche",
            "Tétanos",
            "Diphtérie",
            "Meningite",
            "Listeriose",
            "Salmonellose",
            "Shigellose",
            "Campylobactériose",
            "Croup",
            "Cytomégalovirus",
            "Pneumonie à pneumocoque",
            "Bactéries résistantes aux antibiotiques",
            "Infections à staphylocoques",
            "Infections à streptocoques",
            "Influenza",
            "Norovirus",
            "Rotavirus",
            "Mononucléose infectieuse",
            "Paludisme",
            "Leptospirose",
            "Candidose",
            "Peste",
            "Typhus",
            "Fièvre des canyons",
            "Fièvre de l'Ouest du Nil",
            "Fièvre de Lassa",
            "Diftériose",
            "Rage",
            "Toxoplasmose",
            "Filariose",
            "Méningite virale",
            "Viral exanthema",
            "Syndrome respiratoire aigu sévère (SRAS)",
            "Maladie de Chagas",
            "Fièvre de l'Aérobiose",
            "Fièvre de Dengue",
            "Fièvre de l'Africain",
            "Fièvre des Marais",
            "Virus Ebola",
            "Virus Marburg",
            "Inflammation hépatique virale",
            "Fièvre de l'Ouest",
            "Fièvre à virus Junin",
            "Fièvre à virus Machupo",
            "Fièvre à virus Guanarito",
            "Virus de la fièvre de Lassa",
            "Virus du Nil occidental",
            "Virus du Chikungunya",
            "Virus de la rage",
            "Virus de la rubéole",
            "Virus des oreillons"
        ];
        // Generate 80 diseases
        foreach($diseasesString as $diseaseString){
            $disease = new Disease();
            $disease->setName($diseaseString);
            $manager->persist($disease);

            $diseases[] = $disease;
        }

        // Generate 1000 users
        $users = [];
        for($i=0; $i<1000; $i++){
            $user = new User();
            $user->setEmail(bin2hex(random_bytes(5 / 2)).$this->faker->email());
            $user->setPassword(bin2hex(random_bytes(16 / 2)));
            $manager->persist($user);

            $users[] = $user;
        }

        $departmentNumbers = [
            '03', '15', '43', '63', '69', '73', '74', // Auvergne-Rhône-Alpes
            '21', '25', '39', '58', '71', '90', '89', // Bourgogne-Franche-Comté
            '22', '29', '35', '56', // Bretagne
            '18', '28', '36', '37', '41', '45', // Centre-Val de Loire
            '2A', '2B', // Corse
            '08', '10', '67', '68', '51', '54', '55', '57', '88', // Grand Est
            '02', '59', '60', '62', '80', // Hauts-de-France
            '75', '77', '78', '91', '92', '93', '94', '95', // Île-de-France
            '14', '27', '50', '61', '76', // Normandie
            '16', '17', '19', '23', '24', '40', '47', '64', '79', '86', '87', // Nouvelle-Aquitaine
            '09', '11', '12', '30', '32', '65', '34', '46', '48', '66', '81', '82', // Occitanie
            '44', '49', '53', '72', '85', // Pays de la Loire
            '04', '06', '13', '83', '84' // Provence-Alpes-Côte d'Azur
        ];

        // Generate 2000 reports
        for($y=0; $y<2000; $y++){
            $report = new Report();
            // Get a random department from the array
            $randomDepartmentKey = array_rand($departmentNumbers);
            $randomDepartment = $departmentNumbers[$randomDepartmentKey];
            $report->setDepartment($this->getReference('Department'.$randomDepartment));

            // Get a random user from the array
            $randomUserKey = array_rand($users);
            $randomUser = $users[$randomUserKey];
            $report->setUser($randomUser);

            // Set a random dateReport
            $endDate = new \DateTimeImmutable();
            $startDate = (new \DateTimeImmutable())->modify('-1 year');
            $randomTimestamp = rand($startDate->getTimestamp(), $endDate->getTimestamp());
            $randomDate = \DateTimeImmutable::createFromFormat('U', $randomTimestamp);

            // Get a random disease from the array (50% to be null)
            $randNumber = rand(0,1);
            if($randNumber === 1){
                $randomDiseaseKey = array_rand($diseases);
                $randomDisease = $diseases[$randomDiseaseKey];
                $report->setDisease($randomDisease);
            }

            $randNumber = rand(1,6);
            for($i=0;$i<$randNumber;$i++){
                $randomSymptomKey = array_rand($symptoms);
                $randomSymtom = $symptoms[$randomSymptomKey];
                $report->addSymptom($randomSymtom);
            }


            $report->setDateReport($randomDate);

            $manager->persist($report);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            MapFixtures::class,
        ];
    }
}
