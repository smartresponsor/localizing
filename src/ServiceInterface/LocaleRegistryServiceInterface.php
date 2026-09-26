<?php

declare(strict_types=1);

namespace App\Localizing\ServiceInterface;

/**
 * Exposes the configured locale set and validates runtime locale availability.
 */
interface LocaleRegistryServiceInterface
{
    /**
     * Returns the normalized locale codes currently available to the application.
     *
     * @return list<string>
     */
    public function getAvailableLocaleCodes(): array;

    /**
     * Returns the locale code used as the application fallback default.
     */
    public function getDefaultLocaleCode(): string;

    /**
     * Rejects a locale code that is not present in the configured registry.
     */
    public function assertAvailable(string $localeCode): void;
}
