<?php

namespace App\Repository;

use App\Entity\Contacts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contacts>
 *
 * @method Contacts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contacts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contacts[]    findAll()
 * @method Contacts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contacts::class);
    }

    public function save(Contacts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contacts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function loadContactByEmail($email,$account)
    {
        return $this->createQueryBuilder('u')
        ->where('u.email = :email')   
        ->andWhere('u.accountId = :account')          // Filter users by email
        ->setParameter('email', $email)  
        ->setParameter('account', $account)         // Bind the parameter :email
        ->orderBy('u.id', 'DESC')                // Order by user id in descending order
        ->setMaxResults(1)                       // Limit the result to one record
        ->getQuery()                             // Get the query
        ->getOneOrNullResult(); 
    }

    public function loadContactBsourceAndsourceType($source_id,$source_type)
    {
        return $this->createQueryBuilder('c')
            ->where('c.source_id = :source_id and c.source_type = :source_type  ')
            ->setParameter('source_id', $source_id)
            ->setParameter('source_type', $source_type)
            ->getQuery()
            ->getResult();
    }


         /**
     * @return Contacts[] Returns an array of Plans objects
     */
    public function searchContacts($id): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.id = :id  OR u.firstname LIKE :searchTerm OR u.lastname LIKE :searchTerm OR u.email LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$id.'%')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Contacts[] Returns an array of Contacts objects
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

//    public function findOneBySomeField($value): ?Contacts
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
