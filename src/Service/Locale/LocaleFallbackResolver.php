<?php

declare(strict_types=1);

namespace App\Service\Locale;

use App\ServiceInterface\Locale\LocaleFallbackResolverInterface;
use App\ServiceInterface\Locale\LocaleRegistryInterface;

final readonly class LocaleFallbackResolver implements LocaleFallbackResolverInterface
{
    public function __construct(private LocaleRegistryInterface $localeRegistry)
    {
    }

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
