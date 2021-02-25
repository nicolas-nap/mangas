<?php

namespace App\Repository;

use App\Entity\Mangas;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Request\ParamFetcher;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Mangas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mangas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mangas[]    findAll()
 * @method Mangas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MangasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mangas::class);
    }

    public function filtered(ParamFetcher $paramFetcher): QueryBuilder
    {
        return $this->createQueryBuilder('scan');
    }
}
