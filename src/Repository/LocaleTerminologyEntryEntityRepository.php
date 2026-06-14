<?php

declare(strict_types=1);

namespace App\Localizing\Repository;

use App\Localizing\Entity\LocaleTerminologyEntryEntity;
use App\Localizing\RepositoryInterface\LocaleTerminologyEntryEntityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocaleTerminologyEntryEntity>
 */
final class LocaleTerminologyEntryEntityRepository extends ServiceEntityRepository implements LocaleTerminologyEntryEntityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocaleTerminologyEntryEntity::class);
    }
}
