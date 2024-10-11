<?php
namespace App\Module\ConditionModule\Repository;

use App\Module\ConditionModule\Entity\Report;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Report>
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    //    /**
    //     * @return Report[] Returns an array of Report objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Report
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByFilters($dateFrom, $dateTo, $diseaseId, $symptoms): array
    {
        $dateFrom = $dateFrom->format('Y-m-d');
        $dateTo = $dateTo->format('Y-m-d');
        $conn = $this->getEntityManager()->getConnection();
        $params = [];
    
        // Base SQL query
        $sql = '
            SELECT COUNT(report.id) as countReports, region.nb_residents as nbResidents, region.name, region.latitude, region.longitude
            FROM region
            LEFT JOIN department ON department.region_id = region.id
                LEFT JOIN report ON report.department_id = department.id 
        AND report.date_report > :dateFrom AND report.date_report < :dateTo
        ';
    
        // Add disease filter in the ON clause to preserve LEFT JOIN behavior
        if ($diseaseId !== null) {
            $sql .= ' AND report.disease_id = :diseaseId';
            $params['diseaseId'] = $diseaseId;
        }
    
        // Add the GROUP BY clause
        $sql .= ' GROUP BY region.id';
    
  
        // Add date parameters
        $params['dateFrom'] = $dateFrom;
        $params['dateTo'] = $dateTo;

        //dump($sql);
        //dump($params);die;
        

        // Execute the query with the parameters
        $resultSet = $conn->executeQuery($sql, $params);
    
        // Return the result set as an associative array
        return $resultSet->fetchAllAssociative();
    }
}
