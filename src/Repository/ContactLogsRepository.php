<?php

namespace App\Repository;

use App\Entity\ContactLogs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactLogs>
 *
 * @method ContactLogs|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactLogs|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactLogs[]    findAll()
 * @method ContactLogs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactLogsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactLogs::class);
    }

    public function save(ContactLogs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactLogs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

  



}
