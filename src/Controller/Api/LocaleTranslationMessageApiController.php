<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Api;

use App\Localizing\Repository\LocaleTranslationMessageEntityRepository;
use App\Localizing\Resolver\LocaleFallbackResolverInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resolves translation messages across the configured locale fallback chain.
 */
final readonly class LocaleTranslationMessageApiController
{
    public function __construct(
        private LocaleTranslationMessageEntityRepository $messageRepository,
        private LocaleFallbackResolverInterface $fallbackResolver,
    ) {
    }

    /**
     * Returns the first matching translation message or a not-found response after fallback exhaustion.
     */
    public function resolve(Request $request): JsonResponse
    {
        $locale = trim((string) $request->query->get('locale', ''));
        $domain = trim((string) $request->query->get('domain', ''));
        $key = trim((string) $request->query->get('key', ''));

        if ('' === $locale || '' === $domain || '' === $key) {
            return new JsonResponse(['error' => 'locale, domain and key are required'], 400);
        }

        foreach ($this->fallbackResolver->resolveFallbackChain($locale) as $fallbackLocale) {
            $message = $this->messageRepository->findOneBy([
                'localeCode' => $fallbackLocale,
                'domainName' => $domain,
                'keyName' => $key,
            ]);

            if (null !== $message) {
                return new JsonResponse([
                    'locale' => $locale,
                    'resolved_locale' => $fallbackLocale,
                    'domain' => $domain,
                    'key' => $key,
                    'message' => $message->getMessage(),
                ]);
            }
        }

        return new JsonResponse([
            'locale' => $locale,
            'resolved_locale' => null,
            'domain' => $domain,
            'key' => $key,
            'message' => null,
        ], 404);
    }
}
