<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Admin;

use App\Localizing\Entity\LocaleEntity;
use App\Localizing\Repository\LocaleEntityRepository;
use App\Localizing\ServiceInterface\LocaleCodeNameConverterServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Manages locale registration, ordering, and enabled state through administrative endpoints.
 */
final readonly class LocaleAdminController
{
    public function __construct(
        private LocaleEntityRepository $localeRepository,
        private LocaleCodeNameConverterServiceInterface $codeNameConverter,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Returns registered locales ordered by operational priority and locale code.
     */
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

    /**
     * Validates and persists a unique locale, deriving its display name when omitted.
     */
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            $data = [];
        }
        $codeValue = $data['code'] ?? null;
        $nameValue = $data['name'] ?? null;
        $enabledValue = $data['enabled'] ?? true;
        $priorityValue = $data['priority'] ?? 0;
        $code = is_scalar($codeValue) ? trim((string) $codeValue) : '';
        $name = is_scalar($nameValue) ? trim((string) $nameValue) : '';
        $enabled = is_bool($enabledValue) ? $enabledValue : true;
        $priority = is_int($priorityValue) ? $priorityValue : (is_numeric($priorityValue) ? (int) $priorityValue : 0);

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

    /**
     * Enables an existing locale or reports that the requested locale does not exist.
     */
    public function enable(string $slug): JsonResponse
    {
        $locale = $this->localeRepository->findOneBy(['code' => $slug]);
        if (null === $locale) {
            return new JsonResponse(['error' => sprintf('Locale "%s" not found', $slug)], 404);
        }

        $locale->enable();
        $this->entityManager->flush();

        return new JsonResponse(['code' => $locale->getCode(), 'enabled' => true]);
    }

    /**
     * Disables an existing locale or reports that the requested locale does not exist.
     */
    public function disable(string $slug): JsonResponse
    {
        $locale = $this->localeRepository->findOneBy(['code' => $slug]);
        if (null === $locale) {
            return new JsonResponse(['error' => sprintf('Locale "%s" not found', $slug)], 404);
        }

        $locale->disable();
        $this->entityManager->flush();

        return new JsonResponse(['code' => $locale->getCode(), 'enabled' => false]);
    }
}
