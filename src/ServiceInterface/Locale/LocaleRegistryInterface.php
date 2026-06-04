<?php

declare(strict_types=1);

namespace App\ServiceInterface\Locale;

interface LocaleRegistryInterface
{
    /** @return list<string> */
    public function getAvailableLocaleCodes(): array;

    public function getDefaultLocaleCode(): string;

    public function assertAvailable(string $localeCode): void;
}
