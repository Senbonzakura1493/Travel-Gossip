<?php

namespace App\Repository;

use App\Entity\CriticalArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CriticalArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method CriticalArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method CriticalArticle[]    findAll()
 * @method CriticalArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CriticalArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CriticalArticle::class);
    }

    // /**
    //  * @return CriticalArticle[] Returns an array of CriticalArticle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CriticalArticle
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
