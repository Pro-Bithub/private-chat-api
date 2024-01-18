<?php

namespace App\Repository;

use App\Entity\PlanTariffs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanTariffs>
 *
 * @method PlanTariffs|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanTariffs|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanTariffs[]    findAll()
 * @method PlanTariffs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanTariffsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanTariffs::class);
    }

    public function save(PlanTariffs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlanTariffs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


}
