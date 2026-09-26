<?php

declare(strict_types=1);

namespace App\Localizing\Entity;

use App\Localizing\Repository\LocaleTerminologyEntryEntityRepository;
use App\Objecting\EntityTrait\Embeddable\ObjectAuditEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectVersionEmbeddableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocaleTerminologyEntryEntityRepository::class)]
#[ORM\Table(name: 'locale_terminology_entry')]
#[ORM\UniqueConstraint(name: 'uniq_locale_terminology_term', columns: ['source_term', 'locale_code'])]
/**
 * Persists an approved localized term and optional guidance for one locale.
 */
class LocaleTerminologyEntryEntity
{
    use ObjectAuditEmbeddableTrait;
    use ObjectVersionEmbeddableTrait;

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
        $this->initializeObjectAudit();
        $this->initializeObjectVersion();
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

    public function update(string $approvedTerm, ?string $note): void
    {
        $this->approvedTerm = $approvedTerm;
        $this->note = $note;
        $this->touchModified();
    }
}
