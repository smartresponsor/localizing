<?php

declare(strict_types=1);

namespace App\Localizing\Repository;

use App\Localizing\Entity\LocaleTranslationMessageEntity;
use App\Localizing\RepositoryInterface\LocaleTranslationMessageEntityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocaleTranslationMessageEntity>
 */
final class LocaleTranslationMessageEntityRepository extends ServiceEntityRepository implements LocaleTranslationMessageEntityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocaleTranslationMessageEntity::class);
    }
}
