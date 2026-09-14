<?php

declare(strict_types=1);

namespace App\Localizing\Service;

use App\Localizing\Exception\LocaleNotFoundException;
use App\Localizing\ServiceInterface\LocaleRegistryServiceInterface;

/**
 * Normalizes configured locale codes and enforces runtime locale availability.
 */
final readonly class LocaleRegistryService implements LocaleRegistryServiceInterface
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
        $availableSet = array_fill_keys($available, true);
        if (!isset($availableSet[$localeCode])) {
            throw LocaleNotFoundException::notAvailable($localeCode, $available);
        }
    }
}
