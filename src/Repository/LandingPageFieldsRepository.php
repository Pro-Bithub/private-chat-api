<?php

namespace App\Repository;

use App\Entity\LandingPageFields;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LandingPageFields>
 *
 * @method LandingPageFields|null find($id, $lockMode = null, $lockVersion = null)
 * @method LandingPageFields|null findOneBy(array $criteria, array $orderBy = null)
 * @method LandingPageFields[]    findAll()
 * @method LandingPageFields[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LandingPageFieldsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LandingPageFields::class);
    }

    public function save(LandingPageFields $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LandingPageFields $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

        /**
     * @return LandingPageFields[] Returns an array of PredefinedTextUsers objects
     */
    public function loadpageByFieldData1($page,$field): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.page = :page')
            ->Andwhere('u.field = :field')
            ->Andwhere('u.status = 1')
            ->setParameter('page', $page)
            ->setParameter('field', $field)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return LandingPageFields[] Returns an array of LandingPageFields objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LandingPageFields
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
