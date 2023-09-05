<?php

namespace App\Repository;

use App\Entity\PlanDiscountUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanDiscountUsers>
 *
 * @method PlanDiscountUsers|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanDiscountUsers|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanDiscountUsers[]    findAll()
 * @method PlanDiscountUsers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanDiscountUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanDiscountUsers::class);
    }

    public function save(PlanDiscountUsers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlanDiscountUsers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return PlanDiscountUsers[] Returns an array of PredefinedTextUsers objects
     */
    public function loadPlanDiscountByUserData($discount,$user): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.discount = :discount')
            ->Andwhere('u.user = :user')
            ->Andwhere('u.status = 1')
            ->setParameter('discount', $discount)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return PlanDiscountUsers[] Returns an array of PlanDiscountUsers objects
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

//    public function findOneBySomeField($value): ?PlanDiscountUsers
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
