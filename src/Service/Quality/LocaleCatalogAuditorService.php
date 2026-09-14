<?php

declare(strict_types=1);

namespace App\Localizing\Service\Quality;

use App\Localizing\DTO\Catalog\LocaleCatalogMessageDTO;
use App\Localizing\ServiceInterface\LocaleRegistryServiceInterface;
use App\Localizing\ServiceInterface\Quality\LocaleCatalogAuditorServiceInterface;

/**
 * Detects unsupported locales, empty messages, and missing default-locale translations.
 */
final readonly class LocaleCatalogAuditorService implements LocaleCatalogAuditorServiceInterface
{
    public function __construct(private LocaleRegistryServiceInterface $localeRegistry)
    {
    }

    public function audit(array $messages): array
    {
        $findings = [];
        $availableLocales = $this->localeRegistry->getAvailableLocaleCodes();
        $defaultLocale = $this->localeRegistry->getDefaultLocaleCode();
        $index = [];

        foreach ($messages as $message) {
            $index[$message->domainName][$message->keyName][$message->localeCode] = $message;
            if (!in_array($message->localeCode, $availableLocales, true)) {
                $findings[] = $this->finding('warning', 'unsupported_locale', $message, sprintf('Locale "%s" is not registered.', $message->localeCode));
            }
            if ('' === $message->message) {
                $findings[] = $this->finding('warning', 'empty_message', $message, 'Message is empty.');
            }
        }

        foreach ($index as $domain => $keys) {
            foreach ($keys as $key => $locales) {
                if (!isset($locales[$defaultLocale])) {
                    $findings[] = [
                        'severity' => 'error',
                        'code' => 'missing_default_locale',
                        'domain' => $domain,
                        'key' => $key,
                        'locale' => $defaultLocale,
                        'message' => sprintf('Default locale "%s" message is missing.', $defaultLocale),
                    ];
                }
            }
        }

        return $findings;
    }

    /** @return array{severity:string, code:string, domain:string, key:string, locale:?string, message:string} */
    private function finding(string $severity, string $code, LocaleCatalogMessageDTO $message, string $text): array
    {
        return [
            'severity' => $severity,
            'code' => $code,
            'domain' => $message->domainName,
            'key' => $message->keyName,
            'locale' => $message->localeCode,
            'message' => $text,
        ];
    }
}
