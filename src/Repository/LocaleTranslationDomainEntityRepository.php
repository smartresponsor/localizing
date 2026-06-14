<?php

declare(strict_types=1);

namespace App\Localizing\Repository;

use App\Localizing\Entity\LocaleTranslationDomainEntity;
use App\Localizing\RepositoryInterface\LocaleTranslationDomainEntityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocaleTranslationDomainEntity>
 */
final class LocaleTranslationDomainEntityRepository extends ServiceEntityRepository implements LocaleTranslationDomainEntityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocaleTranslationDomainEntity::class);
    }
}
