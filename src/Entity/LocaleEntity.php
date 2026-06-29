<?php

declare(strict_types=1);

namespace App\Localizing\Entity;

use App\Localizing\Repository\LocaleEntityRepository;
use App\Objecting\EntityTrait\Embeddable\ObjectAuditEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectVersionEmbeddableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocaleEntityRepository::class)]
#[ORM\Table(name: 'locale_locale')]
#[ORM\UniqueConstraint(name: 'uniq_locale_code', columns: ['code'])]
class LocaleEntity
{
    use ObjectAuditEmbeddableTrait;
    use ObjectVersionEmbeddableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 16)]
    private string $code;

    #[ORM\Column(type: 'string', length: 128)]
    private string $name;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled = true;

    #[ORM\Column(type: 'integer')]
    private int $priority = 0;

    public function __construct(string $code, string $name, bool $enabled = true, int $priority = 0)
    {
        $this->code = $code;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->priority = $priority;
        $this->initializeObjectAudit();
        $this->initializeObjectVersion();
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
        return $this->name;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function enable(): void
    {
        $this->enabled = true;
        $this->touchModified();
    }

    public function disable(): void
    {
        $this->enabled = false;
        $this->touchModified();
    }
}
