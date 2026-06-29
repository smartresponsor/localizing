<?php

declare(strict_types=1);

namespace App\Localizing\ServiceInterface\Locale;

interface LocaleCodeNameConverterInterface
{
    public function convertNameToCode(string $name, ?string $displayLocaleCode = null): string;

    public function convertCodeToName(string $code, ?string $displayLocaleCode = null): string;
}
