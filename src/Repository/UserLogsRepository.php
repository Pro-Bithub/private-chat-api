<?php

namespace App\Repository;

use App\Entity\UserLogs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserLogs>
 *
 * @method UserLogs|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLogs|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLogs[]    findAll()
 * @method UserLogs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLogsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLogs::class);
    }

    public function save(UserLogs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserLogs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     /**
     * @return UserLogs[] Returns an array of UserLogs objects
     */
    public function loadlogsByUser($user): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.user_id = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
            ;
            
    }

     /**
     * @return UserLogs[] Returns an array of UserLogs objects
     */
    public function loadlogsByAccount($user): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.user_id != :user')
            ->andwhere('u.source in (2,3)')
            ->setParameter('user', $user)
            ->orderBy('u.log_date', 'DESC')
            ->getQuery()
            ->getResult();
    }

   
//    /**
//     * @return UserLogs[] Returns an array of UserLogs objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserLogs
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
