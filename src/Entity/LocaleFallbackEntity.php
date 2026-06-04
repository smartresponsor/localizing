<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LocaleFallbackEntityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocaleFallbackEntityRepository::class)]
#[ORM\Table(name: 'locale_fallback')]
#[ORM\UniqueConstraint(name: 'uniq_locale_fallback_chain', columns: ['locale_code', 'fallback_locale_code'])]
class LocaleFallbackEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 16)]
    private string $localeCode;

    #[ORM\Column(type: 'string', length: 16)]
    private string $fallbackLocaleCode;

    #[ORM\Column(type: 'integer')]
    private int $position;

    public function __construct(string $localeCode, string $fallbackLocaleCode, int $position)
    {
        $this->localeCode = $localeCode;
        $this->fallbackLocaleCode = $fallbackLocaleCode;
        $this->position = $position;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getFallbackLocaleCode(): string
    {
        return $this->fallbackLocaleCode;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
