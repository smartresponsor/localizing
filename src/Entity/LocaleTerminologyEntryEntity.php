<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LocaleTerminologyEntryEntityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocaleTerminologyEntryEntityRepository::class)]
#[ORM\Table(name: 'locale_terminology_entry')]
#[ORM\UniqueConstraint(name: 'uniq_locale_terminology_term', columns: ['source_term', 'locale_code'])]
class LocaleTerminologyEntryEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $sourceTerm;

    #[ORM\Column(type: 'string', length: 16)]
    private string $localeCode;

    #[ORM\Column(type: 'string', length: 255)]
    private string $approvedTerm;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note = null;

    public function __construct(string $sourceTerm, string $localeCode, string $approvedTerm, ?string $note = null)
    {
        $this->sourceTerm = $sourceTerm;
        $this->localeCode = $localeCode;
        $this->approvedTerm = $approvedTerm;
        $this->note = $note;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceTerm(): string
    {
        return $this->sourceTerm;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getApprovedTerm(): string
    {
        return $this->approvedTerm;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }
}
