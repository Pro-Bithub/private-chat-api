<?php

namespace App\Repository;

use App\Entity\PredefinedTextUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PredefinedTextUsers>
 *
 * @method PredefinedTextUsers|null find($id, $lockMode = null, $lockVersion = null)
 * @method PredefinedTextUsers|null findOneBy(array $criteria, array $orderBy = null)
 * @method PredefinedTextUsers[]    findAll()
 * @method PredefinedTextUsers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PredefinedTextUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PredefinedTextUsers::class);
    }

    public function save(PredefinedTextUsers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PredefinedTextUsers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     /**
     * @return PredefinedTextUsers[] Returns an array of PredefinedTextUsers objects
     */
    public function loadpredefinedtextByUser($user): array
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
    public function loadpredefinedtextByUserData($text): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.text = :text')
            ->setParameter('text', $text)
            ->getQuery()
            ->getResult();
    }

     /**
     * @return PredefinedTextUsers[] Returns an array of PredefinedTextUsers objects
     */
    public function loadpredefinedtextByUserData1($text,$user): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.text = :text')
            ->Andwhere('u.user = :user')
            ->Andwhere('u.status = 1')
            ->setParameter('text', $text)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return PredefinedTextUsers[] Returns an array of PredefinedTextUsers objects
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

//    public function findOneBySomeField($value): ?PredefinedTextUsers
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
