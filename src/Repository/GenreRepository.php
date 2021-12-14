<?php

namespace App\Repository;

use App\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Genre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genre[]    findAll()
 * @method Genre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genre::class);
    }

    public function findByGenreId($id)
    {
        return $this->createQueryBuilder('g')
            ->innerJoin('g.livres', 'lg')
            ->innerJoin('App\Entity\Livre', 'livre', 'WITH', 'lg.id = livre.id')
            ->andWhere('g.id = :id')
            ->setParameter('id', $id)
            ->select('SUM(livre.nbpages) AS total_pages', 'SUM(livre.nbpages)/COUNT(DISTINCT lg.id) AS moyen_pages')
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Genre
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
