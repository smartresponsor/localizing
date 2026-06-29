<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Api;

use App\Localizing\Repository\LocaleTranslationDomainEntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class LocaleTranslationDomainApiController
{
    public function __construct(
        private LocaleTranslationDomainEntityRepository $domainRepository,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $domains = array_map(
            static fn ($domain) => [
                'name' => $domain->getName(),
                'component' => $domain->getComponent(),
            ],
            $this->domainRepository->findBy([], ['name' => 'ASC']),
        );

        return new JsonResponse(['domains' => $domains, 'total' => count($domains)]);
    }
}
