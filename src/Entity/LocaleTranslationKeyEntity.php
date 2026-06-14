<?php

declare(strict_types=1);

namespace App\Localizing\Entity;

use App\Localizing\Repository\LocaleTranslationKeyEntityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocaleTranslationKeyEntityRepository::class)]
#[ORM\Table(name: 'locale_translation_key')]
#[ORM\UniqueConstraint(name: 'uniq_locale_translation_key_domain_name', columns: ['domain_name', 'key_name'])]
class LocaleTranslationKeyEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private string $domainName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $keyName;

    #[ORM\Column(type: 'string', length: 128)]
    private string $component;

    public function __construct(string $domainName, string $keyName, string $component)
    {
        $this->domainName = $domainName;
        $this->keyName = $keyName;
        $this->component = $component;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomainName(): string
    {
        return $this->domainName;
    }

    public function getKeyName(): string
    {
        return $this->keyName;
    }

    public function getComponent(): string
    {
        return $this->component;
    }
}
