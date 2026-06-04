<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LocaleTranslationKeyEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocaleTranslationKeyEntity>
 */
final class LocaleTranslationKeyEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocaleTranslationKeyEntity::class);
    }
}
