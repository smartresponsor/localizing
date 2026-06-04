<?php

declare(strict_types=1);

namespace App\Tests\Service\Locale;

use App\Service\Locale\LocaleFallbackResolver;
use App\Service\Locale\LocaleRegistry;
use PHPUnit\Framework\TestCase;

final class LocaleFallbackResolverTest extends TestCase
{
    public function testResolvesRegionalLocaleToLanguageAndDefaultFallback(): void
    {
        $resolver = new LocaleFallbackResolver(new LocaleRegistry(['en', 'uk', 'uk-UA'], 'en'));

        self::assertSame(['uk-UA', 'uk', 'en'], $resolver->resolveFallbackChain('uk-UA'));
    }
}
