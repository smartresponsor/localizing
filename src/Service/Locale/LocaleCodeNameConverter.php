<?php

declare(strict_types=1);

namespace App\Localizing\Service\Locale;

use App\Localizing\ServiceInterface\Locale\LocaleCodeNameConverterInterface;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Locales;

final class LocaleCodeNameConverter implements LocaleCodeNameConverterInterface
{
    public function convertNameToCode(string $name, ?string $displayLocaleCode = null): string
    {
        $names = Locales::getNames($displayLocaleCode ?? 'en');
        $code = array_search($name, $names, true);

        if (!is_string($code)) {
            throw new \InvalidArgumentException(sprintf('Cannot find locale code for display name "%s".', $name));
        }

        return $code;
    }

    public function convertCodeToName(string $code, ?string $displayLocaleCode = null): string
    {
        try {
            return Locales::getName($code, $displayLocaleCode ?? 'en');
        } catch (MissingResourceException $exception) {
            throw new \InvalidArgumentException(sprintf('Cannot find display name for locale code "%s".', $code), 0, $exception);
        }
    }
}
