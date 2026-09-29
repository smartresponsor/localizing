<?php

declare(strict_types=1);

namespace App\Localizing\Resolver;

use App\Localizing\ServiceInterface\LocaleRegistryServiceInterface;
use Symfony\Component\Translation\LocaleFallbackProvider;

/**
 * Builds deterministic locale fallback chains from regional and default locale rules.
 */
final readonly class LocaleFallbackResolver implements LocaleFallbackResolverInterface
{
    public function __construct(private LocaleRegistryServiceInterface $localeRegistry)
    {
    }

    /**
     * Returns the requested locale, its language fallback, and the configured default without duplicates.
     */
    public function resolveFallbackChain(string $localeCode): array
    {
        $fallbackProvider = new LocaleFallbackProvider([$this->localeRegistry->getDefaultLocaleCode()]);

        return array_values([$localeCode, ...$fallbackProvider->computeFallbackLocales($localeCode)]);
    }
}
