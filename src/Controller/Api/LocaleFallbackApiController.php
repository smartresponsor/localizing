<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Api;

use App\Localizing\ServiceInterface\Locale\LocaleFallbackResolverInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class LocaleFallbackApiController
{
    public function __construct(
        private LocaleFallbackResolverInterface $fallbackResolver,
    ) {
    }

    public function __invoke(string $slug): JsonResponse
    {
        return new JsonResponse([
            'locale' => $slug,
            'fallback_chain' => $this->fallbackResolver->resolveFallbackChain($slug),
        ]);
    }
}
