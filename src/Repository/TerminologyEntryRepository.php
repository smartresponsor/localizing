<?php

declare(strict_types=1);

namespace App\Localizing\Repository;

use App\Localizing\Entity\TerminologyEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TerminologyEntry>
 */
final class TerminologyEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TerminologyEntry::class);
    }
}
