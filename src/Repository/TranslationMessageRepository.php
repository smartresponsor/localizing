<?php

declare(strict_types=1);

namespace App\Localizing\Repository;

use App\Localizing\Entity\TranslationMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TranslationMessage>
 */
final class TranslationMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TranslationMessage::class);
    }
}
