<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Api;

use App\Localizing\ServiceInterface\LocaleCodeNameConverterServiceInterface;
use App\Localizing\ServiceInterface\LocaleRegistryServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Exposes the configured locale registry as a public runtime API response.
 */
final readonly class LocaleApiController
{
    public function __construct(
        private LocaleRegistryServiceInterface $localeRegistry,
        private LocaleCodeNameConverterServiceInterface $codeNameConverter,
    ) {
    }

    /**
     * Returns available locale codes with display names and the configured default marker.
     */
    public function __invoke(): JsonResponse
    {
        $default = $this->localeRegistry->getDefaultLocaleCode();
        $locales = [];

        foreach ($this->localeRegistry->getAvailableLocaleCodes() as $code) {
            $locales[] = [
                'code' => $code,
                'name' => $this->codeNameConverter->convertCodeToName($code),
                'default' => $code === $default,
            ];
        }

        return new JsonResponse(['locales' => $locales, 'default' => $default]);
    }
}
