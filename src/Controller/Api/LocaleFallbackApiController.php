<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Api;

use App\Localizing\Resolver\LocaleFallbackResolverInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Exposes deterministic locale fallback chains through the public API boundary.
 */
final readonly class LocaleFallbackApiController
{
    public function __construct(
        private LocaleFallbackResolverInterface $fallbackResolver,
    ) {
    }

    /**
     * Returns the resolved fallback chain for the requested locale identifier.
     */
    public function __invoke(string $slug): JsonResponse
    {
        return new JsonResponse([
            'locale' => $slug,
            'fallback_chain' => $this->fallbackResolver->resolveFallbackChain($slug),
        ]);
    }
}
