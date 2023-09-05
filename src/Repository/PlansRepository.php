<?php

namespace App\Repository;

use App\Entity\Plans;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plans>
 *
 * @method Plans|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plans|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plans[]    findAll()
 * @method Plans[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlansRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plans::class);
    }

    public function save(Plans $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Plans $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    
     /**
     * @return Plans[] Returns an array of Plans objects
     */
    public function loadplansByAccount($account): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getResult();
    }

      /**
     * @return Plans[] Returns an array of Plans objects
     */
    public function searchPlan($id): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.id = :id  OR u.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$id.'%')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Plans[] Returns an array of Plans objects
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

//    public function findOneBySomeField($value): ?Plans
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
