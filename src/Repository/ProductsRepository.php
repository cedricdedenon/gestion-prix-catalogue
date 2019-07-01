<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function findPriceEgalAt($rule_type, $rule_price)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.type = :type')
            ->andWhere('p.price = :val')
            ->setParameter('type', $rule_type)
            ->setParameter('val', $rule_price)
            ->getQuery()
            ->getResult()
        ;
    } 

    public function findPriceHigherThan($rule_type, $rule_price)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.type = :type')
            ->andWhere('p.price > :val')
            ->setParameter('type', $rule_type)
            ->setParameter('val', $rule_price)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPriceHigherOrEgalThan($rule_type, $rule_price)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.type = :type')
            ->andWhere('p.price >= :val')
            ->setParameter('type', $rule_type)
            ->setParameter('val', $rule_price)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPriceLowerThan($rule_type, $rule_price)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.type = :type')
            ->andWhere('p.price < :val')
            ->setParameter('type', $rule_type)
            ->setParameter('val', $rule_price)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPriceLowerOrEgalThan($rule_type, $rule_price)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.type = :type')
            ->andWhere('p.price <= :val')
            ->setParameter('type', $rule_type)  
            ->setParameter('val', $rule_price)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findProducts (){
        return $this->createQueryBuilder('p')
        ->andWhere('p.discounted_price IS NOT NULL')
        ->getQuery()
        ->getResult()
    ;
    }

    // /**
    //  * @return Products[] Returns an array of Products objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Products
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
