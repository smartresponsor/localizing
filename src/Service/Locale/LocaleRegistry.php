<?php

declare(strict_types=1);

namespace App\Service\Locale;

use App\Exception\LocaleNotFoundException;
use App\ServiceInterface\Locale\LocaleRegistryInterface;

final readonly class LocaleRegistry implements LocaleRegistryInterface
{
    /** @param list<string> $configuredLocales */
    public function __construct(private array $configuredLocales, private string $defaultLocaleCode = 'en')
    {
    }

    public function getAvailableLocaleCodes(): array
    {
        $locales = array_values(array_unique(array_filter(array_map('trim', $this->configuredLocales))));

        return [] === $locales ? [$this->defaultLocaleCode] : $locales;
    }

    public function getDefaultLocaleCode(): string
    {
        return $this->defaultLocaleCode;
    }

    public function assertAvailable(string $localeCode): void
    {
        $available = $this->getAvailableLocaleCodes();
        if (!in_array($localeCode, $available, true)) {
            throw LocaleNotFoundException::notAvailable($localeCode, $available);
        }
    }
}
