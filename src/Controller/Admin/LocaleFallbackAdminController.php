<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Admin;

use App\Localizing\Entity\LocaleFallbackEntity;
use App\Localizing\Repository\LocaleFallbackEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class LocaleFallbackAdminController
{
    public function __construct(
        private LocaleFallbackEntityRepository $fallbackRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function list(string $code): JsonResponse
    {
        $fallbacks = array_map(
            static fn ($fb) => [
                'id' => $fb->getId(),
                'locale' => $fb->getLocaleCode(),
                'fallback_locale' => $fb->getFallbackLocaleCode(),
                'position' => $fb->getPosition(),
            ],
            $this->fallbackRepository->findBy(['localeCode' => $code], ['position' => 'ASC']),
        );

        return new JsonResponse(['locale' => $code, 'fallbacks' => $fallbacks]);
    }

    public function add(string $code, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $fallbackCode = trim((string) ($data['fallback_locale'] ?? ''));
        $position = (int) ($data['position'] ?? 0);

        if ('' === $fallbackCode) {
            return new JsonResponse(['error' => 'fallback_locale is required'], 400);
        }

        $existing = $this->fallbackRepository->findOneBy([
            'localeCode' => $code,
            'fallbackLocaleCode' => $fallbackCode,
        ]);

        if (null !== $existing) {
            return new JsonResponse(['error' => sprintf('Fallback "%s" already exists for locale "%s"', $fallbackCode, $code)], 409);
        }

        $fallback = new LocaleFallbackEntity($code, $fallbackCode, $position);
        $this->entityManager->persist($fallback);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $fallback->getId(),
            'locale' => $fallback->getLocaleCode(),
            'fallback_locale' => $fallback->getFallbackLocaleCode(),
            'position' => $fallback->getPosition(),
        ], 201);
    }

    public function remove(string $code, int $id): JsonResponse
    {
        $fallback = $this->fallbackRepository->findOneBy(['id' => $id, 'localeCode' => $code]);
        if (null === $fallback) {
            return new JsonResponse(['error' => 'Fallback not found'], 404);
        }

        $this->entityManager->remove($fallback);
        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }
}
