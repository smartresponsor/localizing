<?php

declare(strict_types=1);

namespace App\Localizing\Repository;

use App\Localizing\Entity\LocaleTranslationKeyEntity;
use App\Localizing\RepositoryInterface\LocaleTranslationKeyEntityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocaleTranslationKeyEntity>
 */
final class LocaleTranslationKeyEntityRepository extends ServiceEntityRepository implements LocaleTranslationKeyEntityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocaleTranslationKeyEntity::class);
    }
}
