<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\VesselPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VesselPosition>
 *
 * @method VesselPosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method VesselPosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method VesselPosition[]    findAll()
 * @method VesselPosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VesselPositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VesselPosition::class);
    }
}
