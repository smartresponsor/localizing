<?php

declare(strict_types=1);

namespace App\ServiceInterface\Locale;

interface LocaleFallbackResolverInterface
{
    /** @return list<string> */
    public function resolveFallbackChain(string $localeCode): array;
}
