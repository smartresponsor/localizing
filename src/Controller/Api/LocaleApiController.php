<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Api;

use App\Localizing\ServiceInterface\Locale\LocaleCodeNameConverterInterface;
use App\Localizing\ServiceInterface\Locale\LocaleRegistryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class LocaleApiController
{
    public function __construct(
        private LocaleRegistryInterface $localeRegistry,
        private LocaleCodeNameConverterInterface $codeNameConverter,
    ) {
    }

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
