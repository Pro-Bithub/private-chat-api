<?php

namespace App\Repository;

use App\Entity\Profiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Profiles>
 *
 * @method Profiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profiles[]    findAll()
 * @method Profiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profiles::class);
    }

    public function save(Profiles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Profiles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function loadProfileByEmail($login)
    {
        return $this->createQueryBuilder('u')
            ->where('u.login = :login')
            ->setParameter('login', $login)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByContact($id)
    {
        return $this->createQueryBuilder('u')
            ->where('u.u_id = :id')
            ->andWhere('u.u_type = :type')
            ->setParameter('id', $id)
            ->setParameter('type', 2) 
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findProfileByIduser($id)
    {
        return $this->createQueryBuilder('p')
            ->where('p.u_id = :id')
            ->andWhere('p.u_type = :type')
            ->setParameter('id', $id)
            ->setParameter('type', 1) 
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findProfileById($id)
    {
        return $this->createQueryBuilder('u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findContactProfileById($id)
    {
        return $this->createQueryBuilder('u')
            ->where('u.u_id = :id')
            ->Andwhere('u.u_type = 2')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findContactProfileByemail($login,$account)
    {
        return $this->createQueryBuilder('u')
            ->where('u.login = :login')
            ->Andwhere('u.u_type = 2')
            ->Andwhere('u.accountId = :account')
            ->setParameter('login', $login)
            ->setParameter('account', $account)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return Profiles[] Returns an array of Profiles objects
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

//    public function findOneBySomeField($value): ?Profiles
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
