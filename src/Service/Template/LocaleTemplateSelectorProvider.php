<?php

declare(strict_types=1);

namespace App\Service\Template;

use App\Dto\Template\LocaleTemplateSelectorOptionDto;
use App\ServiceInterface\Locale\LocaleCodeNameConverterInterface;
use App\ServiceInterface\Locale\LocaleRegistryInterface;
use App\ServiceInterface\Template\LocaleTemplateSelectorProviderInterface;

final readonly class LocaleTemplateSelectorProvider implements LocaleTemplateSelectorProviderInterface
{
    public function __construct(
        private LocaleRegistryInterface $localeRegistry,
        private LocaleCodeNameConverterInterface $localeCodeNameConverter,
    ) {
    }

    public function provide(string $currentLocaleCode, ?string $displayLocaleCode = null): array
    {
        $this->localeRegistry->assertAvailable($currentLocaleCode);
        $defaultLocaleCode = $this->localeRegistry->getDefaultLocaleCode();
        $options = [];

        foreach ($this->localeRegistry->getAvailableLocaleCodes() as $localeCode) {
            $options[] = new LocaleTemplateSelectorOptionDto(
                $localeCode,
                $this->localeCodeNameConverter->convertCodeToName($localeCode, $displayLocaleCode ?? $currentLocaleCode),
                $this->localeCodeNameConverter->convertCodeToName($localeCode, $localeCode),
                $localeCode === $currentLocaleCode,
                $localeCode === $defaultLocaleCode,
            );
        }

        return $options;
    }
}
