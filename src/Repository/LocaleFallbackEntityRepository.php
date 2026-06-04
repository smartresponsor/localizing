<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LocaleFallbackEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocaleFallbackEntity>
 */
final class LocaleFallbackEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocaleFallbackEntity::class);
    }
}
