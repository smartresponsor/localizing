<?php

declare(strict_types=1);

namespace App\Localizing\Tests\Service\Locale;

use App\Localizing\Resolver\LocaleFallbackResolver;
use App\Localizing\Service\LocaleRegistryService;
use PHPUnit\Framework\TestCase;

final class LocaleFallbackResolverTest extends TestCase
{
    public function testResolvesRegionalLocaleToLanguageAndDefaultFallback(): void
    {
        $resolver = new LocaleFallbackResolver(new LocaleRegistryService(['en', 'uk', 'uk-UA'], 'en'));

        self::assertSame(['uk-UA', 'uk', 'en'], $resolver->resolveFallbackChain('uk-UA'));
        self::assertSame(['uk', 'en'], $resolver->resolveFallbackChain('uk'));
        self::assertSame(['en'], $resolver->resolveFallbackChain('en'));
        self::assertSame(['en-US', 'en'], $resolver->resolveFallbackChain('en-US'));
    }

    public function testUsesSymfonyIcuParentLocaleChain(): void
    {
        $resolver = new LocaleFallbackResolver(new LocaleRegistryService(['en', 'es', 'es_419', 'es_AR'], 'en'));

        self::assertSame(['es_AR', 'es_419', 'es', 'en'], $resolver->resolveFallbackChain('es_AR'));
    }
}
