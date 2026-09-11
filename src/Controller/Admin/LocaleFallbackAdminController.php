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

    public function list(string $slug): JsonResponse
    {
        $fallbacks = array_map(
            static fn ($fb) => [
                'id' => $fb->getId(),
                'locale' => $fb->getLocaleCode(),
                'fallback_locale' => $fb->getFallbackLocaleCode(),
                'position' => $fb->getPosition(),
            ],
            $this->fallbackRepository->findBy(['localeCode' => $slug], ['position' => 'ASC']),
        );

        return new JsonResponse(['locale' => $slug, 'fallbacks' => $fallbacks]);
    }

    public function add(string $slug, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            $data = [];
        }
        $fallbackValue = $data['fallback_locale'] ?? null;
        $positionValue = $data['position'] ?? 0;
        $fallbackCode = is_scalar($fallbackValue) ? trim((string) $fallbackValue) : '';
        $position = is_int($positionValue) ? $positionValue : (is_numeric($positionValue) ? (int) $positionValue : 0);

        if ('' === $fallbackCode) {
            return new JsonResponse(['error' => 'fallback_locale is required'], 400);
        }

        $existing = $this->fallbackRepository->findOneBy([
            'localeCode' => $slug,
            'fallbackLocaleCode' => $fallbackCode,
        ]);

        if (null !== $existing) {
            return new JsonResponse(['error' => sprintf('Fallback "%s" already exists for locale "%s"', $fallbackCode, $slug)], 409);
        }

        $fallback = new LocaleFallbackEntity($slug, $fallbackCode, $position);
        $this->entityManager->persist($fallback);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $fallback->getId(),
            'locale' => $fallback->getLocaleCode(),
            'fallback_locale' => $fallback->getFallbackLocaleCode(),
            'position' => $fallback->getPosition(),
        ], 201);
    }

    public function remove(int $id): JsonResponse
    {
        $fallback = $this->fallbackRepository->find($id);
        if (null === $fallback) {
            return new JsonResponse(['error' => 'Fallback not found'], 404);
        }

        $this->entityManager->remove($fallback);
        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }
}
