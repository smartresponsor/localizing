<?php

declare(strict_types=1);

namespace App\Localizing\Entity;

use App\Localizing\Repository\LocaleTranslationMessageEntityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocaleTranslationMessageEntityRepository::class)]
#[ORM\Table(name: 'locale_translation_message')]
#[ORM\UniqueConstraint(name: 'uniq_locale_translation_message', columns: ['locale_code', 'domain_name', 'key_name'])]
class LocaleTranslationMessageEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 16)]
    private string $localeCode;

    #[ORM\Column(type: 'string', length: 128)]
    private string $domainName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $keyName;

    #[ORM\Column(type: 'text')]
    private string $message;

    public function __construct(string $localeCode = '', string $domainName = '', string $keyName = '', string $message = '')
    {
        $this->localeCode = $localeCode;
        $this->domainName = $domainName;
        $this->keyName = $keyName;
        $this->message = $message;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getDomainName(): string
    {
        return $this->domainName;
    }

    public function getKeyName(): string
    {
        return $this->keyName;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setLocaleCode(string $localeCode): void
    {
        $this->localeCode = $localeCode;
    }

    public function setDomainName(string $domainName): void
    {
        $this->domainName = $domainName;
    }

    public function setKeyName(string $keyName): void
    {
        $this->keyName = $keyName;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
