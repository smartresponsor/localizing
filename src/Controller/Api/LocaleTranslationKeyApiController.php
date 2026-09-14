<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Api;

use App\Localizing\Repository\LocaleTranslationKeyEntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Publishes translation key metadata for runtime and tooling consumers.
 */
final readonly class LocaleTranslationKeyApiController
{
    public function __construct(
        private LocaleTranslationKeyEntityRepository $keyRepository,
    ) {
    }

    /**
     * Returns translation keys filtered by an optional domain query parameter.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $criteria = [];
        $domain = trim($request->query->getString('domain'));
        if ('' !== $domain) {
            $criteria['domainName'] = $domain;
        }

        $keys = array_map(
            static fn ($key) => [
                'domain' => $key->getDomainName(),
                'key' => $key->getKeyName(),
                'component' => $key->getComponent(),
            ],
            $this->keyRepository->findBy($criteria, ['domainName' => 'ASC', 'keyName' => 'ASC']),
        );

        return new JsonResponse(['keys' => $keys, 'total' => count($keys)]);
    }
}
