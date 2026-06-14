<?php

declare(strict_types=1);

namespace App\Localizing\Repository;

use App\Localizing\Entity\LocaleTranslationAuditFindingEntity;
use App\Localizing\RepositoryInterface\LocaleTranslationAuditFindingEntityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocaleTranslationAuditFindingEntity>
 */
final class LocaleTranslationAuditFindingEntityRepository extends ServiceEntityRepository implements LocaleTranslationAuditFindingEntityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocaleTranslationAuditFindingEntity::class);
    }
}
