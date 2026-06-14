<?php

declare(strict_types=1);

namespace App\Localizing\Dto\Template;

final readonly class LocaleTemplateContextDto
{
    /**
     * @param list<LocaleTemplateSelectorOptionDto> $selectorOptions
     * @param list<string>                          $fallbackLocaleCodes
     * @param list<string>                          $domains
     * @param array<string, array<string, string>>  $messages
     */
    public function __construct(
        public string $currentLocaleCode,
        public string $defaultLocaleCode,
        public array $selectorOptions,
        public array $fallbackLocaleCodes,
        public array $domains,
        public array $messages,
    ) {
    }

    /**
     * @return array{
     *     component: string,
     *     currentLocale: string,
     *     defaultLocale: string,
     *     selector: list<array{code: string, name: string, nativeName: string, current: bool, default: bool}>,
     *     fallbackChain: list<string>,
     *     domains: list<string>,
     *     messages: array<string, array<string, string>>
     * }
     */
    public function toArray(): array
    {
        return [
            'component' => 'Localizing',
            'currentLocale' => $this->currentLocaleCode,
            'defaultLocale' => $this->defaultLocaleCode,
            'selector' => array_map(
                static fn (LocaleTemplateSelectorOptionDto $option): array => $option->toArray(),
                $this->selectorOptions,
            ),
            'fallbackChain' => $this->fallbackLocaleCodes,
            'domains' => $this->domains,
            'messages' => $this->messages,
        ];
    }
}
