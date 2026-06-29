<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Admin;

use App\Localizing\Entity\LocaleEntity;
use App\Localizing\Repository\LocaleEntityRepository;
use App\Localizing\ServiceInterface\Locale\LocaleCodeNameConverterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class LocaleAdminController
{
    public function __construct(
        private LocaleEntityRepository $localeRepository,
        private LocaleCodeNameConverterInterface $codeNameConverter,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function list(): JsonResponse
    {
        $locales = array_map(
            static fn ($locale) => [
                'id' => $locale->getId(),
                'code' => $locale->getCode(),
                'name' => $locale->getName(),
                'enabled' => $locale->isEnabled(),
                'priority' => $locale->getPriority(),
            ],
            $this->localeRepository->findBy([], ['priority' => 'DESC', 'code' => 'ASC']),
        );

        return new JsonResponse(['locales' => $locales, 'total' => count($locales)]);
    }

    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $code = trim((string) ($data['code'] ?? ''));
        $name = trim((string) ($data['name'] ?? ''));
        $enabled = (bool) ($data['enabled'] ?? true);
        $priority = (int) ($data['priority'] ?? 0);

        if ('' === $code) {
            return new JsonResponse(['error' => 'code is required'], 400);
        }

        if (null !== $this->localeRepository->findOneBy(['code' => $code])) {
            return new JsonResponse(['error' => sprintf('Locale "%s" already exists', $code)], 409);
        }

        if ('' === $name) {
            $name = $this->codeNameConverter->convertCodeToName($code);
        }

        $locale = new LocaleEntity($code, $name, $enabled, $priority);
        $this->entityManager->persist($locale);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $locale->getId(),
            'code' => $locale->getCode(),
            'name' => $locale->getName(),
            'enabled' => $locale->isEnabled(),
            'priority' => $locale->getPriority(),
        ], 201);
    }

    public function enable(string $code): JsonResponse
    {
        $locale = $this->localeRepository->findOneBy(['code' => $code]);
        if (null === $locale) {
            return new JsonResponse(['error' => sprintf('Locale "%s" not found', $code)], 404);
        }

        $locale->enable();
        $this->entityManager->flush();

        return new JsonResponse(['code' => $locale->getCode(), 'enabled' => true]);
    }

    public function disable(string $code): JsonResponse
    {
        $locale = $this->localeRepository->findOneBy(['code' => $code]);
        if (null === $locale) {
            return new JsonResponse(['error' => sprintf('Locale "%s" not found', $code)], 404);
        }

        $locale->disable();
        $this->entityManager->flush();

        return new JsonResponse(['code' => $locale->getCode(), 'enabled' => false]);
    }
}
