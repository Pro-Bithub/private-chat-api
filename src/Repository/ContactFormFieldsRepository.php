<?php

namespace App\Repository;

use App\Entity\ContactFormFields;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactFormFields>
 *
 * @method ContactFormFields|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactFormFields|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactFormFields[]    findAll()
 * @method ContactFormFields[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactFormFieldsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactFormFields::class);
    }

    public function save(ContactFormFields $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactFormFields $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

        /**
     * @return ContactFormFields[] Returns an array of PredefinedTextUsers objects
     */
    public function loadfromByFieldData1($form,$field): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.form = :form')
            ->Andwhere('u.field = :field')
            ->Andwhere('u.status = 1')
            ->setParameter('form', $form)
            ->setParameter('field', $field)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return ContactFormFields[] Returns an array of ContactFormFields objects
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

//    public function findOneBySomeField($value): ?ContactFormFields
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
