<?php

declare(strict_types=1);

namespace App\Localizing\Entity;

use App\Localizing\Repository\LocaleTranslationDomainEntityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocaleTranslationDomainEntityRepository::class)]
#[ORM\Table(name: 'locale_translation_domain')]
#[ORM\UniqueConstraint(name: 'uniq_locale_translation_domain_name', columns: ['nameEntity'])]
class LocaleTranslationDomainEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private string $nameEntity;

    #[ORM\Column(type: 'string', length: 128)]
    private string $component;

    public function __construct(string $nameEntity, string $component)
    {
        $this->nameEntity = $nameEntity;
        $this->component = $component;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->nameEntity;
    }

    public function getComponent(): string
    {
        return $this->component;
    }
}
