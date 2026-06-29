<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Api;

use App\Localizing\Repository\LocaleTranslationKeyEntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class LocaleTranslationKeyApiController
{
    public function __construct(
        private LocaleTranslationKeyEntityRepository $keyRepository,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $criteria = [];
        $domain = $request->query->get('domain');
        if (is_string($domain) && '' !== trim($domain)) {
            $criteria['domainName'] = trim($domain);
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
