<?php

namespace App\Repository;

use App\Entity\TwoFactorAuthCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TwoFactorAuthCode>
 *
 * @method TwoFactorAuthCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method TwoFactorAuthCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method TwoFactorAuthCode[]    findAll()
 * @method TwoFactorAuthCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwoFactorAuthCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TwoFactorAuthCode::class);
    }

    public function save(TwoFactorAuthCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TwoFactorAuthCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


}
