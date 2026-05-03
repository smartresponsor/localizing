<?php

declare(strict_types=1);

namespace App\Localizing\Service\Template;

use App\Localizing\Service\Catalog\LocaleCatalogMessage;
use App\Localizing\ServiceInterface\Catalog\LocaleCatalogScannerInterface;
use App\Localizing\ServiceInterface\Locale\LocaleFallbackResolverInterface;
use App\Localizing\ServiceInterface\Locale\LocaleRegistryInterface;
use App\Localizing\ServiceInterface\Template\LocaleTemplateContextProviderInterface;
use App\Localizing\ServiceInterface\Template\LocaleTemplateSelectorProviderInterface;
use App\Localizing\ValueObject\LocaleTemplateContext;

final readonly class LocaleTemplateContextProvider implements LocaleTemplateContextProviderInterface
{
    public function __construct(
        private LocaleRegistryInterface $localeRegistry,
        private LocaleFallbackResolverInterface $localeFallbackResolver,
        private LocaleCatalogScannerInterface $localeCatalogScanner,
        private LocaleTemplateSelectorProviderInterface $localeTemplateSelectorProvider,
    ) {
    }

    public function provide(string $currentLocaleCode, array $domains = []): LocaleTemplateContext
    {
        $this->localeRegistry->assertAvailable($currentLocaleCode);

        $fallbackLocaleCodes = $this->localeFallbackResolver->resolveFallbackChain($currentLocaleCode);
        $selectedDomains = $this->normalizeDomains($domains);
        $messages = $this->buildMessages($fallbackLocaleCodes, $selectedDomains);
        $domainsWithMessages = array_keys($messages);
        sort($domainsWithMessages);

        return new LocaleTemplateContext(
            $currentLocaleCode,
            $this->localeRegistry->getDefaultLocaleCode(),
            $this->localeTemplateSelectorProvider->provide($currentLocaleCode),
            $fallbackLocaleCodes,
            [] === $selectedDomains ? $domainsWithMessages : $selectedDomains,
            $messages,
        );
    }

    /** @param list<string> $domains */
    private function normalizeDomains(array $domains): array
    {
        $normalized = array_values(array_unique(array_filter(array_map(
            static fn (string $domain): string => trim($domain),
            $domains,
        ))));
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
            if (!$message instanceof LocaleCatalogMessage) {
                continue;
            }

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
