<?php

declare(strict_types=1);

namespace App\Localizing\Resolver;

interface LocaleFallbackResolverInterface
{
    /** @return list<string> */
    public function resolveFallbackChain(string $localeCode): array;
}
