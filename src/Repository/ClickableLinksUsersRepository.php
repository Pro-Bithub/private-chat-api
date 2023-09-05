<?php

namespace App\Repository;

use App\Entity\ClickableLinksUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClickableLinksUsers>
 *
 * @method ClickableLinksUsers|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClickableLinksUsers|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClickableLinksUsers[]    findAll()
 * @method ClickableLinksUsers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClickableLinksUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClickableLinksUsers::class);
    }

    public function save(ClickableLinksUsers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ClickableLinksUsers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * @return ClickableLinksUsers[] Returns an array of ClickableLinksUsers objects
     */
    public function loadlinksByUser($user): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return PredefinedTextUsers[] Returns an array of PredefinedTextUsers objects
     */
    public function loadlinkByUserData1($link,$user): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.link = :link')
            ->Andwhere('u.user = :user')
            ->Andwhere('u.status = 1')
            ->setParameter('link', $link)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return ClickableLinksUsers[] Returns an array of ClickableLinksUsers objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ClickableLinksUsers
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
