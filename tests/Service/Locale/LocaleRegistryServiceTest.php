<?php

declare(strict_types=1);

namespace App\Localizing\Tests\Service\Locale;

use App\Localizing\Exception\LocaleNotFoundException;
use App\Localizing\Service\LocaleRegistryService;
use PHPUnit\Framework\TestCase;

final class LocaleRegistryServiceTest extends TestCase
{
    public function testNormalizesConfiguredLocalesAndPreservesOrder(): void
    {
        $registry = new LocaleRegistryService([' en ', 'uk', '', 'en', 'fr'], 'en');
        self::assertSame(['en', 'uk', 'fr'], $registry->getAvailableLocaleCodes());
        self::assertSame('en', $registry->getDefaultLocaleCode());
        $registry->assertAvailable('en');
        $this->addToAssertionCount(1);
        $registry->assertAvailable('uk');
        $this->addToAssertionCount(1);
    }

    public function testFallsBackToDefaultWhenConfigurationIsEmpty(): void
    {
        $registry = new LocaleRegistryService(['', '   '], 'de');
        self::assertSame(['de'], $registry->getAvailableLocaleCodes());

        $defaultRegistry = new LocaleRegistryService(['en']);
        self::assertSame('en', $defaultRegistry->getDefaultLocaleCode());
        self::assertSame(['en'], $defaultRegistry->getAvailableLocaleCodes());
    }

    public function testRejectsUnavailableLocale(): void
    {
        $registry = new LocaleRegistryService(['en', 'uk'], 'en');
        $this->expectException(LocaleNotFoundException::class);
        $registry->assertAvailable('pl');
    }
}
