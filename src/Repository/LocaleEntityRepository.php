<?php

declare(strict_types=1);

namespace App\Localizing\Repository;

use App\Localizing\Entity\LocaleEntity;
use App\Localizing\RepositoryInterface\LocaleEntityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocaleEntity>
 */
final class LocaleEntityRepository extends ServiceEntityRepository implements LocaleEntityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocaleEntity::class);
    }
}
