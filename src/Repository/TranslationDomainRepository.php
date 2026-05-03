<?php

declare(strict_types=1);

namespace App\Localizing\Repository;

use App\Localizing\Entity\TranslationDomain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TranslationDomain>
 */
final class TranslationDomainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TranslationDomain::class);
    }
}
