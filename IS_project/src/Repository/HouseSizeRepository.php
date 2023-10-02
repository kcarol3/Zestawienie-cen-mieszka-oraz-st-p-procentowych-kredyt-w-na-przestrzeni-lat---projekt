<?php

namespace App\Repository;

use App\Entity\HouseSize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HouseSize>
 *
 * @method HouseSize|null find($id, $lockMode = null, $lockVersion = null)
 * @method HouseSize|null findOneBy(array $criteria, array $orderBy = null)
 * @method HouseSize[]    findAll()
 * @method HouseSize[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HouseSizeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HouseSize::class);
    }

    public function save(HouseSize $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HouseSize $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
