<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    public function findByDatesNote($date_debut, $date_fin, $note_min, $note_max)
    {
        return $this->createQueryBuilder('l')
            ->where('l.date_de_parution BETWEEN :date_debut AND :date_fin')
            ->andWhere('l.note BETWEEN :note_min AND :note_max')
            ->setParameter('date_debut', $date_debut)
            ->setParameter('date_fin', $date_fin)
            ->setParameter('note_min', $note_min == '' ? 0 : $note_min)
            ->setParameter('note_max', $note_max == '' ? 20 : $note_max)
            ->getQuery()
            ->getResult();
    }

    public function findByLikeTitre($titre)
    {
        return $this->createQueryBuilder('l')
            ->where('l.titre LIKE :titre')
            ->setParameter('titre', '%' . $titre . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByParite()
    {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.auteurs', 'a')
            ->leftJoin('App\Entity\Auteur', 'men', 'WITH', 'a.id = men.id AND men.sexe = :m')
            ->setParameter('m', 'M')
            ->leftJoin('App\Entity\Auteur', 'women', 'WITH', 'a.id = women.id AND women.sexe = :f')
            ->setParameter('f', 'F')
            ->groupBy('l.id')
            ->having('count(men.id) = count(women.id)')
            ->getQuery()
            ->getResult();
    }
    public function findByDifferentNationalite()
    {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.auteurs', 'a')
            ->groupBy('l.id')
            ->having('count(a.id) = count(DISTINCT a.nationalite)')
            ->getQuery()
            ->getResult();
    }
}
