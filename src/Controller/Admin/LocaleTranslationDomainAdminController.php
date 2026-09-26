<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Admin;

use App\Localizing\Entity\LocaleTranslationDomainEntity;
use App\Localizing\Repository\LocaleTranslationDomainEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Manages translation-domain registration and discovery through administrative endpoints.
 */
final readonly class LocaleTranslationDomainAdminController
{
    public function __construct(
        private LocaleTranslationDomainEntityRepository $domainRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Returns all registered translation domains ordered by their stable domain name.
     */
    public function list(): JsonResponse
    {
        $domains = array_map(
            static fn ($domain) => [
                'id' => $domain->getId(),
                'name' => $domain->getName(),
                'component' => $domain->getComponent(),
            ],
            $this->domainRepository->findBy([], ['name' => 'ASC']),
        );

        return new JsonResponse(['domains' => $domains, 'total' => count($domains)]);
    }

    /**
     * Validates and persists a unique translation domain for its owning component.
     */
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            $data = [];
        }
        $nameValue = $data['name'] ?? null;
        $componentValue = $data['component'] ?? null;
        $name = is_scalar($nameValue) ? trim((string) $nameValue) : '';
        $component = is_scalar($componentValue) ? trim((string) $componentValue) : '';

        if ('' === $name || '' === $component) {
            return new JsonResponse(['error' => 'name and component are required'], 400);
        }

        if (null !== $this->domainRepository->findOneBy(['name' => $name])) {
            return new JsonResponse(['error' => sprintf('Domain "%s" already exists', $name)], 409);
        }

        $domain = new LocaleTranslationDomainEntity($name, $component);
        $this->entityManager->persist($domain);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $domain->getId(),
            'name' => $domain->getName(),
            'component' => $domain->getComponent(),
        ], 201);
    }
}
