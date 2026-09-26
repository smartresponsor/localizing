<?php

declare(strict_types=1);

namespace App\Localizing\Resolver;

use App\Localizing\ServiceInterface\LocaleRegistryServiceInterface;

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
        $chain = [$localeCode];
        $separatorPosition = strpos($localeCode, '-');
        if (false !== $separatorPosition) {
            $chain[] = strtolower(substr($localeCode, 0, $separatorPosition));
        }

        $chain[] = $this->localeRegistry->getDefaultLocaleCode();

        return array_values(array_unique($chain));
    }
}
