<?php

declare(strict_types=1);

namespace App\Localizing\Service\Template;

use App\Localizing\Dto\Template\LocaleTemplateContextDto;
use App\Localizing\ServiceInterface\Catalog\LocaleCatalogScannerInterface;
use App\Localizing\ServiceInterface\Locale\LocaleFallbackResolverInterface;
use App\Localizing\ServiceInterface\Locale\LocaleRegistryInterface;
use App\Localizing\ServiceInterface\Template\LocaleTemplateContextProviderInterface;
use App\Localizing\ServiceInterface\Template\LocaleTemplateSelectorProviderInterface;

final readonly class LocaleTemplateContextProvider implements LocaleTemplateContextProviderInterface
{
    public function __construct(
        private LocaleRegistryInterface $localeRegistry,
        private LocaleFallbackResolverInterface $localeFallbackResolver,
        private LocaleCatalogScannerInterface $localeCatalogScanner,
        private LocaleTemplateSelectorProviderInterface $localeTemplateSelectorProvider,
    ) {
    }

    public function provide(string $currentLocaleCode, array $domains = []): LocaleTemplateContextDto
    {
        $this->localeRegistry->assertAvailable($currentLocaleCode);

        $fallbackLocaleCodes = $this->localeFallbackResolver->resolveFallbackChain($currentLocaleCode);
        $selectedDomains = $this->normalizeDomains($domains);
        $messages = $this->buildMessages($fallbackLocaleCodes, $selectedDomains);
        $domainsWithMessages = array_keys($messages);
        sort($domainsWithMessages);

        return new LocaleTemplateContextDto(
            $currentLocaleCode,
            $this->localeRegistry->getDefaultLocaleCode(),
            $this->localeTemplateSelectorProvider->provide($currentLocaleCode),
            $fallbackLocaleCodes,
            [] === $selectedDomains ? $domainsWithMessages : $selectedDomains,
            $messages,
        );
    }

    /**
     * @param array<int|string, mixed> $domains
     *
     * @return list<string>
     */
    private function normalizeDomains(array $domains): array
    {
        $normalized = [];
        foreach ($domains as $domain) {
            if (!is_scalar($domain)) {
                continue;
            }

            $normalizedDomain = trim((string) $domain);
            if ('' !== $normalizedDomain) {
                $normalized[] = $normalizedDomain;
            }
        }

        $normalized = array_values(array_unique($normalized));
        sort($normalized);

        return $normalized;
    }

    /**
     * @param list<string> $fallbackLocaleCodes
     * @param list<string> $domains
     *
     * @return array<string, array<string, string>>
     */
    private function buildMessages(array $fallbackLocaleCodes, array $domains): array
    {
        $messages = [];
        $messageRanks = [];
        $localeRank = array_flip($fallbackLocaleCodes);

        foreach ($this->localeCatalogScanner->scan() as $message) {
            if (!isset($localeRank[$message->localeCode])) {
                continue;
            }

            if ([] !== $domains && !in_array($message->domainName, $domains, true)) {
                continue;
            }

            $currentRank = $localeRank[$message->localeCode];
            $existingRank = $messageRanks[$message->domainName][$message->keyName] ?? PHP_INT_MAX;

            if ($currentRank <= $existingRank) {
                $messages[$message->domainName][$message->keyName] = $message->message;
                $messageRanks[$message->domainName][$message->keyName] = $currentRank;
            }
        }

        foreach ($messages as $domain => $domainMessages) {
            ksort($domainMessages);
            $messages[$domain] = $domainMessages;
        }
        ksort($messages);

        return $messages;
    }
}
