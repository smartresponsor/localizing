<?php

declare(strict_types=1);

namespace App\Localizing\Repository;

use App\Localizing\Entity\TranslationAuditFinding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TranslationAuditFinding>
 */
final class TranslationAuditFindingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TranslationAuditFinding::class);
    }
}
