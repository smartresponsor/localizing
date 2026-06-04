<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LocaleTranslationDomainEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocaleTranslationDomainEntity>
 */
final class LocaleTranslationDomainEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocaleTranslationDomainEntity::class);
    }
}
