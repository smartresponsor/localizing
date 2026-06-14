<?php

declare(strict_types=1);

namespace App\Localizing\ValueObject;

/**
 * Canonical supported-locale catalog restored from the old monolith.
 *
 * This is intentionally not a Doctrine entity: Localizing keeps runtime locale
 * rows in LocaleEntity, while this catalog provides stable semantic constants
 * and default currency hints for seeders/configuration.
 */
final class SupportedLocale
{
    public const EN = 'en';
    public const EN_US = 'en_US';
    public const EN_GB = 'en_GB';
    public const EN_CA = 'en_CA';
    public const EN_AU = 'en_AU';
    public const UK = 'uk';
    public const UK_UA = 'uk_UA';
    public const RU = 'ru';

    /** @return list<string> */
    public static function codes(): array
    {
        return [
            self::EN,
            self::EN_US,
            self::EN_GB,
            self::EN_CA,
            self::EN_AU,
            self::UK,
            self::UK_UA,
            self::RU,
        ];
    }

    /** @return array<string, string> */
    public static function currencies(): array
    {
        return [
            self::EN => 'USD',
            self::EN_US => 'USD',
            self::EN_GB => 'GBP',
            self::EN_CA => 'CAD',
            self::EN_AU => 'AUD',
            self::UK => 'UAH',
            self::UK_UA => 'UAH',
            self::RU => 'USD',
        ];
    }
}
