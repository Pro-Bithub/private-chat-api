<?php

namespace App\Repository;

use App\Entity\TwoFactorAuthRequests;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TwoFactorAuthRequests>
 *
 * @method TwoFactorAuthRequests|null find($id, $lockMode = null, $lockVersion = null)
 * @method TwoFactorAuthRequests|null findOneBy(array $criteria, array $orderBy = null)
 * @method TwoFactorAuthRequests[]    findAll()
 * @method TwoFactorAuthRequests[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwoFactorAuthRequestsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TwoFactorAuthRequests::class);
    }

    public function save(TwoFactorAuthRequests $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TwoFactorAuthRequests $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


}
