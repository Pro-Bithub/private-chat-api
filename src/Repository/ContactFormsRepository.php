<?php

namespace App\Repository;

use App\Entity\ContactForms;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactForms>
 *
 * @method ContactForms|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactForms|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactForms[]    findAll()
 * @method ContactForms[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactFormsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactForms::class);
    }

    public function save(ContactForms $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactForms $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     /**
     * @return ContactForms[] Returns an array of ContactForms objects
     */
    public function loadfromsByAccount($account, array $formtype, $status): array
    {
        
        $qb = $this->createQueryBuilder('u');

        foreach ($formtype as $key => $role) {
           
            $qb->andWhere('u.form_type like :role' . $key)
            ->setParameter('role' . $key, '%' . $role . '%');
           dd($qb->getQuery()
           ->getResult());
        }
        
        $qb->andWhere('u.account = :account')
            ->andWhere('u.status = :status')
            ->setParameter('account', $account)
            ->setParameter('status', $status);
        return  $qb->getQuery()
                    ->getResult();
        
        
       
           
           
    }

  /**
     * @return ContactForms[] Returns an array of Plans objects
     */
    public function searchContactForms($id): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.id = :id OR u.friendly_name LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$id.'%')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
   
    

//    /**
//     * @return ContactForms[] Returns an array of ContactForms objects
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

//    public function findOneBySomeField($value): ?ContactForms
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
