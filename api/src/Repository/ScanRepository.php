<?php

namespace App\Repository;

use App\Entity\Scan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * @method Scan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Scan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Scan[]    findAll()
 * @method Scan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Scan::class);
    }

    public function filtered(ParamFetcher $paramFetcher): QueryBuilder
    {
        return $this->createQueryBuilder('scan');
    }
}
