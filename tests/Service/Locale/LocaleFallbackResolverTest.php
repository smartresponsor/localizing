<?php

declare(strict_types=1);

namespace App\Localizing\Tests\Service\Locale;

use App\Localizing\Service\Locale\LocaleFallbackResolver;
use App\Localizing\Service\Locale\LocaleRegistry;
use PHPUnit\Framework\TestCase;

final class LocaleFallbackResolverTest extends TestCase
{
    public function testResolvesRegionalLocaleToLanguageAndDefaultFallback(): void
    {
        $resolver = new LocaleFallbackResolver(new LocaleRegistry(['en', 'uk', 'uk-UA'], 'en'));

        self::assertSame(['uk-UA', 'uk', 'en'], $resolver->resolveFallbackChain('uk-UA'));
    }
}
