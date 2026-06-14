<?php

declare(strict_types=1);

namespace App\Localizing\Repository;

use App\Localizing\Entity\LocaleFallbackEntity;
use App\Localizing\RepositoryInterface\LocaleFallbackEntityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocaleFallbackEntity>
 */
final class LocaleFallbackEntityRepository extends ServiceEntityRepository implements LocaleFallbackEntityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocaleFallbackEntity::class);
    }
}
