<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Admin;

use App\Localizing\Entity\LocaleTerminologyEntryEntity;
use App\Localizing\Repository\LocaleTerminologyEntryEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class LocaleTerminologyAdminController
{
    public function __construct(
        private LocaleTerminologyEntryEntityRepository $terminologyRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function list(Request $request): JsonResponse
    {
        $criteria = [];
        $locale = $request->query->get('locale');
        if (is_string($locale) && '' !== trim($locale)) {
            $criteria['localeCode'] = trim($locale);
        }

        $entries = array_map(
            static fn ($entry) => [
                'id' => $entry->getId(),
                'source_term' => $entry->getSourceTerm(),
                'locale' => $entry->getLocaleCode(),
                'approved_term' => $entry->getApprovedTerm(),
                'note' => $entry->getNote(),
            ],
            $this->terminologyRepository->findBy($criteria, ['sourceTerm' => 'ASC', 'localeCode' => 'ASC']),
        );

        return new JsonResponse(['entries' => $entries, 'total' => count($entries)]);
    }

    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $sourceTerm = trim((string) ($data['source_term'] ?? ''));
        $locale = trim((string) ($data['locale'] ?? ''));
        $approvedTerm = trim((string) ($data['approved_term'] ?? ''));
        $note = isset($data['note']) ? (string) $data['note'] : null;

        if ('' === $sourceTerm || '' === $locale || '' === $approvedTerm) {
            return new JsonResponse(['error' => 'source_term, locale and approved_term are required'], 400);
        }

        $existing = $this->terminologyRepository->findOneBy([
            'sourceTerm' => $sourceTerm,
            'localeCode' => $locale,
        ]);

        if (null !== $existing) {
            return new JsonResponse(['error' => sprintf('Term "%s" for locale "%s" already exists', $sourceTerm, $locale)], 409);
        }

        $entry = new LocaleTerminologyEntryEntity($sourceTerm, $locale, $approvedTerm, $note);
        $this->entityManager->persist($entry);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $entry->getId(),
            'source_term' => $entry->getSourceTerm(),
            'locale' => $entry->getLocaleCode(),
            'approved_term' => $entry->getApprovedTerm(),
            'note' => $entry->getNote(),
        ], 201);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $entry = $this->terminologyRepository->find($id);
        if (null === $entry) {
            return new JsonResponse(['error' => 'Terminology entry not found'], 404);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        $approvedTerm = trim((string) ($data['approved_term'] ?? $entry->getApprovedTerm()));
        $note = array_key_exists('note', $data) ? (null !== $data['note'] ? (string) $data['note'] : null) : $entry->getNote();

        if ('' === $approvedTerm) {
            return new JsonResponse(['error' => 'approved_term cannot be empty'], 400);
        }

        $entry->update($approvedTerm, $note);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $entry->getId(),
            'source_term' => $entry->getSourceTerm(),
            'locale' => $entry->getLocaleCode(),
            'approved_term' => $entry->getApprovedTerm(),
            'note' => $entry->getNote(),
        ]);
    }
}
