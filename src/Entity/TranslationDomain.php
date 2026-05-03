<?php

declare(strict_types=1);

namespace App\Localizing\Entity;

use App\Localizing\Repository\TranslationDomainRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TranslationDomainRepository::class)]
#[ORM\Table(name: 'locale_translation_domain')]
#[ORM\UniqueConstraint(name: 'uniq_locale_translation_domain_name', columns: ['name'])]
class TranslationDomain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private string $name;

    #[ORM\Column(type: 'string', length: 128)]
    private string $component;

    public function __construct(string $name, string $component)
    {
        $this->name = $name;
        $this->component = $component;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getComponent(): string
    {
        return $this->component;
    }
}
