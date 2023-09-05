<?php

namespace App\Repository;

use App\Entity\ContactCustomFields;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactCustomFields>
 *
 * @method ContactCustomFields|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactCustomFields|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactCustomFields[]    findAll()
 * @method ContactCustomFields[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactCustomFieldsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactCustomFields::class);
    }

    public function save(ContactCustomFields $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactCustomFields $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ContactCustomFields[] Returns an array of ContactCustomFields objects
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

//    public function findOneBySomeField($value): ?ContactCustomFields
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
