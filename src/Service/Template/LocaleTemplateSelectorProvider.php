<?php

declare(strict_types=1);

namespace App\Localizing\Service\Template;

use App\Localizing\Dto\Template\LocaleTemplateSelectorOptionDto;
use App\Localizing\ServiceInterface\Locale\LocaleCodeNameConverterInterface;
use App\Localizing\ServiceInterface\Locale\LocaleRegistryInterface;
use App\Localizing\ServiceInterface\Template\LocaleTemplateSelectorProviderInterface;

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
