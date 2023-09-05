<?php

namespace App\Repository;

use App\Entity\PredefindTexts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PredefindTexts>
 *
 * @method PredefindTexts|null find($id, $lockMode = null, $lockVersion = null)
 * @method PredefindTexts|null findOneBy(array $criteria, array $orderBy = null)
 * @method PredefindTexts[]    findAll()
 * @method PredefindTexts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PredefindTextsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PredefindTexts::class);
    }

    public function save(PredefindTexts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PredefindTexts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    
    /**
     * @return PredefindTexts[] Returns an array of Plans objects
     */
    public function searchPredefindTexts($id): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.id = :id  OR u.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$id.'%')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
    /**
     * @return PredefindTexts[] Returns an array of Plans objects
     */
    public function findBySearch(string $searchTerm, int $offset, int $limit)
    {
        $qb = $this->createQueryBuilder('e');

        // Add search conditions to the query builder
        
        $qb->join('e.predefinedTextUsers', 'r')
        ->where('r.text = e.id')
        ->andwhere('e.id LIKE :searchTerm')
            ->orWhere('e.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$searchTerm.'%');

        // Set the offset and limit for pagination
        $qb->setFirstResult($offset)
            ->setMaxResults($limit);

        // Execute the query and return the results
        return $qb->getQuery()->getResult();
    }
    public function findDataBySearch(string $searchTerm)
{
    $qb = $this->createQueryBuilder('e');

    // Add search conditions to the query builder
    $qb->select('e, u.id as user_id')
        ->join('e.predefinedTextUsers', 'r')
        ->join('r.user', 'u')
        ->where('r.text = e.id')
        ->where('r.user = u.id')
        ->andWhere($qb->expr()->orX(
            $qb->expr()->like('e.id', ':searchTerm'),
            $qb->expr()->like('e.name', ':searchTerm')
        ))
        ->setParameters([
            'searchTerm' => '%' . $searchTerm . '%',
        ])
        ;

    // Execute the query and return the results
    return $qb->getQuery()->getResult();
}
    public function countBySearch(string $searchTerm)
    {
        $qb = $this->createQueryBuilder('e');

        // Add search conditions to the query builder
        $qb->select('COUNT(e.id)')
            ->where('e.id LIKE :searchTerm')
            ->orWhere('e.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$searchTerm.'%');

        // Execute the query and return the result count
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function countAll()
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e.id)');
        //dd($qb->getQuery()->getSQL());
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    
//    /**
//     * @return PredefindTexts[] Returns an array of PredefindTexts objects
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

//    public function findOneBySomeField($value): ?PredefindTexts
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
