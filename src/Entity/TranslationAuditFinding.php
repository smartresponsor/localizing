<?php

declare(strict_types=1);

namespace App\Localizing\Entity;

use App\Localizing\Repository\TranslationAuditFindingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TranslationAuditFindingRepository::class)]
#[ORM\Table(name: 'locale_translation_audit_finding')]
class TranslationAuditFinding
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 32)]
    private string $severity;

    #[ORM\Column(type: 'string', length: 128)]
    private string $code;

    #[ORM\Column(type: 'string', length: 128)]
    private string $domainName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $keyName;

    #[ORM\Column(type: 'string', length: 16, nullable: true)]
    private ?string $localeCode;

    #[ORM\Column(type: 'text')]
    private string $message;

    public function __construct(string $severity, string $code, string $domainName, string $keyName, ?string $localeCode, string $message)
    {
        $this->severity = $severity;
        $this->code = $code;
        $this->domainName = $domainName;
        $this->keyName = $keyName;
        $this->localeCode = $localeCode;
        $this->message = $message;
    }
}
