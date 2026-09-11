<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Admin;

use App\Localizing\Entity\LocaleTranslationKeyEntity;
use App\Localizing\Repository\LocaleTranslationKeyEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class LocaleTranslationKeyAdminController
{
    public function __construct(
        private LocaleTranslationKeyEntityRepository $keyRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function list(Request $request): JsonResponse
    {
        $criteria = [];
        $domain = $request->query->get('domain');
        if (is_string($domain) && '' !== trim($domain)) {
            $criteria['domainName'] = trim($domain);
        }

        $keys = array_map(
            static fn ($key) => [
                'id' => $key->getId(),
                'domain' => $key->getDomainName(),
                'key' => $key->getKeyName(),
                'component' => $key->getComponent(),
            ],
            $this->keyRepository->findBy($criteria, ['domainName' => 'ASC', 'keyName' => 'ASC']),
        );

        return new JsonResponse(['keys' => $keys, 'total' => count($keys)]);
    }

    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            $data = [];
        }
        $domainValue = $data['domain'] ?? null;
        $keyValue = $data['key'] ?? null;
        $componentValue = $data['component'] ?? null;
        $domain = is_scalar($domainValue) ? trim((string) $domainValue) : '';
        $key = is_scalar($keyValue) ? trim((string) $keyValue) : '';
        $component = is_scalar($componentValue) ? trim((string) $componentValue) : '';

        if ('' === $domain || '' === $key || '' === $component) {
            return new JsonResponse(['error' => 'domain, key and component are required'], 400);
        }

        $existing = $this->keyRepository->findOneBy(['domainName' => $domain, 'keyName' => $key]);
        if (null !== $existing) {
            return new JsonResponse(['error' => sprintf('Key "%s" in domain "%s" already exists', $key, $domain)], 409);
        }

        $keyEntity = new LocaleTranslationKeyEntity($domain, $key, $component);
        $this->entityManager->persist($keyEntity);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $keyEntity->getId(),
            'domain' => $keyEntity->getDomainName(),
            'key' => $keyEntity->getKeyName(),
            'component' => $keyEntity->getComponent(),
        ], 201);
    }
}
