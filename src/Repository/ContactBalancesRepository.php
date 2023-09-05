<?php

namespace App\Repository;

use App\Entity\ContactBalances;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactBalances>
 *
 * @method ContactBalances|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactBalances|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactBalances[]    findAll()
 * @method ContactBalances[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactBalancesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactBalances::class);
    }

    public function save(ContactBalances $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactBalances $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     /**
     * @return ContactBalances[] Returns an array of ContactBalances objects
     */
    public function loadbalancesByContact($contact): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.contact = :contact')
            ->setParameter('contact', $contact)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return ContactBalances[] Returns an array of ContactBalances objects
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

//    public function findOneBySomeField($value): ?ContactBalances
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
