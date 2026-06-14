<?php

declare(strict_types=1);

namespace App\Localizing\Entity;

use App\Localizing\Repository\LocaleEntityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocaleEntityRepository::class)]
#[ORM\Table(name: 'locale_locale')]
#[ORM\UniqueConstraint(name: 'uniq_locale_code', columns: ['code'])]
class LocaleEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 16)]
    private string $code;

    #[ORM\Column(type: 'string', length: 128)]
    private string $nameEntity;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled = true;

    #[ORM\Column(type: 'integer')]
    private int $priority = 0;

    public function __construct(string $code, string $nameEntity, bool $enabled = true, int $priority = 0)
    {
        $this->code = $code;
        $this->nameEntity = $nameEntity;
        $this->enabled = $enabled;
        $this->priority = $priority;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->nameEntity;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
