<?php

declare(strict_types=1);

namespace App\ServiceInterface\Template;

use App\Dto\Template\LocaleTemplateSelectorOptionDto;

interface LocaleTemplateSelectorProviderInterface
{
    /** @return list<LocaleTemplateSelectorOptionDto> */
    public function provide(string $currentLocaleCode, ?string $displayLocaleCode = null): array;
}
