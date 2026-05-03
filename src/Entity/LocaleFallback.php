<?php

declare(strict_types=1);

namespace App\Localizing\Entity;

use App\Localizing\Repository\LocaleFallbackRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocaleFallbackRepository::class)]
#[ORM\Table(name: 'locale_fallback')]
#[ORM\UniqueConstraint(name: 'uniq_locale_fallback_chain', columns: ['locale_code', 'fallback_locale_code'])]
class LocaleFallback
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
}
