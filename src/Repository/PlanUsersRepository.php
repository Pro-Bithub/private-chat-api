<?php

namespace App\Repository;

use App\Entity\PlanUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanUsers>
 *
 * @method PlanUsers|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanUsers|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanUsers[]    findAll()
 * @method PlanUsers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanUsers::class);
    }

    public function save(PlanUsers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlanUsers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     /**
     * @return PlanUsers[] Returns an array of PlanUsers objects
     */
    public function loadplansByUser($user): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return PlanUsers[] Returns an array of PredefinedTextUsers objects
     */
    public function loadPlanByUserData($plan,$user): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.plan = :plan')
            ->Andwhere('u.user = :user')
            ->Andwhere('u.status = 1')
            ->setParameter('plan', $plan)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function loaduserByPlansID($plan): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.plan = :plan')
            ->setParameter('plan', $plan)
            ->getQuery()
            ->getResult();
    }



//    /**
//     * @return PlanUsers[] Returns an array of PlanUsers objects
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

//    public function findOneBySomeField($value): ?PlanUsers
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
