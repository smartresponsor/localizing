<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Api;

use App\Localizing\Repository\LocaleTerminologyEntryEntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides read-only terminology lookup for runtime localization consumers.
 */
final readonly class LocaleTerminologyApiController
{
    public function __construct(
        private LocaleTerminologyEntryEntityRepository $terminologyRepository,
    ) {
    }

    /**
     * Returns terminology entries filtered by optional locale and source-term criteria.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $criteria = [];
        $locale = $request->query->get('locale');
        if (is_string($locale) && '' !== trim($locale)) {
            $criteria['localeCode'] = trim($locale);
        }

        $term = $request->query->get('term');
        if (is_string($term) && '' !== trim($term)) {
            $criteria['sourceTerm'] = trim($term);
        }

        $entries = array_map(
            static fn ($entry) => [
                'source_term' => $entry->getSourceTerm(),
                'locale' => $entry->getLocaleCode(),
                'approved_term' => $entry->getApprovedTerm(),
                'note' => $entry->getNote(),
            ],
            $this->terminologyRepository->findBy($criteria, ['sourceTerm' => 'ASC']),
        );

        return new JsonResponse(['entries' => $entries, 'total' => count($entries)]);
    }
}
