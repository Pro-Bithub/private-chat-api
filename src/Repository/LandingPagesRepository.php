<?php

namespace App\Repository;

use App\Entity\LandingPages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LandingPages>
 *
 * @method LandingPages|null find($id, $lockMode = null, $lockVersion = null)
 * @method LandingPages|null findOneBy(array $criteria, array $orderBy = null)
 * @method LandingPages[]    findAll()
 * @method LandingPages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LandingPagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LandingPages::class);
    }

    public function save(LandingPages $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LandingPages $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    
    /**
     * @return LandingPages[] Returns an array of Plans objects
     */
    public function searchLandingPages($id): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.id = :id  OR u.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$id.'%')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

     /**
     * @return LandingPages[] Returns an array of Plans objects
     */
    public function loadLandingPages(): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.date_start', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
//    /**
//     * @return LandingPages[] Returns an array of LandingPages objects
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

//    public function findOneBySomeField($value): ?LandingPages
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
