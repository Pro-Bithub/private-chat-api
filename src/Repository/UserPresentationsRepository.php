<?php

namespace App\Repository;

use App\Entity\UserPresentations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPresentations>
 *
 * @method UserPresentations|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPresentations|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPresentations[]    findAll()
 * @method UserPresentations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPresentationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPresentations::class);
    }

    public function save(UserPresentations $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserPresentations $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function loadActiveUserPresentationByuser($id)
    {
   
            
            return $this->createQueryBuilder('p')
    ->where('p.user = :user_id')
    ->andWhere('p.status = :status')
    ->setParameter('user_id', $id)
    ->setParameter('status', 1)
    ->orderBy('p.id', 'DESC') // Assuming there's a 'createdAt' field indicating the creation timestamp
    ->setMaxResults(1) // Limit the result to only one
    ->getQuery()
    ->getOneOrNullResult();

    }


//    /**
//     * @return UserPresentations[] Returns an array of UserPresentations objects
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

//    public function findOneBySomeField($value): ?UserPresentations
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
