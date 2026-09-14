<?php

declare(strict_types=1);

namespace App\Localizing\Tests\Contract;

use App\Localizing\Exception\LocaleNotFoundException;
use App\Localizing\ValueObject\LocaleSupportedCatalog;
use PHPUnit\Framework\TestCase;

final class LocaleFrameworkFreeContractTest extends TestCase
{
    public function testSupportedCatalogExposesStableCodesAndCurrencies(): void
    {
        self::assertSame(
            ['en', 'en_US', 'en_GB', 'en_CA', 'en_AU', 'uk', 'uk_UA', 'ru'],
            LocaleSupportedCatalog::codes(),
        );
        self::assertSame('USD', LocaleSupportedCatalog::currencies()[LocaleSupportedCatalog::EN]);
        self::assertSame('UAH', LocaleSupportedCatalog::currencies()[LocaleSupportedCatalog::UK]);
        self::assertSame('GBP', LocaleSupportedCatalog::currencies()[LocaleSupportedCatalog::EN_GB]);
    }

    public function testNotFoundExceptionFactoriesPreserveLocaleContext(): void
    {
        self::assertSame(
            'Locale "pl" cannot be found.',
            LocaleNotFoundException::notFound('pl')->getMessage(),
        );
        self::assertSame(
            'Locale "pl" is not available. Available locales: "en", "uk".',
            LocaleNotFoundException::notAvailable('pl', ['en', 'uk'])->getMessage(),
        );
        self::assertSame(
            'Locale "pl" is not available. Available locales: "en".',
            LocaleNotFoundException::notAvailable('pl', ['en'])->getMessage(),
        );
        self::assertSame(
            'Locale "pl" is not available. Available locales: "".',
            LocaleNotFoundException::notAvailable('pl', [])->getMessage(),
        );
    }
}
