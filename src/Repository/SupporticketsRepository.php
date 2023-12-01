<?php

namespace App\Repository;

use App\Entity\Supportickets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Supportickets>
 *
 * @method Supportickets|null find($id, $lockMode = null, $lockVersion = null)
 * @method Supportickets|null findOneBy(array $criteria, array $orderBy = null)
 * @method Supportickets[]    findAll()
 * @method Supportickets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupporticketsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Supportickets::class);
    }

    public function save(Supportickets $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Supportickets $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


}
