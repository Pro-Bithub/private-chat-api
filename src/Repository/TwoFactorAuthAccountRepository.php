<?php

namespace App\Repository;

use App\Entity\TwoFactorAuthAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TwoFactorAuthAccount>
 *
 * @method TwoFactorAuthAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method TwoFactorAuthAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method TwoFactorAuthAccount[]    findAll()
 * @method TwoFactorAuthAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwoFactorAuthAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TwoFactorAuthAccount::class);
    }

    public function save(TwoFactorAuthAccount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TwoFactorAuthAccount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


}
