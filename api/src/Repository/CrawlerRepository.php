<?php

namespace App\Repository;

use App\Entity\Crawler;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Crawler|null find($id, $lockMode = null, $lockVersion = null)
 * @method Crawler|null findOneBy(array $criteria, array $orderBy = null)
 * @method Crawler[]    findAll()
 * @method Crawler[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CrawlerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Crawler::class);
    }
}