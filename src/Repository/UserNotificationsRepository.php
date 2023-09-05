<?php

namespace App\Repository;

use App\Entity\UserNotifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserNotifications>
 *
 * @method UserNotifications|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserNotifications|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserNotifications[]    findAll()
 * @method UserNotifications[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserNotificationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserNotifications::class);
    }

    public function save(UserNotifications $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserNotifications $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return UserNotifications[] 
     */
    public function loadusernotificationsbyUserId($user): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return UserNotifications[] Returns an array of UserNotifications objects
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

//    public function findOneBySomeField($value): ?UserNotifications
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
